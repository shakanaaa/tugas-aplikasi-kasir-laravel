<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProdukRequest;
use App\Http\Resources\ProdukResource;
use App\Models\Produk;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProdukController extends Controller
{
    /**
     * GET /api/produk
     * Daftar semua produk. Bisa filter ?search=&status=
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Produk::query();

        // Filter pencarian nama
        if ($request->filled('search')) {
            $query->where('nama', 'like', '%' . $request->search . '%');
        }

        // Filter status stok: tersedia | hampir_habis | habis
        if ($request->filled('status')) {
            match ($request->status) {
                'habis'       => $query->stokHabis(),
                'hampir_habis'=> $query->stokRendah()->where('stok', '>', 0),
                'tersedia'    => $query->where('stok', '>', 5),
                default       => null,
            };
        }

        $produk = $query->orderBy('nama')->get();

        return ProdukResource::collection($produk);
    }

    /**
     * POST /api/produk
     * Tambah produk baru
     */
    public function store(ProdukRequest $request): JsonResponse
    {
        $produk = Produk::create($request->validated());

        return response()->json([
            'message' => 'Produk berhasil ditambahkan.',
            'data'    => new ProdukResource($produk),
        ], 201);
    }

    /**
     * GET /api/produk/{id}
     * Detail satu produk
     */
    public function show(Produk $produk): JsonResponse
    {
        return response()->json([
            'data' => new ProdukResource($produk),
        ]);
    }

    /**
     * PUT /api/produk/{id}
     * Update produk
     */
    public function update(ProdukRequest $request, Produk $produk): JsonResponse
    {
        $produk->update($request->validated());

        return response()->json([
            'message' => 'Produk berhasil diperbarui.',
            'data'    => new ProdukResource($produk),
        ]);
    }

    /**
     * DELETE /api/produk/{id}
     * Hapus produk
     */
    public function destroy(Produk $produk): JsonResponse
    {
        $produk->delete();

        return response()->json([
            'message' => 'Produk berhasil dihapus.',
        ]);
    }

    /**
     * PATCH /api/produk/{id}/stok
     * Update stok saja (restock / koreksi)
     * Body: { "jumlah": 50, "aksi": "tambah"|"kurang"|"set" }
     */
    public function updateStok(Request $request, Produk $produk): JsonResponse
    {
        $request->validate([
            'jumlah' => 'required|integer|min:0',
            'aksi'   => 'required|in:tambah,kurang,set',
        ]);

        $stokBaru = match ($request->aksi) {
            'tambah' => $produk->stok + $request->jumlah,
            'kurang' => max(0, $produk->stok - $request->jumlah),
            'set'    => $request->jumlah,
        };

        $produk->update(['stok' => $stokBaru]);

        return response()->json([
            'message'   => 'Stok berhasil diperbarui.',
            'stok_lama' => $produk->getOriginal('stok'),
            'stok_baru' => $stokBaru,
            'data'      => new ProdukResource($produk->fresh()),
        ]);
    }

    /**
     * GET /api/produk/stok-rendah
     * Produk dengan stok ≤ threshold (default 5)
     */
    public function stokRendah(Request $request): AnonymousResourceCollection
    {
        $threshold = $request->integer('threshold', 5);
        $produk    = Produk::stokRendah($threshold)->orderBy('stok')->get();

        return ProdukResource::collection($produk);
    }
}