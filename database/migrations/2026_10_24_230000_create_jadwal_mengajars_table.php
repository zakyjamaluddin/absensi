<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal_mengajars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guru_id')->constrained('gurus')->cascadeOnDelete();
            $table->string('hari'); // Monday, Tuesday, Wednesday, Thursday, Friday, Saturday, Sunday
            $table->foreignId('kelas_id')->nullable()->constrained('kelas')->nullOnDelete();
            $table->string('mata_pelajaran');
            $table->string('jam_ke')->nullable(); // Misal: 1, 2, atau "1 - 2"
            $table->time('jam_mulai');            // Misal: 07:20
            $table->time('jam_selesai');          // Misal: 08:40
            $table->timestamps();

            $table->index(['guru_id', 'hari']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_mengajars');
    }
};
