<?php

use App\Http\Controllers\EksporAbsensiController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('filament.auth.login');
});

// Letakkan rute ini di dalam kelompok rute admin atau letakkan secara mandiri
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/kelas/{kelas}/ekspor/{bulan}/{tahun}', [EksporAbsensiController::class, 'ekspor'])
        ->name('admin.kelas.ekspor');
});
