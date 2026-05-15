<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.auth')]
#[Title('Mot de passe oublié — Agro Eco BAARA')]
class ForgotPassword extends Component
{
    #[Rule('required|email', message: "L'e-mail est obligatoire et doit être valide.")]
    public string $email = '';

    public bool $sent = false;

    public function sendResetLink(): void
    {
        $this->validate();

        // Réponse uniforme pour éviter l'énumération de comptes
        $status = Password::sendResetLink(['email' => $this->email]);

        $this->sent = true;
        $this->email = '';
    }

    public function render()
    {
        return view('livewire.auth.forgot-password');
    }
}
