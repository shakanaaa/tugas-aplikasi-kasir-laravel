<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class stock_masuk extends Model
{
    protected $table = 'stock_masuk';
    
    protected $fillable = [
        'id_produk',
        'dicatat_oleh',
        'jumlah',
        'harga_beli',
        'supplier',
        'keterangan',
    ];
    
    public function produk()
    {
        return $this->belongsTo(produk::class, 'id_produk');
    }
    
    public function user()
    {
        return $this->belongsTo(User::class, 'dicatat_oleh');
    }
}
