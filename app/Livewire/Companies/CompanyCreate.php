<?php

namespace App\Livewire\Companies;

use App\Enums\CompanyStatus;
use App\Enums\ContractType;
use App\Enums\OfferStatus;
use App\Models\Company;
use App\Models\ReferentialCommune;
use App\Models\ReferentialSkill;
use App\Services\ReferenceService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Nouvelle entreprise — Agro Eco BAARA')]
class CompanyCreate extends Component
{
    public string $name                 = '';
    public string $status               = '';
    public string $legal_rep_first_name = '';
    public string $legal_rep_last_name  = '';
    public array  $activity_types       = [];
    public string $description          = '';
    public string $phone                = '';
    public string $email                = '';
    public string $website              = '';
    public string $social_facebook      = '';
    public string $social_linkedin      = '';
    public string $social_whatsapp      = '';
    public bool   $need_training        = false;
    public bool   $need_financing       = false;
    public bool   $need_contract_support = false;
    public string $operator_notes       = '';
    public array  $sites                = [
        ['label' => '', 'commune_id' => '', 'address' => '', 'is_main' => true],
    ];
    public array  $offers               = [];

    public function mount(): void
    {
        $this->authorize('create', Company::class);
    }

    public function addSite(): void
    {
        $this->sites[] = ['label' => '', 'commune_id' => '', 'address' => '', 'is_main' => false];
    }

    public function removeSite(int $index): void
    {
        if (count($this->sites) > 1) {
            unset($this->sites[$index]);
            $this->sites = array_values($this->sites);
        }
    }

    public function addOffer(): void
    {
        $this->offers[] = [
            'contract_type'       => '',
            'title'               => '',
            'duration'            => '',
            'economic_conditions' => '',
            'skill_ids'           => [],
            'mission_description' => '',
            'location_text'       => '',
        ];
    }

    public function removeOffer(int $index): void
    {
        unset($this->offers[$index]);
        $this->offers = array_values($this->offers);
    }

    public function setOfferContractType(int $offerIndex, string $type): void
    {
        $current = $this->offers[$offerIndex]['contract_type'] ?? '';
        $this->offers[$offerIndex]['contract_type'] = ($current === $type) ? '' : $type;
    }

    public function toggleOfferSkill(int $offerIndex, string $skillId): void
    {
        $skills = $this->offers[$offerIndex]['skill_ids'] ?? [];
        if (in_array($skillId, $skills)) {
            $this->offers[$offerIndex]['skill_ids'] = array_values(
                array_filter($skills, fn($s) => $s !== $skillId)
            );
        } else {
            $this->offers[$offerIndex]['skill_ids'][] = $skillId;
        }
    }

    public function save(ReferenceService $referenceService): void
    {
        $this->validate([
            'name'               => 'required|string|min:2|max:200',
            'status'             => 'required|in:' . implode(',', array_column(CompanyStatus::cases(), 'value')),
            'activity_types'     => 'required|array|min:1',
            'phone'              => 'required|string|max:30',
            'email'              => 'nullable|email|max:150',
            'website'            => 'nullable|url|max:255',
            'sites'              => 'required|array|min:1',
            'sites.*.label'      => 'required|string|max:150',
            'sites.*.commune_id' => 'required|uuid|exists:referentials_communes,id',
        ], [
            'name.required'               => "Le nom de l'entreprise est obligatoire.",
            'status.required'             => 'Le statut juridique est obligatoire.',
            'activity_types.required'     => "Au moins un type d'activité est requis.",
            'phone.required'              => 'Le téléphone est obligatoire.',
            'sites.*.label.required'      => "L'intitulé du site est obligatoire.",
            'sites.*.commune_id.required' => 'La commune du site est obligatoire.',
        ]);

        DB::transaction(function () use ($referenceService) {
            $company = Company::create([
                'reference'             => $referenceService->generateCompanyReference(),
                'name'                  => $this->name,
                'status'                => $this->status,
                'legal_rep_first_name'  => $this->legal_rep_first_name ?: null,
                'legal_rep_last_name'   => $this->legal_rep_last_name ?: null,
                'activity_types'        => $this->activity_types,
                'description'           => $this->description ?: null,
                'phone'                 => $this->phone,
                'email'                 => $this->email ?: null,
                'website'               => $this->website ?: null,
                'social_links'          => array_filter([
                    'facebook' => $this->social_facebook ?: null,
                    'linkedin' => $this->social_linkedin ?: null,
                    'whatsapp' => $this->social_whatsapp ?: null,
                ]) ?: null,
                'need_training'         => $this->need_training,
                'need_financing'        => $this->need_financing,
                'need_contract_support' => $this->need_contract_support,
                'operator_notes'        => $this->operator_notes ?: null,
                'created_by'            => Auth::id(),
            ]);

            foreach ($this->sites as $site) {
                $company->sites()->create([
                    'label'      => $site['label'],
                    'commune_id' => $site['commune_id'],
                    'address'    => $site['address'] ?? null,
                    'is_main'    => $site['is_main'] ?? false,
                ]);
            }

            foreach ($this->offers as $offerData) {
                if (empty($offerData['contract_type']) || empty($offerData['title'])) {
                    continue;
                }
                $offer = $company->offers()->create([
                    'reference'           => $referenceService->generateOfferReference(),
                    'title'               => $offerData['title'],
                    'contract_type'       => $offerData['contract_type'],
                    'duration'            => $offerData['duration'] ?: null,
                    'economic_conditions' => $offerData['economic_conditions'] ?: null,
                    'mission_description' => $offerData['mission_description'] ?: '',
                    'locations'           => $offerData['location_text']
                        ? [['address' => $offerData['location_text']]]
                        : [],
                    'status'              => OfferStatus::Brouillon,
                    'positions_count'     => 1,
                    'created_by'          => Auth::id(),
                ]);
                if (!empty($offerData['skill_ids'])) {
                    $offer->skills()->sync($offerData['skill_ids']);
                }
                activity()->causedBy(Auth::user())->performedOn($offer)->log('offer_created');
            }

            activity()->causedBy(Auth::user())->performedOn($company)->log('company_created');
        });

        session()->flash('success', 'Entreprise créée avec succès.');
        $this->redirect(route('admin.companies.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.companies.company-create', [
            'statuses'          => CompanyStatus::options(),
            'communes'          => ReferentialCommune::active()->get(),
            'skills'            => ReferentialSkill::active()->get(),
            'contractTypes'     => ContractType::cases(),
            'activityTypesList' => [
                'Maraîchage', 'Élevage', 'Agroforesterie', 'Pisciculture',
                'Transformation agro-alimentaire', 'Bio-pesticides', 'Irrigation',
                'Énergies renouvelables', 'Formation agricole', 'Conseil agricole', 'Autre',
            ],
        ]);
    }
}
