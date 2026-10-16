<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Student;
use App\Services\CodeImageService;
use App\Services\LoanStatusService;
use Illuminate\View\View;

class MemberCardController extends Controller
{
    public function __construct(private readonly LoanStatusService $loanStatusService)
    {
    }

    public function show(CodeImageService $codes): View
    {
        /** @var Student $member */
        $member = auth('member')->user();

        $this->loanStatusService->syncOverdueStatuses();

        $loans = Loan::query()
            ->with('items.book')
            ->where('student_id', $member->id)
            ->whereNull('returned_at')
            ->whereIn('status', ['dipinjam', 'terlambat'])
            ->orderBy('due_date')
            ->get();

        $activeItemsCount = $loans->sum(fn (Loan $loan): int => $loan->items
            ->whereNull('returned_at')
            ->whereIn('status', ['dipinjam', 'terlambat'])
            ->count());
        $nearestDueDate = $loans->first()?->due_date;
        $qrCode = $codes->qrSvg($member->member_code);

        return view('members.card', [
            'member' => $member,
            'photoUrl' => $member->photo_path ? asset('storage/'.$member->photo_path) : null,
            'loans' => $loans,
            'activeItemsCount' => $activeItemsCount,
            'nearestDueDate' => $nearestDueDate,
            'qrCode' => $qrCode,
            'loanStatusService' => $this->loanStatusService,
        ]);
    }
}
