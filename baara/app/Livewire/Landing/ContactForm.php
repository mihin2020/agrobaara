<?php

namespace App\Livewire\Landing;

use App\Models\ContactMessage;
use Livewire\Attributes\Rule;
use Livewire\Component;

class ContactForm extends Component
{
    public string $full_name    = '';
    public string $email        = '';
    public string $phone        = '';
    public string $message      = '';
    public bool   $rgpd_consent = false;
    public string $honeypot     = '';
    public bool   $submitted    = false;

    public function submit(): void
    {
        if ($this->honeypot !== '') {
            return;
        }

        $this->validate([
            'full_name'    => 'required|string|min:2|max:100',
            'email'        => 'required|email|max:150',
            'phone'        => 'nullable|string|max:30',
            'message'      => 'required|string|min:20|max:2000',
            'rgpd_consent' => 'required|accepted',
        ], [
            'full_name.required'    => 'Le nom complet est obligatoire.',
            'email.required'        => "L'e-mail est obligatoire.",
            'message.required'      => 'Le message est obligatoire.',
            'message.min'           => 'Le message doit contenir au moins 20 caractères.',
            'rgpd_consent.accepted' => 'Vous devez accepter la politique de confidentialité.',
        ]);

        ContactMessage::create([
            'full_name'    => $this->full_name,
            'email'        => $this->email,
            'phone'        => $this->phone ?: null,
            'message'      => $this->message,
            'rgpd_consent' => $this->rgpd_consent,
            'ip_address'   => request()->ip(),
        ]);

        $this->reset(['full_name', 'email', 'phone', 'message', 'rgpd_consent']);
        $this->submitted = true;
    }

    public function render()
    {
        return view('livewire.landing.contact-form');
    }
}
