<div class="space-y-6">
    <div>
        <p class="page-kicker">Pengajuan</p>
        <h2 class="page-title">Form Perjalanan Dinas</h2>
        <p class="page-description">Isi tujuan, periode, dan rute perjalanan. Ringkasan biaya akan menyesuaikan pilihan kota dan durasi.</p>
    </div>

    <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_22rem]">
        <section class="app-card p-5 lg:p-6">
            <div class="mb-5 border-b border-slate-200 pb-4">
                <h3 class="text-base font-semibold text-slate-950">Detail perjalanan</h3>
                <p class="mt-1 text-sm text-slate-500">Pastikan tanggal pulang tidak mendahului tanggal berangkat.</p>
            </div>
            <div class="space-y-5">
                <x-form-input label="Tujuan / Maksud Perjalanan" name="purpose" />
                <div class="grid gap-4 sm:grid-cols-2">
                    <x-form-input label="Tanggal Berangkat" name="departure_date" type="date" />
                    <x-form-input label="Tanggal Pulang" name="return_date" type="date" />
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <x-city-select label="Kota Asal" name="origin_city_id" :options="$cities" />
                    <x-city-select label="Kota Tujuan" name="destination_city_id" :options="$cities" />
                </div>
                <div class="flex flex-col-reverse gap-3 border-t border-slate-200 pt-5 sm:flex-row sm:justify-end">
                    <button wire:click="saveDraft" class="btn-secondary">Simpan Draft</button>
                    <button wire:click="submit" class="btn-primary">Submit Pengajuan</button>
                </div>
            </div>
        </section>

        <aside class="lg:sticky lg:top-24 lg:self-start">
            <x-summary-card :summary="$summary" />
        </aside>
    </div>
</div>

