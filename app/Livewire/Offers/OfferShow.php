<?php

namespace App\Livewire\Offers;

use App\Enums\MatchStatus;
use App\Models\Candidate;
use App\Models\CandidateMatch;
use App\Models\JobOffer;
use App\Services\MatchingService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Offre - Agro Eco BAARA')]
class OfferShow extends Component
{
    public JobOffer $offer;
    public bool     $showSuggestedCandidates = false;

    public function mount(JobOffer $offer): void
    {
        $this->authorize('view', $offer);
        $offer->loadMissing('company.sites', 'skills', 'matches.candidate.commune', 'matches.candidate.educationLevel', 'createdBy');
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

    public function proposeMatch(string $candidateId, MatchingService $matchingService): void
    {
        $this->authorize('create', CandidateMatch::class);

        if (CandidateMatch::where('candidate_id', $candidateId)->where('offer_id', $this->offer->id)->exists()) {
            $this->dispatch('notify', type: 'warning', message: 'Cette mise en relation existe déjà.');
            return;
        }

        $candidate = Candidate::with('skills', 'commune')->findOrFail($candidateId);
        $this->offer->loadMissing('skills');

        $score = $matchingService->computeScore(
            $candidate,
            $this->offer,
            $this->offer->skills->pluck('id')->toArray(),
            collect($this->offer->locations ?? [])
        );

        $match = CandidateMatch::create([
            'candidate_id' => $candidate->id,
            'offer_id'     => $this->offer->id,
            'status'       => MatchStatus::Proposee,
            'operator_id'  => Auth::id(),
            'score'        => $score,
            'proposed_at'  => now(),
        ]);

        activity()
            ->causedBy(Auth::user())
            ->performedOn($match)
            ->withProperties(['manual' => true, 'from' => 'offer_suggestions'])
            ->log('match_created_manual');

        $this->offer->load('matches.candidate.commune', 'matches.candidate.educationLevel');
        $this->dispatch('notify', type: 'success', message: 'Mise en relation créée avec ' . $candidate->full_name . '.');
    }

    public function render(MatchingService $matchingService)
    {
        $suggestedCandidates = $this->showSuggestedCandidates
            ? $matchingService->suggestCandidatesForOffer($this->offer)
            : collect();

        return view('livewire.offers.offer-show', compact('suggestedCandidates'));
    }
}
