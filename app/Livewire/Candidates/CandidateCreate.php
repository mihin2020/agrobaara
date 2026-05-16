<?php

namespace App\Livewire\Candidates;

use App\Enums\Gender;
use App\Enums\TransportMode;
use App\Models\Candidate;
use App\Models\ReferentialCommune;
use App\Models\ReferentialEducationLevel;
use App\Models\ReferentialLanguage;
use App\Models\ReferentialLicense;
use App\Models\ReferentialNationality;
use App\Models\ReferentialSkill;
use App\Services\ReferenceService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Nouveau candidat — Agro Eco BAARA')]
class CandidateCreate extends Component
{
    // Section A — Identité
    public string $first_name    = '';
    public string $last_name     = '';
    public string $gender        = '';
    public string $marital_status = '';
    public string $birth_date    = '';
    public string $birth_place   = '';
    public string $nationality         = 'Burkinabè';
    public bool   $showNationalityModal = false;
    public string $newNationality       = '';
    public string $commune_id    = '';
    public string $address       = '';
    public string $transport_mode = '';
    public array  $license_ids   = [];

    // Section B — Contacts
    public string $phone           = '';
    public string $phone_secondary = '';
    public string $email           = '';
    public array  $language_ids    = [];

    // Section C — Formation
    public string $education_level     = '';
    public string $agro_training_text  = '';
    public string $agro_training_place = '';

    // Section D — Expériences
    public array  $skill_ids         = [];
    public string $other_skills_text = '';
    public bool   $has_previous_jobs = false;
    public array  $experiences       = [];

    // Section E — Besoins internes
    public array  $need_employment_types = [];
    public array  $need_formation_types  = [];
    public bool   $need_financing        = false;
    public bool   $need_cv_support       = false;
    public string $operator_notes        = '';

    // Navigation par section
    public int $currentSection = 1;
    public int $totalSections  = 5;

    public function mount(): void
    {
        $this->authorize('create', Candidate::class);
    }

    public function openNationalityModal(): void
    {
        $this->newNationality = '';
        $this->resetValidation('newNationality');
        $this->showNationalityModal = true;
    }

    public function closeNationalityModal(): void
    {
        $this->showNationalityModal = false;
        $this->resetValidation('newNationality');
    }

    public function saveNationality(): void
    {
        $this->validateOnly('newNationality', [
            'newNationality' => 'required|string|min:2|max:100|unique:referentials_nationalities,name',
        ], [
            'newNationality.required' => 'Le nom est obligatoire.',
            'newNationality.min'      => 'Minimum 2 caractères.',
            'newNationality.unique'   => 'Cette nationalité existe déjà.',
        ]);

        ReferentialNationality::create(['name' => trim($this->newNationality), 'is_active' => true]);
        $this->nationality = trim($this->newNationality);
        $this->showNationalityModal = false;
        $this->newNationality = '';
    }

    public function nextSection(): void
    {
        $this->validateCurrentSection();
        if ($this->currentSection < $this->totalSections) {
            $this->currentSection++;
        }
    }

    public function prevSection(): void
    {
        if ($this->currentSection > 1) {
            $this->currentSection--;
        }
    }

    public function goToSection(int $section): void
    {
        if ($section < $this->currentSection) {
            $this->currentSection = $section;
        }
    }

    public function addExperience(): void
    {
        $this->experiences[] = [
            'year'               => '',
            'location'           => '',
            'position'           => '',
            'employer_contacts'  => '',
        ];
    }

    public function removeExperience(int $index): void
    {
        unset($this->experiences[$index]);
        $this->experiences = array_values($this->experiences);
    }

