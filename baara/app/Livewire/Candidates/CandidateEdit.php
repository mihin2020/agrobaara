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
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Modifier candidat — Agro Eco BAARA')]
class CandidateEdit extends Component
{
    #[Locked]
    public string $candidateId = '';

    public Candidate $candidate;

    // Mêmes propriétés que CandidateCreate
    public string $first_name = '';
    public string $last_name = '';
    public string $gender = '';
    public string $marital_status = '';
    public string $birth_date = '';
    public string $birth_place = '';
    public string $nationality          = '';
    public bool   $showNationalityModal = false;
    public string $newNationality       = '';
    public string $commune_id           = '';
    public string $address = '';
    public string $transport_mode = '';
    public array  $license_ids = [];
    public string $phone = '';
    public string $phone_secondary = '';
    public string $email = '';
    public array  $language_ids = [];
    public string $education_level = '';
    public string $agro_training_text = '';
    public string $agro_training_place = '';
    public array  $skill_ids = [];
    public string $other_skills_text = '';
    public bool   $has_previous_jobs = false;
    public array  $experiences = [];
    public array  $need_employment_types = [];
    public array  $need_formation_types  = [];
    public bool   $need_financing        = false;
    public bool   $need_cv_support       = false;
    public string $operator_notes        = '';
    public int    $currentSection = 1;
    public int    $totalSections = 5;

    public function mount(Candidate $candidate): void
    {
        $this->authorize('update', $candidate);

        $candidate->loadMissing('languages', 'licenses', 'skills', 'experiences');
        $this->candidate   = $candidate;
        $this->candidateId = $candidate->id;

        // Hydratation des propriétés
        $this->first_name          = $candidate->first_name;
        $this->last_name           = $candidate->last_name;
        $this->gender              = $candidate->gender->value;
        $this->marital_status      = $candidate->marital_status ?? '';
        $this->birth_date          = $candidate->birth_date->format('Y-m-d');
        $this->birth_place         = $candidate->birth_place ?? '';
        $this->nationality         = $candidate->nationality;
        $this->commune_id          = $candidate->commune_id ?? '';
        $this->address             = $candidate->address ?? '';
        $this->transport_mode      = $candidate->transport_mode?->value ?? '';
        $this->license_ids         = $candidate->licenses->pluck('id')->toArray();
        $this->phone               = $candidate->phone;
        $this->phone_secondary     = $candidate->phone_secondary ?? '';
        $this->email               = $candidate->email ?? '';
        $this->language_ids        = $candidate->languages->pluck('id')->toArray();
        $this->education_level     = $candidate->education_level ?? '';
        $this->agro_training_text  = $candidate->agro_training_text ?? '';
        $this->agro_training_place = $candidate->agro_training_place ?? '';
        $this->skill_ids           = $candidate->skills->pluck('id')->toArray();
        $this->other_skills_text   = $candidate->other_skills_text ?? '';
        $this->has_previous_jobs   = $candidate->has_previous_jobs;
        $this->need_employment_types = $candidate->need_employment_types ?? [];
        $this->need_formation_types  = $candidate->need_formation_types ?? [];
        $this->need_financing        = $candidate->need_financing;
        $this->need_cv_support       = $candidate->need_cv_support;
        $this->operator_notes        = $candidate->operator_notes ?? '';
        $this->experiences         = $candidate->experiences->map(fn($e) => [
            'year'              => $e->year ?? '',
            'location'          => $e->location ?? '',
            'position'          => $e->position ?? '',
            'employer_contacts' => $e->employer_contacts ?? '',
        ])->toArray();
    }

    public function nextSection(): void
    {
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

    public function addExperience(): void
    {
        $this->experiences[] = ['year' => '', 'location' => '', 'position' => '', 'employer_contacts' => ''];
    }

    public function removeExperience(int $index): void
    {
        unset($this->experiences[$index]);
        $this->experiences = array_values($this->experiences);
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

    public function save(): void
    {
        $this->validate([
            'first_name'      => 'required|string|min:2|max:100',
            'last_name'       => 'required|string|min:2|max:100',
            'gender'          => 'required|in:M,F',
            'birth_date'      => 'required|date|before:today',
            'commune_id'      => 'required|uuid|exists:referentials_communes,id',
            'nationality'     => 'required|string|max:100',
            'phone'           => 'required|string|max:30',
            'language_ids'    => 'required|array|min:1',
            'education_level' => 'required|exists:referentials_education_levels,code',
        ], [
            'first_name.required'    => 'Le prénom est obligatoire.',
            'last_name.required'     => 'Le nom est obligatoire.',
            'gender.required'        => 'Le sexe est obligatoire.',
            'birth_date.required'    => 'La date de naissance est obligatoire.',
            'commune_id.required'    => 'La commune est obligatoire.',
            'phone.required'         => 'Le téléphone est obligatoire.',
            'language_ids.required'  => 'Au moins une langue est requise.',
            'education_level.required' => "Le niveau d'étude est obligatoire.",
        ]);

        DB::transaction(function () {
            $this->candidate->update([
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
                'updated_by'          => Auth::id(),
            ]);

            $this->candidate->languages()->sync($this->language_ids);
            $this->candidate->licenses()->sync($this->license_ids);
            $this->candidate->skills()->sync($this->skill_ids);

            // Recréer les expériences
            $this->candidate->experiences()->delete();
            foreach ($this->experiences as $exp) {
                if (!empty($exp['position']) || !empty($exp['location'])) {
                    $this->candidate->experiences()->create([
                        'year'              => $exp['year'] ?: null,
                        'location'          => $exp['location'] ?: null,
                        'position'          => $exp['position'] ?: null,
                        'employer_contacts' => $exp['employer_contacts'] ?: null,
                    ]);
                }
            }

            activity()
                ->causedBy(Auth::user())
                ->performedOn($this->candidate)
                ->log('candidate_updated');
        });

        session()->flash('success', 'Candidat mis à jour avec succès.');
        $this->redirect(route('admin.candidates.show', $this->candidate), navigate: true);
    }

    public function render()
    {
        return view('livewire.candidates.candidate-edit', [
            'communes'   => ReferentialCommune::active()->get(),
            'languages'  => ReferentialLanguage::active()->get(),
            'licenses'   => ReferentialLicense::active()->get(),
            'skills'     => ReferentialSkill::active()->get(),
            'genders'    => Gender::cases(),
            'transports' => TransportMode::cases(),
            'educations'    => ReferentialEducationLevel::active()->get(),
            'nationalities' => ReferentialNationality::active()->get(),
        ]);
    }
}
