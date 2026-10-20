<?php

namespace App\Livewire\Offers;

use App\Enums\ContractType;
use App\Models\JobOffer;
use App\Support\ReferentialCache;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Modifier offre - Agro Eco BAARA')]
class OfferEdit extends Component
{
    #[Locked]
    public string $offerId = '';

    public JobOffer $offer;

    public string $title               = '';
    public string $contract_type       = '';
    public string $duration            = '';
    public bool   $is_periodic         = false;
    public string $mission_description = '';
    public string $economic_conditions = '';
    public string $salary_range        = '';
    public string $other_requirements  = '';
    public string $start_date          = '';
    public string $end_date            = '';
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
        $this->is_periodic         = (bool) $offer->is_periodic || JobOffer::detectPeriodicFromDuration($offer->duration);
        $this->mission_description = $offer->mission_description;
        $this->economic_conditions = $offer->economic_conditions ?? '';
        $this->salary_range        = $offer->salary_range ?? '';
        $this->other_requirements  = $offer->other_requirements ?? '';
        $this->start_date          = $offer->start_date?->format('Y-m-d') ?? '';
        $this->end_date            = $offer->end_date?->format('Y-m-d')
            ?? ($offer->resolvedEndDate()?->format('Y-m-d') ?? '');
        $this->positions_count     = $offer->positions_count ?? 1;
        $this->skill_ids           = $offer->skills->pluck('id')->toArray();
        $this->locations           = !empty($offer->locations)
            ? collect($offer->locations)->map(fn($l) => [
                'commune_id' => $l['commune_id'] ?? '',
                'address'    => $l['address'] ?? '',
            ])->toArray()
            : [['commune_id' => '', 'address' => '']];
    }

    public function updatedDuration(): void
    {
        if (JobOffer::detectPeriodicFromDuration($this->duration)) {
            $this->is_periodic = true;
        }
        $this->recalculateEndDate();
    }

    public function updatedStartDate(): void
    {
        $this->recalculateEndDate();
    }

    public function recalculateEndDate(): void
    {
        if ($this->start_date === '' || trim($this->duration) === '') {
            return;
        }
        try {
            $end = \App\Models\Contract::estimateEndDate(
                \Carbon\Carbon::parse($this->start_date)->startOfDay(),
                $this->duration
            );
            if ($end) {
                $this->end_date = $end->format('Y-m-d');
            }
        } catch (\Throwable) {
            // ignore
        }
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

    public function toggleSkill(string $skillId): void
    {
        if (in_array($skillId, $this->skill_ids, true)) {
            $this->skill_ids = array_values(array_filter(
                $this->skill_ids,
                fn ($id) => (string) $id !== $skillId
            ));
        } else {
            $this->skill_ids[] = $skillId;
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
            'salary_range'        => 'nullable|string|max:150',
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
            'is_periodic'         => $this->is_periodic || JobOffer::detectPeriodicFromDuration($this->duration),
            'mission_description' => $this->mission_description,
            'economic_conditions' => $this->economic_conditions ?: null,
            'salary_range'        => $this->salary_range ?: null,
            'other_requirements'  => $this->other_requirements ?: null,
            'start_date'          => $this->start_date ?: null,
            'end_date'            => $this->end_date ?: null,
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
            'skills'        => ReferentialCache::skills(),
            'communes'      => ReferentialCache::communes(),
            'contractTypes' => ContractType::cases(),
        ]);
    }
}
