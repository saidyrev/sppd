<?php
// File: routes/web.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PegawaiController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Dashboard (sementara redirect ke pegawai)
Route::get('/', function () {
    return redirect()->route('pegawai.index');
})->name('dashboard');

// Routes untuk CRUD Pegawai
Route::resource('pegawai', PegawaiController::class);

// API Routes untuk AJAX (jika diperlukan)
Route::prefix('api')->group(function () {
    Route::get('/pegawai', [PegawaiController::class, 'apiIndex'])->name('api.pegawai.index');
});

// Routes untuk Surat Tugas (akan dibuat nanti)
// Route::resource('surat-tugas', SuratTugasController::class);

// Routes untuk SPPD (akan dibuat nanti)  
// Route::resource('sppd', SppdController::class);

// Routes untuk Laporan (akan dibuat nanti)
// Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');