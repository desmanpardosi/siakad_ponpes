<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Auth::routes(['register' => false, 'reset' => false, 'verify' => false]);

Route::get('/', [App\Http\Controllers\DashboardController::class, 'home'])->name('home');
Route::get('/settings', [App\Http\Controllers\DashboardController::class, 'settings'])->name('settings');
Route::post('/settings', [App\Http\Controllers\DashboardController::class, 'settings_save'])->name('settings.save');
Route::get('/pemasukan', [App\Http\Controllers\DashboardController::class, 'pemasukan'])->name('pemasukan');
Route::post('/pemasukan', [App\Http\Controllers\DashboardController::class, 'pemasukan_save'])->name('pemasukan.save');
Route::get('/pengeluaran', [App\Http\Controllers\DashboardController::class, 'pengeluaran'])->name('pengeluaran');
Route::post('/pengeluaran', [App\Http\Controllers\DashboardController::class, 'pengeluaran_save'])->name('pengeluaran.save');

Route::prefix('master')->group(function () {
    Route::get('roles', [App\Http\Controllers\DashboardController::class, 'roles'])->name('master.roles')->middleware("adminRole");
    Route::get('users', [App\Http\Controllers\DashboardController::class, 'users'])->name('master.users')->middleware("adminRole");
    Route::post('users', [App\Http\Controllers\DashboardController::class, 'users_save'])->name('master.users.save')->middleware("adminRole");
    Route::delete('users', [App\Http\Controllers\DashboardController::class, 'users_delete'])->name('master.users.delete')->middleware("adminRole");
    Route::get('assets', [App\Http\Controllers\DashboardController::class, 'assets'])->name('master.assets')->middleware("adminRole");
    Route::post('asset', [App\Http\Controllers\DashboardController::class, 'asset_save'])->name('master.asset.save')->middleware("adminRole");
    Route::delete('asset', [App\Http\Controllers\DashboardController::class, 'asset_delete'])->name('master.asset.delete')->middleware("adminRole");
    Route::get('guru', [App\Http\Controllers\DashboardController::class, 'guru'])->name('master.guru')->middleware("adminRole");
    Route::get('staff', [App\Http\Controllers\DashboardController::class, 'staff'])->name('master.staff')->middleware("adminRole");
    Route::post('staff', [App\Http\Controllers\DashboardController::class, 'staff_save'])->name('master.staff.save')->middleware("adminRole");
    Route::delete('staff', [App\Http\Controllers\DashboardController::class, 'staff_delete'])->name('master.staff.delete')->middleware("adminRole");
    Route::get('santri', [App\Http\Controllers\DashboardController::class, 'santri'])->name('master.santri')->middleware("adminRole");
    Route::post('santri', [App\Http\Controllers\DashboardController::class, 'santri_save'])->name('master.santri.save')->middleware("adminRole");
    Route::delete('santri', [App\Http\Controllers\DashboardController::class, 'santri_delete'])->name('master.santri.delete')->middleware("adminRole");
    Route::prefix('pemasukan')->group(function () {
        Route::get('kategori', [App\Http\Controllers\DashboardController::class, 'pemasukan_kategori'])->name('master.pemasukan.kategori')->middleware("adminRole");
        Route::post('kategori', [App\Http\Controllers\DashboardController::class, 'pemasukan_kategori_save'])->name('master.pemasukan.kategori.save')->middleware("adminRole");
        Route::delete('kategori', [App\Http\Controllers\DashboardController::class, 'pemasukan_kategori_delete'])->name('master.pemasukan.kategori.delete')->middleware("adminRole");
    });
    Route::prefix('pengeluaran')->group(function () {
        Route::get('kategori', [App\Http\Controllers\DashboardController::class, 'pengeluaran_kategori'])->name('master.pengeluaran.kategori')->middleware("adminRole");
        Route::post('kategori', [App\Http\Controllers\DashboardController::class, 'pengeluaran_kategori_save'])->name('master.pengeluaran.kategori.save')->middleware("adminRole");
        Route::delete('kategori', [App\Http\Controllers\DashboardController::class, 'pengeluaran_kategori_delete'])->name('master.pengeluaran.kategori.delete')->middleware("adminRole");
    });
});