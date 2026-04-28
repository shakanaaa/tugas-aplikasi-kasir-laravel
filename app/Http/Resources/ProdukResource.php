<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProdukResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'nama'       => $this->nama,
            'harga'      => $this->harga,
            'harga_fmt'  => 'Rp ' . number_format($this->harga, 0, ',', '.'),
            'stok'       => $this->stok,
            'status_stok'=> $this->stok === 0
                                ? 'habis'
                                : ($this->stok <= 5 ? 'hampir_habis' : 'tersedia'),
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}