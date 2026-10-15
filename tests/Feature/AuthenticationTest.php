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
            'name' => 'Petugas Test',
            'email' => 'petugas@example.test',
            'password' => Hash::make('password'),
            'role' => 'petugas',
        ]);
    }
}
