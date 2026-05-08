@props(['status'])
@php
    $colors = [
        'draft' => 'bg-slate-100 text-slate-700 ring-slate-200',
        'submitted' => 'bg-amber-100 text-amber-800 ring-amber-200',
        'approved' => 'bg-emerald-100 text-emerald-800 ring-emerald-200',
        'rejected' => 'bg-rose-100 text-rose-800 ring-rose-200',
        'cancelled' => 'bg-zinc-200 text-zinc-700 ring-zinc-300',
    ];
@endphp
<span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset {{ $colors[$status] ?? 'bg-slate-100 text-slate-700 ring-slate-200' }}">
    {{ strtoupper($status) }}
</span>