    public function save(ReferenceService $referenceService): void
    {
        $this->validateAll();

        DB::transaction(function () use ($referenceService) {
            $candidate = Candidate::create([
                'reference'           => $referenceService->generateCandidateReference(),
                'first_name'          => $this->first_name,
                'last_name'           => $this->last_name,
                'gender'              => $this->gender,
                'marital_status'      => $this->marital_status ?: null,
                'birth_date'          => $this->birth_date,
                'birth_place'         => $this->birth_place ?: null,
                'nationality'         => $this->nationality,
                'commune_id'          => $this->commune_id,
                'address'             => $this->address ?: null,
                'transport_mode'      => $this->transport_mode ?: null,
                'phone'               => $this->phone,
                'phone_secondary'     => $this->phone_secondary ?: null,
                'email'               => $this->email ?: null,
                'education_level'     => $this->education_level,
                'agro_training_text'  => $this->agro_training_text ?: null,
                'agro_training_place' => $this->agro_training_place ?: null,
                'other_skills_text'   => $this->other_skills_text ?: null,
                'has_previous_jobs'      => $this->has_previous_jobs,
                'need_employment_types'  => $this->need_employment_types ?: null,
                'need_formation_types'   => $this->need_formation_types ?: null,
                'need_training'          => !empty($this->need_formation_types),
                'need_financing'         => $this->need_financing,
                'need_cv_support'        => $this->need_cv_support,
                'operator_notes'         => $this->operator_notes ?: null,
                'created_by'          => Auth::id(),
            ]);

            // Pivots
            if ($this->language_ids) {
                $candidate->languages()->sync($this->language_ids);
            }
            if ($this->license_ids) {
                $candidate->licenses()->sync($this->license_ids);
            }
            if ($this->skill_ids) {
                $candidate->skills()->sync($this->skill_ids);
            }

            // Expériences
            foreach ($this->experiences as $exp) {
                if (!empty($exp['position']) || !empty($exp['location'])) {
                    $candidate->experiences()->create([
                        'year'              => $exp['year'] ?: null,
                        'location'          => $exp['location'] ?: null,
                        'position'          => $exp['position'] ?: null,
                        'employer_contacts' => $exp['employer_contacts'] ?: null,
                    ]);
                }
            }

            activity()
                ->causedBy(Auth::user())
                ->performedOn($candidate)
                ->withProperties(['reference' => $candidate->reference])
                ->log('candidate_created');
        });

        session()->flash('success', 'Candidat enregistré avec succès.');
        $this->redirect(route('admin.candidates.index'), navigate: true);
    }

    private function validateCurrentSection(): void
    {
        match($this->currentSection) {
            1 => $this->validate($this->sectionARules(), $this->validationMessages()),
            2 => $this->validate($this->sectionBRules(), $this->validationMessages()),
            3 => $this->validate($this->sectionCRules(), $this->validationMessages()),
            4, 5 => null,
            default => null,
        };
    }

    private function validateAll(): void
    {
        $this->validate(array_merge(
            $this->sectionARules(),
            $this->sectionBRules(),
            $this->sectionCRules(),
        ), $this->validationMessages());
    }

    private function sectionARules(): array
    {
        return [
            'first_name'  => 'required|string|min:2|max:100',
            'last_name'   => 'required|string|min:2|max:100',
            'gender'      => 'required|in:M,F',
            'birth_date'  => 'required|date|before:today',
            'commune_id'  => 'required|exists:referentials_communes,id',
            'nationality' => 'required|string|max:100',
        ];
    }

    private function sectionBRules(): array
    {
        return [
            'phone'        => 'required|string|max:30',
            'language_ids' => 'required|array|min:1',
            'email'        => 'nullable|email|max:150',
        ];
    }

    private function sectionCRules(): array
    {
        return [
            'education_level' => 'required|exists:referentials_education_levels,code',
        ];
    }

    private function validationMessages(): array
    {
        return [
            'first_name.required'      => 'Le prénom est obligatoire.',
            'first_name.min'           => 'Le prénom doit contenir au moins 2 caractères.',
            'last_name.required'       => 'Le nom est obligatoire.',
            'last_name.min'            => 'Le nom doit contenir au moins 2 caractères.',
            'gender.required'          => 'Le sexe est obligatoire.',
            'gender.in'                => 'Le sexe sélectionné est invalide.',
            'birth_date.required'      => 'La date de naissance est obligatoire.',
            'birth_date.date'          => 'La date de naissance est invalide.',
            'birth_date.before'        => 'La date de naissance doit être dans le passé.',
            'commune_id.required'      => 'La commune de résidence est obligatoire.',
            'commune_id.exists'        => 'La commune sélectionnée est invalide.',
            'nationality.required'     => 'La nationalité est obligatoire.',
            'phone.required'           => 'Le téléphone principal est obligatoire.',
            'language_ids.required'    => 'Au moins une langue parlée est requise.',
            'language_ids.min'         => 'Au moins une langue parlée est requise.',
            'education_level.required' => "Le niveau d'étude est obligatoire.",
            'education_level.in'       => "Le niveau d'étude sélectionné est invalide.",
        ];
    }

    public function render()
    {
        return view('livewire.candidates.candidate-create', [
            'communes'      => ReferentialCommune::active()->get(),
            'languages'     => ReferentialLanguage::active()->get(),
            'licenses'      => ReferentialLicense::active()->get(),
            'skills'        => ReferentialSkill::active()->get(),
            'genders'       => Gender::cases(),
            'transports'    => TransportMode::cases(),
            'educations'    => ReferentialEducationLevel::active()->get(),
            'nationalities' => ReferentialNationality::active()->get(),
        ]);
    }
}
