<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AplikasiController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AtributController;
use App\Http\Controllers\AtributTambahanController;
use App\Http\Controllers\LinimasaController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\ProyekController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\PendataanController;
use App\Http\Controllers\SuperAdmin\DashboardController as SuperAdminDashboard;
use App\Http\Controllers\SuperAdmin\LogAktivitasController;

// ─── Guest ───────────────────────────────────────────────────────────────────
Route::middleware(['guest', 'throttle:6,1'])->group(function () {
    Route::get('/', fn () => redirect()->route('login'));
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// ─── Password Reset ───────────────────────────────────────────────────────────
Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

// ─── Authenticated ────────────────────────────────────────────────────────────
Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/last-update', [DashboardController::class, 'getLastUpdate'])->name('last.update');
    Route::get('/chart-data', [AplikasiController::class, 'getChartData']);

    // Aplikasi
    Route::prefix('aplikasi')->group(function () {
        Route::get('/', [AplikasiController::class, 'index'])->name('aplikasi.index');
        Route::get('/create', [AplikasiController::class, 'create'])->name('aplikasi.create');
        Route::post('/', [AplikasiController::class, 'store'])->name('aplikasi.store');
        Route::get('/export', [AplikasiController::class, 'export'])->name('aplikasi.export');
        Route::get('/{id}', [AplikasiController::class, 'show'])->name('aplikasi.show');
        Route::get('/{id}/detail', [AplikasiController::class, 'detail'])->name('aplikasi.detail');
        Route::get('/{id}/edit', [AplikasiController::class, 'edit'])->name('aplikasi.edit');
        Route::put('/{id}', [AplikasiController::class, 'update'])->name('aplikasi.update');
        Route::delete('/{id}', [AplikasiController::class, 'destroy'])->name('aplikasi.destroy');
        Route::get('/{id}/atribut', [AplikasiController::class, 'getAtribut']);
        Route::put('/{id}/atribut', [AplikasiController::class, 'updateAtribut'])->name('aplikasi.updateAtribut');
        Route::post('/{id}/atribut', [AtributTambahanController::class, 'updateAtributValues'])->name('aplikasi.atribut.update');
    });

    // Atribut
    Route::prefix('atribut')->group(function () {
        Route::get('/', [AtributController::class, 'index'])->name('atribut.index');
        Route::get('/create', [AtributController::class, 'create'])->name('atribut.create');
        Route::post('/', [AtributController::class, 'store'])->name('atribut.store');
        Route::post('/check-duplicate', [AtributController::class, 'checkDuplicate'])->name('atribut.check-duplicate');
        Route::get('/{id}/detail', [AtributController::class, 'detail'])->name('atribut.detail');
        Route::get('/{id}/edit', [AtributController::class, 'edit'])->name('atribut.edit');
        Route::put('/{id}', [AtributController::class, 'update'])->name('atribut.update');
        Route::delete('/{id}', [AtributController::class, 'destroy'])->name('atribut.destroy');
        Route::put('/{id_aplikasi}/nilai', [AtributController::class, 'updateNilai'])->name('atribut.updateNilai');
        Route::delete('/{id_aplikasi}/{id_atribut}', [AtributController::class, 'removeFromApp'])->name('atribut.removeFromApp');
    });

    // Linimasa
    Route::prefix('linimasa')->group(function () {
        Route::get('/', [LinimasaController::class, 'index'])->name('linimasa.index');
        Route::post('/store', [LinimasaController::class, 'store'])->name('linimasa.store');
        Route::get('/{id}/edit', [LinimasaController::class, 'edit'])->name('linimasa.edit');
        Route::put('/{id}', [LinimasaController::class, 'update'])->name('linimasa.update');
        Route::delete('/{id}', [LinimasaController::class, 'destroy'])->name('linimasa.destroy');
    });

    // Pegawai
    Route::prefix('pegawai')->group(function () {
        Route::get('/', [PegawaiController::class, 'index'])->name('pegawai.index');
        Route::get('/create', [PegawaiController::class, 'create'])->name('pegawai.create');
        Route::post('/', [PegawaiController::class, 'store'])->name('pegawai.store');
        Route::get('/{pegawai}/edit', [PegawaiController::class, 'edit'])->name('pegawai.edit');
        Route::put('/{id}', [PegawaiController::class, 'update'])->name('pegawai.update');
        Route::delete('/{pegawai}', [PegawaiController::class, 'destroy'])->name('pegawai.destroy');
    });

    // Proyek
    Route::prefix('proyek')->group(function () {
        Route::get('/', [ProyekController::class, 'index'])->name('proyek.index');
        Route::get('/create', [ProyekController::class, 'create'])->name('proyek.create');
        Route::post('/', [ProyekController::class, 'store'])->name('proyek.store');
        Route::get('/{proyek}/edit', [ProyekController::class, 'edit'])->name('proyek.edit');
        Route::put('/{id}', [ProyekController::class, 'update'])->name('proyek.update');
        Route::delete('/{proyek}', [ProyekController::class, 'destroy'])->name('proyek.destroy');
    });

    // Kategori
    Route::prefix('kategori')->group(function () {
        Route::get('/', [KategoriController::class, 'index'])->name('kategori.index');
        Route::get('/create', [KategoriController::class, 'create'])->name('kategori.create');
        Route::post('/', [KategoriController::class, 'store'])->name('kategori.store');
        Route::get('/{kategori}/edit', [KategoriController::class, 'edit'])->name('kategori.edit');
        Route::put('/{id}', [KategoriController::class, 'update'])->name('kategori.update');
        Route::delete('/{kategori}', [KategoriController::class, 'destroy'])->name('kategori.destroy');
    });

    // Pendataan
    Route::prefix('pendataan')->group(function () {
        Route::get('/', [PendataanController::class, 'index'])->name('pendataan.index');
        Route::post('/store', [PendataanController::class, 'store'])->name('pendataan.store');
        Route::put('/update/{id}', [PendataanController::class, 'update'])->name('pendataan.update');
        Route::delete('/delete/{id}', [PendataanController::class, 'destroy'])->name('pendataan.destroy');
    });

    // Profil
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('profile.index');
        Route::get('/edit', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/', [ProfileController::class, 'update'])->name('profile.update');
        Route::put('/password', [ProfileController::class, 'updatePassword'])->name('profile.updatePassword');
    });

    // Admin
    Route::prefix('admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/', [AdminController::class, 'index'])->name('admin.index');
        Route::get('/create', [AdminController::class, 'create'])->name('admin.create');
        Route::post('/', [AdminController::class, 'store'])->name('admin.store');
        Route::get('/{admin}/edit', [AdminController::class, 'edit'])->name('admin.edit');
        Route::put('/{admin}', [AdminController::class, 'update'])->name('admin.update');
        Route::delete('/{admin}', [AdminController::class, 'destroy'])->name('admin.destroy');
    });

    // Super Admin
    Route::prefix('super-admin')->middleware(\App\Http\Middleware\CheckRole::class . ':super_admin')->group(function () {
        Route::get('/dashboard', [SuperAdminDashboard::class, 'index'])->name('super-admin.dashboard');
        Route::get('/log/export', [LogAktivitasController::class, 'export'])->name('super-admin.log.export');
    });
});
