<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambah kolom batas_terlambat di tabel jam_settings
        Schema::table('jam_settings', function (Blueprint $table) {
            $table->time('batas_terlambat')->default('07:00:00')->after('selesai_masuk');
        });

        // 2. Tambah kolom status_masuk & status_pulang di tabel absensis
        Schema::table('absensis', function (Blueprint $table) {
            $table->string('status_masuk')->nullable()->after('jam_masuk');
            $table->string('status_pulang')->nullable()->after('jam_pulang');
        });
    }

    public function down(): void
    {
        Schema::table('jam_settings', function (Blueprint $table) {
            $table->dropColumn('batas_terlambat');
        });

        Schema::table('absensis', function (Blueprint $table) {
            $table->dropColumn(['status_masuk', 'status_pulang']);
        });
    }
};
