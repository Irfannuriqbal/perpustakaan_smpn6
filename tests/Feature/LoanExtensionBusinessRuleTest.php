<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
use App\Models\Loan;
use App\Models\Student;
use App\Models\User;
use App\Services\LoanExtensionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class LoanExtensionBusinessRuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_perpanjangan_pertama_berhasil(): void
    {
        $loan = $this->loan();
        $oldDueDate = \Illuminate\Support\Carbon::parse($loan->getAttribute('due_date'))->toDateString();

        $result = app(LoanExtensionService::class)->extend($loan);

        $this->assertSame(1, $result['loan']->extension_count);
        $this->assertSame(\Illuminate\Support\Carbon::parse($oldDueDate)->addDays(LoanExtensionService::EXTENSION_DAYS)->toDateString(), \Illuminate\Support\Carbon::parse($result['loan']->getAttribute('due_date'))->toDateString());
    }

    public function test_perpanjangan_kedua_ditolak(): void
    {
        $this->expectException(ValidationException::class);

        $loan = $this->loan(['extension_count' => 1]);

        app(LoanExtensionService::class)->extend($loan);
    }

    public function test_loan_terlambat_tidak_bisa_diperpanjang(): void
    {
        $this->expectException(ValidationException::class);

        $loan = $this->loan([
            'loan_date' => now()->subDays(10)->toDateString(),
            'due_date' => now()->subDay()->toDateString(),
            'status' => 'terlambat',
        ]);

        app(LoanExtensionService::class)->extend($loan);
    }

    public function test_loan_dikembalikan_tidak_bisa_diperpanjang(): void
    {
        $this->expectException(ValidationException::class);

        $loan = $this->loan([
            'returned_at' => now(),
            'status' => 'dikembalikan',
        ]);

        app(LoanExtensionService::class)->extend($loan);
    }

    private function loan(array $attributes = []): Loan
    {
        $student = Student::create([
            'nis' => '2425888',
            'member_code' => 'AGT-EXT-888',
            'name' => 'Siswa Extend',
            'gender' => 'Laki-laki',
            'class' => 'IX-A',
            'phone' => null,
            'address' => null,
            'status' => 'aktif',
        ]);
        $book = Book::create([
            'book_code' => 'BK-EXT',
            'isbn' => null,
            'title' => 'Buku Extend',
            'author' => 'Penulis Test',
            'publisher' => null,
            'publication_year' => 2026,
            'category_id' => Category::create(['name' => 'Kategori Extend'])->id,
            'stock' => 1,
            'available_stock' => 0,
            'shelf_location' => null,
            'status' => 'aktif',
        ]);
        $loan = Loan::create($attributes + [
            'loan_code' => 'PMJ-EXT-001',
            'student_id' => $student->id,
            'user_id' => $this->officer()->id,
            'loan_date' => now()->toDateString(),
            'due_date' => now()->addDays(7)->toDateString(),
            'returned_at' => null,
            'status' => 'dipinjam',
            'extension_count' => 0,
        ]);
        $loan->items()->create([
            'book_id' => $book->id,
            'returned_at' => $loan->returned_at,
            'status' => $loan->status === 'dikembalikan' ? 'dikembalikan' : 'dipinjam',
        ]);

        return $loan->fresh(['items.book']);
    }

    private function officer(): User
    {
        return User::firstOrCreate(
            ['email' => 'admin-extend@example.test'],
            ['name' => 'Admin Extend', 'password' => Hash::make('password'), 'role' => User::ROLE_ADMIN]
        );
    }
}
