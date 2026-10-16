<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
use App\Models\Loan;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DashboardDueSoonTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_menampilkan_pinjaman_jatuh_tempo_hari_ini_dan_besok(): void
    {
        $admin = $this->admin();
        $todayLoan = $this->loan('Buku Hari Ini', now()->toDateString());
        $tomorrowLoan = $this->loan('Buku Besok', now()->addDay()->toDateString());
        $this->loan('Buku Sudah Kembali', now()->toDateString(), now());

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSeeText('Peringatan Jatuh Tempo')
            ->assertSeeText('2 transaksi harus diperiksa hari ini atau besok.')
            ->assertSeeText($todayLoan->items->first()->book->title)
            ->assertSeeText($tomorrowLoan->items->first()->book->title);
    }

    private function admin(): User
    {
        return User::create([
            'name' => 'Admin Test',
            'email' => 'admin@example.test',
            'password' => Hash::make('password'),
            'role' => User::ROLE_ADMIN,
            'is_active' => true,
        ]);
    }

    private function loan(string $title, string $dueDate, mixed $returnedAt = null): Loan
    {
        $number = Loan::count() + 1;
        $student = Student::create([
            'nis' => '2425'.$number,
            'member_code' => 'AGT-DUE-'.$number,
            'name' => 'Siswa '.$number,
            'gender' => 'Laki-laki',
            'class' => 'VII-A',
            'phone' => null,
            'address' => null,
            'status' => 'aktif',
            'password' => Hash::make('password'),
        ]);
        $book = Book::create([
            'book_code' => 'BK-DUE-'.$number,
            'isbn' => null,
            'title' => $title,
            'author' => 'Penulis Test',
            'publisher' => null,
            'publication_year' => 2026,
            'category_id' => Category::firstOrCreate(['name' => 'Kategori Test'])->id,
            'stock' => 1,
            'available_stock' => 0,
            'shelf_location' => 'A-01',
            'status' => 'aktif',
        ]);
        $loan = Loan::create([
            'loan_code' => 'PMJ-DUE-'.$number,
            'student_id' => $student->id,
            'user_id' => User::firstOrFail()->id,
            'loan_date' => now()->toDateString(),
            'due_date' => $dueDate,
            'returned_at' => $returnedAt,
            'status' => $returnedAt ? 'dikembalikan' : 'dipinjam',
            'extension_count' => 0,
        ]);
        $loan->items()->create([
            'book_id' => $book->id,
            'returned_at' => $returnedAt,
            'status' => $returnedAt ? 'dikembalikan' : 'dipinjam',
        ]);

        return $loan->load('items.book');
    }
}
