<?php

namespace App\Services;

use App\Models\Book;
use App\Models\Loan;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LoanService
{
    public const MAX_ACTIVE_BOOKS = 2;
    public const LOAN_PERIOD_DAYS = 7;

    public function __construct(private readonly LoanStatusService $loanStatusService)
    {
    }

    public function activeBorrowedCount(Student $student): int
    {
        return $student->loans()
            ->whereNull('returned_at')
            ->whereIn('status', ['dipinjam', 'terlambat'])
            ->withCount(['items as active_items_count' => function ($query): void {
                $query->whereNull('returned_at')
                    ->whereIn('status', ['dipinjam', 'terlambat']);
            }])
            ->get()
            ->sum('active_items_count');
    }

    public function hasLateLoan(Student $student): bool
    {
        return $student->loans()
            ->whereNull('returned_at')
            ->where(function ($query): void {
                $query->where('status', 'terlambat')
                    ->orWhereDate('due_date', '<', now()->toDateString());
            })
            ->exists();
    }

    public function validateStudent(Student $student): void
    {
        if ($student->status !== 'aktif') {
            throw ValidationException::withMessages([
                'member_code' => 'Anggota tidak aktif dan tidak dapat melakukan peminjaman.',
            ]);
        }

        if ($this->hasLateLoan($student)) {
            throw ValidationException::withMessages([
                'member_code' => 'Anggota masih memiliki pinjaman terlambat.',
            ]);
        }
    }

    public function validateBookForDraft(Student $student, Book $book, array $draftBookIds): void
    {
        if ($book->status !== 'aktif') {
            throw ValidationException::withMessages([
                'book_code' => 'Buku tidak aktif dan tidak dapat dipinjam.',
            ]);
        }

        if ($book->available_stock < 1) {
            throw ValidationException::withMessages([
                'book_code' => 'Stok tersedia buku sudah habis.',
            ]);
        }

        if (in_array($book->id, $draftBookIds, true)) {
            throw ValidationException::withMessages([
                'book_code' => 'Buku yang sama tidak boleh dimasukkan dua kali dalam transaksi.',
            ]);
        }

        $totalAfterAdd = $this->activeBorrowedCount($student) + count($draftBookIds) + 1;

        if ($totalAfterAdd > self::MAX_ACTIVE_BOOKS) {
            throw ValidationException::withMessages([
                'book_code' => 'Anggota maksimal meminjam '.self::MAX_ACTIVE_BOOKS.' buku aktif.',
            ]);
        }
    }

    public function createLoan(Student $student, array $bookIds, User $officer): Loan
    {
        return DB::transaction(function () use ($student, $bookIds, $officer): Loan {
            $student = Student::query()->lockForUpdate()->findOrFail($student->id);
            $this->validateStudent($student);

            $bookIds = array_values(array_map('intval', $bookIds));

            if (count($bookIds) !== count(array_unique($bookIds))) {
                throw ValidationException::withMessages([
                    'book_code' => 'Buku yang sama tidak boleh dimasukkan dua kali dalam transaksi.',
                ]);
            }

            $bookIds = array_values(array_unique($bookIds));

            if (count($bookIds) < 1) {
                throw ValidationException::withMessages([
                    'book_code' => 'Minimal satu buku harus dimasukkan ke daftar peminjaman.',
                ]);
            }

            if ($this->activeBorrowedCount($student) + count($bookIds) > self::MAX_ACTIVE_BOOKS) {
                throw ValidationException::withMessages([
                    'book_code' => 'Anggota maksimal meminjam '.self::MAX_ACTIVE_BOOKS.' buku aktif.',
                ]);
            }

            $books = Book::query()
                ->whereIn('id', $bookIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            if ($books->count() !== count($bookIds)) {
                throw ValidationException::withMessages([
                    'book_code' => 'Sebagian buku tidak ditemukan.',
                ]);
            }

            foreach ($bookIds as $bookId) {
                $book = $books[$bookId];
                $this->validateBookForDraft($student, $book, array_values(array_diff($bookIds, [$bookId])));
            }

            $loanDate = now()->toDateString();
            $loan = Loan::create([
                'loan_code' => $this->generateLoanCode(),
                'student_id' => $student->id,
                'user_id' => $officer->id,
                'loan_date' => $loanDate,
                'due_date' => now()->addDays(self::LOAN_PERIOD_DAYS)->toDateString(),
                'returned_at' => null,
                'status' => 'dipinjam',
                'extension_count' => 0,
            ]);

            foreach ($bookIds as $bookId) {
                $book = $books[$bookId];
                $book->decrement('available_stock');

                $loan->items()->create([
                    'book_id' => $book->id,
                    'returned_at' => null,
                    'status' => 'dipinjam',
                ]);
            }

            return $loan->load(['student', 'user', 'items.book']);
        });
    }

    private function generateLoanCode(): string
    {
        $prefix = 'PMJ-'.now()->format('Ymd');
        $latest = Loan::query()
            ->where('loan_code', 'like', $prefix.'-%')
            ->lockForUpdate()
            ->latest('id')
            ->first();

        $nextNumber = 1;

        if ($latest) {
            $nextNumber = ((int) str($latest->loan_code)->afterLast('-')->toString()) + 1;
        }

        return $prefix.'-'.str_pad((string) $nextNumber, 3, '0', STR_PAD_LEFT);
    }
}
