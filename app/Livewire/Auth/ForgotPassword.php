<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.auth')]
#[Title('Mot de passe oublié - Agro Eco BAARA')]
class ForgotPassword extends Component
{
    #[Rule('required|email', message: "L'e-mail est obligatoire et doit être valide.")]
    public string $email = '';

    public bool $sent = false;

    public function sendResetLink(): void
    {
        $key = 'password-reset:'.request()->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $this->addError('email', 'Trop de demandes. Réessayez dans '.RateLimiter::availableIn($key).' secondes.');
            return;
        }
        RateLimiter::hit($key, 600);

        $this->validate();

        // Réponse uniforme pour éviter l'énumération de comptes
        Password::sendResetLink(['email' => $this->email]);

        $this->sent = true;
        $this->email = '';
    }

    public function render()
    {
        return view('livewire.auth.forgot-password');
    }
}
