<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoanControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_peminjaman_dicatat_oleh_user_yang_sedang_login(): void
    {
        User::create([
            'name' => 'Kepala Perpustakaan',
            'email' => 'superadmin@example.test',
            'password' => Hash::make('password'),
            'role' => User::ROLE_SUPER_ADMIN,
        ]);
        $admin = User::create([
            'name' => 'Admin Perpustakaan',
            'email' => 'admin@example.test',
            'password' => Hash::make('password'),
            'role' => User::ROLE_ADMIN,
        ]);
        $student = Student::create([
            'nis' => '2425999',
            'member_code' => 'AGT-TEST-001',
            'name' => 'Siswa Test',
            'gender' => 'Laki-laki',
            'class' => 'VII-A',
            'phone' => null,
            'address' => null,
            'status' => 'aktif',
        ]);
        $book = Book::create([
            'book_code' => 'BK-TEST-001',
            'isbn' => null,
            'title' => 'Buku Test',
            'author' => 'Penulis Test',
            'publisher' => null,
            'publication_year' => 2026,
            'category_id' => Category::create(['name' => 'Kategori Test'])->id,
            'stock' => 1,
            'available_stock' => 1,
            'shelf_location' => 'A-01',
            'status' => 'aktif',
        ]);

        $this->actingAs($admin)
            ->post(route('loans.student'), ['member_code' => $student->member_code])
            ->assertRedirect(route('loans.create'));

        $this->post(route('loans.books.add'), ['book_code' => $book->book_code])
            ->assertRedirect(route('loans.create'));

        $this->post(route('loans.store'))
            ->assertRedirect(route('loans.index'));

        $this->assertDatabaseHas('loans', [
            'student_id' => $student->id,
            'user_id' => $admin->id,
        ]);
    }
}
