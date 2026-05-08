<div class="mx-auto grid min-h-[calc(100vh-6rem)] w-full max-w-5xl items-center gap-8 px-4 py-10 lg:grid-cols-[1.05fr_0.95fr]">
    <section class="hidden lg:block">
        <p class="page-kicker">Akhdani Internal</p>
        <h1 class="mt-3 max-w-xl text-4xl font-semibold tracking-tight text-slate-950">
            Kelola pengajuan perjalanan dinas dengan alur yang jelas.
        </h1>
        <p class="mt-4 max-w-lg text-base leading-7 text-slate-600">
            Pengajuan, perhitungan uang saku, approval SDM, dan histori status berada dalam satu ruang kerja.
        </p>
        <div class="mt-8 grid max-w-xl grid-cols-3 gap-3">
            <div class="rounded-lg border border-slate-200 bg-white p-4">
                <p class="text-2xl font-semibold text-slate-950">01</p>
                <p class="mt-1 text-xs font-medium text-slate-500">Draft</p>
            </div>
            <div class="rounded-lg border border-slate-200 bg-white p-4">
                <p class="text-2xl font-semibold text-slate-950">02</p>
                <p class="mt-1 text-xs font-medium text-slate-500">Submit</p>
            </div>
            <div class="rounded-lg border border-slate-200 bg-white p-4">
                <p class="text-2xl font-semibold text-slate-950">03</p>
                <p class="mt-1 text-xs font-medium text-slate-500">Approval</p>
            </div>
        </div>
    </section>

    <section class="app-card p-6 sm:p-8">
        <div class="mb-8">
            <p class="page-kicker">Login</p>
            <h2 class="mt-2 text-2xl font-semibold tracking-tight text-slate-950">Sistem Perdin</h2>
            <p class="mt-2 text-sm leading-6 text-slate-600">Masuk menggunakan akun internal perusahaan.</p>
        </div>
        <form wire:submit="login" class="space-y-5">
            <x-form-input label="Username" name="username" />
            <x-form-input label="Password" name="password" type="password" />
            <button type="submit" class="btn-primary w-full">Login</button>
        </form>
    </section>
</div>

