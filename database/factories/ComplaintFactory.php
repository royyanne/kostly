<?php

namespace Database\Factories;

use App\Enums\ComplaintStatus;
use App\Models\Complaint;
use App\Models\Property;
use App\Models\Room;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Complaint>
 */
class ComplaintFactory extends Factory
{
    public function definition(): array
    {
        return [
            'property_id' => Property::factory(),
            'tenant_id' => Tenant::factory(),
            'room_id' => Room::factory(),
            'title' => fake()->randomElement([
                'AC kurang dingin dan berisik',
                'Lampu kamar mandi redup/mati',
                'Kran wastafel bocor menetes',
                'Koneksi WiFi lantai 2 lambat',
                'Gagang pintu kamar agak longgar',
            ]),
            'description' => fake()->paragraph(),
            'photo_path' => null,
            'status' => fake()->randomElement(ComplaintStatus::cases()),
        ];
    }
}
