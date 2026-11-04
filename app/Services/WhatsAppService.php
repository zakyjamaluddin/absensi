<?php

namespace App\Services;

use App\Models\WaSetting;
use App\Models\Siswa;
use App\Models\Guru;
use App\Models\User;
use App\Models\Absensi;
use App\Models\JamSetting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * Memformat nomor telepon Indonesia agar sesuai instruksi Sidobe (+628xxx)
     */
    public static function formatPhone(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone); // Buang karakter selain angka

        if (str_starts_with($phone, '08')) {
            return '+628' . substr($phone, 2);
        }

        if (str_starts_with($phone, '8')) {
            return '+628' . substr($phone, 1);
        }

        if (str_starts_with($phone, '628')) {
            return '+' . $phone;
        }

        return '+' . $phone; // Fallback jika sudah benar
    }

    /**
     * Mengirim pesan teks menggunakan API Resmi Sidobe (JSON Format)
     */
    public static function send(string $phone, string $message): array
    {
        $setting = WaSetting::first();
        if (!$setting || empty($setting->token)) {
            return [
                'success' => false,
                'message' => 'Gagal: Token API Sidobe belum diisi di database.'
            ];
        }

        $formattedPhone = self::formatPhone($phone);

        try {
            // KIRIM REQUEST SESUAI DOKUMENTASI RESMI SIDOBE (Dengan timeout proteksi 3 detik)
            $response = Http::timeout(3)
                ->connectTimeout(2)
                ->withHeaders([
                    'X-Secret-Key' => $setting->token,
                    'Content-Type' => 'application/json',
                ])->post('https://api.sidobe.com/wa/v1/send-message', [
                    'phone'   => $formattedPhone,
                    'message' => $message,
                ]);

            // Jika Respon Sukses (Status Code 200 - 299)
            if ($response->successful()) {
                Log::info("WhatsApp sukses dikirim ke {$formattedPhone}");
                return [
                    'success' => true,
                    'message' => 'Pesan berhasil terkirim.'
                ];
            }

            // Jika Gagal, tangkap pesan error asli dari server Sidobe
            $errorBody = $response->body();
            $jsonDecoded = $response->json();
            $errorMessage = $jsonDecoded['message'] ?? $jsonDecoded['error'] ?? $errorBody;

            Log::error("API Sidobe Error: " . $errorBody);

            return [
                'success' => false,
                'message' => 'Respon Sidobe: ' . substr($errorMessage, 0, 150)
            ];

        } catch (\Exception $e) {
            Log::error("Koneksi Sidobe Gagal: " . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Koneksi Gagal: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Kirim notifikasi absensi datang (masuk) ke nomor WA wali murid
     */
    public static function sendNotifMasukWali(Siswa $siswa, Absensi $absensi, ?JamSetting $jamSetting = null): array
    {
        $setting = WaSetting::first();
        if (!$setting || empty($setting->token)) {
            return ['success' => false, 'message' => 'Token API Sidobe belum diatur.'];
        }

        if (!$setting->notif_wa_wali_masuk) {
            return ['success' => false, 'message' => 'Fitur notifikasi WA masuk ke wali dinonaktifkan.'];
        }

        if (!$siswa->is_notif_wa_aktif || empty($siswa->nomor_wa_wali)) {
            return ['success' => false, 'message' => 'Nomor WA wali kosong atau dinonaktifkan untuk siswa ini.'];
        }

        $namaSiswa = $siswa->nama;
        $namaKelas = $siswa->kelas?->nama_kelas ?? '-';
        $tanggalFormat = Carbon::parse($absensi->tanggal)->translatedFormat('l, d F Y');
        $jamMasuk = $absensi->jam_masuk ? Carbon::parse($absensi->jam_masuk)->format('H:i') : now()->format('H:i');
        $status = $absensi->status_masuk ?? 'Hadir';

        $iconStatus = ($status === 'Tepat Waktu') ? '✅' : '⚠️';

        $message = "Assalamualaikum Wr. Wb.\n\n";
        $message .= "Yth. Orang Tua / Wali dari ananda:\n";
        $message .= "👤 *{$namaSiswa}*\n";
        $message .= "🏫 Kelas: *{$namaKelas}*\n\n";
        $message .= "Memberitahukan bahwa ananda telah berhasil melakukan *ABSENSI MASUK*:\n";
        $message .= "🗓️ Hari, Tanggal: {$tanggalFormat}\n";
        $message .= "⏰ Waktu: *{$jamMasuk} WIB*\n";
        $message .= "📌 Status: {$iconStatus} *{$status}*\n\n";

        if ($status === 'Terlambat' && $jamSetting && !empty($jamSetting->batas_terlambat)) {
            $batas = Carbon::parse($jamSetting->batas_terlambat)->format('H:i');
            $message .= "_(Batas waktu hadir tepat waktu adalah pukul {$batas} WIB)_\n\n";
        }

        $message .= "Terima kasih atas perhatian Bapak/Ibu.\n";
        $message .= "Wassalamu'alaikum Wr. Wb.\n";
        $message .= "_Sistem Absensi Digital_";

        return self::send($siswa->nomor_wa_wali, $message);
    }

    /**
     * Kirim notifikasi absensi pulang ke nomor WA wali murid
     */
    public static function sendNotifPulangWali(Siswa $siswa, Absensi $absensi): array
    {
        $setting = WaSetting::first();
        if (!$setting || empty($setting->token)) {
            return ['success' => false, 'message' => 'Token API Sidobe belum diatur.'];
        }

        if (!$setting->notif_wa_wali_pulang) {
            return ['success' => false, 'message' => 'Fitur notifikasi WA pulang ke wali dinonaktifkan.'];
        }

        if (!$siswa->is_notif_wa_aktif || empty($siswa->nomor_wa_wali)) {
            return ['success' => false, 'message' => 'Nomor WA wali kosong atau dinonaktifkan untuk siswa ini.'];
        }

        $namaSiswa = $siswa->nama;
        $namaKelas = $siswa->kelas?->nama_kelas ?? '-';
        $tanggalFormat = Carbon::parse($absensi->tanggal)->translatedFormat('l, d F Y');
        $jamPulang = $absensi->jam_pulang ? Carbon::parse($absensi->jam_pulang)->format('H:i') : now()->format('H:i');

        $message = "Assalamualaikum Wr. Wb.\n\n";
        $message .= "Yth. Orang Tua / Wali dari ananda:\n";
        $message .= "👤 *{$namaSiswa}*\n";
        $message .= "🏫 Kelas: *{$namaKelas}*\n\n";
        $message .= "Memberitahukan bahwa ananda telah berhasil melakukan *ABSENSI PULANG*:\n";
        $message .= "🗓️ Hari, Tanggal: {$tanggalFormat}\n";
        $message .= "⏰ Waktu: *{$jamPulang} WIB*\n";
        $message .= "📌 Status: 🏠 *Telah Pulang Sekolah*\n\n";

        $message .= "Terima kasih atas perhatian Bapak/Ibu.\n";
        $message .= "Wassalamu'alaikum Wr. Wb.\n";
        $message .= "_Sistem Absensi Digital_";

        return self::send($siswa->nomor_wa_wali, $message);
    }

    /**
     * Kirim notifikasi tidak hadir (alpa) ke nomor WA wali murid
     */
    public static function sendNotifAlpaWali(Siswa $siswa, string $tanggal, string $batasWaktu): array
    {
        $setting = WaSetting::first();
        if (!$setting || empty($setting->token)) {
            return ['success' => false, 'message' => 'Token API Sidobe belum diatur.'];
        }

        if (!$setting->notif_wa_wali_alpa) {
            return ['success' => false, 'message' => 'Fitur notifikasi WA alpa ke wali dinonaktifkan.'];
        }

        if (!$siswa->is_notif_wa_aktif || empty($siswa->nomor_wa_wali)) {
            return ['success' => false, 'message' => 'Nomor WA wali kosong atau dinonaktifkan untuk siswa ini.'];
        }

        $namaSiswa = $siswa->nama;
        $namaKelas = $siswa->kelas?->nama_kelas ?? '-';
        $tanggalFormat = Carbon::parse($tanggal)->translatedFormat('l, d F Y');
        $batasFormat = Carbon::parse($batasWaktu)->format('H:i');

        $message = "📢 *PEMBERITAHUAN KETIDAKHADIRAN SISWA*\n\n";
        $message .= "Assalamualaikum Wr. Wb.\n\n";
        $message .= "Yth. Orang Tua / Wali dari ananda:\n";
        $message .= "👤 *{$namaSiswa}*\n";
        $message .= "🏫 Kelas: *{$namaKelas}*\n\n";
        $message .= "Hingga batas waktu akhir absensi masuk (*{$batasFormat} WIB*) pada:\n";
        $message .= "🗓️ Hari, Tanggal: *{$tanggalFormat}*\n\n";
        $message .= "Ananda tercatat: ❌ *TIDAK HADIR / BELUM ABSEN (ALPA)*.\n\n";
        $message .= "Mohon segera konfirmasi ke pihak sekolah/wali kelas jika ananda berhalangan hadir karena sakit atau izin.\n\n";
        $message .= "Terima kasih atas perhatian dan kerjasamanya.\n";
        $message .= "Wassalamu'alaikum Wr. Wb.\n";
        $message .= "_Sistem Absensi Digital_";

        return self::send($siswa->nomor_wa_wali, $message);
    }

    /**
     * Kirim notifikasi absensi datang (masuk) ke nomor WA Guru
     */
    public static function sendNotifMasukGuru(Guru $guru, Absensi $absensi, ?string $batasTerlambat = null): array
    {
        $setting = WaSetting::first();
        if (!$setting || empty($setting->token)) {
            return ['success' => false, 'message' => 'Token API Sidobe belum diatur.'];
        }

        if (!$setting->notif_wa_guru_masuk) {
            return ['success' => false, 'message' => 'Fitur notifikasi WA masuk ke guru dinonaktifkan.'];
        }

        if (!$guru->is_notif_wa_aktif || empty($guru->no_hp)) {
            return ['success' => false, 'message' => 'Nomor WA guru kosong atau dinonaktifkan.'];
        }

        $namaGuru = $guru->nama;
        $tanggalFormat = Carbon::parse($absensi->tanggal)->translatedFormat('l, d F Y');
        $jamMasuk = $absensi->jam_masuk ? Carbon::parse($absensi->jam_masuk)->format('H:i') : now()->format('H:i');
        $status = $absensi->status_masuk ?? 'Hadir';

        $iconStatus = ($status === 'Tepat Waktu') ? '✅' : '⚠️';

        $message = "Assalamualaikum Wr. Wb.\n\n";
        $message .= "Yth. Ustadz/Ustadzah *{$namaGuru}*,\n\n";
        $message .= "Alhamdulillah, absensi *MASUK* Anda telah berhasil dicatat oleh sistem:\n";
        $message .= "🗓️ Hari, Tanggal: {$tanggalFormat}\n";
        $message .= "⏰ Waktu Masuk: *{$jamMasuk} WIB*\n";
        $message .= "📌 Status: {$iconStatus} *{$status}*\n\n";

        if ($status === 'Terlambat' && !empty($batasTerlambat)) {
            $batas = Carbon::parse($batasTerlambat)->format('H:i');
            $message .= "_(Batas waktu hadir tepat waktu Anda adalah pukul {$batas} WIB)_\n\n";
        }

        $message .= "Selamat bertugas mendidik para santri. Semoga aktivitas hari ini bernilai ibadah dan berkah.\n\n";
        $message .= "Wassalamu'alaikum Wr. Wb.\n";
        $message .= "_Sistem Absensi Digital_";

        return self::send($guru->no_hp, $message);
    }

    /**
     * Kirim notifikasi absensi pulang ke nomor WA Guru
     */
    public static function sendNotifPulangGuru(Guru $guru, Absensi $absensi): array
    {
        $setting = WaSetting::first();
        if (!$setting || empty($setting->token)) {
            return ['success' => false, 'message' => 'Token API Sidobe belum diatur.'];
        }

        if (!$setting->notif_wa_guru_pulang) {
            return ['success' => false, 'message' => 'Fitur notifikasi WA pulang ke guru dinonaktifkan.'];
        }

        if (!$guru->is_notif_wa_aktif || empty($guru->no_hp)) {
            return ['success' => false, 'message' => 'Nomor WA guru kosong atau dinonaktifkan.'];
        }

        $namaGuru = $guru->nama;
        $tanggalFormat = Carbon::parse($absensi->tanggal)->translatedFormat('l, d F Y');
        $jamPulang = $absensi->jam_pulang ? Carbon::parse($absensi->jam_pulang)->format('H:i') : now()->format('H:i');

        $message = "Assalamualaikum Wr. Wb.\n\n";
        $message .= "Yth. Ustadz/Ustadzah *{$namaGuru}*,\n\n";
        $message .= "Alhamdulillah, absensi *PULANG* Anda telah berhasil dicatat oleh sistem:\n";
        $message .= "🗓️ Hari, Tanggal: {$tanggalFormat}\n";
        $message .= "⏰ Waktu Pulang: *{$jamPulang} WIB*\n";
        $message .= "📌 Status: 🏠 *Telah Selesai Bertugas / Pulang*\n\n";

        $message .= "Jazakumullah khairan atas dedikasi dan bimbingan yang diberikan kepada para santri hari ini.\n\n";
        $message .= "Wassalamu'alaikum Wr. Wb.\n";
        $message .= "_Sistem Absensi Digital_";

        return self::send($guru->no_hp, $message);
    }

    /**
     * Kirim Laporan Harian Guru Alpa ke WhatsApp Admin Sekolah
     */
    public static function sendReportGuruAlpa(array $guruAlpaList, string $tanggal): array
    {
        $setting = WaSetting::first();
        if (!$setting || empty($setting->token)) {
            return ['success' => false, 'message' => 'Token API Sidobe belum diatur.'];
        }

        if (!$setting->report_guru_alpa) {
            return ['success' => false, 'message' => 'Laporan Guru Alpa dinonaktifkan.'];
        }

        $count = count($guruAlpaList);
        if ($count === 0) {
            return ['success' => true, 'message' => 'Tidak ada guru alpa hari ini.'];
        }

        $formattedDate = Carbon::parse($tanggal)->translatedFormat('l, d F Y');
        $message = "📢 *LAPORAN HARIAN GURU/USTADZ TIDAK HADIR (ALPA)*\n";
        $message .= "🗓️ Hari/Tanggal: *{$formattedDate}*\n";
        $message .= "--------------------------------------------\n\n";
        $message .= "Daftar Guru Tidak Hadir:\n";

        foreach ($guruAlpaList as $idx => $item) {
            $nama = $item['nama'] ?? 'Guru';
            $nip = $item['nip'] ? " (NIP: {$item['nip']})" : "";
            $jadwalInfo = !empty($item['jadwal']) ? " • Jadwal: {$item['jadwal']}" : "";
            $message .= "  " . ($idx + 1) . ". *{$nama}*{$nip}{$jadwalInfo}\n";
        }

        $message .= "\n--------------------------------------------\n";
        $message .= "📊 *Total Guru Tidak Hadir: {$count} Orang.*\n\n";
        $message .= "_Laporan dikirim otomatis oleh Sistem Absensi Digital._";

        // Kirim ke semua super_admin
        $adminUsers = User::role('super_admin')->get();
        $sentCount = 0;

        foreach ($adminUsers as $admin) {
            if ($admin->userable instanceof Guru && !empty($admin->userable->no_hp)) {
                self::send($admin->userable->no_hp, $message);
                $sentCount++;
            }
        }

        return [
            'success' => true,
            'sent_count' => $sentCount,
            'message' => "Laporan Guru Alpa terkirim ke {$sentCount} Admin.",
        ];
    }
}
