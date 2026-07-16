<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_login_screen(): void
    {
        $response = $this->get(route('login'));

        $response->assertOk();
    }

    public function test_user_can_login_and_logout(): void
    {
        $password = 'password123';
        $user = User::factory()->create([
            'password' => $password,
        ]);

        $loginResponse = $this->post(route('login.attempt'), [
            'email' => $user->email,
            'password' => $password,
        ]);

        $loginResponse->assertRedirect(route('documents.index'));
        $this->assertAuthenticatedAs($user);

        $logoutResponse = $this->post(route('logout'));

        $logoutResponse->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_guest_can_view_password_reset_request_screen(): void
    {
        $response = $this->get(route('password.request'));

        $response->assertOk();
    }
}
