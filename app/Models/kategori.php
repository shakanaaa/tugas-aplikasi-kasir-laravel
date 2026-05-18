<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    use HasFactory;

    protected $fillable = ['nama_kategori'];

    // Relasi ke Produk (1 kategori punya banyak produk)
    public function produks()
    {
        return $this->hasMany(Produk::class);
    }
}