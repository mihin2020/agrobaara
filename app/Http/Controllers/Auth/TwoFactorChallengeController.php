<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuthService;
use App\Services\EmailTwoFactorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\View;

class TwoFactorChallengeController extends Controller
{
    public function show(Request $request): View|RedirectResponse
    {
        if (auth()->check()) {
            return redirect()->route(auth()->user()->homeRouteName());
        }

        $userId = $request->session()->get('2fa.user_id');
        if (! $userId) {
            return redirect()->route('login');
        }

        $user = User::find($userId);
        if (! $user) {
            $request->session()->forget(['2fa.user_id', '2fa.remember', '2fa.email_mask']);

            return redirect()->route('login');
        }

        return view('auth.two-factor-challenge', [
            'emailMask' => $this->maskEmail($user->email),
        ]);
    }

    public function store(
        Request $request,
        EmailTwoFactorService $twoFactor,
        AuthService $authService,
    ): RedirectResponse {
        $userId = $request->session()->get('2fa.user_id');
        if (! $userId) {
            return redirect()->route('login');
        }

        $request->validate([
            'code' => ['required', 'string', 'size:6', 'regex:/^[0-9]{6}$/'],
        ], [
            'code.required' => 'Le code est obligatoire.',
            'code.size'     => 'Le code doit contenir 6 chiffres.',
            'code.regex'    => 'Le code doit contenir uniquement des chiffres.',
        ]);

        $throttleKey = '2fa:verify:'.$request->ip().'|'.$userId;
        if (RateLimiter::tooManyAttempts($throttleKey, 10)) {
            return back()->withErrors([
                'code' => 'Trop de tentatives. Réessayez dans '.RateLimiter::availableIn($throttleKey).' secondes.',
            ]);
        }
        RateLimiter::hit($throttleKey, 600);

        $user = User::with('roles.permissions', 'permissions')->find($userId);
        if (! $user || ! $twoFactor->verify($user, $request->string('code')->toString())) {
            return back()->withErrors(['code' => 'Code invalide ou expiré.']);
        }

        $remember = (bool) $request->session()->get('2fa.remember', false);
        $request->session()->forget(['2fa.user_id', '2fa.remember', '2fa.email_mask']);

        $result = $authService->completeLogin($user, $request->ip(), $remember, viaTwoFactor: true);

        if ($result->user->needsPasswordChange()) {
            return redirect()->route('password.change');
        }

        return redirect()->intended(route($result->user->homeRouteName()));
    }

    public function resend(Request $request, EmailTwoFactorService $twoFactor): RedirectResponse
    {
        $userId = $request->session()->get('2fa.user_id');
        if (! $userId) {
            return redirect()->route('login');
        }

        if (! $twoFactor->canResend($request->ip())) {
            return back()->withErrors([
                'code' => 'Trop de renvois. Réessayez dans '.$twoFactor->resendAvailableIn($request->ip()).' secondes.',
            ]);
        }

        $user = User::find($userId);
        if (! $user) {
            return redirect()->route('login');
        }

        try {
            $twoFactor->send($user);
            $twoFactor->hitResend($request->ip());
        } catch (\Throwable) {
            return back()->withErrors(['code' => 'Impossible d’envoyer l’e-mail. Réessayez plus tard.']);
        }

        return back()->with('success', 'Un nouveau code a été envoyé à votre adresse e-mail.');
    }

    public function cancel(Request $request): RedirectResponse
    {
        $request->session()->forget(['2fa.user_id', '2fa.remember', '2fa.email_mask']);

        return redirect()->route('login');
    }

    private function maskEmail(string $email): string
    {
        [$local, $domain] = array_pad(explode('@', $email, 2), 2, '');
        if ($local === '' || $domain === '') {
            return '***';
        }
        $visible = substr($local, 0, min(2, strlen($local)));

        return $visible.str_repeat('*', max(3, strlen($local) - 2)).'@'.$domain;
    }
}
