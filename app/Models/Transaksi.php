<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    protected $table = 'transaksi';
    protected $fillable = [
        'kasir_id',
        'total_harga',
        'jumlah_bayar',
        'kembalian',
        'status',
        'metode_pembayaran',
    ];
    
    public function kasir()
    {
        return $this->belongsTo(User::class, 'kasir_id');
    }
}
