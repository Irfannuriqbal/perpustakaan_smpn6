<?php

namespace App\Console\Commands;

use App\Services\LibraryBackupService;
use Illuminate\Console\Command;
use Throwable;

class CreateLibraryBackup extends Command
{
    protected $signature = 'library:backup';
    protected $description = 'Create a MySQL backup for the library application';

    public function handle(LibraryBackupService $backups): int
    {
        try {
            $filename = $backups->create();
            $this->info("Backup berhasil dibuat: {$filename}");

            return self::SUCCESS;
        } catch (Throwable $exception) {
            report($exception);
            $this->error('Backup database gagal. Periksa log aplikasi dan konfigurasi mysqldump.');

            return self::FAILURE;
        }
    }
}
