<?php

namespace App\Livewire\Offers;

use App\Enums\ApprovalStatus;
use App\Enums\ContractType;
use App\Models\Company;
use App\Models\JobOffer;
use App\Services\ReferenceService;
use App\Support\ReferentialCache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Nouvelle offre - Agro Eco BAARA')]
class OfferCreate extends Component
{
    public string $company_id          = '';
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
    public array  $locations           = [
        ['commune_id' => '', 'address' => ''],
    ];
    /** @var string brouillon|publiee */
    public string $publication_status  = 'brouillon';

    public function mount(): void
    {
        $this->authorize('create', JobOffer::class);
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

    public function save(ReferenceService $referenceService): void
    {
        $this->validate([
            'company_id' => [
                'required',
                'uuid',
                Rule::exists('companies', 'id')->where(function ($q) {
                    $q->where(function ($inner) {
                        $inner->whereNull('approval_status')
                            ->orWhere('approval_status', ApprovalStatus::Approved->value);
                    });
                }),
            ],
            'title'               => 'required|string|min:5|max:200',
            'contract_type'       => 'required|in:' . implode(',', array_column(ContractType::cases(), 'value')),
            'mission_description' => 'required|string|min:20',
            'skill_ids'           => 'required|array|min:1',
            'locations'           => 'required|array|min:1',
            'locations.*.commune_id' => 'required|uuid|exists:referentials_communes,id',
            'positions_count'     => 'nullable|integer|min:1',
            'publication_status'  => 'required|in:brouillon,publiee',
            'salary_range'        => 'nullable|string|max:150',
        ], [
            'company_id.required'          => "L'entreprise est obligatoire.",
            'company_id.exists'            => "L'entreprise sélectionnée n'est pas approuvée.",
            'title.required'               => "L'intitulé est obligatoire.",
            'contract_type.required'       => 'Le type de contrat est obligatoire.',
            'mission_description.required' => 'La description des missions est obligatoire.',
            'skill_ids.required'           => 'Au moins une compétence est requise.',
            'locations.*.commune_id.required' => 'La commune est obligatoire.',
            'publication_status.in'        => 'Le statut de publication est invalide.',
        ]);

        $shouldPublish = $this->publication_status === 'publiee';

        if ($shouldPublish) {
            $this->authorize('publish', JobOffer::class);
        }

        $offer = JobOffer::create([
            'reference'           => $referenceService->generateOfferReference(),
            'company_id'          => $this->company_id,
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
            'status'              => \App\Enums\OfferStatus::Brouillon,
            'created_by'          => Auth::id(),
        ]);

        $offer->skills()->sync($this->skill_ids);

        if ($shouldPublish) {
            $offer->publish(Auth::user());
            activity()->causedBy(Auth::user())->performedOn($offer)->log('offer_created_and_published');
            session()->flash('success', 'Offre créée et publiée avec succès.');
        } else {
            activity()->causedBy(Auth::user())->performedOn($offer)->log('offer_created');
            session()->flash('success', 'Offre créée en brouillon.');
        }

        $this->redirect(route('admin.offers.show', $offer), navigate: true);
    }

    public function render()
    {
        return view('livewire.offers.offer-create', [
            'companies'     => Company::query()
                ->where(function ($q) {
                    $q->whereNull('approval_status')
                        ->orWhere('approval_status', 'approved');
                })
                ->orderBy('name')
                ->get(['id', 'name', 'reference']),
            'skills'        => ReferentialCache::skills(),
            'communes'      => ReferentialCache::communes(),
            'contractTypes' => ContractType::cases(),
        ]);
    }
}
