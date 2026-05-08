@php
    $navLink = 'group flex items-center justify-between rounded-md px-3 py-2.5 text-sm font-semibold transition hover:bg-slate-800 hover:text-white focus-visible:outline-cyan-300';
    $activeLink = 'bg-cyan-500/15 text-white ring-1 ring-inset ring-cyan-400/30';
    $idleLink = 'text-slate-300';
@endphp

<aside
    class="fixed inset-y-0 left-0 z-40 flex w-72 transform flex-col bg-slate-950 text-slate-100 shadow-2xl shadow-slate-950/20 transition-transform duration-200 ease-in-out lg:static lg:w-68 lg:translate-x-0 lg:shadow-none"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
    aria-label="Navigasi utama"
>
    <div class="border-b border-white/10 px-5 py-5">
        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-cyan-300">Perdin</p>
        <h1 class="mt-1 text-base font-semibold leading-6 text-white">Sistem Perjalanan Dinas</h1>
        @auth
            <p class="mt-2 text-xs text-slate-400">{{ auth()->user()->role?->name ?? 'Internal' }}</p>
        @endauth
    </div>
    <nav class="flex-1 space-y-6 overflow-y-auto p-3 text-sm">
        @auth
            <div class="space-y-1">
                <p class="px-3 text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-500">Utama</p>
                <a href="{{ route('dashboard') }}" class="{{ $navLink }} {{ request()->routeIs('dashboard') ? $activeLink : $idleLink }}" @click="sidebarOpen = false">
                    <span>Dashboard</span>
                    @if(request()->routeIs('dashboard'))<span class="h-1.5 w-1.5 rounded-full bg-cyan-300"></span>@endif
                </a>
            </div>

            @if(auth()->user()->hasRole('admin'))
                <div class="space-y-1">
                    <p class="px-3 text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-500">Administrasi</p>
                    <a href="{{ route('users.index') }}" class="{{ $navLink }} {{ request()->routeIs('users.*') ? $activeLink : $idleLink }}" @click="sidebarOpen = false">User Management</a>
                    <a href="{{ route('cities.index') }}" class="{{ $navLink }} {{ request()->routeIs('cities.*') ? $activeLink : $idleLink }}" @click="sidebarOpen = false">Master Kota</a>
                </div>
            @endif

            @if(in_array(auth()->user()->role?->code, ['pegawai', 'admin', 'sdm'], true))
                <div class="space-y-1">
                    <p class="px-3 text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-500">Pengajuan</p>
                    <a href="{{ route('trips.form') }}" class="{{ $navLink }} {{ request()->routeIs('trips.form') ? $activeLink : $idleLink }}" @click="sidebarOpen = false">Buat Pengajuan</a>
                    <a href="{{ route('trips.my-list') }}" class="{{ $navLink }} {{ request()->routeIs('trips.my-list') || request()->routeIs('trips.detail') ? $activeLink : $idleLink }}" @click="sidebarOpen = false">Pengajuan Saya</a>
                    <a href="{{ route('history.index') }}" class="{{ $navLink }} {{ request()->routeIs('history.*') ? $activeLink : $idleLink }}" @click="sidebarOpen = false">Histori</a>
                </div>
            @endif

            @if(auth()->user()->hasRole('sdm'))
                <div class="space-y-1">
                    <p class="px-3 text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-500">SDM</p>
                    <a href="{{ route('approvals.queue') }}" class="{{ $navLink }} {{ request()->routeIs('approvals.*') ? $activeLink : $idleLink }}" @click="sidebarOpen = false">Approval Queue</a>
                </div>
            @endif
        @endauth
    </nav>
    @auth
        <div class="border-t border-white/10 p-4">
            <p class="text-xs text-slate-500">Masuk sebagai</p>
            <p class="truncate text-sm font-semibold text-slate-200">{{ auth()->user()->name ?? '' }}</p>
        </div>
    @endauth
</aside>
