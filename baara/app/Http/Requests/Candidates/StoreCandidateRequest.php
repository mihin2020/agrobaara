<?php

namespace App\Http\Requests\Candidates;

use App\Enums\EducationLevel;
use App\Enums\Gender;
use App\Enums\TransportMode;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCandidateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasPermission('candidates.create');
    }

    public function rules(): array
    {
        return [
            // Section A — Obligatoire
            'first_name'      => ['required', 'string', 'min:2', 'max:100'],
            'last_name'       => ['required', 'string', 'min:2', 'max:100'],
            'gender'          => ['required', Rule::in(array_column(Gender::cases(), 'value'))],
            'birth_date'      => ['required', 'date', 'before:today', 'after:' . now()->subYears(80)->toDateString()],
            'commune_id'      => ['required', 'uuid', 'exists:referentials_communes,id'],
            'language_ids'    => ['required', 'array', 'min:1'],
            'language_ids.*'  => ['uuid', 'exists:referentials_languages,id'],

            // Section A — Optionnel
            'marital_status'  => ['nullable', Rule::in(['celibataire', 'marie', 'veuf'])],
            'birth_place'     => ['nullable', 'string', 'max:150'],
            'nationality'     => ['nullable', 'string', 'max:80'],
            'address'         => ['nullable', 'string', 'max:255'],
            'transport_mode'  => ['nullable', Rule::in(array_column(TransportMode::cases(), 'value'))],
            'license_ids'     => ['nullable', 'array'],
            'license_ids.*'   => ['uuid', 'exists:referentials_licenses,id'],

            // Section B — Contacts
            'phone'           => ['required', 'string', 'max:30'],
            'phone_secondary' => ['nullable', 'string', 'max:30'],
            'email'           => ['nullable', 'email', 'max:150'],

            // Section C — Formation
            'education_level'      => ['required', Rule::in(array_column(EducationLevel::cases(), 'value'))],
            'agro_training_text'   => ['nullable', 'string'],
            'agro_training_place'  => ['nullable', 'string', 'max:200'],

            // Section D — Expériences
            'skill_ids'            => ['nullable', 'array'],
            'skill_ids.*'          => ['uuid', 'exists:referentials_skills,id'],
            'other_skills_text'    => ['nullable', 'string'],
            'has_previous_jobs'    => ['boolean'],
            'experiences'          => ['nullable', 'array'],
            'experiences.*.year'   => ['nullable', 'integer', 'min:1990', 'max:' . now()->year],
            'experiences.*.location'          => ['nullable', 'string', 'max:200'],
            'experiences.*.position'          => ['nullable', 'string', 'max:200'],
            'experiences.*.employer_contacts' => ['nullable', 'string'],

            // Section E — Besoins (interne)
            'need_types'      => ['nullable', 'array'],
            'need_training'   => ['boolean'],
            'need_financing'  => ['boolean'],
            'need_cv_support' => ['boolean'],
            'operator_notes'  => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required'   => 'Le prénom est obligatoire.',
            'last_name.required'    => 'Le nom est obligatoire.',
            'gender.required'       => 'Le sexe est obligatoire.',
            'birth_date.required'   => 'La date de naissance est obligatoire.',
            'birth_date.before'     => 'La date de naissance doit être dans le passé.',
            'commune_id.required'   => 'La commune de résidence est obligatoire.',
            'commune_id.exists'     => 'La commune sélectionnée est invalide.',
            'language_ids.required' => 'Au moins une langue est requise.',
            'phone.required'        => 'Le téléphone principal est obligatoire.',
            'education_level.required' => "Le niveau d'étude est obligatoire.",
        ];
    }
}
