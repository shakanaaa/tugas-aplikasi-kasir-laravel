<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StokMasuk extends Model
{
    use HasFactory;

    protected $table = 'stok_masuk';
    
    protected $fillable = [
        'produk_id',
        'dicatat_oleh',
        'jumlah',
        'harga_beli',
        'supplier',
        'keterangan'
    ];

    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }

    public function pencatat()
    {
        return $this->belongsTo(User::class, 'dicatat_oleh');
    }

    protected static function booted()
    {
        static::created(function ($stokMasuk) {
            $produk = $stokMasuk->produk;
            $produk->stok += $stokMasuk->jumlah;
            $produk->save();
        });

        static::updating(function ($stokMasuk) {
            $original = $stokMasuk->getOriginal();
            if (isset($original['jumlah']) && $original['jumlah'] != $stokMasuk->jumlah) {
                $selisih = $stokMasuk->jumlah - $original['jumlah'];
                $produk = $stokMasuk->produk;
                $produk->stok += $selisih;
                $produk->save();
            }
        });

        static::deleting(function ($stokMasuk) {
            $produk = $stokMasuk->produk;
            $produk->stok -= $stokMasuk->jumlah;
            $produk->save();
        });
    }
}