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
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Modifier entreprise — Agro Eco BAARA')]
class CompanyEdit extends Component
{
    #[Locked]
    public string $companyId = '';

    public Company $company;

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
    public array  $sites                = [];

    public function mount(Company $company): void
    {
        $this->authorize('update', $company);

        $company->loadMissing('sites');
        $this->company   = $company;
        $this->companyId = $company->id;

        $this->name                 = $company->name;
        $this->status               = $company->status->value;
        $this->legal_rep_first_name = $company->legal_rep_first_name ?? '';
        $this->legal_rep_last_name  = $company->legal_rep_last_name ?? '';
        $this->activity_types       = $company->activity_types ?? [];
        $this->description          = $company->description ?? '';
        $this->phone                = $company->phone;
        $this->email                = $company->email ?? '';
        $this->website              = $company->website ?? '';
        $this->social_facebook      = $company->social_links['facebook'] ?? '';
        $this->social_linkedin      = $company->social_links['linkedin'] ?? '';
        $this->social_whatsapp      = $company->social_links['whatsapp'] ?? '';
        $this->need_training        = $company->need_training;
        $this->need_financing       = $company->need_financing;
        $this->need_contract_support = $company->need_contract_support;
        $this->operator_notes       = $company->operator_notes ?? '';
        $this->sites                = $company->sites->map(fn($s) => [
            'label'      => $s->label,
            'commune_id' => $s->commune_id ?? '',
            'address'    => $s->address ?? '',
            'is_main'    => $s->is_main,
        ])->toArray();

        if (empty($this->sites)) {
            $this->sites = [['label' => '', 'commune_id' => '', 'address' => '', 'is_main' => true]];
        }
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

    public function save(): void
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

        DB::transaction(function () {
            $this->company->update([
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
                'updated_by'            => Auth::id(),
            ]);

            $this->company->sites()->delete();
            foreach ($this->sites as $site) {
                $this->company->sites()->create([
                    'label'      => $site['label'],
                    'commune_id' => $site['commune_id'],
                    'address'    => $site['address'] ?? null,
                    'is_main'    => $site['is_main'] ?? false,
                ]);
            }

            activity()
                ->causedBy(Auth::user())
                ->performedOn($this->company)
                ->log('company_updated');
        });

        session()->flash('success', 'Entreprise mise à jour avec succès.');
        $this->redirect(route('admin.companies.show', $this->company), navigate: true);
    }

    public function render()
    {
        return view('livewire.companies.company-edit', [
            'statuses'          => CompanyStatus::options(),
            'communes'          => ReferentialCommune::active()->get(),
            'activityTypesList' => [
                'Maraîchage', 'Élevage', 'Agroforesterie', 'Pisciculture',
                'Transformation agro-alimentaire', 'Bio-pesticides', 'Irrigation',
                'Énergies renouvelables', 'Formation agricole', 'Conseil agricole', 'Autre',
            ],
        ]);
    }
}
