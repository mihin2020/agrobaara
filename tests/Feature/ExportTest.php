<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use App\Livewire\Candidates\CandidateIndex;
use App\Livewire\Companies\CompanyIndex;
use App\Livewire\Offers\OfferIndex;
use App\Livewire\Matching\MatchIndex;
use Tests\TestCase;

class ExportTest extends TestCase
{
    use RefreshDatabase;

    protected function createSuperAdmin(): User
    {
        $user = User::factory()->create();
        $role = Role::firstOrCreate(
            ['slug' => 'super_admin'],
            ['name' => 'Super Administrateur', 'is_system' => true]
        );
        $user->roles()->attach($role);
        $user->load('roles.permissions', 'permissions');
        return $user;
    }

    public function test_candidates_export_returns_download(): void
    {
        $user = $this->createSuperAdmin();
        $this->actingAs($user);

        $response = Livewire::test(CandidateIndex::class)
            ->call('export');

        $response->assertFileDownloaded();
    }

    public function test_companies_export_returns_download(): void
    {
        $user = $this->createSuperAdmin();
        $this->actingAs($user);

        $response = Livewire::test(CompanyIndex::class)
            ->call('export');

        $response->assertFileDownloaded();
    }

    public function test_offers_export_returns_download(): void
    {
        $user = $this->createSuperAdmin();
        $this->actingAs($user);

        $response = Livewire::test(OfferIndex::class)
            ->call('export');

        $response->assertFileDownloaded();
    }

    public function test_matches_export_returns_download(): void
    {
        $user = $this->createSuperAdmin();
        $this->actingAs($user);

        $response = Livewire::test(MatchIndex::class)
            ->call('export');

        $response->assertFileDownloaded();
    }

    public function test_candidates_export_with_date_range(): void
    {
        $user = $this->createSuperAdmin();
        $this->actingAs($user);

        $response = Livewire::test(CandidateIndex::class)
            ->set('exportFrom', '2025-01-01')
            ->set('exportTo', '2025-12-31')
            ->call('export');

        $response->assertFileDownloaded();
    }
}
