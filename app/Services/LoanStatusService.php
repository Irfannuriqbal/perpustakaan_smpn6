<?php

namespace App\Services;

use App\Models\Loan;
use App\Models\LoanItem;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class LoanStatusService
{
    public function isReturned(Loan $loan): bool
    {
        return $loan->status === 'dikembalikan' || $loan->returned_at !== null;
    }

    public function isOverdue(Loan $loan): bool
    {
        if ($this->isReturned($loan) || ! $loan->due_date) {
            return false;
        }

        return now()->startOfDay()->greaterThan($this->dueDate($loan));
    }

    public function lateDays(Loan $loan): int
    {
        if (! $this->isOverdue($loan)) {
            return 0;
        }

        return (int) $this->dueDate($loan)->diffInDays(now()->startOfDay());
    }

    public function displayStatus(Loan $loan): string
    {
        if ($this->isReturned($loan)) {
            return 'dikembalikan';
        }

        if ($this->isOverdue($loan)) {
            return 'terlambat';
        }

        return 'dipinjam';
    }

    public function displayLabel(Loan $loan): string
    {
        return match ($this->displayStatus($loan)) {
            'dikembalikan' => 'Dikembalikan',
            'terlambat' => 'Terlambat',
            default => 'Dipinjam',
        };
    }

    public function badgeClass(Loan $loan): string
    {
        return match ($this->displayStatus($loan)) {
            'dikembalikan' => 'bg-emerald-50 text-emerald-800 ring-emerald-200',
            'terlambat' => 'bg-amber-50 text-amber-800 ring-amber-200',
            default => 'bg-blue-50 text-blue-800 ring-blue-200',
        };
    }

    public function syncOverdueStatuses(): int
    {
        return DB::transaction(function (): int {
            $loanIds = Loan::query()
                ->where('status', 'dipinjam')
                ->whereNull('returned_at')
                ->whereDate('due_date', '<', now()->toDateString())
                ->lockForUpdate()
                ->pluck('id');

            if ($loanIds->isEmpty()) {
                return 0;
            }

            Loan::whereIn('id', $loanIds)->update(['status' => 'terlambat']);

            LoanItem::whereIn('loan_id', $loanIds)
                ->whereNull('returned_at')
                ->where('status', 'dipinjam')
                ->update(['status' => 'terlambat']);

            return $loanIds->count();
        });
    }

    private function dueDate(Loan $loan): Carbon
    {
        return ($loan->due_date instanceof Carbon ? $loan->due_date->copy() : Carbon::parse($loan->due_date))->startOfDay();
    }
}
