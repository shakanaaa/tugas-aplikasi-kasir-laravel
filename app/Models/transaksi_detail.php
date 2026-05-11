<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class transaksi_detail extends Model
{
    protected $table = 'transaksi_detail';
    
    protected $fillable = [
        'transaksi_id',
        'produk_id',
        'jumlah',
        'subtotal',
    ];
    
    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class);
    }
    
    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }
}
