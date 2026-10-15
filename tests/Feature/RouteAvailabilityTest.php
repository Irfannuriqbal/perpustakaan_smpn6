<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RouteAvailabilityTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    public function test_login_page_can_be_rendered(): void
    {
        $this->get('/login')->assertOk();
    }

    public function test_main_pages_can_be_rendered_for_authenticated_user(): void
    {
        $this->seed();
        $user = User::firstOrFail();
        $routes = [
            '/dashboard',
            '/students',
            '/categories',
            '/books',
            '/loans',
            '/returns',
            '/visits',
            '/reports',
            '/students/cards/print',
            '/books/barcodes/print',
            '/settings',
        ];

        foreach ($routes as $route) {
            $this->actingAs($user)->get($route)->assertOk();
        }
    }
}
