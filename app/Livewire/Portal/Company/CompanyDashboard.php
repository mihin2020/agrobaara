<?php

namespace App\Livewire\Portal\Company;

use App\Enums\OfferStatus;
use App\Enums\UserRole;
use App\Models\Contract;
use App\Models\JobOffer;
use App\Services\MatchingService;
use App\Services\ReferenceService;
use App\Support\Feature;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.portal')]
#[Title('Espace organisation')]
class CompanyDashboard extends Component
{
    public string $offer_title = '';

    public string $offer_mission = '';

    public string $contract_type = 'salarie';

    public bool $isCenter = false;

    public bool $showSuggestions = false;

    public function mount(): void
    {
        $user = Auth::user();
        $this->isCenter = $user->hasRole(UserRole::CentreFormation);
        $flag = $this->isCenter ? 'portal_training_center' : 'portal_company';
        abort_unless(Feature::enabled($flag), 404);
        abort_unless($user->hasAnyRole([UserRole::Entreprise->value, UserRole::CentreFormation->value]), 403);
    }

    public function toggleSuggestions(): void
    {
        $this->showSuggestions = ! $this->showSuggestions;
    }

    public function createOffer(ReferenceService $refs): void
    {
        $company = Auth::user()->companyProfile;
        abort_unless($company && $company->approval_status?->value === 'approved', 403);

        $this->validate([
            'offer_title'   => 'required|string|min:5|max:200',
            'offer_mission' => 'required|string|min:20',
            'contract_type' => 'required|string|max:50',
        ]);

        JobOffer::create([
            'reference'           => $refs->generateOfferReference(),
            'company_id'          => $company->id,
            'title'               => $this->offer_title,
            'contract_type'       => $this->contract_type,
            'mission_description' => $this->offer_mission,
            'positions_count'     => 1,
            'status'              => OfferStatus::EnAttente,
            'created_by'          => Auth::id(),
            'locations'           => [],
        ]);

        $this->reset('offer_title', 'offer_mission');
        session()->flash('success', 'Offre soumise — en attente de validation par un opérateur.');
    }

    public function render(MatchingService $matching)
    {
        $company = Auth::user()->companyProfile;
        $offers = $company
            ? $company->offers()->latest()->limit(30)->get()
            : collect();
        $contracts = $company
            ? Contract::forCompany($company->id)->withList()->latest()->limit(20)->get()
            : collect();

        $suggestions = collect();
        if ($company && $this->showSuggestions && Feature::autoMatch()) {
            foreach ($offers->where('status', OfferStatus::Publiee)->take(3) as $offer) {
                $suggestions[$offer->id] = [
                    'offer' => $offer,
                    'rows'  => $matching->topSuggestionsForOffer($offer, 5),
                ];
            }
        }

        $trained = $this->isCenter && $company
            ? $company->trainedCandidates()->with('commune')->latest()->limit(20)->get()
            : collect();

        return view('livewire.portal.company.company-dashboard', compact(
            'company', 'offers', 'contracts', 'suggestions', 'trained'
        ));
    }
}
