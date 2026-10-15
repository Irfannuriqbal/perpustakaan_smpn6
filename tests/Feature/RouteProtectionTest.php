<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RouteProtectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_modul_utama_memerlukan_login(): void
    {
        foreach ($this->protectedRoutes() as $route) {
            $this->get($route)->assertRedirect('/login');
        }
    }

    public function test_user_login_bisa_membuka_modul_utama(): void
    {
        $this->seed();
        $user = User::first();

        foreach ($this->protectedRoutes() as $route) {
            $this->actingAs($user)->get($route)->assertOk();
        }
    }

    private function protectedRoutes(): array
    {
        return [
            '/dashboard',
            '/students',
            '/categories',
            '/books',
            '/loans',
            '/returns',
            '/visits',
            '/reports',
            '/settings',
        ];
    }
}
