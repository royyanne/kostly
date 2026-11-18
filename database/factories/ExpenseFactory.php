<?php

namespace Database\Factories;

use App\Models\Expense;
use App\Models\Property;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Expense>
 */
class ExpenseFactory extends Factory
{
    public function definition(): array
    {
        return [
            'property_id' => Property::factory(),
            'title' => fake()->randomElement([
                'Tagihan Listrik PLN',
                'Tagihan Air PDAM',
                'Layanan Internet WiFi',
                'Iuran Kebersihan Lingkungan',
                'Perbaikan Kran & Pipa',
                'Pembelian Token Listrik Cadangan',
            ]),
            'category' => fake()->randomElement(['Utilitas', 'Pemeliharaan', 'Kebersihan', 'Keamanan', 'Lain-lain']),
            'amount' => fake()->randomElement([150000, 300000, 450000, 750000, 1200000]),
            'expense_date' => fake()->dateTimeBetween('-1 month', 'now')->format('Y-m-d'),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
