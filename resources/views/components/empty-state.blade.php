@props(['title' => 'Data belum tersedia', 'description' => 'Belum ada data untuk ditampilkan.'])
<div class="rounded-lg border border-dashed border-slate-300 bg-slate-50 p-8 text-center">
    <div class="mx-auto mb-3 flex h-10 w-10 items-center justify-center rounded-full bg-white text-slate-400 ring-1 ring-slate-200">
        <span class="h-2 w-2 rounded-full bg-cyan-500"></span>
    </div>
    <p class="text-sm font-semibold text-slate-900">{{ $title }}</p>
    <p class="mx-auto mt-1 max-w-sm text-sm leading-6 text-slate-500">{{ $description }}</p>
</div>

