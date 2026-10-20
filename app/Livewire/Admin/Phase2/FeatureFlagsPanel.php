<?php

namespace App\Livewire\Admin\Phase2;

use App\Models\FeatureFlag;
use App\Services\FeatureFlagService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Activation Phase 2 - Agro Eco BAARA')]
class FeatureFlagsPanel extends Component
{
    public bool $phase2_master = false;

    public bool $self_registration = false;

    public bool $portal_candidate = false;

    public bool $portal_company = false;

    public bool $portal_training_center = false;

    public bool $public_offers = false;

    public bool $testimonials = false;

    public bool $news_ribbon = false;

    public function mount(): void
    {
        abort_unless(Auth::user()?->is_system, 403);

        $flags = FeatureFlag::allCached();
        foreach (FeatureFlagService::FLAGS as $key) {
            $this->{$key} = (bool) ($flags[$key] ?? false);
        }
    }

    public function toggleMaster(): void
    {
        abort_unless(Auth::user()?->is_system, 403);
        $this->phase2_master = ! $this->phase2_master;
        app(FeatureFlagService::class)->set('phase2_master', $this->phase2_master);
        session()->flash('success', $this->phase2_master
            ? 'Phase 2 activée (matching auto top-5 inclus).'
            : 'Phase 2 désactivée — toutes les fonctionnalités Phase 2 sont masquées.');
    }

    public function save(): void
    {
        abort_unless(Auth::user()?->is_system, 403);

        app(FeatureFlagService::class)->setMany([
            'phase2_master'           => $this->phase2_master,
            'self_registration'       => $this->self_registration,
            'portal_candidate'        => $this->portal_candidate,
            'portal_company'          => $this->portal_company,
            'portal_training_center'  => $this->portal_training_center,
            'public_offers'           => $this->public_offers,
            'testimonials'            => $this->testimonials,
            'news_ribbon'             => $this->news_ribbon,
        ]);

        session()->flash('success', 'Préférences Phase 2 enregistrées.');
    }

    public function render()
    {
        return view('livewire.admin.phase2.feature-flags-panel', [
            'flagMeta' => FeatureFlag::metaCached(),
        ]);
    }
}
