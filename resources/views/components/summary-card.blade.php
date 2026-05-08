@props(['summary'])
<div class="app-card-muted p-5">
    <div class="flex items-center justify-between gap-3">
        <h3 class="text-sm font-semibold text-slate-950">Ringkasan Kalkulasi</h3>
        <span class="rounded-full bg-cyan-100 px-2.5 py-1 text-xs font-semibold text-cyan-800">Estimasi</span>
    </div>
    <dl class="mt-4 grid grid-cols-1 gap-3 text-sm sm:grid-cols-2">
        <div class="rounded-md bg-white p-3 ring-1 ring-slate-200">
            <dt class="text-xs font-medium text-slate-500">Durasi</dt>
            <dd class="mt-1 font-semibold text-slate-950">{{ $summary['duration_days'] }} hari</dd>
        </div>
        <div class="rounded-md bg-white p-3 ring-1 ring-slate-200">
            <dt class="text-xs font-medium text-slate-500">Jarak</dt>
            <dd class="mt-1 font-semibold text-slate-950">{{ number_format($summary['distance_km'],2) }} km</dd>
        </div>
        <div class="rounded-md bg-white p-3 ring-1 ring-slate-200">
            <dt class="text-xs font-medium text-slate-500">Kategori</dt>
            <dd class="mt-1 font-semibold text-slate-950">{{ $summary['allowance_rule_label'] ?? $summary['allowance_rule_type'] }}</dd>
        </div>
        <div class="rounded-md bg-white p-3 ring-1 ring-slate-200">
            <dt class="text-xs font-medium text-slate-500">Nominal/Hari</dt>
            <dd class="mt-1 font-semibold text-slate-950">{{ $summary['allowance_currency'] }} {{ number_format($summary['allowance_per_day'],0,',','.') }}</dd>
        </div>
        <div class="rounded-lg bg-slate-950 p-4 text-white sm:col-span-2">
            <dt class="text-xs font-medium text-slate-300">Total Uang Saku</dt>
            <dd class="mt-1 text-2xl font-semibold tracking-tight">{{ $summary['allowance_currency'] }} {{ number_format($summary['allowance_total'],0,',','.') }}</dd>
        </div>
    </dl>
</div>

