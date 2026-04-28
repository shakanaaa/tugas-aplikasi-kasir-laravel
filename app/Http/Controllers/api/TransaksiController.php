<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TransaksiRequest;
use App\Http\Resources\TransaksiResource;
use App\Models\Produk;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TransaksiController extends Controller
{
    /**
     * GET /api/transaksi
     * Riwayat transaksi. Filter: ?dari=&sampai=&periode=hari|minggu|bulan
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Transaksi::with(['detail.produk'])->latest('tanggal')->latest('id');

        // Filter periode preset
        if ($request->filled('periode')) {
            match ($request->periode) {
                'hari'   => $query->hariIni(),
                'minggu' => $query->periode(now()->startOfWeek()->toDateString(), now()->toDateString()),
                'bulan'  => $query->periode(now()->startOfMonth()->toDateString(), now()->toDateString()),
                default  => null,
            };
        }

        // Filter tanggal custom
        if ($request->filled('dari') && $request->filled('sampai')) {
            $query->periode($request->dari, $request->sampai);
        }

        $transaksi = $query->get();

        return TransaksiResource::collection($transaksi);
    }

    /**
     * POST /api/transaksi
     * Buat transaksi baru (checkout keranjang)
     *
     * Body:
     * {
     *   "items": [
     *     { "produk_id": 1, "jumlah": 2 },
     *     { "produk_id": 3, "jumlah": 1 }
     *   ]
     * }
     */
    public function store(TransaksiRequest $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            $items      = $request->items;
            $produkIds  = collect($items)->pluck('produk_id')->unique();

            // Ambil semua produk sekaligus (hindari N+1)
            $produkMap  = Produk::whereIn('id', $produkIds)
                                ->lockForUpdate()     // cegah race condition stok
                                ->get()
                                ->keyBy('id');

            // Validasi stok semua item sebelum transaksi dibuat
            $errors = [];
            foreach ($items as $item) {
                $produk = $produkMap->get($item['produk_id']);
                if ($produk->stok < $item['jumlah']) {
                    $errors["items.{$item['produk_id']}"] =
                        "Stok {$produk->nama} tidak cukup. Tersedia: {$produk->stok}, diminta: {$item['jumlah']}.";
                }
            }

            if (!empty($errors)) {
                throw ValidationException::withMessages($errors);
            }

            // Hitung total
            $totalHarga = 0;
            $detailData = [];

            foreach ($items as $item) {
                $produk    = $produkMap->get($item['produk_id']);
                $subtotal  = $produk->harga * $item['jumlah'];
                $totalHarga += $subtotal;

                $detailData[] = [
                    'produk_id' => $produk->id,
                    'jumlah'    => $item['jumlah'],
                    'subtotal'  => $subtotal,
                ];

                // Kurangi stok
                $produk->decrement('stok', $item['jumlah']);
            }

            // Simpan header transaksi
            $transaksi = Transaksi::create([
                'tanggal'     => today(),
                'total_harga' => $totalHarga,
            ]);

            // Simpan detail (bulk insert)
            $transaksi->detail()->createMany(
                collect($detailData)->map(fn ($d) => array_merge($d, [
                    'transaksi_id' => $transaksi->id,
                ]))->all()
            );

            DB::commit();

            return response()->json([
                'message' => 'Transaksi berhasil disimpan.',
                'data'    => new TransaksiResource(
                                $transaksi->load('detail.produk')
                             ),
            ], 201);

        } catch (ValidationException $e) {
            DB::rollBack();
            throw $e;

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Transaksi gagal: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * GET /api/transaksi/{id}
     * Detail satu transaksi beserta item-itemnya
     */
    public function show(Transaksi $transaksi): JsonResponse
    {
        return response()->json([
            'data' => new TransaksiResource(
                         $transaksi->load('detail.produk')
                      ),
        ]);
    }

    /**
     * DELETE /api/transaksi/{id}
     * Batalkan transaksi & kembalikan stok
     */
    public function destroy(Transaksi $transaksi): JsonResponse
    {
        DB::beginTransaction();

        try {
            // Kembalikan stok setiap item
            foreach ($transaksi->detail as $detail) {
                Produk::where('id', $detail->produk_id)
                      ->increment('stok', $detail->jumlah);
            }

            $transaksi->detail()->delete();
            $transaksi->delete();

            DB::commit();

            return response()->json([
                'message' => 'Transaksi berhasil dibatalkan dan stok dikembalikan.',
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Gagal membatalkan transaksi: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * GET /api/transaksi/ringkasan
     * Ringkasan statistik untuk dashboard
     * Query: ?periode=hari|minggu|bulan
     */
    public function ringkasan(Request $request): JsonResponse
    {
        $periode = $request->get('periode', 'hari');

        $query = Transaksi::with('detail');

        match ($periode) {
            'hari'   => $query->hariIni(),
            'minggu' => $query->periode(now()->startOfWeek()->toDateString(), now()->toDateString()),
            'bulan'  => $query->periode(now()->startOfMonth()->toDateString(), now()->toDateString()),
            default  => $query->hariIni(),
        };

        $transaksi = $query->get();

        $totalPendapatan = $transaksi->sum('total_harga');
        $jumlahTransaksi = $transaksi->count();
        $rataRata        = $jumlahTransaksi > 0
                               ? (int) round($totalPendapatan / $jumlahTransaksi)
                               : 0;
        $itemTerjual     = $transaksi->flatMap->detail->sum('jumlah');

        // Top 5 produk terlaris
        $topProduk = TransaksiDetail::select('produk_id', DB::raw('SUM(jumlah) as total_terjual'))
            ->whereHas('transaksi', function ($q) use ($periode) {
                match ($periode) {
                    'hari'   => $q->hariIni(),
                    'minggu' => $q->periode(now()->startOfWeek()->toDateString(), now()->toDateString()),
                    'bulan'  => $q->periode(now()->startOfMonth()->toDateString(), now()->toDateString()),
                    default  => $q->hariIni(),
                };
            })
            ->with('produk:id,nama,harga')
            ->groupBy('produk_id')
            ->orderByDesc('total_terjual')
            ->limit(5)
            ->get()
            ->map(fn ($d) => [
                'produk_id'     => $d->produk_id,
                'nama'          => $d->produk?->nama,
                'total_terjual' => $d->total_terjual,
            ]);

        return response()->json([
            'periode'          => $periode,
            'total_pendapatan' => $totalPendapatan,
            'total_fmt'        => 'Rp ' . number_format($totalPendapatan, 0, ',', '.'),
            'jumlah_transaksi' => $jumlahTransaksi,
            'rata_rata'        => $rataRata,
            'rata_rata_fmt'    => 'Rp ' . number_format($rataRata, 0, ',', '.'),
            'item_terjual'     => $itemTerjual,
            'top_produk'       => $topProduk,
        ]);
    }
}