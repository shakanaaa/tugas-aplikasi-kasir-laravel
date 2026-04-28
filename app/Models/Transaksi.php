<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    protected $table = 'transaksi';

    protected $fillable = [
        'tanggal',
        'total_harga',
    ];

    protected $casts = [
        'tanggal'     => 'date',
        'total_harga' => 'integer',
    ];

    // Relasi ke transaksi_detail
    public function detail()
    {
        return $this->hasMany(TransaksiDetail::class, 'transaksi_id');
    }

    // Eager load detail + produk sekaligus
    public function detailWithProduk()
    {
        return $this->detail()->with('produk');
    }

    // Scope filter tanggal
    public function scopeHariIni($query)
    {
        return $query->whereDate('tanggal', today());
    }

    public function scopePeriode($query, string $dari, string $sampai)
    {
        return $query->whereBetween('tanggal', [$dari, $sampai]);
    }
}