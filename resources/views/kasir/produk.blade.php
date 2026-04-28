@extends('layouts.app')
@section('title', 'Manajemen Produk')

@section('content')
<div class="flex flex-col h-full overflow-hidden">

    {{-- TOPBAR --}}
    <div class="flex-shrink-0 px-7 pt-6 pb-4 border-b border-surface-5 flex items-center justify-between">
        <div>
            <h1 class="text-xl font-semibold tracking-tight">Manajemen Produk</h1>
            <p class="text-xs text-gray-500 mt-0.5" id="produk-sub">Memuat data...</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="relative">
                <input id="search-produk" type="text" placeholder="Cari produk..."
                    class="w-48 bg-surface-3 border border-surface-5 rounded-xl px-4 py-2 text-sm text-gray-200 placeholder-gray-600 outline-none focus:border-accent transition-colors"
                    oninput="filterTable(this.value)">
            </div>
            <button onclick="openModal('modal-tambah')"
                class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-white transition-all hover:opacity-90"
                style="background:linear-gradient(135deg,#7c6ef8,#a78bfa)">
                <span class="text-base leading-none">+</span> Tambah Produk
            </button>
        </div>
    </div>

    <div class="flex-1 overflow-y-auto p-7">

        {{-- STATS --}}
        <div class="grid grid-cols-3 gap-4 mb-6">
            <div class="bg-surface-3 border border-surface-5 rounded-2xl p-5">
                <div class="text-[11px] uppercase tracking-widest text-gray-500 mb-2">Total Produk</div>
                <div id="stat-total" class="text-3xl font-bold font-mono tracking-tight">—</div>
                <div class="text-xs text-ok mt-1" id="stat-total-sub">produk aktif</div>
            </div>
            <div class="bg-surface-3 border border-surface-5 rounded-2xl p-5">
                <div class="text-[11px] uppercase tracking-widest text-gray-500 mb-2">Stok Rendah</div>
                <div id="stat-low" class="text-3xl font-bold font-mono tracking-tight text-warn">—</div>
                <div class="text-xs text-warn mt-1">perlu restock</div>
            </div>
            <div class="bg-surface-3 border border-surface-5 rounded-2xl p-5">
                <div class="text-[11px] uppercase tracking-widest text-gray-500 mb-2">Nilai Inventaris</div>
                <div id="stat-nilai" class="text-2xl font-bold font-mono tracking-tight">—</div>
                <div class="text-xs text-gray-500 mt-1">total stok × harga</div>
            </div>
        </div>

        {{-- TABLE --}}
        <div class="bg-surface-2 border border-surface-5 rounded-2xl overflow-hidden">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-surface-5 bg-surface-3">
                        <th class="text-left text-[11px] uppercase tracking-widest text-gray-500 font-medium px-5 py-3">ID</th>
                        <th class="text-left text-[11px] uppercase tracking-widest text-gray-500 font-medium px-5 py-3">Nama Produk</th>
                        <th class="text-left text-[11px] uppercase tracking-widest text-gray-500 font-medium px-5 py-3">Harga</th>
                        <th class="text-left text-[11px] uppercase tracking-widest text-gray-500 font-medium px-5 py-3">Stok</th>
                        <th class="text-left text-[11px] uppercase tracking-widest text-gray-500 font-medium px-5 py-3">Status</th>
                        <th class="text-left text-[11px] uppercase tracking-widest text-gray-500 font-medium px-5 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody id="produk-tbody">
                    <tr><td colspan="6" class="px-5 py-10 text-center text-gray-600 text-sm">Memuat data...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- MODAL TAMBAH --}}
