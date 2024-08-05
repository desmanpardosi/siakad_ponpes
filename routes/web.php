<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Auth::routes(['register' => false, 'reset' => false, 'verify' => false]);

Route::get('/', [App\Http\Controllers\DashboardController::class, 'home'])->name('home');
Route::get('/settings', [App\Http\Controllers\DashboardController::class, 'settings'])->name('settings');
Route::post('/settings', [App\Http\Controllers\DashboardController::class, 'settings_save'])->name('settings.save')->middleware("adminRole");
Route::get('/pemasukan', [App\Http\Controllers\DashboardController::class, 'pemasukan'])->name('pemasukan')->middleware("adminRole");
Route::post('/pemasukan', [App\Http\Controllers\DashboardController::class, 'pemasukan_save'])->name('pemasukan.save')->middleware("adminRole");
Route::get('/pengeluaran', [App\Http\Controllers\DashboardController::class, 'pengeluaran'])->name('pengeluaran')->middleware("adminRole");
Route::post('/pengeluaran', [App\Http\Controllers\DashboardController::class, 'pengeluaran_save'])->name('pengeluaran.save')->middleware("adminRole");
Route::get('/presensi/{jadwal_id?}', [App\Http\Controllers\DashboardController::class, 'presensi'])->name('presensi')->middleware("role:2");
Route::post('/presensi', [App\Http\Controllers\DashboardController::class, 'presensi_save'])->name('presensi.save')->middleware("role:2");
Route::delete('/presensi', [App\Http\Controllers\DashboardController::class, 'presensi_delete'])->name('presensi.delete')->middleware("role:2");
Route::get('/nilai/{mapel_id?}', [App\Http\Controllers\DashboardController::class, 'nilai'])->name('nilai')->middleware("role:2");
Route::post('/nilai', [App\Http\Controllers\DashboardController::class, 'nilai_save'])->name('nilai.save')->middleware("role:2");
Route::delete('/nilai', [App\Http\Controllers\DashboardController::class, 'nilai_delete'])->name('nilai.delete')->middleware("role:2");
Route::get('/transkrip-nilai', [App\Http\Controllers\DashboardController::class, 'transkrip_nilai'])->name('transkrip_nilai')->middleware("role:3");

Route::prefix('laporan')->group(function () {
    Route::get('keuangan', [App\Http\Controllers\DashboardController::class, 'laporan_keuangan'])->name('laporan.keuangan');
});

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
    Route::get('santri', [App\Http\Controllers\DashboardController::class, 'santri'])->name('master.santri');
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
    Route::get('ruangan', [App\Http\Controllers\DashboardController::class, 'ruangan'])->name('master.ruangan')->middleware("adminRole");
    Route::post('ruangan', [App\Http\Controllers\DashboardController::class, 'ruangan_save'])->name('master.ruangan.save')->middleware("adminRole");
    Route::delete('ruangan', [App\Http\Controllers\DashboardController::class, 'ruangan_delete'])->name('master.ruangan.delete')->middleware("adminRole");
    Route::get('kelas', [App\Http\Controllers\DashboardController::class, 'kelas'])->name('master.kelas')->middleware("adminRole");
    Route::post('kelas', [App\Http\Controllers\DashboardController::class, 'kelas_save'])->name('master.kelas.save')->middleware("adminRole");
    Route::delete('kelas', [App\Http\Controllers\DashboardController::class, 'kelas_delete'])->name('master.kelas.delete')->middleware("adminRole");
    Route::get('jp', [App\Http\Controllers\DashboardController::class, 'jp'])->name('master.jp')->middleware("adminRole");
    Route::post('jp', [App\Http\Controllers\DashboardController::class, 'jp_save'])->name('master.jp.save')->middleware("adminRole");
    Route::delete('jp', [App\Http\Controllers\DashboardController::class, 'jp_delete'])->name('master.jp.delete')->middleware("adminRole");
    Route::get('mapel', [App\Http\Controllers\DashboardController::class, 'mapel'])->name('master.mapel')->middleware("adminRole");
    Route::post('mapel', [App\Http\Controllers\DashboardController::class, 'mapel_save'])->name('master.mapel.save')->middleware("adminRole");
    Route::delete('mapel', [App\Http\Controllers\DashboardController::class, 'mapel_delete'])->name('master.mapel.delete')->middleware("adminRole");
    Route::get('jadwal', [App\Http\Controllers\DashboardController::class, 'jadwal'])->name('master.jadwal')->middleware("adminRole");
    Route::post('jadwal', [App\Http\Controllers\DashboardController::class, 'jadwal_save'])->name('master.jadwal.save')->middleware("adminRole");
    Route::delete('jadwal', [App\Http\Controllers\DashboardController::class, 'jadwal_delete'])->name('master.jadwal.delete')->middleware("adminRole");
    Route::get('tp', [App\Http\Controllers\DashboardController::class, 'tp'])->name('master.tp');
    Route::post('tp', [App\Http\Controllers\DashboardController::class, 'tp_save'])->name('master.tp.save')->middleware("adminRole");
    Route::delete('tp', [App\Http\Controllers\DashboardController::class, 'tp_delete'])->name('master.tp.delete')->middleware("adminRole");
});