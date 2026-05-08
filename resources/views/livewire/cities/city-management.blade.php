<div class="space-y-6">
    <div>
        <p class="page-kicker">Master Data</p>
        <h2 class="page-title">Master Data Kota</h2>
        <p class="page-description">Kelola kota, koordinat, dan klasifikasi wilayah untuk perhitungan jarak dan uang saku.</p>
    </div>

    <div class="grid gap-6 lg:grid-cols-[22rem_minmax(0,1fr)]">
        <section class="app-card p-5 lg:p-6">
            <h3 class="text-base font-semibold text-slate-950">Tambah / Edit Kota</h3>
            <div class="mt-5 space-y-4">
                <x-form-input label="Nama Kota" name="name" />
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-1">
                    <x-form-input label="Latitude" name="latitude" />
                    <x-form-input label="Longitude" name="longitude" />
                </div>
                <x-form-input label="Provinsi" name="province_name" />
                <x-form-input label="Pulau" name="island_name" />
                <label class="flex items-center justify-between gap-3 rounded-md border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm font-medium text-slate-700">
                    <span>Luar Negeri</span>
                    <input type="checkbox" wire:model.live="is_foreign" class="h-4 w-4 rounded border-slate-300 text-cyan-700 focus:ring-cyan-600">
                </label>
                <label class="flex items-center justify-between gap-3 rounded-md border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm font-medium text-slate-700">
                    <span>Aktif</span>
                    <input type="checkbox" wire:model.live="is_active" class="h-4 w-4 rounded border-slate-300 text-cyan-700 focus:ring-cyan-600">
                </label>
                <div class="flex flex-col-reverse gap-3 pt-2 sm:flex-row">
                    <button wire:click="resetForm" class="btn-secondary flex-1">Reset</button>
                    <button wire:click="save" class="btn-primary flex-1">{{ $editing_city_id ? 'Update Kota' : 'Simpan Kota' }}</button>
                </div>
            </div>
        </section>

        <section class="space-y-3">
            <input wire:model.live.debounce.300ms="search" placeholder="Cari kota atau provinsi" class="field-control" />
            <x-table-wrapper>
                <thead class="bg-slate-50">
                    <tr>
                        <th class="table-heading">Kota</th>
                        <th class="table-heading">Provinsi</th>
                        <th class="table-heading">Pulau</th>
                        <th class="table-heading">Tipe</th>
                        <th class="table-heading">Status</th>
                        <th class="table-heading">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($rows as $row)
                        <tr class="hover:bg-slate-50/80">
                            <td class="table-cell font-semibold text-slate-950">{{ $row['name'] }}</td>
                            <td class="table-cell">{{ $row['province_name'] }}</td>
                            <td class="table-cell">{{ $row['island_name'] }}</td>
                            <td class="table-cell">{{ $row['is_foreign'] ? 'Luar Negeri' : 'Domestik' }}</td>
                            <td class="table-cell">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset {{ $row['is_active'] ? 'bg-emerald-50 text-emerald-700 ring-emerald-200' : 'bg-slate-100 text-slate-600 ring-slate-200' }}">{{ $row['is_active'] ? 'Aktif' : 'Nonaktif' }}</span>
                            </td>
                            <td class="table-cell">
                                <div class="flex flex-wrap gap-3">
                                    <button wire:click="edit({{ $row['id'] }})" class="text-link">Edit</button>
                                    @if($row['is_active'])<button wire:click="deactivate({{ $row['id'] }})" class="text-link text-rose-700 hover:text-rose-800">Nonaktifkan</button>@endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-8"><x-empty-state title="Data kota tidak ditemukan" /></td></tr>
                    @endforelse
                </tbody>
            </x-table-wrapper>
            <div>
                {{ $rows->links() }}
            </div>
        </section>
    </div>
</div>

