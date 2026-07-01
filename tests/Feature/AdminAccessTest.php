<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function createUserWithRole(string $roleName): User
    {
        $user = User::factory()->create();
        $role = Role::firstOrCreate(['slug' => $roleName], ['name' => ucfirst($roleName)]);
        $user->roles()->attach($role);
        return $user;
    }

    public function test_authenticated_user_can_access_dashboard(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/tableau-de-bord');
        $response->assertStatus(200);
    }

    public function test_authenticated_user_can_access_candidates_list(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/candidats');
        $response->assertStatus(200);
    }

    public function test_authenticated_user_can_access_companies_list(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/entreprises');
        $response->assertStatus(200);
    }

    public function test_authenticated_user_can_access_offers_list(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/offres');
        $response->assertStatus(200);
    }

    public function test_authenticated_user_can_access_matching_list(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/matching');
        $response->assertStatus(200);
    }

    public function test_authenticated_user_can_access_messages(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/messages');
        $response->assertStatus(200);
    }

    public function test_authenticated_user_can_access_profile(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/profil');
        $response->assertStatus(200);
    }
}
