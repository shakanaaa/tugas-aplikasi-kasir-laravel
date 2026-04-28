<?php

use App\Http\Controllers\Api\ProdukController;
use App\Http\Controllers\Api\TransaksiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — Kasir App
|--------------------------------------------------------------------------
|
| Base URL: /api
|
| Semua response dalam format JSON.
| Tambahkan middleware 'auth:sanctum' jika pakai autentikasi.
|
*/

Route::prefix('v1')->group(function () {

    // ---------------------------------------------------------------
    // PRODUK
    // ---------------------------------------------------------------
    Route::prefix('produk')->controller(ProdukController::class)->group(function () {

        Route::get('/',              'index');       // GET    /api/v1/produk
        Route::post('/',             'store');       // POST   /api/v1/produk
        Route::get('/stok-rendah',   'stokRendah'); // GET    /api/v1/produk/stok-rendah?threshold=5

        Route::get('/{produk}',      'show');        // GET    /api/v1/produk/{id}
        Route::put('/{produk}',      'update');      // PUT    /api/v1/produk/{id}
        Route::delete('/{produk}',   'destroy');     // DELETE /api/v1/produk/{id}
        Route::patch('/{produk}/stok', 'updateStok'); // PATCH  /api/v1/produk/{id}/stok
    });

    // ---------------------------------------------------------------
    // TRANSAKSI
    // ---------------------------------------------------------------
    Route::prefix('transaksi')->controller(TransaksiController::class)->group(function () {

        Route::get('/',            'index');      // GET    /api/v1/transaksi?periode=hari
        Route::post('/',           'store');      // POST   /api/v1/transaksi
        Route::get('/ringkasan',   'ringkasan'); // GET    /api/v1/transaksi/ringkasan?periode=minggu

        Route::get('/{transaksi}',    'show');    // GET    /api/v1/transaksi/{id}
        Route::delete('/{transaksi}', 'destroy'); // DELETE /api/v1/transaksi/{id}
    });
});