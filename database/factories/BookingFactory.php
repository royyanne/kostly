<?php

namespace Database\Factories;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\Property;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Booking>
 */
class BookingFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'property_id' => Property::factory(),
            'room_id' => Room::factory(),
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'phone' => '08'.fake()->numerify('##########'),
            'start_date' => fake()->dateTimeBetween('now', '+1 month')->format('Y-m-d'),
            'duration_type' => 'monthly',
            'duration_months' => 1,
            'total_price' => 1500000,
            'notes' => fake()->sentence(),
            'status' => BookingStatus::Pending,
            'rejection_reason' => null,
        ];
    }

    public function approved(): static
    {
        return $this->state(fn () => [
            'status' => BookingStatus::Approved,
        ]);
    }

    public function rejected(?string $reason = 'Kamar sudah terisi'): static
    {
        return $this->state(fn () => [
            'status' => BookingStatus::Rejected,
            'rejection_reason' => $reason,
        ]);
    }
}
