<?php

namespace Database\Factories;

use App\Models\Property;
use App\Models\Room;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Tenant>
 */
class TenantFactory extends Factory
{
    public function definition(): array
    {
        return [
            'property_id' => Property::factory(),
            'room_id' => Room::factory(),
            'name' => fake()->name(),
            'phone' => '08'.fake()->numerify('##########'),
            'ktp_path' => null,
            'emergency_contact' => '08'.fake()->numerify('##########').' ('.fake()->firstName().')',
            'start_date' => fake()->dateTimeBetween('-6 months', 'now')->format('Y-m-d'),
            'due_day' => fake()->numberBetween(1, 28),
            'access_token' => Str::random(40),
            'is_active' => true,
        ];
    }
}
