<div class="space-y-6">
    <div>
        <p class="page-kicker">Administrasi</p>
        <h2 class="page-title">User Management</h2>
        <p class="page-description">Kelola akun, role, dan status akses pengguna sistem perjalanan dinas.</p>
    </div>

    <div class="grid gap-6 lg:grid-cols-[22rem_minmax(0,1fr)]">
        <section class="app-card p-5 lg:p-6">
            <h3 class="text-base font-semibold text-slate-950">Tambah / Edit User</h3>
            <div class="mt-5 space-y-4">
                <x-form-input label="Nama" name="name" />
                <x-form-input label="Username" name="username" />
                <x-form-input label="Email" name="email" type="email" />
                <x-form-input label="Password" name="password" type="password" />
                <div>
                    <label for="role_id" class="field-label">Role</label>
                    <select id="role_id" wire:model.live="role_id" class="field-control">
                        <option value="">Pilih role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role['id'] }}">{{ $role['name'] }} ({{ $role['code'] }})</option>
                        @endforeach
                    </select>
                    @error('role_id')<p class="mt-1.5 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
                </div>
                <label class="flex items-center justify-between gap-3 rounded-md border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm font-medium text-slate-700">
                    <span>Aktif</span>
                    <input type="checkbox" wire:model.live="is_active" class="h-4 w-4 rounded border-slate-300 text-cyan-700 focus:ring-cyan-600">
                </label>
                <div class="flex flex-col-reverse gap-3 pt-2 sm:flex-row">
                    <button wire:click="resetForm" class="btn-secondary flex-1">Reset</button>
                    <button wire:click="save" class="btn-primary flex-1">{{ $editing_user_id ? 'Update' : 'Simpan' }}</button>
                </div>
            </div>
        </section>

        <section class="space-y-3">
            <input wire:model.live.debounce.300ms="search" placeholder="Cari nama, username, atau email" class="field-control" />
            <x-table-wrapper>
                <thead class="bg-slate-50">
                    <tr>
                        <th class="table-heading">Nama</th>
                        <th class="table-heading">Username</th>
                        <th class="table-heading">Email</th>
                        <th class="table-heading">Role</th>
                        <th class="table-heading">Status</th>
                        <th class="table-heading">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($rows as $row)
                        <tr class="hover:bg-slate-50/80">
                            <td class="table-cell font-semibold text-slate-950">{{ $row['name'] }}</td>
                            <td class="table-cell">{{ $row['username'] }}</td>
                            <td class="table-cell">{{ $row['email'] }}</td>
                            <td class="table-cell uppercase">{{ $row['role'] }}</td>
                            <td class="table-cell">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset {{ $row['is_active'] ? 'bg-emerald-50 text-emerald-700 ring-emerald-200' : 'bg-slate-100 text-slate-600 ring-slate-200' }}">{{ $row['is_active'] ? 'Aktif' : 'Nonaktif' }}</span>
                            </td>
                            <td class="table-cell">
                                <div class="flex flex-wrap gap-3">
                                    <button wire:click="edit({{ $row['id'] }})" class="text-link">Edit</button>
                                    <button wire:click="toggleActive({{ $row['id'] }})" class="text-link {{ $row['is_active'] ? 'text-rose-700 hover:text-rose-800' : 'text-emerald-700 hover:text-emerald-800' }}">{{ $row['is_active'] ? 'Nonaktifkan' : 'Aktifkan' }}</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-8"><x-empty-state title="User tidak ditemukan" /></td></tr>
                    @endforelse
                </tbody>
            </x-table-wrapper>
            <div>
                {{ $rows->links() }}
            </div>
        </section>
    </div>
</div>

