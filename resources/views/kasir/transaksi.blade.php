@extends('layouts.app')
@section('title', 'Riwayat Transaksi')

@section('content')
<div class="flex flex-col h-full overflow-hidden">

    {{-- TOPBAR --}}
    <div class="flex-shrink-0 px-7 pt-6 pb-4 border-b border-surface-5 flex items-center justify-between">
        <div>
            <h1 class="text-xl font-semibold tracking-tight">Riwayat Transaksi</h1>
            <p class="text-xs text-gray-500 mt-0.5">Semua riwayat penjualan</p>
        </div>
        <div class="flex items-center gap-2 bg-surface-3 border border-surface-5 rounded-xl p-1">
            <button onclick="setPeriode(this, 'hari')"
                class="periode-tab active px-3 py-1.5 rounded-lg text-xs font-medium transition-all">Hari Ini</button>
            <button onclick="setPeriode(this, 'minggu')"
                class="periode-tab px-3 py-1.5 rounded-lg text-xs font-medium text-gray-500 transition-all">Minggu Ini</button>
            <button onclick="setPeriode(this, 'bulan')"
                class="periode-tab px-3 py-1.5 rounded-lg text-xs font-medium text-gray-500 transition-all">Bulan Ini</button>
        </div>
    </div>

    <div class="flex-1 overflow-y-auto p-7">

        {{-- STATS --}}
        <div class="grid grid-cols-4 gap-4 mb-6">
            <div class="bg-surface-3 border border-surface-5 rounded-2xl p-5">
                <div class="text-[11px] uppercase tracking-widest text-gray-500 mb-2">Pendapatan</div>
                <div id="stat-rev" class="text-2xl font-bold font-mono tracking-tight text-ok">—</div>
                <div id="stat-rev-sub" class="text-xs text-gray-500 mt-1">hari ini</div>
            </div>
            <div class="bg-surface-3 border border-surface-5 rounded-2xl p-5">
                <div class="text-[11px] uppercase tracking-widest text-gray-500 mb-2">Transaksi</div>
                <div id="stat-trx" class="text-3xl font-bold font-mono tracking-tight">—</div>
                <div class="text-xs text-gray-500 mt-1">selesai</div>
            </div>
            <div class="bg-surface-3 border border-surface-5 rounded-2xl p-5">
                <div class="text-[11px] uppercase tracking-widest text-gray-500 mb-2">Rata-rata</div>
                <div id="stat-avg" class="text-xl font-bold font-mono tracking-tight">—</div>
                <div class="text-xs text-gray-500 mt-1">per transaksi</div>
            </div>
            <div class="bg-surface-3 border border-surface-5 rounded-2xl p-5">
                <div class="text-[11px] uppercase tracking-widest text-gray-500 mb-2">Item Terjual</div>
                <div id="stat-items" class="text-3xl font-bold font-mono tracking-tight">—</div>
                <div class="text-xs text-gray-500 mt-1">unit produk</div>
            </div>
        </div>

        {{-- TABLE --}}
        <div class="bg-surface-2 border border-surface-5 rounded-2xl overflow-hidden">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-surface-5 bg-surface-3">
                        <th class="text-left text-[11px] uppercase tracking-widest text-gray-500 font-medium px-5 py-3">No. Transaksi</th>
                        <th class="text-left text-[11px] uppercase tracking-widest text-gray-500 font-medium px-5 py-3">Tanggal</th>
                        <th class="text-left text-[11px] uppercase tracking-widest text-gray-500 font-medium px-5 py-3">Item</th>
                        <th class="text-left text-[11px] uppercase tracking-widest text-gray-500 font-medium px-5 py-3">Total</th>
                        <th class="text-left text-[11px] uppercase tracking-widest text-gray-500 font-medium px-5 py-3">Status</th>
                        <th class="text-left text-[11px] uppercase tracking-widest text-gray-500 font-medium px-5 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody id="trx-tbody">
                    <tr><td colspan="6" class="px-5 py-10 text-center text-gray-600 text-sm">Memuat data...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- MODAL DETAIL --}}
<div id="modal-detail" class="hidden fixed inset-0 z-50 flex items-center justify-center modal-overlay" style="background:rgba(0,0,0,.75)">
    <div class="modal-box w-96 bg-surface-2 border border-surface-5 rounded-2xl overflow-hidden shadow-2xl">
        <div class="px-6 py-4 border-b border-surface-5 flex items-center justify-between">
            <span class="font-semibold text-sm" id="modal-detail-title">Detail Transaksi</span>
            <button onclick="closeModal('modal-detail')" class="text-gray-500 hover:text-white text-lg">✕</button>
        </div>
        <div id="modal-detail-body" class="px-6 py-5"></div>
        <div class="px-6 pb-5 flex gap-3">
            <button onclick="closeModal('modal-detail')"
                class="flex-1 py-2.5 rounded-xl text-sm font-medium bg-surface-4 border border-surface-5 text-gray-400 hover:text-white transition-colors">Tutup</button>
            <button id="btn-batal-trx"
                class="flex-1 py-2.5 rounded-xl text-sm font-medium bg-red-500/10 border border-red-500/20 text-red-400 hover:bg-red-500/20 transition-colors">
                Batalkan
            </button>
        </div>
    </div>
