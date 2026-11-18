<?php

namespace Database\Factories;

use App\Enums\RoomStatus;
use App\Models\Property;
use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Room>
 */
class RoomFactory extends Factory
{
    public function definition(): array
    {
        return [
            'property_id' => Property::factory(),
            'room_number' => (string) fake()->unique()->numberBetween(101, 399),
            'type' => fake()->randomElement(['Standard Fan', 'Deluxe AC', 'VIP Eksklusif']),
            'price' => fake()->randomElement([1200000, 1500000, 1800000, 2200000]),
            'status' => RoomStatus::Available,
        ];
    }

    public function occupied(): static
    {
        return $this->state(fn () => [
            'status' => RoomStatus::Occupied,
        ]);
    }

    public function maintenance(): static
    {
        return $this->state(fn () => [
            'status' => RoomStatus::Maintenance,
        ]);
    }
}

