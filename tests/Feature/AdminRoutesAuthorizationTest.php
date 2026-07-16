<?php

namespace Tests\Feature;

use App\Models\Group;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminRoutesAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_and_redirect_to_is_stored(): void
    {
        $targetUrl = route('admin.users.index');

        $response = $this->get($targetUrl);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('redirect_to', $targetUrl);
    }

    public function test_non_administrator_user_is_redirected_to_login_and_redirect_to_is_stored(): void
    {
        $user = User::factory()->create();
        $targetUrl = route('admin.users.index');

        $response = $this->actingAs($user)->get($targetUrl);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('redirect_to', $targetUrl);
        $this->assertGuest();
    }

    public function test_administrator_user_can_access_admin_routes(): void
    {
        $user = User::factory()->create();
        $adminGroup = Group::query()->create([
            'name' => 'administradores',
            'description' => 'Grupo con acceso al panel de administracion',
        ]);

        $user->groups()->attach($adminGroup->id);

        $response = $this->actingAs($user)->get(route('admin.users.index'));

        $response->assertOk();
    }

    public function test_successful_login_redirects_to_redirect_to_and_clears_it(): void
    {
        $password = 'password123';
        $user = User::factory()->create([
            'password' => $password,
        ]);

        $adminGroup = Group::query()->create([
            'name' => 'administradores',
            'description' => 'Grupo con acceso al panel de administracion',
        ]);

        $user->groups()->attach($adminGroup->id);

        $targetUrl = route('admin.users.index');

        $response = $this->withSession([
            'redirect_to' => $targetUrl,
        ])->post(route('login.attempt'), [
            'email' => $user->email,
            'password' => $password,
        ]);

        $response->assertRedirect($targetUrl);
        $response->assertSessionMissing('redirect_to');
    }
}
