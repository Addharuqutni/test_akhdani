<header class="sticky top-0 z-20 border-b border-slate-200/80 bg-white/90 backdrop-blur">
    <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-3 sm:px-6 lg:px-8">
        @auth
            <div class="flex min-w-0 items-center gap-3">
                <button type="button" class="btn-secondary px-3 lg:hidden" @click="sidebarOpen = !sidebarOpen" aria-label="Buka menu navigasi">
                    Menu
                </button>
                <div class="min-w-0">
                    <p class="truncate text-sm font-semibold text-slate-900">{{ auth()->user()->name ?? '' }}</p>
                    <p class="hidden text-xs text-slate-500 sm:block">{{ now()->translatedFormat('l, d F Y') }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="btn-secondary">Logout</button>
            </form>
        @endauth
    </div>
</header>
