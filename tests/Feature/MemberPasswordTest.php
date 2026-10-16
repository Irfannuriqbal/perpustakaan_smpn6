<?php

namespace Tests\Feature;

use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class MemberPasswordTest extends TestCase
{
    use RefreshDatabase;

    public function test_anggota_bisa_mengganti_password_sendiri(): void
    {
        $member = $this->member();

        $this->actingAs($member, 'member')
            ->put(route('members.password.update'), [
                'current_password' => 'password-lama',
                'password' => 'password-baru',
                'password_confirmation' => 'password-baru',
            ])
            ->assertRedirect(route('members.card'));

        $this->post(route('members.logout'));

        $this->post(route('members.login.store'), [
            'nis' => $member->nis,
            'password' => 'password-baru',
        ])->assertRedirect(route('members.card'));

        $this->assertAuthenticated('member');
    }

    public function test_password_lama_salah_ditolak(): void
    {
        $member = $this->member();

        $this->actingAs($member, 'member')
            ->put(route('members.password.update'), [
                'current_password' => 'salah-password',
                'password' => 'password-baru',
                'password_confirmation' => 'password-baru',
            ])
            ->assertSessionHasErrors('current_password');

        $this->assertTrue(Hash::check('password-lama', $member->fresh()->password));
    }

    private function member(): Student
    {
        return Student::create([
            'nis' => '2425001',
            'member_code' => 'AGT-TEST-001',
            'name' => 'Anggota Test',
            'gender' => 'Laki-laki',
            'class' => 'VII-A',
            'phone' => null,
            'address' => null,
            'status' => 'aktif',
            'password' => Hash::make('password-lama'),
        ]);
    }
}
