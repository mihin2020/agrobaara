<?php

namespace App\Http\Requests\Companies;

use App\Enums\CompanyStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasPermission('companies.create');
    }

    public function rules(): array
    {
        return [
            'name'                   => ['required', 'string', 'min:2', 'max:200'],
            'status'                 => ['required', Rule::in(array_column(CompanyStatus::cases(), 'value'))],
            'activity_types'         => ['required', 'array', 'min:1'],
            'phone'                  => ['required', 'string', 'max:30'],
            'legal_rep_first_name'   => ['nullable', 'string', 'max:100'],
            'legal_rep_last_name'    => ['nullable', 'string', 'max:100'],
            'description'            => ['nullable', 'string'],
            'email'                  => ['nullable', 'email', 'max:150'],
            'website'                => ['nullable', 'url', 'max:255'],
            'social_links'           => ['nullable', 'array'],
            'social_links.*.type'    => ['nullable', 'string'],
            'social_links.*.url'     => ['nullable', 'url'],

            // Sites d'activité (au moins un requis)
            'sites'                  => ['required', 'array', 'min:1'],
            'sites.*.label'          => ['required', 'string', 'max:150'],
            'sites.*.commune_id'     => ['required', 'uuid', 'exists:referentials_communes,id'],
            'sites.*.address'        => ['nullable', 'string', 'max:255'],
            'sites.*.gps_coordinates'=> ['nullable', 'string', 'max:100'],
            'sites.*.is_main'        => ['boolean'],

            // Besoins
            'need_training'          => ['boolean'],
            'need_financing'         => ['boolean'],
            'need_contract_support'  => ['boolean'],
            'operator_notes'         => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'             => "Le nom de l'entreprise est obligatoire.",
            'status.required'           => 'Le statut juridique est obligatoire.',
            'activity_types.required'   => "Au moins un type d'activité est requis.",
            'phone.required'            => 'Le téléphone est obligatoire.',
            'sites.required'            => "Au moins un site d'activité est requis.",
            'sites.*.label.required'    => "L'intitulé du site est obligatoire.",
            'sites.*.commune_id.required' => 'La commune du site est obligatoire.',
        ];
    }
}
