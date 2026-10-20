<?php

namespace App\Livewire\Offers;

use App\Enums\MatchStatus;
use App\Models\Candidate;
use App\Models\CandidateMatch;
use App\Models\JobOffer;
use App\Services\MatchingService;
use App\Services\ReferenceService;
use App\Support\Feature;
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

    public bool $showRelaunchModal = false;

    /** @var string fresh|reconduce */
    public string $relaunchMatchMode = 'fresh';

    public string $relaunch_start_date = '';

    public function mount(JobOffer $offer): void
    {
        $this->authorize('view', $offer);
        $offer->loadMissing(
            'company.sites',
            'skills',
            'matches.candidate.commune',
            'matches.candidate.educationLevel',
            'createdBy',
            'contracts.candidate',
            'contracts.renewedFrom',
            'renewedFrom',
            'renewals'
        );
        $offer->loadCount(['contracts', 'renewals']);
        $this->offer = $offer;
    }

    public function publish(): void
    {
        $this->authorize('publish', $this->offer);
        $this->offer->publish(Auth::user());
        activity()->causedBy(Auth::user())->performedOn($this->offer)->log('offer_published');
        $msg = 'Offre publiée avec succès.';
        if (Feature::autoMatch()) {
            $msg .= ' Suggestions auto top-5 disponibles ci-dessous.';
            $this->showSuggestedCandidates = true;
        }
        $this->dispatch('notify', type: 'success', message: $msg);
    }

    /**
     * Valider un profil du top-5 auto → match accepté + formulaire contrat.
     */
    public function validateAutoSuggestion(string $candidateId, MatchingService $matchingService): void
    {
        abort_unless(Feature::autoMatch(), 404);
        $this->authorize('create', CandidateMatch::class);
        $this->authorize('update', $this->offer);
        abort_unless($this->offer->isPublished(), 422);

        if (CandidateMatch::where('candidate_id', $candidateId)->where('offer_id', $this->offer->id)->exists()) {
            $this->dispatch('notify', type: 'warning', message: 'Cette mise en relation existe déjà.');
            return;
        }

        $top5Ids = $matchingService->topSuggestionsForOffer($this->offer, 5)
            ->pluck('candidate.id')
            ->filter()
            ->all();

        if (! in_array($candidateId, $top5Ids, true)) {
            $this->dispatch('notify', type: 'warning', message: 'Ce candidat n’est plus dans le top 5 suggéré.');
            return;
        }

        $candidate = Candidate::with('skills', 'commune')
            ->approvedForMatching()
            ->findOrFail($candidateId);
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
            'status'       => MatchStatus::Acceptee,
            'operator_id'  => Auth::id(),
            'score'        => $score,
            'source'       => 'auto_top5',
            'proposed_at'  => now(),
            'closed_at'    => now(),
        ]);

        activity()
            ->causedBy(Auth::user())
            ->performedOn($match)
            ->withProperties(['source' => 'auto_top5'])
            ->log('match_created_auto_top5');

        $this->redirect(route('admin.contracts.create', ['match_id' => $match->id]), navigate: true);
    }

    public function archive(): void
    {
        $this->authorize('archive', $this->offer);
        $this->offer->archive();
        activity()->causedBy(Auth::user())->performedOn($this->offer)->log('offer_archived');
        $this->dispatch('notify', type: 'success', message: 'Offre archivée.');
        $this->offer->refresh();
    }

    public function openRelaunchModal(): void
    {
        $this->authorize('create', JobOffer::class);
        $this->authorize('update', $this->offer);

        if (! $this->offer->canRelaunch()) {
            $msg = 'Relance impossible.';
            if (! $this->offer->isPeriodic()) {
                $msg = 'Seules les offres périodiques peuvent être relancées.';
            } elseif (! $this->offer->hasPeriod()) {
                $msg = 'Renseignez d’abord la période (date de début et durée / date de fin) de l’offre.';
            } elseif (! $this->offer->isArchived() && ! $this->offer->isExpired()) {
                $msg = 'L’offre doit être expirée ou archivée avant reconduction.';
            } elseif (($this->offer->renewals_count ?? $this->offer->renewals()->count()) > 0) {
                $msg = 'Cette offre a déjà été relancée.';
            }
            $this->dispatch('notify', type: 'warning', message: $msg);
            return;
        }

        $this->relaunchMatchMode = 'fresh';
        $end = $this->offer->resolvedEndDate();
        $this->relaunch_start_date = $end
            ? $end->copy()->addDay()->format('Y-m-d')
            : now()->addDay()->format('Y-m-d');

        $this->showRelaunchModal = true;
    }

    public function closeRelaunchModal(): void
    {
        $this->showRelaunchModal = false;
    }

    public function confirmRelaunch(ReferenceService $referenceService): void
    {
        $this->authorize('create', JobOffer::class);
        $this->authorize('update', $this->offer);

        if (! $this->offer->canRelaunch()) {
            $this->dispatch('notify', type: 'warning', message: 'Cette offre ne peut pas être relancée (période, expiration / archivage requis).');
            return;
        }

        $this->validate([
            'relaunchMatchMode'   => 'required|in:fresh,reconduce',
            'relaunch_start_date' => 'nullable|date',
        ]);

        $executableCount = $this->offer->matches()
            ->where('status', MatchStatus::Acceptee->value)
            ->pluck('candidate_id')
            ->merge($this->offer->contracts()->pluck('candidate_id'))
            ->unique()
            ->count();

        if ($this->relaunchMatchMode === 'reconduce' && $executableCount === 0) {
            $this->dispatch(
                'notify',
                type: 'warning',
                message: 'Aucun candidat accepté / sous contrat à reconduire. Choisissez un nouveau matching.'
            );
            return;
        }

        $clone = $this->offer->relaunch($referenceService, Auth::user(), [
            'match_mode'  => $this->relaunchMatchMode,
            'archive_old' => true,
            'start_date'  => $this->relaunch_start_date ?: null,
            'keep_periodic' => true,
        ]);

        activity()
            ->causedBy(Auth::user())
            ->performedOn($clone)
            ->withProperties([
                'renewed_from' => $this->offer->id,
                'match_mode'   => $this->relaunchMatchMode,
                'old_reference'=> $this->offer->reference,
                'new_reference'=> $clone->reference,
            ])
            ->log('offer_relaunched');

        activity()
            ->causedBy(Auth::user())
            ->performedOn($this->offer)
            ->withProperties(['relaunched_as' => $clone->id, 'historized' => true])
            ->log('offer_historized_on_relaunch');

        $msg = $this->relaunchMatchMode === 'reconduce'
            ? 'Offre relancée avec reconduction des candidats. L’ancienne est archivée / historisée.'
            : 'Offre relancée (nouveau matching requis). L’ancienne est archivée / historisée.';

        $this->showRelaunchModal = false;
        $this->dispatch('notify', type: 'success', message: $msg);
        $this->redirect(route('admin.offers.edit', $clone), navigate: true);
    }

    /** Relance simple (non périodique) : clone brouillon sans archiver. */
    public function renew(ReferenceService $referenceService): void
    {
        $this->authorize('create', JobOffer::class);
        $this->authorize('update', $this->offer);

        if ($this->offer->isPeriodic()) {
            $this->openRelaunchModal();
            return;
        }

        $clone = $this->offer->renew($referenceService, Auth::user());

        activity()
            ->causedBy(Auth::user())
            ->performedOn($clone)
            ->withProperties(['renewed_from' => $this->offer->id])
            ->log('offer_renewed');

        $this->dispatch('notify', type: 'success', message: 'Offre clonée en brouillon.');
        $this->redirect(route('admin.offers.edit', $clone), navigate: true);
    }

    public function toggleSuggestedCandidates(): void
    {
        $this->showSuggestedCandidates = ! $this->showSuggestedCandidates;
    }

    public function proposeMatch(string $candidateId, MatchingService $matchingService): void
    {
        $this->authorize('create', CandidateMatch::class);
        abort_unless($this->offer->isPublished() || $this->offer->isDraft(), 422);

        if (CandidateMatch::where('candidate_id', $candidateId)->where('offer_id', $this->offer->id)->exists()) {
            $this->dispatch('notify', type: 'warning', message: 'Cette mise en relation existe déjà.');
            return;
        }

        $candidate = Candidate::with('skills', 'commune')
            ->approvedForMatching()
            ->findOrFail($candidateId);
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
            'source'       => 'manual',
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
        $autoMatchOn = Feature::autoMatch() && $this->offer->isPublished();

        $autoTop5 = $autoMatchOn
            ? $matchingService->topSuggestionsForOffer($this->offer, 5)
            : collect();

        $suggestedCandidates = $this->showSuggestedCandidates
            ? $matchingService->suggestCandidatesForOffer($this->offer)
            : collect();

        $executableCandidates = $this->offer->matches
            ->where('status', MatchStatus::Acceptee)
            ->pluck('candidate')
            ->merge($this->offer->contracts->pluck('candidate'))
            ->filter()
            ->unique('id')
            ->values();

        return view('livewire.offers.offer-show', compact(
            'suggestedCandidates',
            'executableCandidates',
            'autoTop5',
            'autoMatchOn'
        ));
    }
}
