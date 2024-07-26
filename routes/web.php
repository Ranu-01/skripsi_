<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\BarangController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\TransaksiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::middleware('auth')->group(function () {

    Route::controller(BarangController::class)->prefix('/barang')->name('barang.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
        Route::get('/show', 'show')->name('show');
        Route::get('/destroy', 'destroy')->name('destroy');
    });

    Route::controller(AuthController::class)->prefix('/auth')->name('auth.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
        Route::get('/destroy', 'destroy')->name('destroy');
        Route::get('/show', 'show')->name('show');
    });

    Route::controller(TransaksiController::class)->prefix('/transaksi')->name('transaksi.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/kasir', 'indexKasir')->name('kasir');
        Route::get('/kasir/history', 'indexPenjualan')->name('kasir.history');
        Route::get('/kasir/history/detail', 'getDetailPenjualan')->name('kasir.history.detail');
        Route::get('/persediaan-masuk', 'indexPersediaanMasuk')->name('persediaan-masuk');
        Route::post('/persediaan-masuk/store', 'storePersediaanMasuk')->name('persediaan-masuk.store');
        Route::post('/kasir/store', 'storeKasir')->name('kasir.store');
        Route::get('/persediaan-masuk/history', 'indexHistoryPersediaan')->name('persediaan-masuk.history');
        Route::get('/persediaan-masuk/history/detail', 'getDetailPersediaan')->name('persediaan-masuk.history.detail');
    });

    Route::controller(DashboardController::class)->prefix('/')->name('dashboard.')->group(function () {
        Route::get('', 'index')->name('index');
    });
});

Route::controller(AuthController::class)->prefix('/auth')->name('auth.')->group(function () {
    Route::get('/login', 'indexLogin')->name('login');
    Route::post('/login', 'login')->name('login-in');
    Route::get('/logout', 'logout')->name('logout');
});
