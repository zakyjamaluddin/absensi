<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Buat tabel master jam_pelajarans
        Schema::create('jam_pelajarans', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('jam_ke');
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->timestamps();
        });

        // 2. Isi default 8 jam pelajaran standar sekolah
        $defaultJam = [
            ['jam_ke' => 1, 'jam_mulai' => '07:00:00', 'jam_selesai' => '07:45:00'],
            ['jam_ke' => 2, 'jam_mulai' => '07:45:00', 'jam_selesai' => '08:30:00'],
            ['jam_ke' => 3, 'jam_mulai' => '08:30:00', 'jam_selesai' => '09:15:00'],
            ['jam_ke' => 4, 'jam_mulai' => '09:15:00', 'jam_selesai' => '10:00:00'],
            ['jam_ke' => 5, 'jam_mulai' => '10:15:00', 'jam_selesai' => '11:00:00'],
            ['jam_ke' => 6, 'jam_mulai' => '11:00:00', 'jam_selesai' => '11:45:00'],
            ['jam_ke' => 7, 'jam_mulai' => '12:30:00', 'jam_selesai' => '13:15:00'],
            ['jam_ke' => 8, 'jam_mulai' => '13:15:00', 'jam_selesai' => '14:00:00'],
        ];

        foreach ($defaultJam as $item) {
            DB::table('jam_pelajarans')->insert([
                'jam_ke' => $item['jam_ke'],
                'jam_mulai' => $item['jam_mulai'],
                'jam_selesai' => $item['jam_selesai'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 3. Tambahkan kolom mulai_jam_ke & sampai_jam_ke ke jadwal_mengajars, dan buat mata_pelajaran nullable
        Schema::table('jadwal_mengajars', function (Blueprint $table) {
            $table->unsignedInteger('mulai_jam_ke')->nullable()->after('jam_ke');
            $table->unsignedInteger('sampai_jam_ke')->nullable()->after('mulai_jam_ke');
            $table->string('mata_pelajaran')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('jadwal_mengajars', function (Blueprint $table) {
            $table->dropColumn(['mulai_jam_ke', 'sampai_jam_ke']);
        });

        Schema::dropIfExists('jam_pelajarans');
    }
};
