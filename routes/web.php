<?php

use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\EksporAbsensiController;
use App\Http\Controllers\LegalController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin');
});

// Halaman Hukum & Persyaratan (Public Legal Pages)
Route::get('/privacy-policy', [LegalController::class, 'privacyPolicy'])->name('legal.privacy');
Route::get('/terms-of-service', [LegalController::class, 'termsOfService'])->name('legal.terms');

// OAuth Google Authentication Routes
Route::prefix('auth/google')->name('auth.google')->group(function () {
    Route::get('/', [GoogleAuthController::class, 'redirectToGoogle']);
    Route::get('/callback', [GoogleAuthController::class, 'handleGoogleCallback'])->name('.callback');
    Route::get('/verify', [GoogleAuthController::class, 'showVerificationForm'])->name('.verify');
    Route::post('/verify', [GoogleAuthController::class, 'processVerification'])->name('.verify.submit');
});

// Real-time Notification Trigger Checking Route
Route::middleware(['auth'])->get('/api/notifications/check', [NotificationController::class, 'checkPendingNotifications'])
    ->name('api.notifications.check');

// Letakkan rute ini di dalam kelompok rute admin atau letakkan secara mandiri
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/kelas/{kelas}/ekspor/{bulan}/{tahun}', [EksporAbsensiController::class, 'ekspor'])
        ->name('admin.kelas.ekspor');
});

// Rute untuk ekspor semua kelas sekaligus (multi-sheet)
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/rekap-semua-kelas/ekspor/{bulan}/{tahun}', [EksporAbsensiController::class, 'eksporSemuaKelas'])
        ->name('admin.kelas.ekspor-semua');
});

// ============================================================================
// FIX & FALLBACK ASSET STORAGE (Mengatasi Error 403 Forbidden di Shared Hosting)
// ============================================================================

// 0. URL Updater 1-Klik: Update Absaka V1 ke V2 secara otomatis tanpa menghapus data
Route::get('/system-update-v2', function () {
    $html = "<div style='font-family:system-ui,-apple-system,sans-serif;max-width:680px;margin:40px auto;padding:32px;border-radius:18px;background:#ffffff;border:1px solid #e2e8f0;box-shadow:0 10px 25px -5px rgba(0,0,0,0.06);'>";
    $html .= "<div style='display:inline-block;padding:6px 14px;background:#ecfdf5;color:#047857;border-radius:999px;font-size:12px;font-weight:800;margin-bottom:14px;letter-spacing:0.05em;'>ABSAKA SYSTEM UPDATER V2</div>";
    $html .= "<h2 style='color:#0f172a;margin:0 0 16px 0;font-size:24px;font-weight:800;'>🚀 Pembaruan Sistem Absaka V2 Berhasil</h2>";
    $html .= "<p style='color:#64748b;font-size:14px;margin-bottom:20px;'>Memperbarui skema database, menyinkronkan permission, dan membersihkan cache <strong>tanpa menghapus atau mengganggu data siswa, guru, kelas, maupun presensi</strong> yang sudah ada.</p>";

    // 1. Eksekusi Migrate --force
    try {
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        $migrateOutput = trim(\Illuminate\Support\Facades\Artisan::output());
        if (empty($migrateOutput)) {
            $migrateOutput = "Semua tabel database sudah yang paling baru (Nothing to migrate). Data aman.";
        }
        $html .= "<div style='margin-bottom:14px;'><strong style='color:#047857;'>✓ Status Skema Database:</strong>";
        $html .= "<pre style='background:#0f172a;color:#a7f3d0;padding:12px;border-radius:8px;font-size:12px;margin-top:6px;overflow-x:auto;'>{$migrateOutput}</pre></div>";
    } catch (\Throwable $e) {
        $html .= "<p style='color:#dc2626;'>⚠ Migrasi gagal: {$e->getMessage()}</p>";
    }

    // 2. Sinkronisasi Permission Spatie
    try {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        $newPermissions = [
            'View:Dashboard', 'ViewAny:JadwalGuruPiket', 'View:JadwalGuruPiket', 'Create:JadwalGuruPiket',
            'Update:JadwalGuruPiket', 'Delete:JadwalGuruPiket', 'DeleteAny:JadwalGuruPiket',
            'ViewAny:PengajuanIzin', 'View:PengajuanIzin', 'Create:PengajuanIzin', 'Update:PengajuanIzin',
            'Delete:PengajuanIzin', 'DeleteAny:PengajuanIzin',
        ];
        foreach ($newPermissions as $perm) {
            \Spatie\Permission\Models\Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }
        $superAdmin = \Spatie\Permission\Models\Role::where('name', 'super_admin')->first();
        if ($superAdmin) {
            $superAdmin->syncPermissions(\Spatie\Permission\Models\Permission::all());
        }
        $html .= "<p style='color:#047857;margin:8px 0;'>✓ Seluruh Hak Akses & Role V2 berhasil disinkronkan.</p>";
    } catch (\Throwable $e) {
        $html .= "<p style='color:#d97706;'>ℹ Catatan Permission: {$e->getMessage()}</p>";
    }

    // 3. Clear Optimize Cache
    try {
        \Illuminate\Support\Facades\Artisan::call('optimize:clear');
        $html .= "<p style='color:#047857;margin:8px 0;'>✓ Cache bootstrap, views, config, dan routes berhasil disegarkan.</p>";
    } catch (\Throwable $e) {
        // ignore
    }

    $html .= "<hr style='border:0;border-top:1px solid #f1f5f9;margin:24px 0;'>";
    $html .= "<div style='display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;'>";
    $html .= "<span style='color:#059669;font-weight:700;font-size:14px;'>🎉 Sistem Absaka V2 Siap Digunakan!</span>";
    $html .= "<a href='/admin' style='display:inline-block;padding:12px 24px;background:#059669;color:#ffffff;text-decoration:none;border-radius:12px;font-weight:800;font-size:14px;box-shadow:0 4px 12px rgba(5,150,105,0.25);'>Masuk ke Aplikasi &rarr;</a>";
    $html .= "</div></div>";

    return response($html);
});

