<?php

namespace App\Livewire\Dashboard;

use App\Enums\MatchStatus;
use App\Enums\OfferStatus;
use App\Enums\UserStatus;
use App\Models\Candidate;
use App\Models\CandidateMatch;
use App\Models\Company;
use App\Models\JobOffer;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Tableau de bord — Agro Eco BAARA')]
class OperatorDashboard extends Component
{
    public function render()
    {
        $user = Auth::user();

        $isOperator   = $user->isOperator();
        $candidateQuery = Candidate::query();
        $matchQuery     = CandidateMatch::query();
        $offerQuery     = JobOffer::query();

        if ($isOperator) {
            $candidateQuery->byOperator($user->id);
            $matchQuery->byOperator($user->id);
            $offerQuery->byOperator($user->id);
        }

        // KPIs
        $totalCandidates = $candidateQuery->count();
        $totalCompanies  = $isOperator ? Company::byOperator($user->id)->count() : Company::count();
        $activeOffers    = $offerQuery->published()->count();
        $successMatches  = $matchQuery->where('status', MatchStatus::Acceptee)->count();

        // Mises en relation prioritaires (ouverts, récents)
        $priorityMatches = CandidateMatch::with(['candidate', 'offer.company'])
            ->when($isOperator, fn($q) => $q->byOperator($user->id))
            ->open()
            ->latest()
            ->take(5)
            ->get();

        // Dernières offres
        $latestOffers = JobOffer::with('company')
            ->when($isOperator, fn($q) => $q->byOperator($user->id))
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        // Outils d'audit interne (super-admin / modérateur seulement)
        $auditData = null;
        if (!$isOperator) {
            $auditData = [
                'duplicates'      => 0, // TODO: logique de détection doublons (Phase 2)
                'missing_data'    => Candidate::whereNull('email')->count(),
                'active_operators' => User::active()
                    ->whereHas('roles', fn($q) => $q->where('slug', 'operateur'))
                    ->count(),
            ];
        }

        return view('livewire.dashboard.operator-dashboard', compact(
            'totalCandidates',
            'totalCompanies',
            'activeOffers',
            'successMatches',
            'priorityMatches',
            'latestOffers',
            'auditData',
            'isOperator',
        ));
    }
}
