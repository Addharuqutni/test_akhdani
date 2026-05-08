<div class="space-y-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <p class="page-kicker">SDM Review</p>
            <h2 class="page-title">{{ $trip['request_number'] }}</h2>
            <p class="page-description">Periksa informasi perjalanan sebelum menyetujui atau menolak pengajuan.</p>
        </div>
        <x-status-badge :status="$trip['status']" />
    </div>

    <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_22rem]">
        <section class="space-y-5">
            <div class="app-card p-5 lg:p-6">
                <h3 class="text-base font-semibold text-slate-950">Informasi pengajuan</h3>
                <dl class="mt-5 grid gap-4 text-sm sm:grid-cols-2">
                    <div><dt class="text-xs font-medium text-slate-500">Pegawai</dt><dd class="mt-1 font-semibold text-slate-950">{{ $trip['employee'] }}</dd></div>
                    <div><dt class="text-xs font-medium text-slate-500">Tujuan</dt><dd class="mt-1 font-semibold text-slate-950">{{ $trip['purpose'] }}</dd></div>
                    <div><dt class="text-xs font-medium text-slate-500">Durasi</dt><dd class="mt-1 font-semibold text-slate-950">{{ $trip['duration_days'] }} hari</dd></div>
                    <div><dt class="text-xs font-medium text-slate-500">Jarak</dt><dd class="mt-1 font-semibold text-slate-950">{{ $trip['distance_km'] }} km</dd></div>
                    <div><dt class="text-xs font-medium text-slate-500">Rule Uang Saku</dt><dd class="mt-1 font-semibold text-slate-950">{{ $trip['allowance_rule_label'] }}</dd></div>
                </dl>
            </div>

            <x-summary-card :summary="$trip" />

            <div class="app-card p-5 lg:p-6">
                <label for="approval_note" class="field-label">Catatan Approval</label>
                <textarea id="approval_note" wire:model.live="approval_note" rows="4" class="field-control" placeholder="Catatan tambahan untuk keputusan SDM"></textarea>
                @error('approval_note')<p class="mt-1.5 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
                <div class="mt-5 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                    <button wire:click="reject" class="btn-danger">Reject</button>
                    <button wire:click="approve" class="btn-success">Approve</button>
                </div>
            </div>
        </section>

        <x-status-timeline :items="$history" />
    </div>
</div>

