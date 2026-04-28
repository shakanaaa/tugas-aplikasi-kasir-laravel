<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProdukRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $produkId = $this->route('produk');

        return [
            'nama'  => 'required|string|max:255',
            'harga' => 'required|integer|min:0',
            'stok'  => 'required|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'nama.required'  => 'Nama produk wajib diisi.',
            'harga.required' => 'Harga produk wajib diisi.',
            'harga.min'      => 'Harga tidak boleh minus.',
            'stok.required'  => 'Stok produk wajib diisi.',
            'stok.min'       => 'Stok tidak boleh minus.',
        ];
    }
}