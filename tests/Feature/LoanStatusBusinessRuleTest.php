<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
use App\Models\Loan;
use App\Models\Student;
use App\Models\User;
use App\Services\LoanStatusService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoanStatusBusinessRuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_transaksi_terlambat_terdeteksi_dengan_benar(): void
    {
        $loan = $this->loan([
            'loan_date' => now()->subDays(10)->toDateString(),
            'due_date' => now()->subDays(2)->toDateString(),
            'returned_at' => null,
            'status' => 'dipinjam',
        ]);
        $service = app(LoanStatusService::class);

        $this->assertTrue($service->isOverdue($loan));
        $this->assertSame('terlambat', $service->displayStatus($loan));
        $this->assertGreaterThanOrEqual(2, $service->lateDays($loan));
    }

    public function test_transaksi_sudah_dikembalikan_tidak_dianggap_terlambat(): void
    {
        $loan = $this->loan([
            'loan_date' => now()->subDays(10)->toDateString(),
            'due_date' => now()->subDays(2)->toDateString(),
            'returned_at' => now(),
            'status' => 'dikembalikan',
        ]);
        $service = app(LoanStatusService::class);

        $this->assertFalse($service->isOverdue($loan));
        $this->assertSame('dikembalikan', $service->displayStatus($loan));
        $this->assertSame(0, $service->lateDays($loan));
    }

    private function loan(array $attributes): Loan
    {
        $student = Student::create([
            'nis' => '2425777',
            'member_code' => 'AGT-STATUS-777',
            'name' => 'Siswa Status',
            'gender' => 'Perempuan',
            'class' => 'VII-B',
            'phone' => null,
            'address' => null,
            'status' => 'aktif',
        ]);
        $book = Book::create([
            'book_code' => 'BK-STATUS',
            'isbn' => null,
            'title' => 'Buku Status',
            'author' => 'Penulis Test',
            'publisher' => null,
            'publication_year' => 2026,
            'category_id' => Category::create(['name' => 'Kategori Status'])->id,
            'stock' => 1,
            'available_stock' => 0,
            'shelf_location' => null,
            'status' => 'aktif',
        ]);
        $loan = Loan::create($attributes + [
            'loan_code' => 'PMJ-STATUS-001',
            'student_id' => $student->id,
            'user_id' => $this->officer()->id,
            'extension_count' => 0,
        ]);
        $loan->items()->create([
            'book_id' => $book->id,
            'returned_at' => $loan->returned_at,
            'status' => $loan->status,
        ]);

        return $loan->fresh(['items.book']);
    }

    private function officer(): User
    {
        return User::firstOrCreate(
            ['email' => 'petugas-status@example.test'],
            ['name' => 'Petugas Status', 'password' => Hash::make('password'), 'role' => 'petugas']
        );
    }
}
