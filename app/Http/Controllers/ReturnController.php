<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchReturnBookRequest;
use App\Models\LoanItem;
use App\Services\LoanStatusService;
use App\Services\ReturnService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class ReturnController extends Controller
{
    private const DRAFT_SESSION_KEY = 'return_loan_item_id';

    public function __construct(
        private readonly ReturnService $returnService,
        private readonly LoanStatusService $loanStatusService,
    ) {
    }

    public function index(Request $request): View
    {
        $this->loanStatusService->syncOverdueStatuses();

        $loanItem = null;
        $lateDays = 0;
        $isLate = false;

        if ($request->session()->has(self::DRAFT_SESSION_KEY)) {
            $loanItem = LoanItem::with(['book', 'loan.student'])->find($request->session()->get(self::DRAFT_SESSION_KEY));

            if ($loanItem && $loanItem->returned_at === null && in_array($loanItem->status, ['dipinjam', 'terlambat'], true)) {
                $lateDays = $this->returnService->lateDays($loanItem);
                $isLate = $this->returnService->isLate($loanItem);
            } else {
                $loanItem = null;
                $request->session()->forget(self::DRAFT_SESSION_KEY);
            }
        }

        $returnedItems = LoanItem::with(['book', 'loan.student'])
            ->where('status', 'dikembalikan')
            ->whereNotNull('returned_at')
            ->latest('returned_at')
            ->limit(10)
            ->get();

        return view('returns.index', compact('loanItem', 'lateDays', 'isLate', 'returnedItems'));
    }

    public function search(SearchReturnBookRequest $request): RedirectResponse
    {
        $this->loanStatusService->syncOverdueStatuses();

        $loanItem = $this->returnService->findActiveLoanItemByBookCode($request->validated('book_code'));

        if (! $loanItem) {
            $request->session()->forget(self::DRAFT_SESSION_KEY);

            return back()->withInput()->with('error', 'Buku tidak sedang dipinjam atau sudah dikembalikan.');
        }

        $request->session()->put(self::DRAFT_SESSION_KEY, $loanItem->id);

        return redirect()->route('returns.index')->with('success', 'Data peminjaman ditemukan. Periksa detail sebelum konfirmasi pengembalian.');
    }

    public function store(Request $request, LoanItem $loanItem): RedirectResponse
    {
        try {
            $returnedItem = $this->returnService->processReturn($loanItem);
            $request->session()->forget(self::DRAFT_SESSION_KEY);

            return redirect()->route('returns.index')->with('success', 'Buku '.$returnedItem->book->book_code.' berhasil dikembalikan.');
        } catch (ValidationException $exception) {
            return back()->withErrors($exception->errors())->with('error', collect($exception->errors())->flatten()->first());
        } catch (Throwable) {
            return back()->with('error', 'Pengembalian gagal diproses. Periksa kembali data transaksi.');
        }
    }
}
