<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function createStaffUser(array $attrs = []): User
    {
        $user = User::factory()->create($attrs);
        $role = Role::firstOrCreate(
            ['slug' => 'super_admin'],
            ['name' => 'Super Administrateur', 'is_system' => true]
        );
        $user->roles()->attach($role);
        $user->load('roles.permissions', 'permissions');

        return $user;
    }

    public function test_login_page_is_accessible(): void
    {
        $response = $this->get('/connexion');
        $response->assertStatus(200);
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        SiteSetting::set('two_factor_email_enabled', false, 'boolean');

        $user = $this->createStaffUser([
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

    public function test_staff_login_requires_email_2fa_when_enabled(): void
    {
        Mail::fake();
        SiteSetting::set('two_factor_email_enabled', true, 'boolean');

        $user = $this->createStaffUser([
            'email' => 'staff2fa@example.com',
            'password' => bcrypt('Password@1'),
        ]);

        $response = $this->post('/connexion', [
            'email' => 'staff2fa@example.com',
            'password' => 'Password@1',
        ]);

        $response->assertRedirect(route('login.2fa'));
        $this->assertGuest();
        $this->assertEquals($user->id, session('2fa.user_id'));
        Mail::assertSent(\App\Mail\TwoFactorCodeMail::class);
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

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_authenticated_user_is_redirected_from_login(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/connexion');

        $response->assertRedirect('/');
    }

    public function test_forgot_password_page_is_accessible(): void
    {
        $response = $this->get('/mot-de-passe-oublie');
        $response->assertStatus(200);
    }
}
