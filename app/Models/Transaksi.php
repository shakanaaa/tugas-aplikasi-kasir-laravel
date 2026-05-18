<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    protected $fillable = [
        'kasir_id',
        'no_transaksi',
        'total_harga',
        'jumlah_bayar',
        'kembalian',
        'metode_bayar',
        'status'
    ];

    public function kasir()
    {
        return $this->belongsTo(User::class, 'kasir_id');
    }

    public function detailTransaksis()
    {
        return $this->hasMany(DetailTransaksi::class);
    }

    public static function generateNoTransaksi()
    {
        $today = date('Ymd');
        $lastTransaksi = self::whereDate('created_at', today())->latest()->first();
        
        if ($lastTransaksi) {
            $lastNumber = intval(substr($lastTransaksi->no_transaksi, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }
        
        return 'TRX-' . $today . '-' . $newNumber;
    }

    public function hitungTotal()
    {
        $total = $this->detailTransaksis()->sum('subtotal');
        $this->total_harga = $total;
        $this->save();
        return $total;
    }

    public function hitungKembalian()
    {
        $this->kembalian = $this->jumlah_bayar - $this->total_harga;
        $this->save();
        return $this->kembalian;
    }
}