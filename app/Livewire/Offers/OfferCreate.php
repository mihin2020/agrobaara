<?php

namespace App\Livewire\Offers;

use App\Enums\ContractType;
use App\Models\Company;
use App\Models\JobOffer;
use App\Models\ReferentialCommune;
use App\Models\ReferentialSkill;
use App\Services\ReferenceService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Nouvelle offre — Agro Eco BAARA')]
class OfferCreate extends Component
{
    public string $company_id          = '';
    public string $title               = '';
    public string $contract_type       = '';
    public string $duration            = '';
    public string $mission_description = '';
    public string $economic_conditions = '';
    public string $other_requirements  = '';
    public string $start_date          = '';
    public int    $positions_count     = 1;
    public array  $skill_ids           = [];
    public array  $locations           = [
        ['commune_id' => '', 'address' => ''],
    ];

    public function mount(): void
    {
        $this->authorize('create', JobOffer::class);
    }

    public function addLocation(): void
    {
        $this->locations[] = ['commune_id' => '', 'address' => ''];
    }

    public function removeLocation(int $index): void
    {
        if (count($this->locations) > 1) {
            unset($this->locations[$index]);
            $this->locations = array_values($this->locations);
        }
    }

    public function save(ReferenceService $referenceService): void
    {
        $this->validate([
            'company_id'          => 'required|uuid|exists:companies,id',
            'title'               => 'required|string|min:5|max:200',
            'contract_type'       => 'required|in:' . implode(',', array_column(ContractType::cases(), 'value')),
            'mission_description' => 'required|string|min:20',
            'skill_ids'           => 'required|array|min:1',
            'locations'           => 'required|array|min:1',
            'locations.*.commune_id' => 'required|uuid|exists:referentials_communes,id',
            'positions_count'     => 'nullable|integer|min:1',
        ], [
            'company_id.required'          => "L'entreprise est obligatoire.",
            'title.required'               => "L'intitulé est obligatoire.",
            'contract_type.required'       => 'Le type de contrat est obligatoire.',
            'mission_description.required' => 'La description des missions est obligatoire.',
            'skill_ids.required'           => 'Au moins une compétence est requise.',
            'locations.*.commune_id.required' => 'La commune est obligatoire.',
        ]);

        $offer = JobOffer::create([
            'reference'           => $referenceService->generateOfferReference(),
            'company_id'          => $this->company_id,
            'title'               => $this->title,
            'contract_type'       => $this->contract_type,
            'duration'            => $this->duration ?: null,
            'mission_description' => $this->mission_description,
            'economic_conditions' => $this->economic_conditions ?: null,
            'other_requirements'  => $this->other_requirements ?: null,
            'start_date'          => $this->start_date ?: null,
            'positions_count'     => $this->positions_count,
            'locations'           => $this->locations,
            'status'              => \App\Enums\OfferStatus::Brouillon,
            'created_by'          => Auth::id(),
        ]);

        $offer->skills()->sync($this->skill_ids);

        activity()->causedBy(Auth::user())->performedOn($offer)->log('offer_created');

        session()->flash('success', 'Offre créée en brouillon.');
        $this->redirect(route('admin.offers.show', $offer), navigate: true);
    }

    public function render()
    {
        return view('livewire.offers.offer-create', [
            'companies'     => Company::orderBy('name')->get(),
            'skills'        => ReferentialSkill::active()->get(),
            'communes'      => ReferentialCommune::active()->get(),
            'contractTypes' => ContractType::cases(),
        ]);
    }
}
