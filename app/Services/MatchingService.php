<?php

namespace App\Services;

use App\Models\Candidate;
use App\Models\CandidateMatch;
use App\Models\JobOffer;
use Illuminate\Support\Collection;

class MatchingService
{
    private const WEIGHT_SKILLS     = 0.50;
    private const WEIGHT_GEOGRAPHY  = 0.20;
    private const WEIGHT_EDUCATION  = 0.10;
    private const WEIGHT_EXPERIENCE = 0.10;
    private const WEIGHT_TRANSPORT  = 0.10;

    /** Plafond SQL avant scoring PHP (évite de charger toute la table). */
    private const CANDIDATE_POOL = 200;

    private const OFFER_POOL = 200;

    /**
     * Top suggestions auto (Phase 2 master).
     */
    public function topSuggestionsForOffer(JobOffer $offer, int $limit = 5): Collection
    {
        return $this->suggestCandidatesForOffer($offer, $limit);
    }

    public function suggestCandidatesForOffer(JobOffer $offer, int $limit = 20): Collection
    {
        $offer->loadMissing('skills', 'company.sites');

        $offerSkillIds = $offer->skills->pluck('id')->toArray();
        $offerLocations = collect($offer->locations ?? []);

        $existingMatchIds = CandidateMatch::where('offer_id', $offer->id)
            ->pluck('candidate_id')
            ->all();

        $query = Candidate::query()
            ->with(['skills', 'commune', 'experiences', 'educationLevel'])
            ->approvedForMatching()
            ->when($existingMatchIds !== [], fn ($q) => $q->whereNotIn('id', $existingMatchIds));

        if (! empty($offerSkillIds)) {
            $query->whereHas('skills', fn ($q) => $q->whereIn('referentials_skills.id', $offerSkillIds));
        }

        $candidates = $query
            ->latest()
            ->limit(self::CANDIDATE_POOL)
            ->get();

        return $candidates
            ->map(fn (Candidate $candidate) => [
                'candidate' => $candidate,
                'score'     => $this->computeScore($candidate, $offer, $offerSkillIds, $offerLocations),
            ])
            ->sortByDesc('score')
            ->values()
            ->take($limit);
    }

    public function suggestOffersForCandidate(Candidate $candidate, int $limit = 20): Collection
    {
        $candidate->loadMissing('skills', 'commune');

        $existingOfferIds = CandidateMatch::where('candidate_id', $candidate->id)
            ->pluck('offer_id')
            ->all();

        $candidateSkillIds = $candidate->skills->pluck('id')->toArray();

        $query = JobOffer::query()
            ->with(['skills', 'company.sites'])
            ->published()
            ->when($existingOfferIds !== [], fn ($q) => $q->whereNotIn('id', $existingOfferIds));

        if (! empty($candidateSkillIds)) {
            $query->whereHas('skills', fn ($q) => $q->whereIn('referentials_skills.id', $candidateSkillIds));
        }

        $offers = $query
            ->latest('published_at')
            ->limit(self::OFFER_POOL)
            ->get();

        return $offers
            ->map(fn (JobOffer $offer) => [
                'offer' => $offer,
                'score' => $this->computeScore(
                    $candidate,
                    $offer,
                    $offer->skills->pluck('id')->toArray(),
                    collect($offer->locations ?? [])
                ),
            ])
            ->sortByDesc('score')
            ->values()
            ->take($limit);
    }

    public function computeScore(
        Candidate $candidate,
        JobOffer $offer,
        array $offerSkillIds,
        Collection $offerLocations
    ): float {
        return round(
            $this->skillsScore($candidate, $offerSkillIds) * self::WEIGHT_SKILLS
            + $this->geographyScore($candidate, $offerLocations) * self::WEIGHT_GEOGRAPHY
            + $this->educationScore($candidate) * self::WEIGHT_EDUCATION
            + $this->experienceScore($candidate) * self::WEIGHT_EXPERIENCE
            + $this->transportScore($candidate) * self::WEIGHT_TRANSPORT,
            2
        );
    }

    private function skillsScore(Candidate $candidate, array $offerSkillIds): float
    {
        if (empty($offerSkillIds)) {
            return 0;
        }

        $candidateSkillIds = $candidate->skills->pluck('id')->toArray();
        $common = count(array_intersect($candidateSkillIds, $offerSkillIds));

        return ($common / count($offerSkillIds)) * 100;
    }

    private function geographyScore(Candidate $candidate, Collection $offerLocations): float
    {
        if ($offerLocations->isEmpty() || ! $candidate->commune_id) {
            return 50;
        }

        $communeIds = $offerLocations->pluck('commune_id')->filter()->toArray();

        return in_array($candidate->commune_id, $communeIds, true) ? 100 : 0;
    }

    private function educationScore(Candidate $candidate): float
    {
        return $candidate->agro_training_text ? 100 : 0;
    }

    private function experienceScore(Candidate $candidate): float
    {
        return $candidate->has_previous_jobs ? 100 : 0;
    }

    private function transportScore(Candidate $candidate): float
    {
        return match ($candidate->transport_mode?->value) {
            'deux_roues', 'voiture' => 100,
            'autre'                 => 50,
            default                 => 0,
        };
    }
}
