<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class produk extends Model
{
    protected $table = 'produk';
    
    protected $fillable = [
        'id_kategori',
        'kode_produk',
        'nama',
        'harga_beli',
        'harga_jual',
        'stok',
        'stok_minimum',
        'status',
    ];
    
    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'id_kategori');
    }
    
    public function transaksiDetails()
    {
        return $this->hasMany(TransaksiDetail::class);
    }
}
