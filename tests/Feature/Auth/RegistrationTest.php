<?php

namespace Tests\Feature\Auth;

use App\Models\Role;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(RouteServiceProvider::HOME);
    }

    public function test_new_users_can_register_with_the_selected_role(): void
    {
        Role::query()->firstOrCreate(['name' => 'cashier']);

        $this->post('/register', [
            'name' => 'Cashier User',
            'email' => 'cashier@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'cashier',
        ]);

        $this->assertAuthenticated();
        $this->assertSame(
            'cashier',
            User::query()->where('email', 'cashier@example.com')->firstOrFail()->role?->name
        );
    }
}
