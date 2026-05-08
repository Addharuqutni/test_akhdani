<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Sistem Perjalanan Dinas' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-stone-50 text-slate-800">
@auth
    <div class="flex min-h-screen" x-data="{sidebarOpen: false}" @keydown.escape.window="sidebarOpen = false">
        <x-sidebar />
        <div
            class="fixed inset-0 z-30 bg-slate-950/40 lg:hidden"
            x-show="sidebarOpen"
            x-transition.opacity
            @click="sidebarOpen = false"
            aria-hidden="true"
        ></div>
        <div class="flex min-w-0 flex-1 flex-col">
            <x-topbar />
            <main class="mx-auto w-full max-w-7xl flex-1 px-4 py-6 sm:px-6 lg:px-8">
                <x-flash-message />
                {{ $slot }}
            </main>
        </div>
    </div>
@else
    <main class="min-h-screen">
        <x-flash-message />
        {{ $slot }}
    </main>
@endauth
@livewireScripts
</body>
</html>

