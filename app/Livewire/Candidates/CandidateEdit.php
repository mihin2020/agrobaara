<?php

namespace App\Livewire\Candidates;

use App\Enums\Gender;
use App\Enums\TransportMode;
use App\Models\Candidate;
use App\Models\ReferentialNationality;
use App\Services\SecureUploadService;
use App\Support\ReferentialCache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.app')]
#[Title('Modifier candidat - Agro Eco BAARA')]
class CandidateEdit extends Component
{
    use WithFileUploads;

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
    // Fichiers (null = pas de changement, fichier = nouveau upload)
    public $photo             = null;
    public $identity_document = null;
    public array $diploma_files = [];
    public $cv_file           = null;
    public array  $need_employment_types = [];
    public array  $need_formation_types  = [];
    public bool   $need_financing        = false;
    public bool   $need_cv_support       = false;
    public string $salary_min            = '';
    public string $salary_max            = '';
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
        $this->salary_min            = $candidate->salary_min !== null ? (string) $candidate->salary_min : '';
        $this->salary_max            = $candidate->salary_max !== null ? (string) $candidate->salary_max : '';
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

    public function save(SecureUploadService $uploads): void
    {
        $maxBirthDate = now()->subYears(16)->format('Y-m-d');

        $this->validate([
            'first_name'       => 'required|string|min:2|max:100',
            'last_name'        => 'required|string|min:2|max:100',
            'gender'           => 'required|in:M,F',
            'birth_date'       => "required|date|before_or_equal:{$maxBirthDate}",
            'commune_id'       => 'required|uuid|exists:referentials_communes,id',
            'nationality'      => 'required|string|max:100',
            'phone'            => 'required|string|max:30',
            'language_ids'     => 'required|array|min:1',
            'education_level'  => 'required|exists:referentials_education_levels,code',
            'photo'            => 'nullable|image|max:2048',
            'identity_document'=> 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'diploma_files.*'  => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240',
            'cv_file'          => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'salary_min' => 'nullable|integer|min:0',
            'salary_max' => array_filter([
                'nullable',
                'integer',
                'min:0',
                $this->salary_min !== '' ? 'gte:salary_min' : null,
            ]),
        ], [
            'first_name.required'        => 'Le prénom est obligatoire.',
            'last_name.required'         => 'Le nom est obligatoire.',
            'gender.required'            => 'Le sexe est obligatoire.',
            'birth_date.required'        => 'La date de naissance est obligatoire.',
            'birth_date.before_or_equal' => 'Le candidat doit avoir au moins 16 ans.',
            'commune_id.required'        => 'La ville est obligatoire.',
            'phone.required'             => 'Le téléphone est obligatoire.',
            'language_ids.required'      => 'Au moins une langue est requise.',
            'education_level.required'   => "Le niveau d'étude est obligatoire.",
            'salary_max.gte'             => 'Le maximum doit être supérieur ou égal au minimum.',
        ]);

        DB::transaction(function () {
            $updates = [
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
                'salary_min'             => $this->salary_min !== '' ? (int) $this->salary_min : null,
                'salary_max'             => $this->salary_max !== '' ? (int) $this->salary_max : null,
                'salary_expectation'     => Candidate::buildSalaryExpectationLabel(
                    $this->salary_min !== '' ? (int) $this->salary_min : null,
                    $this->salary_max !== '' ? (int) $this->salary_max : null
                ),
                'operator_notes'         => $this->operator_notes ?: null,
                'updated_by'             => Auth::id(),
            ];

            // Fichiers — photo public ; docs sensibles private + UUID
            if ($this->photo) {
                $uploads->delete($this->candidate->photo_path, SecureUploadService::DISK_PUBLIC);
                $updates['photo_path'] = $uploads->storePublic($this->photo, 'candidates/photos');
            }
            if ($this->identity_document) {
                $uploads->delete($this->candidate->identity_document_path);
                // Ancien éventuellement encore sur public
                $uploads->delete($this->candidate->identity_document_path, SecureUploadService::DISK_PUBLIC);
                $updates['identity_document_path'] = $uploads->storePrivate($this->identity_document, 'candidates/documents');
            }
            if (! empty($this->diploma_files)) {
                $uploads->deleteMany($this->candidate->diploma_paths);
                $uploads->deleteMany($this->candidate->diploma_paths, SecureUploadService::DISK_PUBLIC);
                $updates['diploma_paths'] = collect($this->diploma_files)
                    ->map(fn ($f) => $uploads->storePrivate($f, 'candidates/diplomas'))
                    ->toArray();
            }
            if ($this->cv_file) {
                $uploads->delete($this->candidate->cv_path);
                $uploads->delete($this->candidate->cv_path, SecureUploadService::DISK_PUBLIC);
                $updates['cv_path'] = $uploads->storePrivate($this->cv_file, 'candidates/cvs');
            }

            $this->candidate->update($updates);

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
            'communes'        => ReferentialCache::communes(),
            'languages'       => ReferentialCache::languages(),
            'licenses'        => ReferentialCache::licenses(),
            'skills'          => ReferentialCache::skills(),
            'genders'         => Gender::cases(),
            'transports'      => TransportMode::cases(),
            'educations'      => ReferentialCache::educations(),
            'nationalities'   => ReferentialCache::nationalities(),
            'employmentTypes' => ReferentialCache::employmentTypes(),
            'formationTypes'  => ReferentialCache::formationTypes(),
        ]);
    }
}