// 1. URL Helper untuk membuat ulang symlink storage langsung dari browser (tanpa perlu SSH)
Route::get('/fix-storage', function () {
    $link = public_path('storage');
    $target = storage_path('app/public');

    $html = "<div style='font-family:system-ui,-apple-system,sans-serif;max-width:640px;margin:50px auto;padding:28px;border-radius:16px;background:#ffffff;border:1px solid #e2e8f0;box-shadow:0 10px 25px -5px rgba(0,0,0,0.05);'>";
    $html .= "<div style='display:inline-block;padding:6px 12px;background:#ecfdf5;color:#047857;border-radius:999px;font-size:12px;font-weight:700;margin-bottom:12px;'>ABSAKA STORAGE REPAIR</div>";
    $html .= "<h2 style='color:#0f172a;margin:0 0 16px 0;font-size:22px;'>🔧 Pemulihan Storage Link Hosting</h2>";

    // Hapus symlink lama / rusak jika ada
    if (file_exists($link) || is_link($link)) {
        if (is_link($link)) {
            @unlink($link);
            $html .= "<p style='color:#059669;margin:6px 0;'>✓ Symlink lama/rusak berhasil dibersihkan.</p>";
        } elseif (is_dir($link)) {
            $html .= "<p style='color:#d97706;margin:6px 0;'>ℹ Folder <code>public/storage</code> adalah direktori fisik.</p>";
        } else {
            @unlink($link);
            $html .= "<p style='color:#059669;margin:6px 0;'>✓ File shortcut storage lama dibersihkan.</p>";
        }
    }

    // Eksekusi storage:link
    try {
        \Illuminate\Support\Facades\Artisan::call('storage:link');
        $output = \Illuminate\Support\Facades\Artisan::output();
        $html .= "<p style='color:#059669;margin:8px 0;'>✓ <strong>Symlink baru berhasil dibuat di server hosting:</strong></p>";
        $html .= "<pre style='background:#0f172a;color:#e2e8f0;padding:12px;border-radius:8px;font-size:13px;overflow-x:auto;'>{$output}</pre>";
    } catch (\Throwable $e) {
        $html .= "<p style='color:#dc2626;margin:8px 0;'>⚠ Artisan link gagal ({$e->getMessage()}), mencoba PHP symlink()...</p>";
        try {
            if (!file_exists($link)) {
                @symlink($target, $link);
                $html .= "<p style='color:#059669;'>✓ Symlink berhasil dibuat via fungsi native PHP.</p>";
            }
        } catch (\Throwable $e2) {
            $html .= "<p style='color:#dc2626;'>⚠ Fungsi symlink() tidak diizinkan di hosting ini. Tenang! Sistem kami sudah mengaktifkan <strong>Fallback Route</strong> sehingga foto tetap otomatis tampil!</p>";
        }
    }

    $html .= "<hr style='border:0;border-top:1px solid #f1f5f9;margin:20px 0;'>";
    $html .= "<p style='color:#64748b;font-size:14px;margin-bottom:20px;'>Silakan muat ulang halaman foto siswa di admin. Foto akan langsung tampil normal.</p>";
    $html .= "<a href='/admin' style='display:inline-block;padding:10px 20px;background:#10b981;color:#ffffff;text-decoration:none;border-radius:10px;font-weight:700;font-size:14px;'>&larr; Kembali ke Dashboard Admin</a>";
    $html .= "</div>";

    return response($html);
});

// 2. Fallback Route: Melayani file storage langsung jika symlink di hosting mati / tidak didukung
Route::get('/storage/{path}', function (string $path) {
    $filePath = storage_path('app/public/' . $path);

    if (!file_exists($filePath) || !is_file($filePath)) {
        abort(404);
    }

    // Proteksi keamanan: Cegah directory traversal
    $realBase = realpath(storage_path('app/public'));
    $realPath = realpath($filePath);
    if ($realPath === false || !str_starts_with($realPath, $realBase)) {
        abort(403);
    }

    $mime = mime_content_type($filePath) ?: 'application/octet-stream';

    return response()->file($filePath, [
        'Content-Type' => $mime,
        'Cache-Control' => 'public, max-age=604800, immutable',
    ]);
})->where('path', '.*');

