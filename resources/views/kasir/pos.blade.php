@extends('layouts.app')
@section('title', 'Point of Sale')

@section('content')
<div class="flex flex-col h-full overflow-hidden">

    {{-- TOPBAR --}}
    <div class="flex-shrink-0 px-7 pt-6 pb-4 flex items-center justify-between border-b border-surface-5">
        <div>
            <h1 class="text-xl font-semibold tracking-tight">Point of Sale</h1>
            <p class="text-xs text-gray-500 mt-0.5">{{ now()->isoFormat('dddd, D MMMM YYYY') }}</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="relative">
                <input id="search-input" type="text" placeholder="Cari produk..."
                    class="w-52 bg-surface-3 border border-surface-5 rounded-xl px-4 py-2 text-sm text-gray-200 placeholder-gray-600 outline-none focus:border-accent transition-colors"
                    oninput="filterProduk(this.value)">
                <svg class="w-4 h-4 text-gray-600 absolute right-3 top-2.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                </svg>
            </div>
        </div>
    </div>

    {{-- BODY: PRODUK + CART --}}
    <div class="flex-1 flex overflow-hidden">

        {{-- PRODUK GRID --}}
        <div class="flex-1 overflow-y-auto p-6">
            <div id="prod-grid" class="grid grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-3">
                {{-- dirender via JS --}}
            </div>
            <div id="empty-state" class="hidden flex-col items-center justify-center py-20 text-center">
                <div class="text-5xl mb-3">📦</div>
                <p class="text-gray-500 text-sm">Produk tidak ditemukan</p>
            </div>
        </div>

        {{-- CART PANEL --}}
        <div class="w-80 flex-shrink-0 border-l border-surface-5 flex flex-col bg-surface-2">

            {{-- Cart Header --}}
            <div class="px-5 py-4 border-b border-surface-5 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="text-sm font-semibold">Keranjang</span>
                    <span id="cart-badge" class="w-5 h-5 rounded-full text-[11px] font-bold flex items-center justify-center text-white" style="background:#7c6ef8">0</span>
                </div>
                <button onclick="clearCart()" class="text-xs text-gray-500 hover:text-danger transition-colors">Bersihkan</button>
            </div>

            {{-- Cart Items --}}
            <div id="cart-items" class="flex-1 overflow-y-auto p-3">
                <div id="cart-empty" class="flex flex-col items-center justify-center h-full py-10 text-gray-600">
                    <svg class="w-12 h-12 mb-3 opacity-30" fill="none" stroke="currentColor" stroke-width="1.2" viewBox="0 0 24 24">
                        <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/>
                        <path d="M16 10a4 4 0 01-8 0"/>
                    </svg>
                    <p class="text-sm">Keranjang kosong</p>
                </div>
            </div>

            {{-- Cart Footer --}}
            <div class="p-4 border-t border-surface-5 space-y-3">
                {{-- Totals --}}
                <div class="space-y-1.5 text-sm">
                    <div class="flex justify-between text-gray-400">
                        <span>Subtotal</span>
                        <span id="subtotal" class="font-mono">Rp 0</span>
                    </div>
                    <div class="flex justify-between text-gray-400">
                        <span>Pajak (11%)</span>
                        <span id="pajak" class="font-mono">Rp 0</span>
                    </div>
                    <div class="h-px bg-surface-5 my-2"></div>
                    <div class="flex justify-between font-semibold text-base">
                        <span>Total</span>
                        <span id="grand-total" class="font-mono" style="color:#a78bfa">Rp 0</span>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex gap-2 pt-1">
                    <button onclick="clearCart()"
                        class="flex-none px-4 py-2.5 rounded-xl text-sm font-medium bg-surface-4 text-gray-400 hover:text-white border border-surface-5 hover:border-surface-5 transition-colors">
                        Batal
                    </button>
                    <button onclick="checkout()"
                        class="flex-1 py-2.5 rounded-xl text-sm font-semibold text-white transition-all hover:opacity-90 active:scale-95"
                        style="background: linear-gradient(135deg,#7c6ef8,#a78bfa)">
                        💳 Bayar Sekarang
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL STRUK --}}
<div id="modal-struk" class="hidden fixed inset-0 z-50 flex items-center justify-center modal-overlay" style="background:rgba(0,0,0,.75)">
    <div class="modal-box w-80 bg-surface-2 border border-surface-5 rounded-2xl overflow-hidden shadow-2xl">
        <div class="px-6 py-5 border-b border-surface-5 flex items-center justify-between">
            <span class="font-semibold text-sm">Struk Pembayaran</span>
            <button onclick="closeModal('modal-struk')" class="text-gray-500 hover:text-white text-lg leading-none">✕</button>
        </div>
        <div id="struk-content" class="px-6 py-5"></div>
        <div class="px-6 pb-5">
            <button onclick="closeModal('modal-struk'); clearCart();"
                class="w-full py-2.5 rounded-xl text-sm font-semibold bg-surface-3 border border-surface-5 hover:border-accent text-gray-300 hover:text-white transition-colors">
                Transaksi Baru ✓
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const API = '/api/v1';
let produkList = [];
let cart = {};

