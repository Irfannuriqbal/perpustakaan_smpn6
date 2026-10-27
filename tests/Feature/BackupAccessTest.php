<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class BackupAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_backup_hanya_dapat_dibuka_super_admin(): void
    {
        $admin = $this->user(User::ROLE_ADMIN, 'admin@example.test');
        $superAdmin = $this->user(User::ROLE_SUPER_ADMIN, 'superadmin@example.test');

        $this->actingAs($admin)->get(route('backups.index'))->assertForbidden();
        $this->actingAs($superAdmin)->get(route('backups.index'))->assertOk();
    }

    public function test_file_backup_tidak_valid_tidak_dapat_diunduh(): void
    {
        $superAdmin = $this->user(User::ROLE_SUPER_ADMIN, 'superadmin@example.test');

        $this->actingAs($superAdmin)
            ->get('/backups/not-a-backup.sql/download')
            ->assertNotFound();
    }

    private function user(string $role, string $email): User
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
