<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class VisitKioskTest extends TestCase
{
    use RefreshDatabase;

    public function test_layar_scan_memerlukan_login(): void
    {
        $this->get(route('visits.kiosk'))
            ->assertRedirect(route('login'));
    }

    public function test_scan_kartu_anggota_aktif_mencatat_kunjungan(): void
    {
        $user = $this->admin();
        $student = $this->student();

        $this->actingAs($user)
            ->post(route('visits.kiosk.store'), ['member_code' => $student->member_code])
            ->assertRedirect(route('visits.kiosk'))
            ->assertSessionHas('kiosk_result.status', 'success')
            ->assertSessionHas('kiosk_result.name', $student->name)
            ->assertSessionHas('kiosk_result.class', $student->class);

        $this->assertTrue(Visit::query()
            ->where('student_id', $student->id)
            ->whereDate('visit_date', now()->toDateString())
            ->exists());
    }

    public function test_scan_kode_tidak_dikenal_menampilkan_pesan_gagal(): void
    {
        $visitCount = Visit::count();

        $this->actingAs($this->admin())
            ->post(route('visits.kiosk.store'), ['member_code' => 'AGT-TIDAK-ADA'])
            ->assertRedirect(route('visits.kiosk'))
            ->assertSessionHas('kiosk_result.status', 'error')
            ->assertSessionHas('kiosk_result.message', 'Siswa dengan kode anggota tersebut tidak ditemukan.');

        $this->assertSame($visitCount, Visit::count());
    }

    public function test_scan_kartu_anggota_nonaktif_menampilkan_pesan_gagal(): void
    {
        $student = $this->student(['status' => 'nonaktif']);
        $visitCount = Visit::count();

        $this->actingAs($this->admin())
            ->post(route('visits.kiosk.store'), ['member_code' => $student->member_code])
            ->assertRedirect(route('visits.kiosk'))
            ->assertSessionHas('kiosk_result.status', 'error')
            ->assertSessionHas('kiosk_result.message', 'Siswa tidak aktif dan tidak dapat dicatat kunjungannya.');

        $this->assertSame($visitCount, Visit::count());
    }

    public function test_scan_duplikat_tidak_membuat_kunjungan_baru(): void
    {
        $user = $this->admin();
        $student = $this->student();
        Visit::create([
            'student_id' => $student->id,
            'visit_date' => now()->toDateString(),
            'check_in_time' => now()->format('H:i'),
        ]);
        $visitCount = Visit::count();

        $this->actingAs($user)
            ->post(route('visits.kiosk.store'), ['member_code' => $student->member_code])
            ->assertRedirect(route('visits.kiosk'))
            ->assertSessionHas('kiosk_result.status', 'error')
            ->assertSessionHas('kiosk_result.message', 'Kunjungan siswa ini sudah tercatat kurang dari 5 menit yang lalu.');

        $this->assertSame($visitCount, Visit::count());
    }

    private function admin(): User
    {
        return User::create([
            'name' => 'Admin Perpustakaan',
            'email' => 'admin@example.test',
            'password' => Hash::make('password'),
            'role' => User::ROLE_ADMIN,
        ]);
    }

    private function student(array $attributes = []): Student
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
        ]);
    }
}
