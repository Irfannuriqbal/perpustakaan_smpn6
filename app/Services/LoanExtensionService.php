<?php

namespace App\Services;

use App\Models\Loan;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LoanExtensionService
{
    public const MAX_EXTENSION_COUNT = 1;
    public const EXTENSION_DAYS = 7;

    public function __construct(private readonly LoanStatusService $loanStatusService)
    {
    }

    public function extend(Loan $loan): array
    {
        return DB::transaction(function () use ($loan): array {
            $loan = Loan::query()->lockForUpdate()->findOrFail($loan->id);
            $this->validateCanExtend($loan);

            $oldDueDate = $loan->due_date instanceof Carbon
                ? $loan->due_date->copy()
                : Carbon::parse($loan->due_date);
            $newDueDate = $oldDueDate->copy()->addDays(self::EXTENSION_DAYS);

            $loan->update([
                'due_date' => $newDueDate->toDateString(),
                'extension_count' => $loan->extension_count + 1,
            ]);

            return [
                'loan' => $loan->fresh(['student', 'user', 'items.book']),
                'old_due_date' => $oldDueDate,
                'new_due_date' => $newDueDate,
            ];
        });
    }

    public function canExtend(Loan $loan): bool
    {
        try {
            $this->validateCanExtend($loan);

            return true;
        } catch (ValidationException) {
            return false;
        }
    }

    private function validateCanExtend(Loan $loan): void
    {
        if ($this->loanStatusService->isReturned($loan)) {
            throw ValidationException::withMessages([
                'loan' => 'Transaksi yang sudah dikembalikan tidak dapat diperpanjang.',
            ]);
        }

        if ($this->loanStatusService->isOverdue($loan)) {
            throw ValidationException::withMessages([
                'loan' => 'Transaksi yang sudah terlambat tidak dapat diperpanjang.',
            ]);
        }

        if ($loan->status !== 'dipinjam') {
            throw ValidationException::withMessages([
                'loan' => 'Hanya transaksi aktif yang dapat diperpanjang.',
            ]);
        }

        if ($loan->extension_count >= self::MAX_EXTENSION_COUNT) {
            throw ValidationException::withMessages([
                'loan' => 'Peminjaman hanya dapat diperpanjang satu kali.',
            ]);
        }
    }
}
