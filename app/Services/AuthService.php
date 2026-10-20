<?php

namespace App\Services;

use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthService
{
    private const MAX_ATTEMPTS = 5;

    public function __construct(
        private readonly EmailTwoFactorService $twoFactor,
    ) {}

    public function attempt(string $email, string $password, string $ip, bool $remember = false): AuthResult
    {
        $user = User::where('email', $email)
            ->with('roles.permissions', 'permissions')
            ->first();

        if (! $user) {
            return AuthResult::failed('Identifiants invalides.');
        }

        if ($user->status === UserStatus::Inactive) {
            return AuthResult::failed('Ce compte est désactivé. Contactez un administrateur.');
        }

        if ($user->isLocked()) {
            $remaining = now()->diffInMinutes($user->locked_until);

            return AuthResult::failed("Compte verrouillé. Réessayez dans {$remaining} minute(s).");
        }

        if (! Hash::check($password, $user->password)) {
            $user->incrementFailedAttempts();

            if ($user->fresh()->isLocked()) {
                Log::warning('Account locked after failed attempts', ['user_id' => $user->id, 'ip' => $ip]);

                return AuthResult::failed('Trop de tentatives. Compte verrouillé 10 minutes.');
            }

            $remaining = self::MAX_ATTEMPTS - $user->failed_login_attempts;

            return AuthResult::failed("Identifiants invalides. {$remaining} tentative(s) restante(s).");
        }

        // 2FA e-mail pour le staff : pas de session complète tant que le code n’est pas validé
        if ($this->twoFactor->requiredFor($user)) {
            return AuthResult::needsTwoFactor($user);
        }

        return $this->completeLogin($user, $ip, $remember);
    }

    public function completeLogin(User $user, string $ip, bool $remember = false, bool $viaTwoFactor = false): AuthResult
    {
        $user->recordLogin($ip);

        Auth::login($user, $remember);
        session()->regenerate();

        activity()
            ->causedBy($user)
            ->withProperties(['ip' => $ip, 'two_factor' => $viaTwoFactor])
            ->log('login');

        return AuthResult::success($user);
    }

    public function logout(): void
    {
        if ($user = Auth::user()) {
            activity()->causedBy($user)->log('logout');
        }

        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();
    }

    public function resetPassword(User $user, string $newPassword): void
    {
        $user->update([
            'password' => Hash::make($newPassword),
            'status'   => UserStatus::Active,
        ]);

        Auth::logoutOtherDevices($newPassword);

        activity()
            ->causedBy($user)
            ->log('password_reset');
    }

    public function forcePasswordChange(User $user, string $newPassword): void
    {
        $user->update([
            'password' => Hash::make($newPassword),
            'status'   => UserStatus::Active,
        ]);

        activity()
            ->causedBy($user)
            ->log('password_changed_on_first_login');
    }
}
