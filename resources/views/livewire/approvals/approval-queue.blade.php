<div class="space-y-5">
    <div>
        <p class="page-kicker">SDM</p>
        <h2 class="page-title">Approval Queue</h2>
        <p class="page-description">Daftar pengajuan yang membutuhkan keputusan SDM.</p>
    </div>
    <x-table-wrapper>
        <thead class="bg-slate-50">
            <tr>
                <th class="table-heading">No</th>
                <th class="table-heading">Pegawai</th>
                <th class="table-heading">Tujuan</th>
                <th class="table-heading">Perjalanan</th>
                <th class="table-heading">Rute</th>
                <th class="table-heading">Total</th>
                <th class="table-heading">Status</th>
                <th class="table-heading">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 bg-white">
            @forelse($rows as $row)
                <tr class="hover:bg-slate-50/80">
                    <td class="table-cell font-semibold text-slate-950">{{ $row['request_number'] }}</td>
                    <td class="table-cell whitespace-nowrap">{{ $row['employee'] }}</td>
                    <td class="table-cell min-w-56">{{ $row['purpose'] }}</td>
                    <td class="table-cell whitespace-nowrap">{{ $row['period'] }}</td>
                    <td class="table-cell min-w-48">{{ $row['route'] }}</td>
                    <td class="table-cell whitespace-nowrap font-semibold text-slate-950">{{ $row['currency'] }} {{ number_format($row['total'],0,',','.') }}</td>
                    <td class="table-cell"><x-status-badge :status="$row['status']" /></td>
                    <td class="table-cell"><a href="{{ route('approvals.detail', $row['id']) }}" class="text-link">Review</a></td>
                </tr>
            @empty
                <tr><td colspan="8" class="px-4 py-8"><x-empty-state title="Tidak ada pengajuan submitted" /></td></tr>
            @endforelse
        </tbody>
    </x-table-wrapper>
    <div>
        {{ $rows->links() }}
    </div>
</div>
