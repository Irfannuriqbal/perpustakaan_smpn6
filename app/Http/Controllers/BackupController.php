<?php

namespace App\Http\Controllers;

use App\Services\LibraryBackupService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;

class BackupController extends Controller
{
    public function __construct(private readonly LibraryBackupService $backups)
    {
    }

    public function index(): View
    {
        return view('backups.index', ['backups' => $this->backups->all()]);
    }

    public function store(): RedirectResponse
    {
        try {
            $filename = $this->backups->create();

            return redirect()->route('backups.index')->with('success', "Backup {$filename} berhasil dibuat.");
        } catch (Throwable $exception) {
            report($exception);

            return redirect()->route('backups.index')->with('error', 'Backup database gagal. Periksa mysqldump dan konfigurasi database.');
        }
    }

    public function download(string $filename): BinaryFileResponse
    {
        return response()->download($this->backups->path($filename));
    }
}
