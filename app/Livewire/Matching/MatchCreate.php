<?php

namespace App\Livewire\Matching;

use App\Enums\MatchStatus;
use App\Enums\OfferStatus;
use App\Models\Candidate;
use App\Models\CandidateMatch;
use App\Models\JobOffer;
use App\Services\MatchingService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Nouvelle mise en relation - Agro Eco BAARA')]
class MatchCreate extends Component
{
    #[Url]
    public string $candidate_id = '';

    #[Url]
    public string $offer_id = '';

    public string $notes = '';
    public string $candidateSearch = '';
    public string $offerSearch = '';

    public function mount(): void
    {
        $this->authorize('create', CandidateMatch::class);
    }

    public function updatedCandidateSearch(): void
    {
        // Recherche locale via render — pas de reset id
    }

    public function selectCandidate(string $id): void
    {
        $this->candidate_id = $id;
        $this->candidateSearch = '';
        $this->resetValidation('candidate_id');
    }

    public function clearCandidate(): void
    {
        $this->candidate_id = '';
    }

    public function selectOffer(string $id): void
    {
        $this->offer_id = $id;
        $this->offerSearch = '';
        $this->resetValidation('offer_id');
    }

    public function clearOffer(): void
    {
        $this->offer_id = '';
    }

    public function save(MatchingService $matchingService): void
    {
        $this->authorize('create', CandidateMatch::class);

        $this->validate([
            'candidate_id' => 'required|uuid|exists:candidates,id',
            'offer_id'     => [
                'required',
                'uuid',
                'exists:job_offers,id',
                function (string $attribute, mixed $value, \Closure $fail) {
                    $exists = CandidateMatch::where('candidate_id', $this->candidate_id)
                        ->where('offer_id', $value)
                        ->exists();
                    if ($exists) {
                        $fail('Cette mise en relation existe déjà pour ce candidat et cette offre.');
                    }
                },
            ],
            'notes' => 'nullable|string|max:2000',
        ], [
            'candidate_id.required' => 'Sélectionnez un candidat.',
            'offer_id.required'     => 'Sélectionnez une offre.',
        ]);

        $candidate = Candidate::with('skills', 'commune')->findOrFail($this->candidate_id);
        $offer     = JobOffer::with('skills', 'company.sites')->findOrFail($this->offer_id);

        $score = $matchingService->computeScore(
            $candidate,
            $offer,
            $offer->skills->pluck('id')->toArray(),
            collect($offer->locations ?? [])
        );

        $match = CandidateMatch::create([
            'candidate_id' => $candidate->id,
            'offer_id'     => $offer->id,
            'status'       => MatchStatus::Proposee,
            'operator_id'  => Auth::id(),
            'notes'        => $this->notes ?: null,
            'score'        => $score,
            'proposed_at'  => now(),
        ]);

        activity()
            ->causedBy(Auth::user())
            ->performedOn($match)
            ->withProperties([
                'candidate_id' => $candidate->id,
                'offer_id'     => $offer->id,
                'score'        => $score,
                'manual'       => true,
            ])
            ->log('match_created_manual');

        session()->flash('success', 'Mise en relation créée avec succès.');
        $this->redirect(route('admin.matches.show', $match), navigate: true);
    }

    public function render()
    {
        $selectedCandidate = $this->candidate_id
            ? Candidate::with('commune')->find($this->candidate_id)
            : null;

        $selectedOffer = $this->offer_id
            ? JobOffer::with('company')->find($this->offer_id)
            : null;

        $candidates = collect();
        if ($this->candidateSearch !== '' && strlen($this->candidateSearch) >= 2) {
            $term = '%' . $this->candidateSearch . '%';
            $candidates = Candidate::with('commune')
                ->where(function ($q) use ($term) {
                    $q->where('first_name', 'like', $term)
                      ->orWhere('last_name', 'like', $term)
                      ->orWhere('reference', 'like', $term)
                      ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", [$term]);
                })
                ->orderBy('last_name')
                ->limit(15)
                ->get();
        }

        $offers = collect();
        if ($this->offerSearch !== '' && strlen($this->offerSearch) >= 2) {
            $term = '%' . $this->offerSearch . '%';
            $offers = JobOffer::with('company')
                ->where(function ($q) use ($term) {
                    $q->where('title', 'like', $term)
                      ->orWhere('reference', 'like', $term)
                      ->orWhereHas('company', fn ($cq) => $cq->where('name', 'like', $term));
                })
                ->whereIn('status', [OfferStatus::Brouillon, OfferStatus::Publiee])
                ->latest()
                ->limit(15)
                ->get();
        }

        return view('livewire.matching.match-create', compact(
            'selectedCandidate',
            'selectedOffer',
            'candidates',
            'offers'
        ));
    }
}
