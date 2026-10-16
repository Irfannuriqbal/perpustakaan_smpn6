<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use RuntimeException;
use Symfony\Component\Process\Process;

class LibraryBackupService
{
    private const DIRECTORY = 'backups';
    private const RETENTION_DAYS = 30;

    public function create(): string
    {
        $connection = config('database.default');
        $database = config("database.connections.{$connection}");

        if (($database['driver'] ?? null) !== 'mysql') {
            throw new RuntimeException('Backup otomatis hanya mendukung koneksi MySQL.');
        }

        $directory = $this->directory();
        File::ensureDirectoryExists($directory);

        $filename = 'library-'.now()->format('Ymd-His').'.sql';
        $path = $directory.DIRECTORY_SEPARATOR.$filename;
        $binary = env('MYSQLDUMP_PATH', 'mysqldump');
        $command = [
            $binary,
            '--host='.(string) ($database['host'] ?? '127.0.0.1'),
            '--port='.(string) ($database['port'] ?? 3306),
            '--user='.(string) ($database['username'] ?? ''),
            '--single-transaction',
            '--routines',
            '--events',
            '--skip-lock-tables',
            '--result-file='.$path,
            (string) ($database['database'] ?? ''),
        ];
        $process = new Process($command, null, ['MYSQL_PWD' => (string) ($database['password'] ?? '')]);
        $process->setTimeout(300);
        $process->run();

        if (! $process->isSuccessful() || ! File::exists($path)) {
            File::delete($path);

            throw new RuntimeException('Backup database gagal. Periksa mysqldump dan konfigurasi database.');
        }

        $this->prune();

        return $filename;
    }

    public function all(): Collection
    {
        $directory = $this->directory();

        if (! File::isDirectory($directory)) {
            return collect();
        }

        return collect(File::files($directory))
            ->filter(fn ($file): bool => $this->isValidFilename($file->getFilename()))
            ->sortByDesc(fn ($file) => $file->getMTime())
            ->map(fn ($file): array => [
                'filename' => $file->getFilename(),
                'size' => $file->getSize(),
                'created_at' => now()->setTimestamp($file->getMTime()),
            ])
            ->values();
    }

    public function path(string $filename): string
    {
        if (! $this->isValidFilename($filename)) {
            abort(404);
        }

        $path = $this->directory().DIRECTORY_SEPARATOR.$filename;

        abort_unless(File::isFile($path), 404);

        return $path;
    }

    private function prune(): void
    {
        $cutoff = now()->subDays(self::RETENTION_DAYS)->getTimestamp();

        foreach ($this->all() as $backup) {
            $path = $this->directory().DIRECTORY_SEPARATOR.$backup['filename'];

            if (File::lastModified($path) < $cutoff) {
                File::delete($path);
            }
        }
    }

    private function directory(): string
    {
        return storage_path('app/'.self::DIRECTORY);
    }

    private function isValidFilename(string $filename): bool
    {
        return preg_match('/^library-\d{8}-\d{6}\.sql$/', $filename) === 1;
    }
}
