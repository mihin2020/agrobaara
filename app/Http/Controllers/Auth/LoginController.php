<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
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
            return redirect()->route('admin.dashboard');
        }

        return view('auth.login');
    }

    public function store(Request $request, AuthService $authService): RedirectResponse
    {
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required'    => "L'adresse e-mail est obligatoire.",
            'email.email'       => "L'adresse e-mail est invalide.",
            'password.required' => 'Le mot de passe est obligatoire.',
        ]);

        $throttleKey = Str::lower($request->email) . '|' . $request->ip();

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

        if ($result->user->needsPasswordChange()) {
            return redirect()->route('password.change');
        }

        return redirect()->intended(route('admin.dashboard'));
    }
}
