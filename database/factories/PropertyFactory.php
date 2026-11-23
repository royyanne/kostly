<?php

namespace Database\Factories;

use App\Models\Property;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Property>
 */
class PropertyFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => 'Kost '.fake()->randomElement(['Putra', 'Putri', 'Bahagia', 'Sejahtera', 'Harmoni']).' '.fake()->lastName(),
            'address' => fake()->address(),
            'description' => 'Kost nyaman, aman, dan strategis dekat fasilitas umum.',
        ];
    }
}
