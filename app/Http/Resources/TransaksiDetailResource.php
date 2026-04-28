<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransaksiDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'transaksi_id' => $this->transaksi_id,
            'produk_id'    => $this->produk_id,
            'produk_nama'  => $this->whenLoaded('produk', fn () => $this->produk->nama),
            'harga_satuan' => $this->whenLoaded('produk', fn () => $this->produk->harga),
            'jumlah'       => $this->jumlah,
            'subtotal'     => $this->subtotal,
            'subtotal_fmt' => 'Rp ' . number_format($this->subtotal, 0, ',', '.'),
        ];
    }
}