<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Loan;
use App\Models\LoanItem;
use App\Models\Student;
use App\Services\LoanExtensionService;
use App\Services\LoanStatusService;
use App\Services\ReturnService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;

class MemberLoanController extends Controller
{
    public function __construct(
        private readonly LoanStatusService    $loanStatusService,
        private readonly LoanExtensionService $extensionService,
        private readonly ReturnService        $returnService,
    ) {
    }

    // ─── Katalog Buku ──────────────────────────────────────────────────────────

    public function catalog(Request $request): View
    {
        $search     = $request->input('search');
        $categoryId = $request->input('category_id');
        $available  = $request->input('available');

        $books = Book::with('category')
            ->where('status', 'aktif')
            ->when($search, function ($q) use ($search): void {
                $q->where(function ($q2) use ($search): void {
                    $q2->where('title', 'like', "%{$search}%")
                       ->orWhere('author', 'like', "%{$search}%")
                       ->orWhere('isbn', 'like', "%{$search}%")
                       ->orWhere('book_code', 'like', "%{$search}%");
                });
            })
            ->when($categoryId, fn ($q) => $q->where('category_id', $categoryId))
            ->when($available === '1', fn ($q) => $q->where('available_stock', '>', 0))
            ->orderBy('title')
            ->paginate(12)
            ->withQueryString();

        $categories = \App\Models\Category::orderBy('name')->get();

        return view('members.catalog', compact('books', 'categories', 'search', 'categoryId', 'available'));
    }

    // ─── Riwayat & Pinjaman Aktif ──────────────────────────────────────────────

    public function loans(Request $request): View
    {
        /** @var Student $member */
        $member = auth('member')->user();

        $this->loanStatusService->syncOverdueStatuses();

        $filter = $request->input('filter', 'aktif'); // aktif | semua

        $query = Loan::with('items.book')->where('student_id', $member->id);

        if ($filter === 'aktif') {
            $query->whereNull('returned_at')->whereIn('status', ['dipinjam', 'terlambat']);
        }

        $loans = $query->orderByDesc('loan_date')->paginate(10)->withQueryString();

        return view('members.loans', [
            'member'            => $member,
            'loans'             => $loans,
            'filter'            => $filter,
            'loanStatusService' => $this->loanStatusService,
            'extensionService'  => $this->extensionService,
        ]);
    }

    // ─── Perpanjang Pinjaman ───────────────────────────────────────────────────

    public function extend(Request $request, Loan $loan): RedirectResponse
    {
        /** @var Student $member */
        $member = auth('member')->user();

        // Pastikan loan milik member yg login
        if ($loan->student_id !== $member->id) {
            abort(403);
        }

        try {
            $result = $this->extensionService->extend($loan);

            return redirect()->route('members.loans')
                ->with('success', 'Peminjaman berhasil diperpanjang. Jatuh tempo baru: ' . $result['new_due_date']->format('d M Y') . '.');
        } catch (ValidationException $e) {
            return back()->with('error', collect($e->errors())->flatten()->first());
        }
    }

    // ─── Pengembalian Mandiri ──────────────────────────────────────────────────

    /**
     * Show pending return items for the logged-in member.
     */
    public function returns(Request $request): View
    {
        /** @var Student $member */
        $member = auth('member')->user();

        $this->loanStatusService->syncOverdueStatuses();

        $activeLoans = Loan::with(['items.book'])
            ->where('student_id', $member->id)
            ->whereNull('returned_at')
            ->whereIn('status', ['dipinjam', 'terlambat'])
            ->orderBy('due_date')
            ->get();

        return view('members.returns', [
            'member'            => $member,
            'activeLoans'       => $activeLoans,
            'loanStatusService' => $this->loanStatusService,
        ]);
    }

    /**
     * Member submits a return request (creates a flag — actual processing still by admin).
     * Here we allow the member to report that they have physically returned the book,
     * pending admin confirmation. For simplicity, we process it directly if desired.
     */
    public function storeReturn(Request $request, LoanItem $loanItem): RedirectResponse
    {
        /** @var Student $member */
        $member = auth('member')->user();

        // Verify ownership
        if ($loanItem->loan?->student_id !== $member->id) {
            abort(403);
        }

        try {
            $this->returnService->processReturn($loanItem);

            return redirect()->route('members.returns')
                ->with('success', 'Pengembalian buku "' . ($loanItem->book?->title ?? '') . '" berhasil dicatat.');
        } catch (ValidationException $e) {
            return back()->with('error', collect($e->errors())->flatten()->first());
        }
    }
}
