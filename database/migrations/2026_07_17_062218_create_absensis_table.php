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
        Schema::create('absensis', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal'); // Tanggal absensi (Y-m-d)

            // Menggunakan Polymorphic untuk Siswa atau Guru
            $table->morphs('absensable'); // Menghasilkan: absensable_type & absensable_id

            $table->time('jam_masuk')->nullable();
            $table->time('jam_pulang')->nullable();
            $table->timestamps();

            // Mencegah duplikasi data absensi di hari yang sama untuk orang yang sama
            $table->unique(['tanggal', 'absensable_id', 'absensable_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensis');
    }
};
