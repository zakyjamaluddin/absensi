<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wa_settings', function (Blueprint $table) {
            // Jam eksekusi alpa kustom (misal: 10:30)
            $table->time('jam_proses_alpa')->nullable()->after('token'); 
            
            // Tipe eksekusi: 'Otomatis' atau 'Manual'
            $table->string('tipe_proses_alpa')->default('Otomatis')->after('jam_proses_alpa'); 
        });
    }

    public function down(): void
    {
        Schema::table('wa_settings', function (Blueprint $table) {
            $table->dropColumn(['jam_proses_alpa', 'tipe_proses_alpa']);
        });
    }
};