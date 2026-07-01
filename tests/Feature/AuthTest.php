<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_is_accessible(): void
    {
        $response = $this->get('/connexion');
        $response->assertStatus(200);
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('Password@1'),
        ]);

        $response = $this->post('/connexion', [
            'email' => 'test@example.com',
            'password' => 'Password@1',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_user_cannot_login_with_invalid_credentials(): void
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('Password@1'),
        ]);

        $response = $this->post('/connexion', [
            'email' => 'test@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_inactive_user_cannot_login(): void
    {
        User::factory()->inactive()->create([
            'email' => 'inactive@example.com',
            'password' => bcrypt('Password@1'),
        ]);

        $response = $this->post('/connexion', [
            'email' => 'inactive@example.com',
            'password' => 'Password@1',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_locked_user_cannot_login(): void
    {
        User::factory()->locked()->create([
            'email' => 'locked@example.com',
            'password' => bcrypt('Password@1'),
        ]);

        $response = $this->post('/connexion', [
            'email' => 'locked@example.com',
            'password' => 'Password@1',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_authenticated_user_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/deconnexion');

        $response->assertRedirect('/');
        $this->assertGuest();
    }

    public function test_authenticated_user_is_redirected_from_login(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/connexion');

        $response->assertRedirect(route('admin.dashboard'));
    }

    public function test_forgot_password_page_is_accessible(): void
    {
        $response = $this->get('/mot-de-passe-oublie');
        $response->assertStatus(200);
    }
}
