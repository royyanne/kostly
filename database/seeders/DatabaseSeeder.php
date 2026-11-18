<?php

namespace Database\Seeders;

use App\Enums\ComplaintStatus;
use App\Enums\InvoiceStatus;
use App\Enums\RoomStatus;
use App\Enums\SubscriptionPlan;
use App\Models\Complaint;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Property;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Buat 1 Akun Pemilik Kost (Owner) Utama
        $owner = User::firstOrCreate(
            ['email' => 'owner@kostly.id'],
            [
                'name' => 'Budi Santoso',
                'phone' => '081234567890',
                'subscription_plan' => SubscriptionPlan::Pro,
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
            ]
        );

        // 2. Buat Data Properti Kost Milik Owner
        $property = Property::create([
            'user_id' => $owner->id,
            'name' => 'Kost Griya Nyaman Pasteur',
            'address' => 'Jl. Pasteur No. 45, Sukajadi, Kota Bandung, Jawa Barat',
            'description' => 'Kost modern eksklusif dekat kawasan kampus Maranatha & gerbang tol Pasteur. Fasilitas lengkap: Kamar mandi dalam, AC, Smart TV, WiFi 100 Mbps, Dapur bersama, Parkir mobil & motor luas, CCTV 24 Jam.',
        ]);

        // 3. Buat 10 Kamar dengan variasi status & harga
        $roomDefinitions = [
            ['room_number' => '101', 'type' => 'Deluxe AC', 'price' => 1750000, 'status' => RoomStatus::Occupied],
            ['room_number' => '102', 'type' => 'Deluxe AC', 'price' => 1750000, 'status' => RoomStatus::Occupied],
            ['room_number' => '103', 'type' => 'Deluxe AC', 'price' => 1750000, 'status' => RoomStatus::Occupied],
            ['room_number' => '104', 'type' => 'VIP Eksklusif', 'price' => 2200000, 'status' => RoomStatus::Occupied],
            ['room_number' => '105', 'type' => 'VIP Eksklusif', 'price' => 2200000, 'status' => RoomStatus::Occupied],
            ['room_number' => '201', 'type' => 'Standard AC', 'price' => 1500000, 'status' => RoomStatus::Occupied],
            ['room_number' => '202', 'type' => 'Standard AC', 'price' => 1500000, 'status' => RoomStatus::Occupied],
            ['room_number' => '203', 'type' => 'Standard Fan', 'price' => 1200000, 'status' => RoomStatus::Available],
            ['room_number' => '204', 'type' => 'Standard Fan', 'price' => 1200000, 'status' => RoomStatus::Available],
            ['room_number' => '205', 'type' => 'Standard AC', 'price' => 1500000, 'status' => RoomStatus::Maintenance],
        ];

        $rooms = [];
        foreach ($roomDefinitions as $def) {
            $rooms[$def['room_number']] = Room::create([
                'property_id' => $property->id,
                'room_number' => $def['room_number'],
                'type' => $def['type'],
                'price' => $def['price'],
                'status' => $def['status'],
            ]);
        }

        // 4. Buat 7 Data Penghuni Aktif untuk Kamar Terisi (Occupied)
        $tenantsData = [
            [
                'room_number' => '101',
                'name' => 'Andika Pratama',
                'phone' => '081298765431',
                'emergency_contact' => '081298765400 (Ayah - Joko)',
                'start_date' => Carbon::parse('2026-02-01'),
                'due_day' => 5,
                'access_token' => 'andika-kostly-token-101',
            ],
            [
                'room_number' => '102',
                'name' => 'Siti Rahmawati',
                'phone' => '081387654322',
                'emergency_contact' => '081387654300 (Ibu - Maryam)',
                'start_date' => Carbon::parse('2026-03-10'),
                'due_day' => 10,
                'access_token' => 'siti-kostly-token-102',
            ],
            [
                'room_number' => '103',
                'name' => 'Dimas Setiawan',
                'phone' => '081576543213',
                'emergency_contact' => '081576543200 (Kakak - Hendra)',
                'start_date' => Carbon::parse('2026-01-15'),
                'due_day' => 15,
                'access_token' => 'dimas-kostly-token-103',
            ],
            [
                'room_number' => '104',
                'name' => 'Nabila Putri',
                'phone' => '081965432104',
                'emergency_contact' => '081965432100 (Ibu - Lina)',
                'start_date' => Carbon::parse('2025-11-01'),
                'due_day' => 1,
                'access_token' => 'nabila-kostly-token-104',
            ],
            [
                'room_number' => '105',
                'name' => 'Rizky Ramadhan',
                'phone' => '085254321095',
                'emergency_contact' => '085254321000 (Ayah - Bambang)',
                'start_date' => Carbon::parse('2026-04-20'),
                'due_day' => 20,
                'access_token' => 'rizky-kostly-token-105',
            ],
            [
                'room_number' => '201',
                'name' => 'Maya Anggraini',
                'phone' => '087743210986',
                'emergency_contact' => '087743210900 (Kakak - Dedi)',
                'start_date' => Carbon::parse('2025-12-25'),
                'due_day' => 25,
                'access_token' => 'maya-kostly-token-201',
            ],
            [
                'room_number' => '202',
                'name' => 'Fajar Nugraha',
                'phone' => '089632109877',
                'emergency_contact' => '089632109800 (Ibu - Ratna)',
                'start_date' => Carbon::parse('2026-05-28'),
                'due_day' => 28,
                'access_token' => 'fajar-kostly-token-202',
            ],
        ];

        $tenants = [];
        foreach ($tenantsData as $tData) {
            $room = $rooms[$tData['room_number']];
            $tenants[$tData['room_number']] = Tenant::create([
                'property_id' => $property->id,
                'room_id' => $room->id,
                'name' => $tData['name'],
                'phone' => $tData['phone'],
                'ktp_path' => null,
                'emergency_contact' => $tData['emergency_contact'],
                'start_date' => $tData['start_date'],
                'due_day' => $tData['due_day'],
                'access_token' => $tData['access_token'],
                'is_active' => true,
            ]);
        }

        // 5. Buat Data Tagihan (Invoices)
        // a. Tagihan Lunas (Nabila Kamar 104)
        Invoice::create([
            'property_id' => $property->id,
            'tenant_id' => $tenants['104']->id,
            'invoice_code' => 'INV-202611-NB104',
            'amount' => 2200000,
            'additional_fee' => 0,
            'total_amount' => 2200000,
            'due_date' => Carbon::parse('2026-11-01'),
            'status' => InvoiceStatus::Paid,
            'payment_method' => 'QRIS Realtime',
            'payment_url' => url('/pay/INV-202611-NB104'),
            'paid_at' => Carbon::parse('2026-11-01 08:30:00'),
        ]);

        // b. Tagihan Lunas (Rizky Kamar 105)
        Invoice::create([
            'property_id' => $property->id,
            'tenant_id' => $tenants['105']->id,
            'invoice_code' => 'INV-202610-RK105',
            'amount' => 2200000,
            'additional_fee' => 50000, // Misal charge kulkas mini
            'total_amount' => 2250000,
            'due_date' => Carbon::parse('2026-10-20'),
            'status' => InvoiceStatus::Paid,
            'payment_method' => 'Transfer Bank BCA',
            'payment_url' => url('/pay/INV-202610-RK105'),
            'paid_at' => Carbon::parse('2026-10-19 14:15:00'),
        ]);

        // c. Tagihan Belum Lunas Mendekati Jatuh Tempo H-3 (Andika Kamar 101)
        Invoice::create([
            'property_id' => $property->id,
            'tenant_id' => $tenants['101']->id,
            'invoice_code' => 'INV-202611-AP101',
            'amount' => 1750000,
            'additional_fee' => 0,
            'total_amount' => 1750000,
            'due_date' => now()->addDays(3)->toDateString(),
            'status' => InvoiceStatus::Unpaid,
            'payment_method' => null,
            'payment_url' => url('/pay/INV-202611-AP101'),
            'paid_at' => null,
        ]);

        // d. Tagihan Hari Ini Jatuh Tempo Hari-H (Dimas Kamar 103)
        Invoice::create([
            'property_id' => $property->id,
            'tenant_id' => $tenants['103']->id,
            'invoice_code' => 'INV-202611-DS103',
            'amount' => 1750000,
            'additional_fee' => 25000,
            'total_amount' => 1775000,
            'due_date' => now()->toDateString(),
            'status' => InvoiceStatus::Unpaid,
            'payment_method' => null,
            'payment_url' => url('/pay/INV-202611-DS103'),
            'paid_at' => null,
        ]);

        // e. Tagihan Jatuh Tempo / Terlambat (Maya Kamar 201)
        Invoice::create([
            'property_id' => $property->id,
            'tenant_id' => $tenants['201']->id,
            'invoice_code' => 'INV-202610-MA201',
            'amount' => 1500000,
            'additional_fee' => 0,
            'total_amount' => 1500000,
            'due_date' => now()->subDays(5)->toDateString(),
            'status' => InvoiceStatus::Overdue,
            'payment_method' => null,
            'payment_url' => url('/pay/INV-202610-MA201'),
            'paid_at' => null,
        ]);

        // f. Tagihan Belum Lunas (Siti Kamar 102 & Fajar Kamar 202)
        Invoice::create([
            'property_id' => $property->id,
            'tenant_id' => $tenants['102']->id,
            'invoice_code' => 'INV-202611-SR102',
            'amount' => 1750000,
            'additional_fee' => 0,
            'total_amount' => 1750000,
            'due_date' => now()->addDays(8)->toDateString(),
            'status' => InvoiceStatus::Unpaid,
            'payment_method' => null,
            'payment_url' => url('/pay/INV-202611-SR102'),
            'paid_at' => null,
        ]);

        Invoice::create([
            'property_id' => $property->id,
            'tenant_id' => $tenants['202']->id,
            'invoice_code' => 'INV-202611-FN202',
            'amount' => 1500000,
            'additional_fee' => 0,
            'total_amount' => 1500000,
            'due_date' => now()->addDays(12)->toDateString(),
            'status' => InvoiceStatus::Unpaid,
            'payment_method' => null,
            'payment_url' => url('/pay/INV-202611-FN202'),
            'paid_at' => null,
        ]);

        // 6. Buat Pengeluaran Operasional (Expenses) Realistis
        $expenses = [
            [
                'title' => 'Tagihan Listrik PLN Pascabayar',
                'category' => 'Utilitas',
                'amount' => 840000,
                'expense_date' => now()->subDays(8)->toDateString(),
                'notes' => 'Listrik gedung utama dan kamar kost bulan berjalan.',
            ],
            [
                'title' => 'Layanan Internet Indihome Fiber 100 Mbps',
                'category' => 'Utilitas',
                'amount' => 450000,
                'expense_date' => now()->subDays(10)->toDateString(),
                'notes' => 'Biaya langganan WiFi kost bulanan.',
            ],
            [
                'title' => 'Iuran Kebersihan Lingkungan & Sampah RT 04',
                'category' => 'Kebersihan',
                'amount' => 150000,
                'expense_date' => now()->subDays(14)->toDateString(),
                'notes' => 'Iuran rutin pengambilan sampah 3x seminggu.',
            ],
            [
                'title' => 'Servis & Cuci AC Kamar 101, 102, 103',
                'category' => 'Pemeliharaan',
                'amount' => 240000,
                'expense_date' => now()->subDays(4)->toDateString(),
                'notes' => 'Cuci rutin AC berkala 3 bulan sekali (3 unit @ Rp 80.000).',
            ],
            [
                'title' => 'Perlengkapan Kebersihan & Air Galon Dapur',
                'category' => 'Operasional',
                'amount' => 125000,
                'expense_date' => now()->subDays(2)->toDateString(),
                'notes' => 'Isi ulang 4 galon air minum & refill sabun cuci piring dapur.',
            ],
        ];

        foreach ($expenses as $expense) {
            Expense::create(array_merge($expense, ['property_id' => $property->id]));
        }

        // 7. Buat Komplain Penghuni (Complaints) Realistis
        Complaint::create([
            'property_id' => $property->id,
            'tenant_id' => $tenants['103']->id,
            'room_id' => $rooms['103']->id,
            'title' => 'AC Kamar 103 Kurang Dingin',
            'description' => 'Halo Pak Budi, AC di kamar 103 hembusan anginnya terasa kurang dingin saat siang hari, mohon dibantu teknisi dicek freonnya.',
            'photo_path' => null,
            'status' => ComplaintStatus::InProgress,
        ]);

        Complaint::create([
            'property_id' => $property->id,
            'tenant_id' => $tenants['201']->id,
            'room_id' => $rooms['201']->id,
            'title' => 'Lampu Kamar Mandi Mati',
            'description' => 'Lampu LED di dalam kamar mandi kamar 201 berkedip lalu mati total tadi pagi.',
            'photo_path' => null,
            'status' => ComplaintStatus::Resolved,
        ]);

        Complaint::create([
            'property_id' => $property->id,
            'tenant_id' => $tenants['101']->id,
            'room_id' => $rooms['101']->id,
            'title' => 'Kran Wastafel Dapur Sedikit Menetes',
            'description' => 'Kran wastafel di area dapur bersama lantai 1 ada sedikit rembesan air meskipun sudah ditutup rapat.',
            'photo_path' => null,
            'status' => ComplaintStatus::Pending,
        ]);
    }
}
