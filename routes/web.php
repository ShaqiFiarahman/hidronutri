<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\RekomendasiController;
use App\Http\Controllers\DiagnosaController;
use App\Http\Controllers\SesiTanamController;
use App\Http\Controllers\RiwayatController;
use App\Http\Controllers\DashboardController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::get('/rekomendasi', [RekomendasiController::class, 'index'])->name('rekomendasi');
Route::post('/rekomendasi', [RekomendasiController::class, 'proses']);
Route::get('/hasil', [RekomendasiController::class, 'hasil']);
Route::get('/cek-kondisi', [DiagnosaController::class, 'index'])->name('cek-kondisi');
Route::post('/cek-kondisi/diagnosa', [DiagnosaController::class, 'diagnosa']);
Route::get('/jadwal', [SesiTanamController::class, 'jadwal'])->name('jadwal');
Route::get('/riwayat', [RiwayatController::class, 'index'])->name('riwayat');
Route::post('/sesi-tanam', [SesiTanamController::class, 'store']);
Route::patch('/sesi-tanam/{id}/panen', [SesiTanamController::class, 'panen']);
