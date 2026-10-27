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
            'no' => $member->no,
            'password' => 'password',
        ])->assertRedirect(route('members.home'));

        $this->assertAuthenticatedAs($member, 'member');

        $this->get(route('members.card'))
            ->assertOk()
            ->assertSeeText($member->name)
            ->assertSeeText($member->no)
            ->assertSeeText($member->class)
            ->assertSeeText($member->member_code)
            ->assertSee('QR Code '.$member->member_code);
    }

    public function test_login_anggota_mengabaikan_url_tujuan_lama_dan_membuka_beranda_anggota(): void
    {
        $member = $this->member();

        $this->withSession(['url.intended' => '/login'])
            ->post(route('members.login.store'), [
                'no' => $member->no,
                'password' => 'password',
            ])
            ->assertRedirect(route('members.home'));
    }

    public function test_portal_anggota_menyediakan_beranda_notifikasi_profil_dan_cetak_kartu(): void
    {
        $member = $this->member([
            'gender' => 'Perempuan',
            'address' => 'Bandung',
        ]);

        $this->actingAs($member, 'member')
            ->get(route('members.home'))
            ->assertOk()
            ->assertSeeText('Halo, '.$member->name)
            ->assertSeeText('Peminjaman Aktif');

        $this->actingAs($member, 'member')
            ->get(route('members.notifications'))
            ->assertOk()
            ->assertSeeText('Tidak ada notifikasi keterlambatan');

        $this->actingAs($member, 'member')
            ->get(route('members.profile'))
            ->assertOk()
            ->assertSeeText('Perempuan')
            ->assertSeeText('Bandung')
            ->assertSeeText('Cetak Kartu Anggota');

        $this->actingAs($member, 'member')
            ->get(route('members.portal-card.print'))
            ->assertOk()
            ->assertSeeText('SMP Negeri 6 Bandung')
            ->assertSee('QR Code '.$member->member_code);
    }

    public function test_beranda_anggota_guru_tidak_memerlukan_data_kelas(): void
    {
        $member = $this->member([
            'no' => '6010701',
            'member_code' => 'AGT-GURU-001',
            'name' => 'Guru Test',
            'member_type' => Student::TYPE_STAFF,
            'class' => null,
        ]);

        $this->actingAs($member, 'member')
            ->get(route('members.home'))
            ->assertOk()
            ->assertSeeText('Guru Test')
            ->assertSeeText(Student::TYPE_STAFF);
    }

    public function test_notifikasi_menampilkan_pinjaman_terlambat_dan_jatuh_tempo(): void
    {
        $member = $this->member();
        $this->loan($member, $this->book('BK-LATE', 'Buku Terlambat'), now()->subDay()->toDateString());
        $this->loan($member, $this->book('BK-DUE', 'Buku Segera Jatuh Tempo'), now()->addDays(2)->toDateString());

        $this->actingAs($member, 'member')
            ->get(route('members.notifications'))
            ->assertOk()
            ->assertSeeText('Buku Terlambat')
            ->assertSeeText('Buku Segera Jatuh Tempo')
            ->assertSeeText('Terlambat');
    }

    public function test_password_anggota_salah_ditolak(): void
    {
        $member = $this->member();

        $this->post(route('members.login.store'), [
            'no' => $member->no,
            'password' => 'salah-password',
        ])->assertSessionHasErrors('no');

        $this->assertGuest('member');
    }

    public function test_anggota_nonaktif_tidak_bisa_login(): void
    {
        $member = $this->member(['status' => 'nonaktif']);

        $this->post(route('members.login.store'), [
            'no' => $member->no,
            'password' => 'password',
        ])->assertSessionHasErrors('no');

        $this->assertGuest('member');
    }

    public function test_kartu_digital_hanya_menampilkan_pinjaman_anggota_login(): void
    {
        $member = $this->member();
        $otherMember = $this->member([
            'no' => '2425998',
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
            'no' => '2425999',
            'member_type' => Student::TYPE_STUDENT,
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
