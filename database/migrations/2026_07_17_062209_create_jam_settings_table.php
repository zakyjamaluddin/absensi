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
        Schema::create('jam_settings', function (Blueprint $table) {
            $table->id();
            $table->time('mulai_masuk');      // Mulai boleh tap masuk (misal 06:00)
            $table->time('selesai_masuk');    // Batas akhir boleh tap masuk (misal 08:00)
            $table->time('mulai_pulang');     // Mulai boleh tap pulang (misal 13:30)
            $table->time('selesai_pulang');   // Batas akhir boleh tap pulang (misal 16:00)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jam_settings');
    }
};
