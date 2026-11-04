<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wa_settings', function (Blueprint $table) {
            $table->boolean('notif_wa_wali_masuk')->default(true)->after('tipe_proses_alpa');
            $table->boolean('notif_wa_wali_pulang')->default(true)->after('notif_wa_wali_masuk');
            $table->boolean('notif_wa_wali_alpa')->default(true)->after('notif_wa_wali_pulang');
        });
    }

    public function down(): void
    {
        Schema::table('wa_settings', function (Blueprint $table) {
            $table->dropColumn(['notif_wa_wali_masuk', 'notif_wa_wali_pulang', 'notif_wa_wali_alpa']);
        });
    }
};
