<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'KasirPro') — KasirPro</title>

    {{-- Google Fonts: DM Sans + DM Mono --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,400&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">

    {{-- Tailwind CDN (ganti dengan vite jika sudah setup) --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['DM Sans', 'sans-serif'],
                        mono: ['DM Mono', 'monospace'],
                    },
                    colors: {
                        surface: {
                            DEFAULT: '#0f0f13',
                            2: '#16161d',
                            3: '#1c1c26',
                            4: '#23232f',
                            5: '#2a2a3a',
                        },
                        accent: {
                            DEFAULT: '#7c6ef8',
                            light: '#a78bfa',
                            lighter: '#c4b5fd',
                        },
                        ok: '#34d399',
                        warn: '#fbbf24',
                        danger: '#f87171',
                    },
                    borderColor: {
                        DEFAULT: '#2a2a3a',
                    }
                }
            }
        }
    </script>

    <style>
        * { box-sizing: border-box; }
        body { background: #0f0f13; color: #f0effe; font-family: 'DM Sans', sans-serif; }
        ::-webkit-scrollbar { width: 4px; height: 4px; }
        ::-webkit-scrollbar-track { background: #16161d; }
        ::-webkit-scrollbar-thumb { background: #2a2a3a; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #7c6ef8; }

        .sidebar-item.active { background: rgba(124,110,248,.15); color: #a78bfa; }
        .sidebar-item.active svg { color: #a78bfa; }
        .sidebar-item:not(.active):hover { background: #1c1c26; color: #f0effe; }

        .prod-card:hover { border-color: #7c6ef8; background: rgba(124,110,248,.05); transform: translateY(-1px); }
        .prod-card.in-cart { border-color: #34d399; background: rgba(52,211,153,.05); }
        .prod-card { transition: all .15s ease; }

        .qty-btn:hover { background: #7c6ef8; border-color: #7c6ef8; color: white; }

        @keyframes slideUp {
            from { opacity:0; transform: translateY(8px); }
            to   { opacity:1; transform: translateY(0); }
        }
        .animate-up { animation: slideUp .2s ease forwards; }

        @keyframes toastIn {
            from { opacity:0; transform: translateX(20px); }
            to   { opacity:1; transform: translateX(0); }
        }
        .toast { animation: toastIn .25s ease forwards; }

        .modal-overlay { backdrop-filter: blur(4px); }
        .modal-box { animation: slideUp .2s ease forwards; }

        input[type=number]::-webkit-inner-spin-button,
        input[type=number]::-webkit-outer-spin-button { -webkit-appearance: none; }
    </style>
</head>
<body class="h-full flex overflow-hidden">

    {{-- SIDEBAR --}}
    <aside class="w-56 flex-shrink-0 flex flex-col bg-surface-2 border-r border-surface-5">
        {{-- Logo --}}
        <div class="px-5 py-6 border-b border-surface-5">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center" style="background: linear-gradient(135deg,#7c6ef8,#a78bfa)">
                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M7 4h10a2 2 0 012 2v1H5V6a2 2 0 012-2zm-3 5h16l-1.5 9A2 2 0 0116.5 20h-9a2 2 0 01-1.98-1.71L4 9zm8 2a1 1 0 00-1 1v2H9a1 1 0 000 2h2v2a1 1 0 002 0v-2h2a1 1 0 000-2h-2v-2a1 1 0 00-1-1z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-sm font-semibold tracking-tight">KasirPro</div>
                    <div class="text-[10px] text-gray-500 uppercase tracking-widest">Point of Sale</div>
                </div>
            </div>
        </div>

        {{-- Nav --}}
        <nav class="flex-1 py-3 px-2">
            <a href="{{ route('kasir.pos') }}"
               class="sidebar-item flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-400 mb-1 {{ request()->routeIs('kasir.pos') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/>
                </svg>
                Point of Sale
            </a>
            <a href="{{ route('kasir.produk') }}"
               class="sidebar-item flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-400 mb-1 {{ request()->routeIs('kasir.produk') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path d="M20 7H4a2 2 0 00-2 2v10a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2z"/><path d="M16 21V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v16"/>
                </svg>
                Produk
            </a>
            <a href="{{ route('kasir.transaksi') }}"
               class="sidebar-item flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-400 mb-1 {{ request()->routeIs('kasir.transaksi') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/><path d="M9 12h6M9 16h4"/>
                </svg>
                Transaksi
            </a>
        </nav>

        {{-- Footer --}}
        <div class="p-3 border-t border-surface-5">
            <div class="flex items-center gap-2 px-2 py-2 rounded-lg bg-surface-3">
                <div class="w-7 h-7 rounded-full flex items-center justify-center text-[11px] font-bold text-white flex-shrink-0"
                     style="background: linear-gradient(135deg,#7c6ef8,#a78bfa)">A</div>
                <div>
                    <div class="text-xs font-medium">Admin</div>
                    <div class="text-[10px] text-gray-500">Kasir</div>
                </div>
            </div>
        </div>
    </aside>

    {{-- MAIN --}}
    <main class="flex-1 flex flex-col overflow-hidden">
        @yield('content')
    </main>

    {{-- TOAST --}}
    <div id="toast-container" class="fixed bottom-5 right-5 z-50 flex flex-col gap-2 pointer-events-none"></div>

    {{-- GLOBAL SCRIPTS --}}
    <script>
        // Toast
        function showToast(msg, type = 'success') {
            const colors = {
                success: '#34d399',
                error: '#f87171',
                warning: '#fbbf24',
            };
            const el = document.createElement('div');
            el.className = 'toast pointer-events-auto flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium shadow-2xl';
            el.style.cssText = `background:#1c1c26;border:1px solid #2a2a3a;color:#f0effe;min-width:220px`;
            el.innerHTML = `<span style="width:8px;height:8px;border-radius:50%;background:${colors[type]};flex-shrink:0;display:inline-block"></span>${msg}`;
            document.getElementById('toast-container').appendChild(el);
            setTimeout(() => el.remove(), 3000);
        }

        // CSRF helper
        const CSRF = document.querySelector('meta[name=csrf-token]')?.content;

        async function apiPost(url, data) {
            const r = await fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                body: JSON.stringify(data),
            });
            return r.json();
        }

        async function apiDelete(url) {
            const r = await fetch(url, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            });
            return r.json();
        }

        async function apiPut(url, data) {
            const r = await fetch(url, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                body: JSON.stringify(data),
            });
            return r.json();
        }

        function rp(n) {
            return 'Rp ' + parseInt(n).toLocaleString('id-ID');
        }

        // Modal helpers
        function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
        function closeModal(id) { document.getElementById(id).classList.add('hidden'); }
    </script>

    @stack('scripts')
</body>
</html>