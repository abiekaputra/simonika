<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AplikasiAtributController;
use App\Http\Controllers\AplikasiChartController;
use App\Http\Controllers\AplikasiController;
use App\Http\Controllers\AplikasiExportController;
use App\Http\Controllers\AtributController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\LinimasaController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\PendataanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProyekController;
use App\Http\Controllers\SuperAdmin\DashboardController as SuperAdminDashboard;
use App\Http\Controllers\SuperAdmin\LogAktivitasController;
use App\Http\Middleware\CheckRole;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/', fn () => redirect()->route('login'));
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:6,1');
    Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])
        ->middleware('throttle:5,1')->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])
        ->middleware('throttle:5,1')->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/last-update', [DashboardController::class, 'getLastUpdate'])->name('last.update');
    Route::get('/chart-data', AplikasiChartController::class)->name('chart.data');

    Route::prefix('aplikasi')->name('aplikasi.')->group(function () {
        Route::get('/', [AplikasiController::class, 'index'])->name('index');
        Route::post('/', [AplikasiController::class, 'store'])->name('store');
        Route::get('/export', AplikasiExportController::class)->name('export');
        Route::get('/{aplikasi}/detail', [AplikasiController::class, 'detail'])->name('detail');
        Route::get('/{aplikasi}/edit', [AplikasiController::class, 'edit'])->name('edit');
        Route::put('/{aplikasi}', [AplikasiController::class, 'update'])->name('update');
        Route::delete('/{aplikasi}', [AplikasiController::class, 'destroy'])->name('destroy');
        Route::get('/{aplikasi}/atribut', [AplikasiAtributController::class, 'show'])->name('atribut');
        Route::match(['post', 'put'], '/{aplikasi}/atribut', [AplikasiAtributController::class, 'update'])
            ->name('atribut.update');
    });

    Route::resource('atribut', AtributController::class)->only(['index', 'store', 'edit', 'update', 'destroy']);
    Route::get('/atribut/{atribut}/detail', [AtributController::class, 'detail'])->name('atribut.detail');
    Route::resource('linimasa', LinimasaController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('pegawai', PegawaiController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('proyek', ProyekController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('kategori', KategoriController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('pendataan', PendataanController::class)->only(['index', 'store', 'update', 'destroy']);

    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.updatePassword');

    Route::prefix('admin')->middleware(CheckRole::class.':super_admin')->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('admin.index');
        Route::post('/', [AdminController::class, 'store'])->name('admin.store');
        Route::get('/{admin}/edit', [AdminController::class, 'edit'])->name('admin.edit');
        Route::put('/{admin}', [AdminController::class, 'update'])->name('admin.update');
        Route::delete('/{admin}', [AdminController::class, 'destroy'])->name('admin.destroy');
    });

    Route::prefix('super-admin')->middleware(CheckRole::class.':super_admin')->group(function () {
        Route::get('/dashboard', [SuperAdminDashboard::class, 'index'])->name('super-admin.dashboard');
        Route::get('/log/export', [LogAktivitasController::class, 'export'])->name('super-admin.log.export');
    });
});
