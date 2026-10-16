<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StudentPhotoTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_bisa_upload_dan_hapus_foto_anggota(): void
    {
        Storage::fake('public');
        $admin = $this->admin();

        $this->actingAs($admin)
            ->post(route('students.store'), $this->studentData([
                'photo' => UploadedFile::fake()->image('anggota.png'),
            ]))
            ->assertRedirect(route('students.index'));

        $student = Student::firstOrFail();
        $this->assertNotNull($student->photo_path);
        Storage::disk('public')->assertExists($student->photo_path);

        $this->actingAs($admin)
            ->put(route('students.update', $student), $this->studentData([
                'remove_photo' => '1',
                'password' => '',
                'password_confirmation' => '',
            ]))
            ->assertRedirect(route('students.index'));

        Storage::disk('public')->assertMissing($student->photo_path);
        $this->assertNull($student->fresh()->photo_path);
    }

    public function test_file_yang_bukan_gambar_ditolak(): void
    {
        $this->actingAs($this->admin())
            ->post(route('students.store'), $this->studentData([
                'photo' => UploadedFile::fake()->create('bukan-gambar.pdf', 100, 'application/pdf'),
            ]))
            ->assertSessionHasErrors('photo');
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

    private function studentData(array $overrides = []): array
    {
        return $overrides + [
            'nis' => '2425001',
            'member_code' => 'AGT-TEST-001',
            'name' => 'Siswa Test',
            'gender' => 'Laki-laki',
            'class' => 'VII-A',
            'phone' => null,
            'address' => null,
            'status' => 'aktif',
            'password' => 'password-baru',
            'password_confirmation' => 'password-baru',
        ];
    }
}
