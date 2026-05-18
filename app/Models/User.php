<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory;

    protected $fillable = ['nama', 'email', 'password', 'role'];
    protected $hidden = ['password'];

    public function transaksis()
    {
        return $this->hasMany(Transaksi::class, 'kasir_id');
    }

    public function stokMasuks()
    {
        return $this->hasMany(StokMasuk::class, 'dicatat_oleh');
    }
}
