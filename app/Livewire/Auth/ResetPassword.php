<?php

namespace App\Livewire\Auth;

use App\Services\AuthService;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.auth')]
#[Title('Réinitialiser le mot de passe — Agro Eco BAARA')]
class ResetPassword extends Component
{
    #[Locked]
    public string $token = '';

    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function mount(string $token): void
    {
        $this->token = $token;
        $this->email = request()->string('email')->value();
    }

    public function resetPassword(AuthService $authService): void
    {
        $this->validate([
            'token'    => 'required',
            'email'    => 'required|email',
            'password' => [
                'required',
                'confirmed',
                \Illuminate\Validation\Rules\Password::min(8)->mixedCase()->numbers()->symbols(),
            ],
        ], [
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
            'password.min'       => 'Le mot de passe doit contenir au moins 8 caractères.',
        ]);

        $status = Password::reset(
            [
                'email'                 => $this->email,
                'password'              => $this->password,
                'password_confirmation' => $this->password_confirmation,
                'token'                 => $this->token,
            ],
            function ($user, $password) use ($authService) {
                $authService->resetPassword($user, $password);
                event(new PasswordReset($user));
            }
        );

        if ($status !== Password::PasswordReset) {
            $this->addError('email', __($status));
            return;
        }

        session()->flash('success', 'Mot de passe réinitialisé avec succès. Connectez-vous.');
        $this->redirect(route('login'), navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.reset-password');
    }
}
