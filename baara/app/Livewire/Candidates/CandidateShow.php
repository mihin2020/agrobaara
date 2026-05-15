<?php

namespace App\Livewire\Candidates;

use App\Enums\MatchStatus;
use App\Models\Candidate;
use App\Models\JobOffer;
use App\Services\MatchingService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Profil candidat — Agro Eco BAARA')]
class CandidateShow extends Component
{
    #[Locked]
    public string $candidateId = '';

    public Candidate $candidate;
    public bool $showSuggestedOffers = false;

    public function mount(Candidate $candidate): void
    {
        $this->authorize('view', $candidate);
        $candidate->loadMissing(
            'commune', 'languages', 'licenses', 'skills', 'experiences',
            'matches.offer.company', 'createdBy'
        );
        $this->candidate   = $candidate;
        $this->candidateId = $candidate->id;
    }

    public function toggleSuggestedOffers(): void
    {
        $this->showSuggestedOffers = !$this->showSuggestedOffers;
    }

    public function render(MatchingService $matchingService)
    {
        $suggestedOffers = $this->showSuggestedOffers
            ? $matchingService->suggestOffersForCandidate($this->candidate)
            : collect();

        return view('livewire.candidates.candidate-show', [
            'suggestedOffers' => $suggestedOffers,
        ]);
    }
}
