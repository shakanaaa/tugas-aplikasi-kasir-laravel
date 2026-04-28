<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\TransaksiDetail;

class Produk extends Model
{
    use HasFactory;

    protected $table = 'produk';

    protected $fillable = [
        'nama',
        'harga',
        'stok',
    ];

    protected $casts = [
        'harga' => 'integer',
        'stok'  => 'integer',
    ];

    // Relasi ke transaksi_detail
    public function transaksiDetail()
    {
        return $this->hasMany(TransaksiDetail::class, 'produk_id');
    }

    // Scope: stok rendah (≤ 5)
    public function scopeStokRendah($query, int $threshold = 5)
    {
        return $query->where('stok', '<=', $threshold);
    }

    // Scope: stok habis
    public function scopeStokHabis($query)
    {
        return $query->where('stok', 0);
    }
}