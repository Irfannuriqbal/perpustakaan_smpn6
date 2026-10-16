<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_valid_berhasil(): void
    {
        $user = $this->user();

        $this->post('/login', [
            'email' => $user->getAttribute('email'),
            'password' => 'password',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_login_invalid_ditolak(): void
    {
        $user = $this->user();

        $this->post('/login', [
            'email' => $user->getAttribute('email'),
            'password' => 'salah',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_dashboard_redirect_ke_login_jika_belum_login(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_dashboard_menampilkan_label_role_admin(): void
    {
        $user = $this->user();

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertOk()
            ->assertSeeText('Admin Perpustakaan');
    }

    public function test_role_super_admin_memiliki_label_kepala_perpustakaan(): void
    {
        $user = User::create([
            'name' => 'Kepala Perpustakaan',
            'email' => 'superadmin@example.test',
            'password' => Hash::make('password'),
            'role' => User::ROLE_SUPER_ADMIN,
        ]);

        $this->assertSame('Kepala Perpustakaan', $user->roleLabel());
    }

    public function test_logout_menghapus_sesi(): void
    {
        $user = $this->user();

        $this->actingAs($user)
            ->post('/logout')
            ->assertRedirect('/login');

        $this->assertGuest();
    }

    private function user(): User
    {
        return User::create([
            'name' => 'Admin Test',
            'email' => 'admin@example.test',
            'password' => Hash::make('password'),
            'role' => User::ROLE_ADMIN,
        ]);
    }
}
