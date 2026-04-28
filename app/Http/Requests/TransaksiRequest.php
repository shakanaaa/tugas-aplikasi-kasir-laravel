<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransaksiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Array items (keranjang belanja)
            'items'              => 'required|array|min:1',
            'items.*.produk_id'  => 'required|integer|exists:produk,id',
            'items.*.jumlah'     => 'required|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'items.required'             => 'Keranjang tidak boleh kosong.',
            'items.min'                  => 'Minimal satu produk harus dipilih.',
            'items.*.produk_id.required' => 'Produk ID wajib ada di setiap item.',
            'items.*.produk_id.exists'   => 'Produk tidak ditemukan.',
            'items.*.jumlah.required'    => 'Jumlah wajib diisi.',
            'items.*.jumlah.min'         => 'Jumlah minimal 1.',
        ];
    }
}