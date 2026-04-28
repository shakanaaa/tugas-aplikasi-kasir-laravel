<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes — KasirPro
|--------------------------------------------------------------------------
*/

// Redirect root ke POS
Route::get('/', fn () => redirect()->route('kasir.pos'));

// Halaman Kasir
Route::prefix('kasir')->name('kasir.')->group(function () {
    Route::get('/pos',       fn () => view('kasir.pos'))->name('pos');
    Route::get('/produk',    fn () => view('kasir.produk'))->name('produk');
    Route::get('/transaksi', fn () => view('kasir.transaksi'))->name('transaksi');
});

