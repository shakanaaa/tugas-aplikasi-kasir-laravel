<?php

namespace Database\Seeders;

use App\Models\Produk;
use App\Models\Kategori;
use Illuminate\Database\Seeder;

class ProdukSeeder extends Seeder
{
    public function run(): void
    {
        // Truncate table to prevent duplicates
        Produk::truncate();
        
        $kategoriMakanan = Kategori::where('nama_kategori', 'Makanan')->first();
        $kategoriMinuman = Kategori::where('nama_kategori', 'Minuman')->first();
        $kategoriSnack = Kategori::where('nama_kategori', 'Snack')->first();

        Produk::create([
            'kategori_id' => $kategoriMakanan->id,
            'kode_produk' => 'F001',
            'nama_produk' => 'Nasi Goreng',
            'harga_beli' => 10000,
            'harga_jual' => 15000,
            'stok' => 50,
            'stok_minimum' => 10,
            'status' => 'aktif'
        ]);

        Produk::create([
            'kategori_id' => $kategoriMinuman->id,
            'kode_produk' => 'D001',
            'nama_produk' => 'Es Teh Manis',
            'harga_beli' => 3000,
            'harga_jual' => 5000,
            'stok' => 100,
            'stok_minimum' => 20,
            'status' => 'aktif'
        ]);

        Produk::create([
            'kategori_id' => $kategoriSnack->id,
            'kode_produk' => 'S001',
            'nama_produk' => 'Keripik Kentang',
            'harga_beli' => 5000,
            'harga_jual' => 8000,
            'stok' => 30,
            'stok_minimum' => 5,
            'status' => 'aktif'
        ]);
    }
}