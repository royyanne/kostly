<?php

namespace Database\Factories;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use App\Models\Property;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Invoice>
 */
class InvoiceFactory extends Factory
{
    public function definition(): array
    {
        $amount = fake()->randomElement([1200000, 1500000, 1800000, 2200000]);
        $additional = fake()->randomElement([0, 50000, 100000]);
        $invoiceCode = 'INV-'.now()->format('Ym').'-'.strtoupper(Str::random(5));

        return [
            'property_id' => Property::factory(),
            'tenant_id' => Tenant::factory(),
            'invoice_code' => $invoiceCode,
            'amount' => $amount,
            'additional_fee' => $additional,
            'total_amount' => $amount + $additional,
            'due_date' => fake()->dateTimeBetween('now', '+15 days')->format('Y-m-d'),
            'status' => InvoiceStatus::Unpaid,
            'payment_method' => null,
            'payment_url' => url('/pay/'.$invoiceCode),
            'paid_at' => null,
        ];
    }

    public function paid(): static
    {
        return $this->state(fn (array $attrs) => [
            'status' => InvoiceStatus::Paid,
            'payment_method' => fake()->randomElement(['QRIS', 'Transfer BCA', 'Transfer Mandiri']),
            'paid_at' => now()->subDays(fake()->numberBetween(1, 10)),
        ]);
    }

    public function overdue(): static
    {
        return $this->state(fn (array $attrs) => [
            'status' => InvoiceStatus::Overdue,
            'due_date' => now()->subDays(fake()->numberBetween(2, 10))->format('Y-m-d'),
        ]);
    }
}
