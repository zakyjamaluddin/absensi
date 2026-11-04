<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jam_settings', function (Blueprint $table) {
            $table->string('mode_absensi_guru')->default('statis')->after('batas_terlambat'); // 'statis' atau 'jadwal'
            $table->string('aturan_terlambat_guru')->default('jadwal_mengajar')->after('mode_absensi_guru'); // 'jadwal_mengajar' atau 'jam_sekolah'
            $table->time('batas_terlambat_guru')->nullable()->after('aturan_terlambat_guru'); // Opsional kustom statis untuk guru
        });

        Schema::table('wa_settings', function (Blueprint $table) {
            $table->boolean('notif_wa_guru_masuk')->default(true)->after('notif_wa_wali_alpa');
            $table->boolean('notif_wa_guru_pulang')->default(true)->after('notif_wa_guru_masuk');
            $table->boolean('report_guru_alpa')->default(true)->after('notif_wa_guru_pulang');
        });

        Schema::table('gurus', function (Blueprint $table) {
            $table->boolean('is_notif_wa_aktif')->default(true)->after('no_hp');
        });
    }

    public function down(): void
    {
        Schema::table('jam_settings', function (Blueprint $table) {
            $table->dropColumn(['mode_absensi_guru', 'aturan_terlambat_guru', 'batas_terlambat_guru']);
        });

        Schema::table('wa_settings', function (Blueprint $table) {
            $table->dropColumn(['notif_wa_guru_masuk', 'notif_wa_guru_pulang', 'report_guru_alpa']);
        });

        Schema::table('gurus', function (Blueprint $table) {
            $table->dropColumn(['is_notif_wa_aktif']);
        });
    }
};
