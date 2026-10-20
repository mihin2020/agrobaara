<?php

namespace App\Services;

use App\Mail\TwoFactorCodeMail;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;

class EmailTwoFactorService
{
    private const TTL_SECONDS = 600; // 10 min

    private const MAX_VERIFY_ATTEMPTS = 5;

    public function isEnabled(): bool
    {
        return SiteSetting::twoFactorEmailEnabled();
    }

    public function requiredFor(User $user): bool
    {
        if (! $this->isEnabled()) {
            return false;
        }

        // Staff back-office + compte système uniquement (pas les portails)
        return $user->isStaffUser() || (bool) $user->is_system;
    }

    public function send(User $user): void
    {
        $code = (string) random_int(100000, 999999);

        Cache::put($this->codeKey($user->id), [
            'hash'    => Hash::make($code),
            'attempts'=> 0,
        ], self::TTL_SECONDS);

        try {
            Mail::to($user->email)->send(new TwoFactorCodeMail($user, $code));
        } catch (\Throwable $e) {
            Log::error('2FA email failed', [
                'user_id' => $user->id,
                'error'   => $e->getMessage(),
            ]);
            throw $e;
        }

        activity()
            ->causedBy($user)
            ->withProperties(['channel' => 'email'])
            ->log('two_factor_code_sent');
    }

    public function canResend(string $ip): bool
    {
        return ! RateLimiter::tooManyAttempts($this->resendKey($ip), 3);
    }

    public function hitResend(string $ip): void
    {
        RateLimiter::hit($this->resendKey($ip), 600);
    }

    public function resendAvailableIn(string $ip): int
    {
        return RateLimiter::availableIn($this->resendKey($ip));
    }

    public function verify(User $user, string $code): bool
    {
        $payload = Cache::get($this->codeKey($user->id));

        if (! is_array($payload) || empty($payload['hash'])) {
            return false;
        }

        $attempts = (int) ($payload['attempts'] ?? 0);
        if ($attempts >= self::MAX_VERIFY_ATTEMPTS) {
            Cache::forget($this->codeKey($user->id));

            return false;
        }

        $payload['attempts'] = $attempts + 1;
        Cache::put($this->codeKey($user->id), $payload, self::TTL_SECONDS);

        if (! Hash::check($code, $payload['hash'])) {
            return false;
        }

        Cache::forget($this->codeKey($user->id));

        return true;
    }

    private function codeKey(string $userId): string
    {
        return '2fa:email:'.$userId;
    }

    private function resendKey(string $ip): string
    {
        return '2fa:resend:'.$ip;
    }
}
