<?php

namespace App\Http\Requests\Contact;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreContactMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'full_name'    => ['required', 'string', 'min:2', 'max:100'],
            'email'        => ['required', 'email', 'max:150'],
            'phone'        => ['nullable', 'string', 'max:30'],
            'profile'      => ['required', Rule::in(['jeune', 'entreprise', 'ong', 'autre'])],
            'subject'      => ['required', 'string', 'min:5', 'max:150'],
            'message'      => ['required', 'string', 'min:20', 'max:2000'],
            'rgpd_consent' => ['required', 'accepted'],
            'honeypot'     => ['nullable', 'max:0'], // Anti-spam honeypot
        ];
    }

    public function messages(): array
    {
        return [
            'full_name.required'    => 'Le nom complet est obligatoire.',
            'email.required'        => "L'adresse e-mail est obligatoire.",
            'email.email'           => "L'adresse e-mail est invalide.",
            'profile.required'      => 'Veuillez sélectionner votre profil.',
            'subject.required'      => "L'objet est obligatoire.",
            'message.required'      => 'Le message est obligatoire.',
            'message.min'           => 'Le message doit contenir au moins 20 caractères.',
            'rgpd_consent.required' => 'Vous devez accepter la politique de confidentialité.',
            'rgpd_consent.accepted' => 'Vous devez accepter la politique de confidentialité.',
            'honeypot.max'          => 'Envoi refusé.',
        ];
    }
}
