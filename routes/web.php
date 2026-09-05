<?php

use App\Http\Controllers\EksporAbsensiController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin');
});

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
