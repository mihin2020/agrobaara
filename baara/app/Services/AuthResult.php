<?php

namespace App\Services;

use App\Models\User;

final class AuthResult
{
    private function __construct(
        public readonly bool $success,
        public readonly string $message,
        public readonly ?User $user = null,
    ) {}

    public static function success(User $user): self
    {
        return new self(true, '', $user);
    }

    public static function failed(string $message): self
    {
        return new self(false, $message);
    }
}
