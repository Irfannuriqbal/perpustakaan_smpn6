<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
use App\Models\Loan;
use App\Models\Student;
use App\Models\User;
use App\Services\LoanService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class LoanBusinessRuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_siswa_nonaktif_tidak_bisa_meminjam(): void
    {
        $this->expectException(ValidationException::class);

        $student = $this->student(['status' => 'nonaktif']);
        $book = $this->book();

        $this->loanService()->createLoan($student, [$book->id], $this->officer());
    }

    public function test_siswa_dengan_pinjaman_terlambat_tidak_bisa_meminjam(): void
    {
        $this->expectException(ValidationException::class);

        $student = $this->student();
        $lateBook = $this->book(['book_code' => 'BK-LATE', 'title' => 'Buku Lama']);
        $loan = Loan::create([
            'loan_code' => 'PMJ-TEST-001',
            'student_id' => $student->id,
            'user_id' => $this->officer()->id,
            'loan_date' => now()->subDays(10)->toDateString(),
            'due_date' => now()->subDays(3)->toDateString(),
            'returned_at' => null,
            'status' => 'dipinjam',
            'extension_count' => 0,
        ]);
        $loan->items()->create(['book_id' => $lateBook->id, 'status' => 'dipinjam']);

        $newBook = $this->book(['book_code' => 'BK-NEW', 'title' => 'Buku Baru']);
        $this->loanService()->createLoan($student, [$newBook->id], $this->officer());
    }

    public function test_maksimal_dua_buku_aktif(): void
    {
        $this->expectException(ValidationException::class);

        $student = $this->student();
        $existingLoan = Loan::create([
            'loan_code' => 'PMJ-TEST-002',
            'student_id' => $student->id,
            'user_id' => $this->officer()->id,
            'loan_date' => now()->toDateString(),
            'due_date' => now()->addDays(7)->toDateString(),
            'returned_at' => null,
            'status' => 'dipinjam',
            'extension_count' => 0,
        ]);
        $existingLoan->items()->create(['book_id' => $this->book(['book_code' => 'BK-001'])->id, 'status' => 'dipinjam']);
        $existingLoan->items()->create(['book_id' => $this->book(['book_code' => 'BK-002'])->id, 'status' => 'dipinjam']);

        $thirdBook = $this->book(['book_code' => 'BK-003']);
        $this->loanService()->createLoan($student, [$thirdBook->id], $this->officer());
    }

    public function test_buku_tanpa_stok_tidak_bisa_dipinjam(): void
    {
        $this->expectException(ValidationException::class);

        $student = $this->student();
        $book = $this->book(['stock' => 1, 'available_stock' => 0]);

        $this->loanService()->createLoan($student, [$book->id], $this->officer());
    }

    public function test_buku_sama_tidak_boleh_dipinjam_dua_kali_dalam_transaksi(): void
    {
        $this->expectException(ValidationException::class);

        $student = $this->student();
        $book = $this->book(['stock' => 2, 'available_stock' => 2]);

        $this->loanService()->createLoan($student, [$book->id, $book->id], $this->officer());
    }

    public function test_peminjaman_mengurangi_stok_dan_membuat_item(): void
    {
        $student = $this->student();
        $book = $this->book(['stock' => 3, 'available_stock' => 3]);

        $loan = $this->loanService()->createLoan($student, [$book->id], $this->officer());

        $this->assertSame(2, $book->fresh()->available_stock);
        $this->assertCount(1, $loan->items);
        $this->assertSame('dipinjam', $loan->status);
    }

    private function loanService(): LoanService
    {
        return app(LoanService::class);
    }

    private function officer(): User
    {
        return User::firstOrCreate(
            ['email' => 'admin@example.test'],
            ['name' => 'Admin Test', 'password' => Hash::make('password'), 'role' => User::ROLE_ADMIN]
        );
    }

    private function student(array $attributes = []): Student
    {
        return Student::create($attributes + [
            'nis' => 'NIS'.str_pad((string) (Student::count() + 1), 4, '0', STR_PAD_LEFT),
            'member_code' => 'AGT-TEST-'.str_pad((string) (Student::count() + 1), 3, '0', STR_PAD_LEFT),
            'name' => 'Siswa Test',
            'gender' => 'Laki-laki',
            'class' => 'VII-A',
            'phone' => null,
            'address' => null,
            'status' => 'aktif',
        ]);
    }

    private function book(array $attributes = []): Book
    {
        $number = Book::count() + 1;

        return Book::create($attributes + [
            'book_code' => 'BK-TEST-'.str_pad((string) $number, 3, '0', STR_PAD_LEFT),
            'isbn' => null,
            'title' => 'Buku Test '.$number,
            'author' => 'Penulis Test',
            'publisher' => null,
            'publication_year' => 2026,
            'category_id' => $this->category()->id,
            'stock' => 1,
            'available_stock' => 1,
            'shelf_location' => null,
            'status' => 'aktif',
        ]);
    }

    private function category(): Category
    {
        return Category::firstOrCreate(['name' => 'Kategori Test'], ['description' => null]);
    }
}
