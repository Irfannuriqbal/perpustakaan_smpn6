<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddLoanBookRequest;
use App\Http\Requests\FindLoanStudentRequest;
use App\Http\Requests\StoreLoanRequest;
use App\Http\Requests\UpdateLoanRequest;
use App\Models\Book;
use App\Models\Loan;
use App\Models\Student;
use App\Models\User;
use App\Services\LoanExtensionService;
use App\Services\LoanService;
use App\Services\LoanStatusService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class LoanController extends Controller
{
    private const DRAFT_SESSION_KEY = 'loan_draft';

    public function __construct(
        private readonly LoanService $loanService,
        private readonly LoanExtensionService $loanExtensionService,
        private readonly LoanStatusService $loanStatusService,
    ) {
    }

    public function index(): View
    {
        $this->loanStatusService->syncOverdueStatuses();

        $loans = Loan::with(['student', 'user', 'items.book'])
            ->latest()
            ->paginate(10);

        return view('loans.index', [
            'loans' => $loans,
            'loanStatusService' => $this->loanStatusService,
        ]);
    }

    public function create(Request $request): View
    {
        $draft = $this->draft($request);
        $student = isset($draft['student_id']) ? Student::find($draft['student_id']) : null;
        $books = Book::with('category')
            ->whereIn('id', $draft['book_ids'] ?? [])
            ->get();

        $activeBorrowedCount = $student ? $this->loanService->activeBorrowedCount($student) : 0;
        $hasLateLoan = $student ? $this->loanService->hasLateLoan($student) : false;
        $loanDate = now();
        $dueDate = now()->addDays(LoanService::LOAN_PERIOD_DAYS);

        return view('loans.create', compact('student', 'books', 'activeBorrowedCount', 'hasLateLoan', 'loanDate', 'dueDate'));
    }

    public function selectStudent(FindLoanStudentRequest $request): RedirectResponse
    {
        $student = Student::where('member_code', $request->validated('member_code'))->first();

        if (! $student) {
            return back()->withInput()->with('error', 'Siswa dengan kode anggota tersebut tidak ditemukan.');
        }

        $this->loanService->validateStudent($student);

        $request->session()->put(self::DRAFT_SESSION_KEY, [
            'student_id' => $student->id,
            'book_ids' => [],
        ]);

        return redirect()->route('loans.create')->with('success', 'Data siswa ditemukan. Silakan scan atau masukkan kode buku.');
    }

    public function addBook(AddLoanBookRequest $request): RedirectResponse
    {
        $draft = $this->draft($request);

        if (empty($draft['student_id'])) {
            return back()->with('error', 'Cari siswa terlebih dahulu sebelum memasukkan buku.');
        }

        $student = Student::find($draft['student_id']);

        if (! $student) {
            $this->clearDraft($request);

            return redirect()->route('loans.create')->with('error', 'Data siswa pada draft tidak ditemukan. Silakan scan ulang kode anggota.');
        }

        $book = Book::where('book_code', $request->validated('book_code'))->first();

        if (! $book) {
            return back()->withInput()->with('error', 'Buku dengan kode tersebut tidak ditemukan.');
        }

        $bookIds = $draft['book_ids'] ?? [];
        $this->loanService->validateStudent($student);
        $this->loanService->validateBookForDraft($student, $book, $bookIds);

        $bookIds[] = $book->id;
        $request->session()->put(self::DRAFT_SESSION_KEY, [
            'student_id' => $student->id,
            'book_ids' => array_values($bookIds),
        ]);

        return redirect()->route('loans.create')->with('success', 'Buku berhasil ditambahkan ke daftar peminjaman sementara.');
    }

    public function removeBook(Request $request, Book $book): RedirectResponse
    {
        $draft = $this->draft($request);
        $bookIds = array_values(array_filter($draft['book_ids'] ?? [], fn ($bookId): bool => (int) $bookId !== (int) $book->id));

        $request->session()->put(self::DRAFT_SESSION_KEY, [
            'student_id' => $draft['student_id'] ?? null,
            'book_ids' => $bookIds,
        ]);

        return redirect()->route('loans.create')->with('success', 'Buku dihapus dari daftar sementara.');
    }

    public function resetDraft(Request $request): RedirectResponse
    {
        $this->clearDraft($request);

        return redirect()->route('loans.create')->with('success', 'Draft peminjaman berhasil dikosongkan.');
    }

    public function store(StoreLoanRequest $request): RedirectResponse
    {
        $draft = $this->draft($request);

        if (empty($draft['student_id'])) {
            return redirect()->route('loans.create')->with('error', 'Cari siswa terlebih dahulu sebelum menyimpan peminjaman.');
        }

        if (empty($draft['book_ids'])) {
            return redirect()->route('loans.create')->with('error', 'Minimal satu buku harus dimasukkan ke daftar peminjaman.');
        }

        $student = Student::find($draft['student_id']);
        $officer = $this->currentOfficer();

        if (! $student) {
            $this->clearDraft($request);

            return redirect()->route('loans.create')->with('error', 'Data siswa pada draft tidak ditemukan. Silakan scan ulang kode anggota.');
        }

        if (! $officer) {
            return redirect()->route('loans.create')->with('error', 'Data petugas belum tersedia. Jalankan seeder atau tambahkan user petugas terlebih dahulu.');
        }

        try {
            $loan = $this->loanService->createLoan($student, $draft['book_ids'], $officer);
            $this->clearDraft($request);

            return redirect()->route('loans.index')->with('success', 'Peminjaman '.$loan->loan_code.' berhasil disimpan.');
        } catch (ValidationException $exception) {
            return back()->withErrors($exception->errors())->with('error', collect($exception->errors())->flatten()->first());
        } catch (Throwable) {
            return back()->with('error', 'Peminjaman gagal disimpan. Periksa kembali data transaksi.');
        }
    }

    public function show(Loan $loan): View
    {
        $this->loanStatusService->syncOverdueStatuses();

        $loan->load(['student', 'user', 'items.book']);
        $canExtend = $this->loanExtensionService->canExtend($loan);
        $statusLabel = $this->loanStatusService->displayLabel($loan);
        $statusClass = $this->loanStatusService->badgeClass($loan);
        $lateDays = $this->loanStatusService->lateDays($loan);

        return view('loans.show', compact('loan', 'canExtend', 'statusLabel', 'statusClass', 'lateDays'));
    }

    public function extend(Loan $loan): RedirectResponse
    {
        try {
            $result = $this->loanExtensionService->extend($loan);

            return redirect()
                ->route('loans.show', $result['loan'])
                ->with('success', 'Peminjaman berhasil diperpanjang. Jatuh tempo lama: '.$result['old_due_date']->format('d M Y').'. Jatuh tempo baru: '.$result['new_due_date']->format('d M Y').'.');
        } catch (ValidationException $exception) {
            return back()->withErrors($exception->errors())->with('error', collect($exception->errors())->flatten()->first());
        } catch (Throwable) {
            return back()->with('error', 'Perpanjangan gagal diproses. Periksa kembali status transaksi.');
        }
    }

    public function edit(Loan $loan): RedirectResponse
    {
        return redirect()->route('loans.index')->with('status', 'Form edit peminjaman akan dibuat pada tahap berikutnya.');
    }

    public function update(UpdateLoanRequest $request, Loan $loan): RedirectResponse
    {
        $validated = $request->validated();
        $bookIds = $validated['book_ids'] ?? null;
        unset($validated['book_ids']);

        DB::transaction(function () use ($loan, $validated, $bookIds): void {
            $loan->update($validated);

            if (is_array($bookIds)) {
                $loan->items()->delete();

                foreach ($bookIds as $bookId) {
                    $loan->items()->create([
                        'book_id' => $bookId,
                        'status' => $loan->status,
                    ]);
                }
            }
        });

        return redirect()->route('loans.index')->with('status', 'Data peminjaman berhasil diperbarui.');
    }

    public function destroy(Loan $loan): RedirectResponse
    {
        $loan->items()->delete();
        $loan->delete();

        return redirect()->route('loans.index')->with('status', 'Data peminjaman berhasil dihapus.');
    }

    private function draft(Request $request): array
    {
        return $request->session()->get(self::DRAFT_SESSION_KEY, [
            'student_id' => null,
            'book_ids' => [],
        ]);
    }

    private function clearDraft(Request $request): void
    {
        $request->session()->forget(self::DRAFT_SESSION_KEY);
    }

    private function currentOfficer(): ?User
    {
        return User::where('role', 'petugas')->first() ?? User::first();
    }
}
