<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SuperAdminAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_ditolak_dari_modul_khusus_super_admin(): void
    {
        $admin = $this->user(User::ROLE_ADMIN);

        foreach ([route('settings.index'), route('users.index'), route('backups.index')] as $route) {
            $this->actingAs($admin)->get($route)->assertForbidden();
        }
    }

    public function test_super_admin_bisa_mengelola_akun_admin(): void
    {
        $superAdmin = $this->user(User::ROLE_SUPER_ADMIN, 'superadmin@example.test');

        $this->actingAs($superAdmin)
            ->post(route('users.store'), [
                'name' => 'Admin Baru',
                'email' => 'admin-baru@example.test',
                'password' => 'password-baru',
                'password_confirmation' => 'password-baru',
            ])
            ->assertRedirect(route('users.index'));

        $this->assertDatabaseHas('users', [
            'email' => 'admin-baru@example.test',
            'role' => User::ROLE_ADMIN,
            'is_active' => true,
        ]);
    }

    public function test_super_admin_dapat_menonaktifkan_admin(): void
    {
        $superAdmin = $this->user(User::ROLE_SUPER_ADMIN, 'superadmin@example.test');
        $admin = $this->user(User::ROLE_ADMIN, 'admin@example.test');

        $this->actingAs($superAdmin)
            ->patch(route('users.status', $admin))
            ->assertRedirect(route('users.index'));

        $this->assertFalse($admin->fresh()->is_active);
    }

    private function user(string $role, string $email = 'admin@example.test'): User
    {
        return User::create([
            'name' => 'User Test',
            'email' => $email,
            'password' => Hash::make('password'),
            'role' => $role,
            'is_active' => true,
        ]);
    }
}
