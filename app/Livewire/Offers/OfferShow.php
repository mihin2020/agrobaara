<?php

namespace App\Livewire\Offers;

use App\Models\JobOffer;
use App\Services\MatchingService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Offre — Agro Eco BAARA')]
class OfferShow extends Component
{
    public JobOffer $offer;
    public bool     $showSuggestedCandidates = false;

    public function mount(JobOffer $offer): void
    {
        $this->authorize('view', $offer);
        $offer->loadMissing('company.sites', 'skills', 'matches.candidate', 'createdBy');
        $this->offer = $offer;
    }

    public function publish(): void
    {
        $this->authorize('publish', $this->offer);
        $this->offer->publish(Auth::user());
        activity()->causedBy(Auth::user())->performedOn($this->offer)->log('offer_published');
        $this->dispatch('notify', type: 'success', message: 'Offre publiée avec succès.');
    }

    public function archive(): void
    {
        $this->authorize('archive', $this->offer);
        $this->offer->archive();
        activity()->causedBy(Auth::user())->performedOn($this->offer)->log('offer_archived');
        $this->dispatch('notify', type: 'success', message: 'Offre archivée.');
    }

    public function toggleSuggestedCandidates(): void
    {
        $this->showSuggestedCandidates = !$this->showSuggestedCandidates;
    }

    public function render(MatchingService $matchingService)
    {
        $suggestedCandidates = $this->showSuggestedCandidates
            ? $matchingService->suggestCandidatesForOffer($this->offer)
            : collect();

        return view('livewire.offers.offer-show', compact('suggestedCandidates'));
    }
}
