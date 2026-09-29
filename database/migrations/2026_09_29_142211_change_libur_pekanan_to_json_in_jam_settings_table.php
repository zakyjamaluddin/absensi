<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // <--- PASTIKAN IMPORT INI ADA

return new class extends Migration
{
    public function up(): void
    {
        // 1. UPDATE DATA LAMA: Paksa ubah isi data string lama menjadi format Array JSON yang valid
        DB::statement("UPDATE jam_settings SET libur_pekanan = '[\"Friday\"]'");

        // 2. SEKARANG UBAH TIPE KOLOMNYA: Dijamin sukses 100% karena datanya sudah berformat JSON valid
        Schema::table('jam_settings', function (Blueprint $table) {
            $table->json('libur_pekanan')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('jam_settings', function (Blueprint $table) {
            $table->string('libur_pekanan')->default('Friday')->change();
        });
    }
};
