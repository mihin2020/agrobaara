<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_landing_page_is_accessible(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    public function test_mediatheque_page_is_accessible(): void
    {
        $response = $this->get('/mediatheque');
        $response->assertStatus(200);
    }

    public function test_bibliotheque_page_is_accessible(): void
    {
        $response = $this->get('/bibliotheque');
        $response->assertStatus(200);
    }

    public function test_privacy_policy_page_is_accessible(): void
    {
        $response = $this->get('/politique-de-confidentialite');
        $response->assertStatus(200);
    }

    public function test_admin_pages_require_authentication(): void
    {
        $response = $this->get('/admin/tableau-de-bord');
        $response->assertRedirect('/connexion');
    }

    public function test_admin_candidates_require_authentication(): void
    {
        $response = $this->get('/admin/candidats');
        $response->assertRedirect('/connexion');
    }

    public function test_admin_companies_require_authentication(): void
    {
        $response = $this->get('/admin/entreprises');
        $response->assertRedirect('/connexion');
    }
}