<div id="modal-tambah" class="hidden fixed inset-0 z-50 flex items-center justify-center modal-overlay" style="background:rgba(0,0,0,.75)">
    <div class="modal-box w-96 bg-surface-2 border border-surface-5 rounded-2xl overflow-hidden shadow-2xl">
        <div class="px-6 py-4 border-b border-surface-5 flex items-center justify-between">
            <span class="font-semibold text-sm">Tambah Produk Baru</span>
            <button onclick="closeModal('modal-tambah')" class="text-gray-500 hover:text-white text-lg">✕</button>
        </div>
        <div class="px-6 py-5 space-y-4">
            <div>
                <label class="block text-xs font-medium text-gray-400 mb-1.5">Nama Produk</label>
                <input id="inp-nama" type="text" placeholder="Masukkan nama produk..."
                    class="w-full bg-surface-3 border border-surface-5 rounded-xl px-4 py-2.5 text-sm text-gray-200 placeholder-gray-600 outline-none focus:border-accent transition-colors">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1.5">Harga (Rp)</label>
                    <input id="inp-harga" type="number" placeholder="0"
                        class="w-full bg-surface-3 border border-surface-5 rounded-xl px-4 py-2.5 text-sm text-gray-200 placeholder-gray-600 outline-none focus:border-accent transition-colors">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1.5">Stok Awal</label>
                    <input id="inp-stok" type="number" placeholder="0"
                        class="w-full bg-surface-3 border border-surface-5 rounded-xl px-4 py-2.5 text-sm text-gray-200 placeholder-gray-600 outline-none focus:border-accent transition-colors">
                </div>
            </div>
        </div>
        <div class="px-6 pb-5 flex gap-3 justify-end">
            <button onclick="closeModal('modal-tambah')"
                class="px-4 py-2 rounded-xl text-sm font-medium bg-surface-4 text-gray-400 border border-surface-5 hover:text-white transition-colors">Batal</button>
            <button onclick="simpanProduk()"
                class="px-5 py-2 rounded-xl text-sm font-semibold text-white hover:opacity-90 transition-all"
                style="background:linear-gradient(135deg,#7c6ef8,#a78bfa)">Simpan</button>
        </div>
    </div>
</div>

{{-- MODAL EDIT --}}
<div id="modal-edit" class="hidden fixed inset-0 z-50 flex items-center justify-center modal-overlay" style="background:rgba(0,0,0,.75)">
    <div class="modal-box w-96 bg-surface-2 border border-surface-5 rounded-2xl overflow-hidden shadow-2xl">
        <div class="px-6 py-4 border-b border-surface-5 flex items-center justify-between">
            <span class="font-semibold text-sm">Edit Produk</span>
            <button onclick="closeModal('modal-edit')" class="text-gray-500 hover:text-white text-lg">✕</button>
        </div>
        <div class="px-6 py-5 space-y-4">
            <input type="hidden" id="edit-id">
            <div>
                <label class="block text-xs font-medium text-gray-400 mb-1.5">Nama Produk</label>
                <input id="edit-nama" type="text"
                    class="w-full bg-surface-3 border border-surface-5 rounded-xl px-4 py-2.5 text-sm text-gray-200 outline-none focus:border-accent transition-colors">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1.5">Harga (Rp)</label>
                    <input id="edit-harga" type="number"
                        class="w-full bg-surface-3 border border-surface-5 rounded-xl px-4 py-2.5 text-sm text-gray-200 outline-none focus:border-accent transition-colors">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1.5">Stok</label>
                    <input id="edit-stok" type="number"
                        class="w-full bg-surface-3 border border-surface-5 rounded-xl px-4 py-2.5 text-sm text-gray-200 outline-none focus:border-accent transition-colors">
                </div>
            </div>
        </div>
        <div class="px-6 pb-5 flex gap-3 justify-end">
            <button onclick="closeModal('modal-edit')"
                class="px-4 py-2 rounded-xl text-sm font-medium bg-surface-4 text-gray-400 border border-surface-5 hover:text-white transition-colors">Batal</button>
            <button onclick="updateProduk()"
                class="px-5 py-2 rounded-xl text-sm font-semibold text-white hover:opacity-90 transition-all"
                style="background:linear-gradient(135deg,#7c6ef8,#a78bfa)">Simpan Perubahan</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const API = '/api/v1';
let allProduk = [];

async function loadProduk() {
    const res  = await fetch(`${API}/produk`, { headers: { Accept: 'application/json' } });
    const json = await res.json();
    allProduk  = json.data || [];
    renderStats(allProduk);
    renderTable(allProduk);
}

function renderStats(list) {
    const total = list.length;
    const low   = list.filter(p => p.stok <= 5 && p.stok > 0).length + list.filter(p => p.stok === 0).length;
    const nilai = list.reduce((s, p) => s + p.harga * p.stok, 0);
    document.getElementById('stat-total').textContent    = total;
    document.getElementById('stat-total-sub').textContent= `${total} produk aktif`;
    document.getElementById('stat-low').textContent      = low;
    document.getElementById('stat-nilai').textContent    = rp(nilai);
    document.getElementById('produk-sub').textContent    = `${total} produk terdaftar`;
}

const EMOJI = ['☕','🍕','🥤','🍜','🥗','🍰','🧃','🍱','🎁','🥐','🍔','🧋'];