// ── INIT ──────────────────────────────────────────
async function init() {
    const res = await fetch(`${API}/produk`, { headers: { Accept: 'application/json' } });
    const json = await res.json();
    produkList = json.data || [];
    renderGrid(produkList);
}

// ── GRID ──────────────────────────────────────────
const EMOJI = ['☕','🍕','🥤','🍜','🥗','🍰','🧃','🍱','🎁','🥐','🍔','🧋'];
const COLORS = ['rgba(124,110,248,.18)','rgba(52,211,153,.18)','rgba(251,191,36,.18)','rgba(248,113,113,.18)'];

function renderGrid(list) {
    const grid = document.getElementById('prod-grid');
    const empty = document.getElementById('empty-state');
    if (!list.length) { grid.innerHTML = ''; empty.classList.remove('hidden'); return; }
    empty.classList.add('hidden');
    grid.innerHTML = list.map((p, i) => {
        const inCart = cart[p.id] ? 'in-cart' : '';
        const emoji  = EMOJI[p.id % EMOJI.length];
        const color  = COLORS[p.id % COLORS.length];
        const low    = p.stok <= 5 && p.stok > 0;
        const habis  = p.stok === 0;
        return `
        <div class="prod-card ${inCart} bg-surface-3 border border-surface-5 rounded-2xl p-4 cursor-pointer select-none animate-up"
             style="animation-delay:${i * 0.03}s"
             onclick="${habis ? '' : `addToCart(${p.id})`}">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-xl mb-3" style="background:${color}">${emoji}</div>
            <div class="text-sm font-medium leading-snug mb-1">${p.nama}</div>
            <div class="font-mono text-sm font-semibold mb-2" style="color:#a78bfa">${p.harga_fmt}</div>
            <div class="flex items-center justify-between">
                <span class="text-[11px] px-2 py-0.5 rounded-full font-medium
                    ${habis ? 'bg-red-500/10 text-red-400' : low ? 'bg-yellow-500/10 text-yellow-400' : 'bg-emerald-500/10 text-emerald-400'}">
                    ${habis ? 'Habis' : `Stok: ${p.stok}`}
                </span>
                ${cart[p.id] ? `<span class="text-[11px] text-ok font-medium">✓ ${cart[p.id]}x</span>` : ''}
            </div>
        </div>`;
    }).join('');
}

function filterProduk(q) {
    const f = produkList.filter(p => p.nama.toLowerCase().includes(q.toLowerCase()));
    renderGrid(f);
}

// ── CART ──────────────────────────────────────────
function addToCart(id) {
    const p = produkList.find(x => x.id === id);
    if (!p || p.stok === 0) return;
    const cur = cart[id] || 0;
    if (cur >= p.stok) { showToast('Stok tidak cukup', 'warning'); return; }
    cart[id] = cur + 1;
    renderCart();
    renderGrid(produkList.filter(p => document.getElementById('search-input').value
        ? p.nama.toLowerCase().includes(document.getElementById('search-input').value.toLowerCase())
        : true));
}

function changeQty(id, delta) {
    if (!cart[id]) return;
    cart[id] += delta;
    if (cart[id] <= 0) delete cart[id];
    renderCart();
    renderGrid(produkList);
}

function clearCart() {
    cart = {};
    renderCart();
    renderGrid(produkList);
}

