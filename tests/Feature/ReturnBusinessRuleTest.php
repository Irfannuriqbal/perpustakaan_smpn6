<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
use App\Models\Loan;
use App\Models\LoanItem;
use App\Models\Student;
use App\Models\User;
use App\Services\ReturnService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ReturnBusinessRuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_pengembalian_menaikkan_stok(): void
    {
        [$loanItem, $book] = $this->activeLoanItem();

        app(ReturnService::class)->processReturn($loanItem);

        $this->assertSame(1, $book->fresh()->available_stock);
        $this->assertSame('dikembalikan', $loanItem->fresh()->status);
        $this->assertNotNull($loanItem->fresh()->returned_at);
    }

    public function test_buku_yang_sudah_dikembalikan_tidak_boleh_dikembalikan_lagi(): void
    {
        $this->expectException(ValidationException::class);

        [$loanItem] = $this->activeLoanItem();
        $returnService = app(ReturnService::class);
        $returnService->processReturn($loanItem);
        $returnService->processReturn($loanItem->fresh());
    }

    public function test_loan_berubah_dikembalikan_ketika_semua_item_kembali(): void
    {
        [$loanItem] = $this->activeLoanItem();

        app(ReturnService::class)->processReturn($loanItem);

        $this->assertSame('dikembalikan', $loanItem->loan->fresh()->status);
        $this->assertNotNull($loanItem->loan->fresh()->returned_at);
    }

    private function activeLoanItem(): array
    {
        $student = Student::create([
            'nis' => '2425999',
            'member_code' => 'AGT-TEST-999',
            'name' => 'Siswa Return',
            'gender' => 'Perempuan',
            'class' => 'VIII-A',
            'phone' => null,
            'address' => null,
            'status' => 'aktif',
        ]);
        $book = Book::create([
            'book_code' => 'BK-RETURN',
            'isbn' => null,
            'title' => 'Buku Return',
            'author' => 'Penulis Test',
            'publisher' => null,
            'publication_year' => 2026,
            'category_id' => Category::create(['name' => 'Kategori Return'])->id,
            'stock' => 1,
            'available_stock' => 0,
            'shelf_location' => null,
            'status' => 'aktif',
        ]);
        $loan = Loan::create([
            'loan_code' => 'PMJ-RETURN-001',
            'student_id' => $student->id,
            'user_id' => $this->officer()->id,
            'loan_date' => now()->toDateString(),
            'due_date' => now()->addDays(7)->toDateString(),
            'returned_at' => null,
            'status' => 'dipinjam',
            'extension_count' => 0,
        ]);
        $loanItem = LoanItem::create([
            'loan_id' => $loan->id,
            'book_id' => $book->id,
            'returned_at' => null,
            'status' => 'dipinjam',
        ]);

        return [$loanItem->load('loan', 'book'), $book];
    }

    private function officer(): User
    {
        return User::firstOrCreate(
            ['email' => 'admin-return@example.test'],
            ['name' => 'Admin Return', 'password' => Hash::make('password'), 'role' => User::ROLE_ADMIN]
        );
    }
}
