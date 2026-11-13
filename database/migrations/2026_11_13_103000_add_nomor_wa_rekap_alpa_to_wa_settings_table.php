<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wa_settings', function (Blueprint $table) {
            $table->string('nomor_wa_rekap_alpa')->nullable()->after('tipe_proses_alpa');
            $table->boolean('notif_wa_admin_rekap_alpa')->default(true)->after('nomor_wa_rekap_alpa');
        });
    }

    public function down(): void
    {
        Schema::table('wa_settings', function (Blueprint $table) {
            $table->dropColumn(['nomor_wa_rekap_alpa', 'notif_wa_admin_rekap_alpa']);
        });
    }
};