function renderCart() {
    const container = document.getElementById('cart-items');
    const emptyEl   = document.getElementById('cart-empty');
    const ids        = Object.keys(cart).map(Number);
    const badge      = document.getElementById('cart-badge');

    const totalQty = ids.reduce((s, id) => s + cart[id], 0);
    badge.textContent = totalQty;

    if (!ids.length) {
        container.innerHTML = '';
        container.appendChild(emptyEl);
        emptyEl.classList.remove('hidden');
        updateTotals(0);
        return;
    }

    emptyEl.classList.add('hidden');
    let subtotal = 0;
    container.innerHTML = ids.map(id => {
        const p   = produkList.find(x => x.id === id);
        const sub = p.harga * cart[id];
        subtotal += sub;
        return `
        <div class="flex items-center gap-3 bg-surface-3 rounded-xl px-3 py-2.5 mb-2 animate-up">
            <div class="flex-1 min-w-0">
                <div class="text-xs font-medium truncate">${p.nama}</div>
                <div class="text-[11px] text-gray-500 font-mono mt-0.5">${p.harga_fmt} × ${cart[id]} = ${rp(sub)}</div>
            </div>
            <div class="flex items-center gap-1.5 flex-shrink-0">
                <button class="qty-btn w-6 h-6 rounded-md bg-surface-4 border border-surface-5 text-gray-400 text-sm flex items-center justify-center transition-colors"
                    onclick="changeQty(${id}, -1)">−</button>
                <span class="w-5 text-center text-xs font-bold font-mono">${cart[id]}</span>
                <button class="qty-btn w-6 h-6 rounded-md bg-surface-4 border border-surface-5 text-gray-400 text-sm flex items-center justify-center transition-colors"
                    onclick="changeQty(${id}, 1)">+</button>
            </div>
        </div>`;
    }).join('');

    updateTotals(subtotal);
}

function updateTotals(sub) {
    const pajak = Math.round(sub * 0.11);
    const grand = sub + pajak;
    document.getElementById('subtotal').textContent   = rp(sub);
    document.getElementById('pajak').textContent      = rp(pajak);
    document.getElementById('grand-total').textContent = rp(grand);
}

// ── CHECKOUT ──────────────────────────────────────
async function checkout() {
    const ids = Object.keys(cart).map(Number);
    if (!ids.length) { showToast('Keranjang kosong!', 'warning'); return; }

    const items = ids.map(id => ({ produk_id: id, jumlah: cart[id] }));

    try {
        const res  = await apiPost(`${API}/transaksi`, { items });
        if (res.data) {
            showStruk(res.data);
            showToast('Transaksi berhasil! 🎉');
            // Reload stok terbaru dari server
            const fresh = await fetch(`${API}/produk`, { headers: { Accept: 'application/json' } });
            const json  = await fresh.json();
            produkList  = json.data || [];
        } else {
            const msg = res.errors ? Object.values(res.errors).flat().join(', ') : res.message;
            showToast(msg || 'Transaksi gagal', 'error');
        }
    } catch (e) {
        showToast('Koneksi error', 'error');
    }
}

function showStruk(trx) {
    const sub  = trx.detail.reduce((s, d) => s + d.subtotal, 0);
    const pajak = trx.total_harga - sub; // approximate
    document.getElementById('struk-content').innerHTML = `
        <div class="text-center mb-4 pb-4 border-b border-dashed border-surface-5">
            <div class="w-10 h-10 rounded-full flex items-center justify-center mx-auto mb-2"
                 style="background:rgba(52,211,153,.15);color:#34d399;font-size:18px">✓</div>
            <div class="font-semibold">KasirPro</div>
            <div class="text-[11px] text-gray-500 mt-1">TRX-${String(trx.id).padStart(4,'0')} · ${trx.tanggal}</div>
        </div>
        <div class="space-y-1.5 mb-4">
            ${trx.detail.map(d => `
            <div class="flex justify-between text-xs text-gray-400">
                <span>${d.produk_nama} <span class="text-gray-600">× ${d.jumlah}</span></span>
                <span class="font-mono text-gray-300">${d.subtotal_fmt}</span>
            </div>`).join('')}
        </div>
        <div class="pt-3 border-t border-dashed border-surface-5 flex justify-between font-semibold">
            <span>Total</span>
            <span class="font-mono" style="color:#a78bfa">${trx.total_fmt}</span>
        </div>`;
    openModal('modal-struk');
}

init();
</script>
@endpush