@props(['label','value','hint' => null])
<div class="app-card p-4">
    <div class="flex items-start justify-between gap-3">
        <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ $label }}</p>
        <span class="mt-0.5 h-2 w-2 rounded-full bg-cyan-500"></span>
    </div>
    <p class="mt-3 text-3xl font-semibold tracking-tight text-slate-950">{{ $value }}</p>
    @if($hint)<p class="mt-2 text-xs leading-5 text-slate-500">{{ $hint }}</p>@endif
</div>

