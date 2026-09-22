<?php

namespace App\Livewire\Candidates;

use App\Enums\MatchStatus;
use App\Models\Candidate;
use App\Models\CandidateMatch;
use App\Models\JobOffer;
use App\Services\MatchingService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Profil candidat - Agro Eco BAARA')]
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

    public function proposeMatch(string $offerId, MatchingService $matchingService): void
    {
        $this->authorize('create', CandidateMatch::class);

        if (CandidateMatch::where('candidate_id', $this->candidate->id)->where('offer_id', $offerId)->exists()) {
            $this->dispatch('notify', type: 'warning', message: 'Cette mise en relation existe déjà.');
            return;
        }

        $offer = JobOffer::with('skills', 'company.sites')->findOrFail($offerId);
        $this->candidate->loadMissing('skills', 'commune');

        $score = $matchingService->computeScore(
            $this->candidate,
            $offer,
            $offer->skills->pluck('id')->toArray(),
            collect($offer->locations ?? [])
        );

        $match = CandidateMatch::create([
            'candidate_id' => $this->candidate->id,
            'offer_id'     => $offer->id,
            'status'       => MatchStatus::Proposee,
            'operator_id'  => Auth::id(),
            'score'        => $score,
            'proposed_at'  => now(),
        ]);

        activity()
            ->causedBy(Auth::user())
            ->performedOn($match)
            ->withProperties(['manual' => true, 'from' => 'candidate_suggestions'])
            ->log('match_created_manual');

        $this->candidate->load('matches.offer.company');
        $this->dispatch('notify', type: 'success', message: 'Mise en relation créée avec l\'offre « ' . $offer->title . ' ».');
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
