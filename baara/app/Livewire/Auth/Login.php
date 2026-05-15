<?php

namespace App\Livewire\Auth;

use App\Services\AuthService;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.auth')]
#[Title('Connexion — Agro Eco BAARA')]
class Login extends Component
{
    #[Rule('required|email', message: "L'e-mail est obligatoire.")]
    public string $email = '';

    #[Rule('required', message: 'Le mot de passe est obligatoire.')]
    public string $password = '';

    public bool $remember = false;

    public function authenticate(AuthService $authService): void
    {
        $this->validate();

        $throttleKey = Str::lower($this->email) . '|' . request()->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $this->addError('email', "Trop de tentatives. Réessayez dans {$seconds} secondes.");
            return;
        }

        $result = $authService->attempt(
            email: $this->email,
            password: $this->password,
            ip: request()->ip(),
            remember: $this->remember
        );

        if (!$result->success) {
            RateLimiter::hit($throttleKey, 600);
            $this->addError('email', $result->message);
            $this->password = '';
            return;
        }

        RateLimiter::clear($throttleKey);

        $user = $result->user;

        if ($user->needsPasswordChange()) {
            $this->redirect(route('password.change'), navigate: true);
            return;
        }

        $this->redirect(route('admin.dashboard'), navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
