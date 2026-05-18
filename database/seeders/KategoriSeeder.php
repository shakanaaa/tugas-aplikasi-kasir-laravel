<?php

namespace Database\Seeders;

use App\Models\Kategori;
use Illuminate\Database\Seeder;

class KategoriSeeder extends Seeder
{
    public function run(): void
    {
        // Truncate table to prevent duplicates
        Kategori::truncate();
        Kategori::create(['nama_kategori' => 'Makanan']);
        Kategori::create(['nama_kategori' => 'Minuman']);
        Kategori::create(['nama_kategori' => 'Snack']);
    }
}