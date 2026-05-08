<div class="space-y-5">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="page-kicker">Pengajuan</p>
            <h2 class="page-title">Daftar Pengajuan Saya</h2>
            <p class="page-description">Pantau draft, pengajuan terkirim, dan hasil keputusan perjalanan dinas.</p>
        </div>
        <a href="{{ route('trips.form') }}" class="btn-primary w-full sm:w-auto">Buat Pengajuan</a>
    </div>
    <x-table-wrapper>
        <thead class="bg-slate-50">
            <tr>
                <th class="table-heading">No</th>
                <th class="table-heading">Tujuan</th>
                <th class="table-heading">Tanggal</th>
                <th class="table-heading">Kota</th>
                <th class="table-heading">Durasi</th>
                <th class="table-heading">Total</th>
                <th class="table-heading">Status</th>
                <th class="table-heading">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 bg-white">
        @forelse($trips as $trip)
            <tr class="hover:bg-slate-50/80">
                <td class="table-cell font-semibold text-slate-950">{{ $trip->request_number }}</td>
                <td class="table-cell min-w-56">{{ $trip->purpose }}</td>
                <td class="table-cell whitespace-nowrap">{{ $trip->departure_date->format('Y-m-d') }} s/d {{ $trip->return_date->format('Y-m-d') }}</td>
                <td class="table-cell min-w-48">{{ $trip->originCity->name }} -> {{ $trip->destinationCity->name }}</td>
                <td class="table-cell whitespace-nowrap">{{ $trip->duration_days }} hari</td>
                <td class="table-cell whitespace-nowrap font-semibold text-slate-950">{{ $trip->allowance_currency }} {{ number_format($trip->allowance_total,0,',','.') }}</td>
                <td class="table-cell"><x-status-badge :status="$trip->status" /></td>
                <td class="table-cell"><a class="text-link" href="{{ route('trips.detail', $trip->id) }}">Detail</a></td>
            </tr>
        @empty
            <tr><td colspan="8" class="px-4 py-8"><x-empty-state title="Belum ada pengajuan" /></td></tr>
        @endforelse
        </tbody>
    </x-table-wrapper>
    <div>
        {{ $trips->links() }}
    </div>
</div>
