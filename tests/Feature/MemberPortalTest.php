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

class MemberPortalTest extends TestCase
{
    use RefreshDatabase;

    public function test_anggota_aktif_bisa_login_dan_melihat_kartu_digital(): void
    {
        $member = $this->member();

        $this->post(route('members.login.store'), [
            'nis' => $member->nis,
            'password' => 'password',
        ])->assertRedirect(route('members.card'));

        $this->assertAuthenticatedAs($member, 'member');

        $this->get(route('members.card'))
            ->assertOk()
            ->assertSeeText($member->name)
            ->assertSeeText($member->nis)
            ->assertSeeText($member->class)
            ->assertSeeText($member->member_code)
            ->assertSee('QR Code '.$member->member_code);
    }

    public function test_password_anggota_salah_ditolak(): void
    {
        $member = $this->member();

        $this->post(route('members.login.store'), [
            'nis' => $member->nis,
            'password' => 'salah-password',
        ])->assertSessionHasErrors('nis');

        $this->assertGuest('member');
    }

    public function test_anggota_nonaktif_tidak_bisa_login(): void
    {
        $member = $this->member(['status' => 'nonaktif']);

        $this->post(route('members.login.store'), [
            'nis' => $member->nis,
            'password' => 'password',
        ])->assertSessionHasErrors('nis');

        $this->assertGuest('member');
    }

    public function test_kartu_digital_hanya_menampilkan_pinjaman_anggota_login(): void
    {
        $member = $this->member();
        $otherMember = $this->member([
            'nis' => '2425998',
            'member_code' => 'AGT-TEST-002',
            'name' => 'Anggota Lain',
            'class' => 'VIII-B',
        ]);
        $ownBook = $this->book('BK-ANGGOTA-001', 'Buku Milik Anggota');
        $otherBook = $this->book('BK-ANGGOTA-002', 'Buku Anggota Lain');

        $this->loan($member, $ownBook, now()->addDays(3)->toDateString());
        $this->loan($otherMember, $otherBook, now()->addDays(7)->toDateString());

        $this->actingAs($member, 'member')
            ->get(route('members.card'))
            ->assertOk()
            ->assertSeeText('Buku Milik Anggota')
            ->assertSeeText(now()->addDays(3)->format('d M Y'))
            ->assertDontSeeText('Buku Anggota Lain')
            ->assertSeeText('1');
    }

    private function member(array $attributes = []): Student
    {
        return Student::create($attributes + [
            'nis' => '2425999',
            'member_code' => 'AGT-TEST-001',
            'name' => 'Siswa Test',
            'gender' => 'Laki-laki',
            'class' => 'VII-A',
            'phone' => null,
            'address' => null,
            'status' => 'aktif',
            'password' => Hash::make('password'),
        ]);
    }

    private function book(string $code, string $title): Book
    {
        return Book::create([
            'book_code' => $code,
            'isbn' => null,
            'title' => $title,
            'author' => 'Penulis Test',
            'publisher' => null,
            'publication_year' => 2026,
            'category_id' => Category::firstOrCreate(['name' => 'Kategori Anggota'])->id,
            'stock' => 1,
            'available_stock' => 0,
            'shelf_location' => 'A-01',
            'status' => 'aktif',
        ]);
    }

    private function loan(Student $member, Book $book, string $dueDate): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.test'],
            ['name' => 'Admin Test', 'password' => Hash::make('password'), 'role' => User::ROLE_ADMIN]
        );
        $loan = Loan::create([
            'loan_code' => 'PMJ-ANGGOTA-'.str_pad((string) (Loan::count() + 1), 3, '0', STR_PAD_LEFT),
            'student_id' => $member->id,
            'user_id' => $admin->id,
            'loan_date' => now()->toDateString(),
            'due_date' => $dueDate,
            'returned_at' => null,
            'status' => 'dipinjam',
            'extension_count' => 0,
        ]);

        $loan->items()->create([
            'book_id' => $book->id,
            'returned_at' => null,
            'status' => 'dipinjam',
        ]);
    }
}
