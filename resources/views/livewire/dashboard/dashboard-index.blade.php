<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="page-kicker">Overview</p>
            <h2 class="page-title">Dashboard</h2>
            <p class="page-description">Ringkasan data perjalanan dinas berdasarkan role dan status terbaru.</p>
        </div>
        <a href="{{ route('trips.form') }}" class="btn-primary w-full sm:w-auto">Buat Pengajuan</a>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">
        @foreach($stats as $stat)
            <x-stat-card :label="$stat['label']" :value="$stat['value']" />
        @endforeach
    </div>

    <div class="app-card p-5">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h3 class="text-base font-semibold text-slate-950">Prioritas kerja</h3>
                <p class="mt-1 text-sm text-slate-600">Gunakan menu samping untuk membuka antrean approval, draft, atau histori pengajuan.</p>
            </div>
            <span class="inline-flex w-fit rounded-full bg-cyan-50 px-3 py-1 text-xs font-semibold text-cyan-800 ring-1 ring-inset ring-cyan-200">Real-time Livewire</span>
        </div>
    </div>
</div>

