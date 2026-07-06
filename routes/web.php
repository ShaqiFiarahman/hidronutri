<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\RekomendasiController;
use App\Http\Controllers\DiagnosaController;
use App\Http\Controllers\SesiTanamController;
use App\Http\Controllers\RiwayatController;
use App\Http\Controllers\DashboardController;

use App\Http\Controllers\LogPerawatanController;
use App\Http\Controllers\SupabaseAuthController;
use App\Http\Middleware\SupabaseAuthMiddleware;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::get('/rekomendasi', [RekomendasiController::class, 'index'])->name('rekomendasi');
Route::post('/rekomendasi', [RekomendasiController::class, 'proses']);
Route::get('/hasil', [RekomendasiController::class, 'hasil']);

// Auth Routes
Route::get('/login', [SupabaseAuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [SupabaseAuthController::class, 'login'])->name('login.post');
Route::get('/register', [SupabaseAuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [SupabaseAuthController::class, 'register'])->name('register.post');
Route::post('/logout', [SupabaseAuthController::class, 'logout'])->name('logout');
Route::get('/cek-kondisi', [DiagnosaController::class, 'index'])->name('cek-kondisi')->middleware(SupabaseAuthMiddleware::class);
Route::post('/cek-kondisi/diagnosa', [DiagnosaController::class, 'diagnosa'])->middleware(SupabaseAuthMiddleware::class);
Route::get('/jadwal', [SesiTanamController::class, 'jadwal'])->name('jadwal')->middleware(SupabaseAuthMiddleware::class);
Route::get('/riwayat', [RiwayatController::class, 'index'])->name('riwayat')->middleware(SupabaseAuthMiddleware::class);
Route::post('/sesi-tanam', [SesiTanamController::class, 'store'])->middleware(SupabaseAuthMiddleware::class);
Route::patch('/sesi-tanam/{id}/panen', [SesiTanamController::class, 'panen'])->middleware(SupabaseAuthMiddleware::class);
Route::post('/log-perawatan', [LogPerawatanController::class, 'store'])->middleware(SupabaseAuthMiddleware::class);
