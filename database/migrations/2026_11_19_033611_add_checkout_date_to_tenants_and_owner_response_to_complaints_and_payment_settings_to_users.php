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
        // 1. Tambah kolom check_out_date ke tabel tenants
        Schema::table('tenants', function (Blueprint $table) {
            $table->date('check_out_date')->nullable()->after('due_day');
        });

        // 2. Tambah kolom owner_response ke tabel complaints
        Schema::table('complaints', function (Blueprint $table) {
            $table->text('owner_response')->nullable()->after('status');
        });

        // 3. Tambah kolom rekening & QRIS ke tabel users
        Schema::table('users', function (Blueprint $table) {
            $table->string('bank_name')->nullable()->after('plan_expires_at');
            $table->string('bank_account_number')->nullable()->after('bank_name');
            $table->string('bank_account_name')->nullable()->after('bank_account_number');
            $table->string('qris_image_path')->nullable()->after('bank_account_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['bank_name', 'bank_account_number', 'bank_account_name', 'qris_image_path']);
        });

        Schema::table('complaints', function (Blueprint $table) {
            $table->dropColumn('owner_response');
        });

        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn('check_out_date');
        });
    }
};
