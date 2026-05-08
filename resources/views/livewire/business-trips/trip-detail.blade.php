<div class="space-y-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <p class="page-kicker">Detail Pengajuan</p>
            <h2 class="page-title">{{ $trip['request_number'] }}</h2>
            <p class="page-description">Detail rute, durasi, estimasi uang saku, dan riwayat perubahan status.</p>
        </div>
        <x-status-badge :status="$trip['status']" />
    </div>

    <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_22rem]">
        <section class="space-y-5">
            <div class="app-card p-5 lg:p-6">
                <h3 class="text-base font-semibold text-slate-950">Informasi perjalanan</h3>
                <dl class="mt-5 grid grid-cols-1 gap-4 text-sm sm:grid-cols-2">
                    <div><dt class="text-xs font-medium text-slate-500">Pegawai</dt><dd class="mt-1 font-semibold text-slate-950">{{ $trip['employee'] }}</dd></div>
                    <div><dt class="text-xs font-medium text-slate-500">Tujuan</dt><dd class="mt-1 font-semibold text-slate-950">{{ $trip['purpose'] }}</dd></div>
                    <div><dt class="text-xs font-medium text-slate-500">Tanggal</dt><dd class="mt-1 font-semibold text-slate-950">{{ $trip['departure_date'] }} s/d {{ $trip['return_date'] }} ({{ $trip['duration_days'] }} hari)</dd></div>
                    <div><dt class="text-xs font-medium text-slate-500">Rute</dt><dd class="mt-1 font-semibold text-slate-950">{{ $trip['origin'] }} -> {{ $trip['destination'] }}</dd></div>
                    <div><dt class="text-xs font-medium text-slate-500">Jarak</dt><dd class="mt-1 font-semibold text-slate-950">{{ $trip['distance_km'] }} km</dd></div>
                </dl>
            </div>
            <x-summary-card :summary="$trip" />
        </section>

        <x-status-timeline :items="$history" />
    </div>
</div>