function renderTable(list) {
    const tbody = document.getElementById('produk-tbody');
    if (!list.length) {
        tbody.innerHTML = `<tr><td colspan="6" class="px-5 py-10 text-center text-gray-600 text-sm">Tidak ada produk</td></tr>`;
        return;
    }
    tbody.innerHTML = list.map(p => {
        const status = p.status_stok;
        const badge  = status === 'habis'
            ? `<span class="px-2 py-0.5 rounded-full text-[11px] font-medium bg-red-500/10 text-red-400">Habis</span>`
            : status === 'hampir_habis'
            ? `<span class="px-2 py-0.5 rounded-full text-[11px] font-medium bg-yellow-500/10 text-yellow-400">Hampir Habis</span>`
            : `<span class="px-2 py-0.5 rounded-full text-[11px] font-medium bg-emerald-500/10 text-emerald-400">Tersedia</span>`;
        const emoji = EMOJI[p.id % EMOJI.length];
        return `
        <tr class="border-b border-surface-5/50 hover:bg-surface-3/50 transition-colors">
            <td class="px-5 py-3.5 text-xs font-mono text-gray-500">#${p.id}</td>
            <td class="px-5 py-3.5">
                <div class="flex items-center gap-2.5">
                    <span class="text-lg">${emoji}</span>
                    <span class="text-sm font-medium">${p.nama}</span>
                </div>
            </td>
            <td class="px-5 py-3.5 text-sm font-mono text-accent-light">${p.harga_fmt}</td>
            <td class="px-5 py-3.5 text-sm font-mono">${p.stok}</td>
            <td class="px-5 py-3.5">${badge}</td>
            <td class="px-5 py-3.5">
                <div class="flex gap-2">
                    <button onclick="bukaEdit(${p.id})"
                        class="px-3 py-1.5 rounded-lg text-xs font-medium bg-surface-4 border border-surface-5 text-gray-400 hover:text-white hover:border-accent transition-colors">
                        Edit
                    </button>
                    <button onclick="hapusProduk(${p.id})"
                        class="px-3 py-1.5 rounded-lg text-xs font-medium bg-red-500/10 border border-red-500/20 text-red-400 hover:bg-red-500/20 transition-colors">
                        Hapus
                    </button>
                </div>
            </td>
        </tr>`;
    }).join('');
}

function filterTable(q) {
    renderTable(allProduk.filter(p => p.nama.toLowerCase().includes(q.toLowerCase())));
}

async function simpanProduk() {
    const nama  = document.getElementById('inp-nama').value.trim();
    const harga = parseInt(document.getElementById('inp-harga').value) || 0;
    const stok  = parseInt(document.getElementById('inp-stok').value) || 0;
    if (!nama) { showToast('Nama produk wajib diisi', 'warning'); return; }

    const res = await apiPost(`${API}/produk`, { nama, harga, stok });
    if (res.data) {
        showToast('Produk berhasil ditambahkan!');
        closeModal('modal-tambah');
        document.getElementById('inp-nama').value  = '';
        document.getElementById('inp-harga').value = '';
        document.getElementById('inp-stok').value  = '';
        loadProduk();
    } else {
        showToast(res.message || 'Gagal menyimpan', 'error');
    }
}

function bukaEdit(id) {
    const p = allProduk.find(x => x.id === id);
    if (!p) return;
    document.getElementById('edit-id').value   = p.id;
    document.getElementById('edit-nama').value = p.nama;
    document.getElementById('edit-harga').value= p.harga;
    document.getElementById('edit-stok').value = p.stok;
    openModal('modal-edit');
}

async function updateProduk() {
    const id    = document.getElementById('edit-id').value;
    const nama  = document.getElementById('edit-nama').value.trim();
    const harga = parseInt(document.getElementById('edit-harga').value) || 0;
    const stok  = parseInt(document.getElementById('edit-stok').value) || 0;

    const res = await apiPut(`${API}/produk/${id}`, { nama, harga, stok });
    if (res.data) {
        showToast('Produk berhasil diperbarui!');
        closeModal('modal-edit');
        loadProduk();
    } else {
        showToast(res.message || 'Gagal memperbarui', 'error');
    }
}

async function hapusProduk(id) {
    if (!confirm('Yakin hapus produk ini?')) return;
    const res = await apiDelete(`${API}/produk/${id}`);
    showToast(res.message || 'Produk dihapus', res.message ? 'success' : 'error');
    loadProduk();
}

loadProduk();
</script>
@endpush