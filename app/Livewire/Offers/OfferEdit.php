<?php

namespace App\Livewire\Offers;

use App\Enums\ContractType;
use App\Models\JobOffer;
use App\Models\ReferentialCommune;
use App\Models\ReferentialSkill;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Modifier offre — Agro Eco BAARA')]
class OfferEdit extends Component
{
    #[Locked]
    public string $offerId = '';

    public JobOffer $offer;

    public string $title               = '';
    public string $contract_type       = '';
    public string $duration            = '';
    public string $mission_description = '';
    public string $economic_conditions = '';
    public string $other_requirements  = '';
    public string $start_date          = '';
    public int    $positions_count     = 1;
    public array  $skill_ids           = [];
    public array  $locations           = [];

    public function mount(JobOffer $offer): void
    {
        $this->authorize('update', $offer);

        $offer->loadMissing('skills');
        $this->offer   = $offer;
        $this->offerId = $offer->id;

        $this->title               = $offer->title;
        $this->contract_type       = $offer->contract_type->value;
        $this->duration            = $offer->duration ?? '';
        $this->mission_description = $offer->mission_description;
        $this->economic_conditions = $offer->economic_conditions ?? '';
        $this->other_requirements  = $offer->other_requirements ?? '';
        $this->start_date          = $offer->start_date?->format('Y-m-d') ?? '';
        $this->positions_count     = $offer->positions_count ?? 1;
        $this->skill_ids           = $offer->skills->pluck('id')->toArray();
        $this->locations           = !empty($offer->locations)
            ? collect($offer->locations)->map(fn($l) => [
                'commune_id' => $l['commune_id'] ?? '',
                'address'    => $l['address'] ?? '',
            ])->toArray()
            : [['commune_id' => '', 'address' => '']];
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

    public function save(): void
    {
        $this->validate([
            'title'               => 'required|string|min:5|max:200',
            'contract_type'       => 'required|in:' . implode(',', array_column(ContractType::cases(), 'value')),
            'mission_description' => 'required|string|min:20',
            'skill_ids'           => 'required|array|min:1',
            'locations'           => 'required|array|min:1',
            'locations.*.commune_id' => 'required|uuid|exists:referentials_communes,id',
            'positions_count'     => 'nullable|integer|min:1',
        ], [
            'title.required'               => "L'intitulé est obligatoire.",
            'contract_type.required'       => 'Le type de contrat est obligatoire.',
            'mission_description.required' => 'La description des missions est obligatoire.',
            'skill_ids.required'           => 'Au moins une compétence est requise.',
            'locations.*.commune_id.required' => 'La ville est obligatoire.',
        ]);

        $this->offer->update([
            'title'               => $this->title,
            'contract_type'       => $this->contract_type,
            'duration'            => $this->duration ?: null,
            'mission_description' => $this->mission_description,
            'economic_conditions' => $this->economic_conditions ?: null,
            'other_requirements'  => $this->other_requirements ?: null,
            'start_date'          => $this->start_date ?: null,
            'positions_count'     => $this->positions_count,
            'locations'           => $this->locations,
        ]);

        $this->offer->skills()->sync($this->skill_ids);

        activity()->causedBy(Auth::user())->performedOn($this->offer)->log('offer_updated');

        session()->flash('success', 'Offre mise à jour avec succès.');
        $this->redirect(route('admin.offers.show', $this->offer), navigate: true);
    }

    public function render()
    {
        return view('livewire.offers.offer-edit', [
            'skills'        => ReferentialSkill::active()->get(),
            'communes'      => ReferentialCommune::active()->get(),
            'contractTypes' => ContractType::cases(),
        ]);
    }
}
