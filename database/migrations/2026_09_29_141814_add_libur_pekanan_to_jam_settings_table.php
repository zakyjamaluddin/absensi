<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jam_settings', function (Blueprint $table) {
            // Kolom baru untuk menampung hari libur mingguan (default: Friday / Jumat)
            $table->string('libur_pekanan')->default('Friday')->after('selesai_pulang');
        });
    }

    public function down(): void
    {
        Schema::table('jam_settings', function (Blueprint $table) {
            $table->dropColumn('libur_pekanan');
        });
    }
};