</div>

<style>
    .periode-tab.active { background: #1c1c26; color: #f0effe; }
</style>
@endsection

@push('scripts')
<script>
const API = '/api/v1';
let currentPeriode = 'hari';

async function loadData(periode = 'hari') {
    // Stats
    const rs = await fetch(`${API}/transaksi/ringkasan?periode=${periode}`, { headers: { Accept: 'application/json' } });
    const stats = await rs.json();
    const labels = { hari: 'hari ini', minggu: 'minggu ini', bulan: 'bulan ini' };
    document.getElementById('stat-rev').textContent      = stats.total_fmt || 'Rp 0';
    document.getElementById('stat-rev-sub').textContent  = labels[periode] || '';
    document.getElementById('stat-trx').textContent      = stats.jumlah_transaksi ?? '0';
    document.getElementById('stat-avg').textContent      = stats.rata_rata_fmt || 'Rp 0';
    document.getElementById('stat-items').textContent    = stats.item_terjual ?? '0';

    // List
    const rl   = await fetch(`${API}/transaksi?periode=${periode}`, { headers: { Accept: 'application/json' } });
    const json = await rl.json();
    renderTable(json.data || []);
}

function renderTable(list) {
    const tbody = document.getElementById('trx-tbody');
    if (!list.length) {
        tbody.innerHTML = `<tr><td colspan="6" class="px-5 py-10 text-center text-gray-600 text-sm">Belum ada transaksi</td></tr>`;
        return;
    }
    tbody.innerHTML = list.map(t => `
        <tr class="border-b border-surface-5/50 hover:bg-surface-3/50 transition-colors">
            <td class="px-5 py-3.5">
                <span class="text-sm font-mono font-semibold" style="color:#a78bfa">TRX-${String(t.id).padStart(4,'0')}</span>
            </td>
            <td class="px-5 py-3.5 text-sm text-gray-400">${t.tanggal}</td>
            <td class="px-5 py-3.5 text-sm text-gray-400">${t.jumlah_item ?? t.detail?.length ?? '—'} item</td>
            <td class="px-5 py-3.5 text-sm font-mono font-semibold">${t.total_fmt}</td>
            <td class="px-5 py-3.5">
                <span class="px-2 py-0.5 rounded-full text-[11px] font-medium bg-emerald-500/10 text-emerald-400">Selesai</span>
            </td>
            <td class="px-5 py-3.5">
                <button onclick="lihatDetail(${t.id})"
                    class="px-3 py-1.5 rounded-lg text-xs font-medium bg-surface-4 border border-surface-5 text-gray-400 hover:text-white hover:border-accent transition-colors">
                    Detail
                </button>
            </td>
        </tr>`).join('');
}

function setPeriode(el, periode) {
    document.querySelectorAll('.periode-tab').forEach(t => {
        t.classList.remove('active');
        t.classList.add('text-gray-500');
    });
    el.classList.add('active');
    el.classList.remove('text-gray-500');
    currentPeriode = periode;
    loadData(periode);
}

async function lihatDetail(id) {
    const res  = await fetch(`${API}/transaksi/${id}`, { headers: { Accept: 'application/json' } });
    const json = await res.json();
    const t    = json.data;
    if (!t) return;

    document.getElementById('modal-detail-title').textContent = `TRX-${String(t.id).padStart(4,'0')}`;
    document.getElementById('modal-detail-body').innerHTML = `
        <div class="text-xs text-gray-500 mb-4">${t.tanggal}</div>
        <div class="space-y-2 mb-4">
            ${(t.detail || []).map(d => `
            <div class="flex justify-between items-center py-2 border-b border-surface-5/50">
                <div>
                    <div class="text-sm font-medium">${d.produk_nama}</div>
                    <div class="text-xs text-gray-500 mt-0.5">${rp(d.harga_satuan)} × ${d.jumlah}</div>
                </div>
                <div class="text-sm font-mono font-semibold">${d.subtotal_fmt}</div>
            </div>`).join('')}
        </div>
        <div class="flex justify-between font-semibold pt-1">
            <span>Total</span>
            <span class="font-mono" style="color:#a78bfa">${t.total_fmt}</span>
        </div>`;

    document.getElementById('btn-batal-trx').onclick = () => batalTransaksi(id);
    openModal('modal-detail');
}

async function batalTransaksi(id) {
    if (!confirm('Batalkan transaksi ini? Stok akan dikembalikan.')) return;
    const res = await apiDelete(`${API}/transaksi/${id}`);
    showToast(res.message || 'Transaksi dibatalkan', 'warning');
    closeModal('modal-detail');
    loadData(currentPeriode);
}

loadData('hari');
</script>
@endpush