<?php

namespace App\Services;

use App\Models\Book;
use App\Models\Loan;
use App\Models\LoanItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReturnService
{
    public function __construct(private readonly LoanStatusService $loanStatusService)
    {
    }

    public function findActiveLoanItemByBookCode(string $bookCode): ?LoanItem
    {
        return LoanItem::query()
            ->with(['book', 'loan.student'])
            ->whereNull('returned_at')
            ->whereIn('status', ['dipinjam', 'terlambat'])
            ->whereHas('book', fn ($query) => $query->where('book_code', $bookCode))
            ->oldest('id')
            ->first();
    }

    public function isLate(LoanItem $loanItem): bool
    {
        return $loanItem->loan ? $this->loanStatusService->isOverdue($loanItem->loan) : false;
    }

    public function lateDays(LoanItem $loanItem): int
    {
        return $loanItem->loan ? $this->loanStatusService->lateDays($loanItem->loan) : 0;
    }

    public function processReturn(LoanItem $loanItem): LoanItem
    {
        return DB::transaction(function () use ($loanItem): LoanItem {
            $loanItem = LoanItem::query()
                ->with(['book', 'loan.items'])
                ->lockForUpdate()
                ->findOrFail($loanItem->id);

            if ($loanItem->returned_at !== null || $loanItem->status === 'dikembalikan') {
                throw ValidationException::withMessages([
                    'book_code' => 'Buku ini sudah dikembalikan dan tidak boleh diproses dua kali.',
                ]);
            }

            if (! in_array($loanItem->status, ['dipinjam', 'terlambat'], true)) {
                throw ValidationException::withMessages([
                    'book_code' => 'Buku ini tidak sedang dalam status dipinjam.',
                ]);
            }

            $book = Book::query()->lockForUpdate()->findOrFail($loanItem->book_id);

            if ($book->available_stock >= $book->stock) {
                throw ValidationException::withMessages([
                    'book_code' => 'Stok tersedia tidak boleh melebihi stok buku.',
                ]);
            }

            $loan = Loan::query()->lockForUpdate()->findOrFail($loanItem->loan_id);

            $loanItem->update([
                'returned_at' => now(),
                'status' => 'dikembalikan',
            ]);

            $book->increment('available_stock');

            $hasActiveItems = $loan->items()
                ->where('id', '!=', $loanItem->id)
                ->whereNull('returned_at')
                ->whereIn('status', ['dipinjam', 'terlambat'])
                ->exists();

            if (! $hasActiveItems) {
                $loan->update([
                    'returned_at' => now(),
                    'status' => 'dikembalikan',
                ]);
            }

            return $loanItem->fresh(['book', 'loan.student']);
        });
    }
}
