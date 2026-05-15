<?php

namespace App\Services;

use App\Models\Candidate;
use App\Models\CandidateMatch;
use App\Models\JobOffer;
use Illuminate\Support\Collection;

class MatchingService
{
    // Pondération MVP (paramétrable en Phase 2)
    private const WEIGHT_SKILLS      = 0.50;
    private const WEIGHT_GEOGRAPHY   = 0.20;
    private const WEIGHT_EDUCATION   = 0.10;
    private const WEIGHT_EXPERIENCE  = 0.10;
    private const WEIGHT_TRANSPORT   = 0.10;

    /**
     * Retourne les candidats suggérés pour une offre, triés par score décroissant.
     * Exclut les candidats déjà mis en relation avec cette offre.
     */
    public function suggestCandidatesForOffer(JobOffer $offer, int $limit = 20): Collection
    {
        $offer->loadMissing('skills', 'company.sites');

        $offerSkillIds = $offer->skills->pluck('id')->toArray();
        $offerLocations = collect($offer->locations ?? []);

        $existingMatchIds = CandidateMatch::where('offer_id', $offer->id)
                                          ->pluck('candidate_id')
                                          ->toArray();

        $candidates = Candidate::with('skills', 'commune', 'experiences')
                               ->whereNotIn('id', $existingMatchIds)
                               ->get();

        return $candidates
            ->map(fn(Candidate $candidate) => [
                'candidate' => $candidate,
                'score'     => $this->computeScore($candidate, $offer, $offerSkillIds, $offerLocations),
            ])
            ->sortByDesc('score')
            ->values()
            ->take($limit);
    }

    /**
     * Retourne les offres pertinentes pour un candidat, triées par score décroissant.
     */
    public function suggestOffersForCandidate(Candidate $candidate, int $limit = 20): Collection
    {
        $candidate->loadMissing('skills', 'commune');

        $existingOfferIds = CandidateMatch::where('candidate_id', $candidate->id)
                                          ->pluck('offer_id')
                                          ->toArray();

        $offers = JobOffer::with('skills', 'company.sites')
                          ->published()
                          ->whereNotIn('id', $existingOfferIds)
                          ->get();

        $candidateSkillIds = $candidate->skills->pluck('id')->toArray();

        return $offers
            ->map(fn(JobOffer $offer) => [
                'offer' => $offer,
                'score' => $this->computeScore($candidate, $offer, $offer->skills->pluck('id')->toArray(), collect($offer->locations ?? [])),
            ])
            ->sortByDesc('score')
            ->values()
            ->take($limit);
    }

    // -----------------------------------------------------------------
    // Score calculation
    // -----------------------------------------------------------------

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
        if ($offerLocations->isEmpty() || !$candidate->commune_id) {
            return 50; // score neutre si pas d'info géographique
        }

        $communeIds = $offerLocations->pluck('commune_id')->filter()->toArray();

        return in_array($candidate->commune_id, $communeIds) ? 100 : 0;
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
        return match($candidate->transport_mode?->value) {
            'deux_roues', 'voiture' => 100,
            'autre'                 => 50,
            default                 => 0,
        };
    }
}
