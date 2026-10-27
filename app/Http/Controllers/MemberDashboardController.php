<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Student;
use App\Services\LoanStatusService;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class MemberDashboardController extends Controller
{
    public function __construct(private readonly LoanStatusService $loanStatusService)
    {
    }

    public function home(): View
    {
        /** @var Student $member */
        $member = auth('member')->user();
        $loans = $this->activeLoans($member);
        $today = now()->startOfDay();
        $dueSoon = $loans->filter(fn (Loan $loan): bool => $loan->due_date
            && $loan->due_date->greaterThanOrEqualTo($today)
            && $loan->due_date->lessThanOrEqualTo($today->copy()->addDays(3)));

        return view('members.home', [
            'member' => $member,
            'loans' => $loans->take(4),
            'activeItemsCount' => $loans->sum(fn (Loan $loan): int => $loan->items
                ->whereNull('returned_at')
                ->whereIn('status', ['dipinjam', 'terlambat'])
                ->count()),
            'overdueLoans' => $loans->filter(fn (Loan $loan): bool => $this->loanStatusService->isOverdue($loan)),
            'dueSoonLoans' => $dueSoon,
            'nearestDueDate' => $loans->first()?->due_date,
            'loanStatusService' => $this->loanStatusService,
        ]);
    }

    public function notifications(): View
    {
        /** @var Student $member */
        $member = auth('member')->user();
        $loans = $this->activeLoans($member);
        $today = now()->startOfDay();
        $overdueLoans = $loans->filter(fn (Loan $loan): bool => $this->loanStatusService->isOverdue($loan));
        $dueSoonLoans = $loans->filter(fn (Loan $loan): bool => $loan->due_date
            && $loan->due_date->greaterThanOrEqualTo($today)
            && $loan->due_date->lessThanOrEqualTo($today->copy()->addDays(3)));

        return view('members.notifications', [
            'member' => $member,
            'overdueLoans' => $overdueLoans,
            'dueSoonLoans' => $dueSoonLoans,
            'loanStatusService' => $this->loanStatusService,
        ]);
    }

    private function activeLoans(Student $member): Collection
    {
        $this->loanStatusService->syncOverdueStatuses();

        return Loan::query()
            ->with('items.book')
            ->where('student_id', $member->id)
            ->whereNull('returned_at')
            ->whereIn('status', ['dipinjam', 'terlambat'])
            ->orderBy('due_date')
            ->get();
    }
}
