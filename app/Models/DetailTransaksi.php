<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailTransaksi extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaksi_id',
        'produk_id',
        'jumlah',
        'harga_satuan',
        'subtotal'
    ];

    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class);
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }

    public function hitungSubtotal()
    {
        $this->subtotal = $this->jumlah * $this->harga_satuan;
        $this->save();
        return $this->subtotal;
    }

    protected static function booted()
    {
        static::creating(function ($detail) {
            $detail->subtotal = $detail->jumlah * $detail->harga_satuan;
        });

        static::saving(function ($detail) {
            $detail->subtotal = $detail->jumlah * $detail->harga_satuan;
        });
    }
}