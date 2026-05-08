@if(session()->has('success'))
    <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800 shadow-sm">
        {{ session('success') }}
    </div>
@endif
@if(session()->has('error'))
    <div class="mb-4 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-800 shadow-sm">
        {{ session('error') }}
    </div>
@endif

