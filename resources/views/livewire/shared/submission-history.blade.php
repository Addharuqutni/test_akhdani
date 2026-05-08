<div class="space-y-5">
    <div>
        <p class="page-kicker">Histori</p>
        <h2 class="page-title">Histori Pengajuan</h2>
        <p class="page-description">Lacak status akhir dan waktu pembaruan pengajuan perjalanan dinas.</p>
    </div>
    <x-table-wrapper>
        <thead class="bg-slate-50">
            <tr>
                <th class="table-heading">No Pengajuan</th>
                <th class="table-heading">Pegawai</th>
                <th class="table-heading">Status Akhir</th>
                <th class="table-heading">Waktu Update</th>
                <th class="table-heading">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 bg-white">
            @forelse($rows as $row)
                <tr class="hover:bg-slate-50/80">
                    <td class="table-cell font-semibold text-slate-950">{{ $row['request_number'] }}</td>
                    <td class="table-cell">{{ $row['employee'] }}</td>
                    <td class="table-cell"><x-status-badge :status="$row['status']" /></td>
                    <td class="table-cell whitespace-nowrap">{{ $row['updated_at'] }}</td>
                    <td class="table-cell"><a href="{{ route('trips.detail', $row['id']) }}" class="text-link">Detail</a></td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-4 py-8"><x-empty-state title="Histori belum tersedia" /></td></tr>
            @endforelse
        </tbody>
    </x-table-wrapper>
    <div>
        {{ $rows->links() }}
    </div>
</div>
