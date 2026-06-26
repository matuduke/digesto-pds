<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Group;
use Tests\TestCase;

class UserGroupControllerTest extends TestCase
{
    public function test_can_list_user_groups(): void
    {
        Group::factory()->count(3)->create();

        $response = $this->getJson('/api/user-groups');

        $response->assertStatus(200)
            ->assertJsonCount(3);
    }

    public function test_can_show_user_group(): void
    {
        $group = Group::factory()->create();
        $users = User::factory()->count(2)->create();
        $group->users()->attach($users);

        $response = $this->getJson("/api/user-groups/{$group->id}");

        $response->assertStatus(200)
            ->assertJsonPath('id', $group->id)
            ->assertJsonPath('name', $group->name)
            ->assertJsonPath('users', function ($users) {
                return count($users) === 2;
            });
    }

    public function test_can_create_user_group(): void
    {
        $users = User::factory()->count(2)->create();

        $response = $this->postJson('/api/user-groups', [
            'name' => 'Administrators',
            'description' => 'System administrators',
            'user_ids' => $users->pluck('id')->toArray(),
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('name', 'Administrators')
            ->assertJsonPath('description', 'System administrators')
            ->assertJsonPath('users', function ($usersInResponse) {
                return count($usersInResponse) === 2;
            });

        $this->assertDatabaseHas('user_groups', [
            'name' => 'Administrators',
        ]);
    }

    public function test_create_user_group_requires_name(): void
    {
        $response = $this->postJson('/api/user-groups', [
            'description' => 'System administrators',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('name');
    }

    public function test_create_user_group_name_must_be_unique(): void
    {
        Group::factory()->create(['name' => 'Administrators']);

        $response = $this->postJson('/api/user-groups', [
            'name' => 'Administrators',
            'description' => 'Another admin group',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('name');
    }

    public function test_can_update_user_group(): void
    {
        $group = Group::factory()->create();
        $newUsers = User::factory()->count(2)->create();

        $response = $this->putJson("/api/user-groups/{$group->id}", [
            'name' => 'Updated Name',
            'description' => 'Updated description',
            'user_ids' => $newUsers->pluck('id')->toArray(),
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('name', 'Updated Name')
            ->assertJsonPath('description', 'Updated description');

        $this->assertDatabaseHas('user_groups', [
            'id' => $group->id,
            'name' => 'Updated Name',
        ]);
    }

    public function test_can_delete_user_group(): void
    {
        $group = Group::factory()->create();

        $response = $this->deleteJson("/api/user-groups/{$group->id}");

        $response->assertStatus(204);

        $this->assertSoftDeleted('user_groups', [
            'id' => $group->id,
        ]);
    }
}
