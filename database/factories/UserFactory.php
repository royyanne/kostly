<?php

namespace Database\Factories;

use App\Enums\SubscriptionPlan;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => '08'.fake()->numerify('##########'),
            'role' => UserRole::Owner,
            'is_active' => true,
            'subscription_plan' => SubscriptionPlan::Free,
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Set subscription plan to Pro.
     */
    public function pro(): static
    {
        return $this->state(fn () => [
            'subscription_plan' => SubscriptionPlan::Pro,
        ]);
    }

    /**
     * Indicate that the user is a tenant.
     */
    public function tenant(): static
    {
        return $this->state(fn () => [
            'role' => UserRole::Tenant,
            'subscription_plan' => SubscriptionPlan::Free,
        ]);
    }

    /**
     * Indicate that the user is a super admin.
     */
    public function superAdmin(): static
    {
        return $this->state(fn () => [
            'role' => UserRole::SuperAdmin,
            'subscription_plan' => SubscriptionPlan::Pro,
        ]);
    }

    /**
     * Indicate that the user is suspended / inactive.
     */
    public function suspended(): static
    {
        return $this->state(fn () => [
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
