<?php

namespace Database\Factories;

use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'first_name'      => fake()->firstName(),
            'last_name'       => fake()->lastName(),
            'email'           => fake()->unique()->safeEmail(),
            'password'        => static::$password ??= Hash::make('Password@1'),
            'status'          => UserStatus::Active,
            'remember_token'  => Str::random(10),
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['status' => UserStatus::Inactive]);
    }

    public function locked(): static
    {
        return $this->state(fn () => [
            'status'       => UserStatus::Locked,
            'locked_until' => now()->addMinutes(10),
        ]);
    }

    public function pendingPassword(): static
    {
        return $this->state(fn () => ['status' => UserStatus::PendingPassword]);
    }
}
