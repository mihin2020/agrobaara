<?php

namespace App\Http\Requests\Offers;

use App\Enums\ContractType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOfferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasPermission('offers.create');
    }

    public function rules(): array
    {
        return [
            'company_id'          => ['required', 'uuid', 'exists:companies,id'],
            'title'               => ['required', 'string', 'min:5', 'max:200'],
            'contract_type'       => ['required', Rule::in(array_column(ContractType::cases(), 'value'))],
            'mission_description' => ['required', 'string', 'min:20'],
            'skill_ids'           => ['required', 'array', 'min:1'],
            'skill_ids.*'         => ['uuid', 'exists:referentials_skills,id'],
            'locations'           => ['required', 'array', 'min:1'],
            'locations.*.commune_id' => ['required', 'uuid', 'exists:referentials_communes,id'],
            'locations.*.address' => ['nullable', 'string', 'max:255'],

            'duration'            => ['nullable', 'string', 'max:100'],
            'economic_conditions' => ['nullable', 'string'],
            'other_requirements'  => ['nullable', 'string'],
            'start_date'          => ['nullable', 'date', 'after_or_equal:today'],
            'positions_count'     => ['nullable', 'integer', 'min:1', 'max:999'],
        ];
    }

    public function messages(): array
    {
        return [
            'company_id.required'          => "L'entreprise est obligatoire.",
            'company_id.exists'            => "L'entreprise sélectionnée est invalide.",
            'title.required'               => "L'intitulé du poste est obligatoire.",
            'title.min'                    => "L'intitulé doit contenir au moins 5 caractères.",
            'contract_type.required'       => 'Le type de contrat est obligatoire.',
            'mission_description.required' => 'La description des missions est obligatoire.',
            'mission_description.min'      => 'La description doit contenir au moins 20 caractères.',
            'skill_ids.required'           => 'Au moins une compétence est requise.',
            'locations.required'           => "Au moins un lieu d'activité est requis.",
        ];
    }
}
