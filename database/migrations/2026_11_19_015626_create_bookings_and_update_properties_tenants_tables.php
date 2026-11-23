<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tambah kolom slug, city, image_url, facilities ke tabel properties
        Schema::table('properties', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('name');
            $table->string('city')->nullable()->after('address');
            $table->string('image_url')->nullable()->after('city');
            $table->json('facilities')->nullable()->after('description');
        });

        // 2. Tambah kolom user_id ke tabel tenants
        Schema::table('tenants', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id')->constrained()->nullOnDelete();
        });

        // 3. Buat tabel bookings (pengajuan sewa kamar)
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->foreignId('room_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('email');
            $table->string('phone');
            $table->date('start_date');
            $table->string('duration_type')->default('monthly'); // monthly, yearly
            $table->unsignedSmallInteger('duration_months')->default(1);
            $table->decimal('total_price', 12, 2);
            $table->text('notes')->nullable();
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');

        Schema::table('tenants', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });

        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn(['slug', 'city', 'image_url', 'facilities']);
        });
    }
};
