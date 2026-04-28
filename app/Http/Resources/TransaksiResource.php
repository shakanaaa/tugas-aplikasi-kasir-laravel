<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransaksiResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'tanggal'     => $this->tanggal?->toDateString(),
            'total_harga' => $this->total_harga,
            'total_fmt'   => 'Rp ' . number_format($this->total_harga, 0, ',', '.'),
            'detail'      => TransaksiDetailResource::collection(
                                $this->whenLoaded('detail')
                             ),
            'jumlah_item' => $this->whenLoaded('detail',
                                fn () => $this->detail->sum('jumlah')
                             ),
            'created_at'  => $this->created_at?->toDateTimeString(),
        ];
    }
}