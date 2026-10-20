<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use App\Services\EmailTwoFactorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function show(): View|RedirectResponse
    {
        if (auth()->check()) {
            return redirect()->route(auth()->user()->homeRouteName());
        }

        return view('auth.login');
    }

    public function store(
        Request $request,
        AuthService $authService,
        EmailTwoFactorService $twoFactor,
    ): RedirectResponse {
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required'    => "L'adresse e-mail est obligatoire.",
            'email.email'       => "L'adresse e-mail est invalide.",
            'password.required' => 'Le mot de passe est obligatoire.',
        ]);

        $throttleKey = Str::lower($request->email).'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            return back()->withErrors([
                'email' => "Trop de tentatives. Réessayez dans {$seconds} secondes.",
            ])->withInput($request->only('email'));
        }

        $result = $authService->attempt(
            email: $request->email,
            password: $request->password,
            ip: $request->ip(),
            remember: $request->boolean('remember'),
        );

        if (! $result->success) {
            RateLimiter::hit($throttleKey, 600);

            return back()->withErrors(['email' => $result->message])
                ->withInput($request->only('email'));
        }

        RateLimiter::clear($throttleKey);

        if ($result->needsTwoFactor && $result->user) {
            try {
                $twoFactor->send($result->user);
            } catch (\Throwable) {
                return back()->withErrors([
                    'email' => 'Identifiants corrects, mais l’envoi du code e-mail a échoué. Vérifiez la configuration mail.',
                ])->withInput($request->only('email'));
            }

            $request->session()->put([
                '2fa.user_id'  => $result->user->id,
                '2fa.remember' => $request->boolean('remember'),
            ]);

            return redirect()->route('login.2fa')
                ->with('success', 'Un code de validation a été envoyé à votre adresse e-mail.');
        }

        if ($result->user->needsPasswordChange()) {
            return redirect()->route('password.change');
        }

        return redirect()->intended(route($result->user->homeRouteName()));
    }
}
