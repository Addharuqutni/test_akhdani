@props(['items' => []])
<div class="app-card p-5">
    <h3 class="text-sm font-semibold text-slate-950">Riwayat Status</h3>
    <ol class="mt-4 space-y-4">
        @forelse($items as $item)
            <li class="relative border-l-2 border-slate-200 pl-4">
                <span class="absolute -left-[5px] top-1.5 h-2 w-2 rounded-full bg-cyan-600 ring-4 ring-cyan-50"></span>
                <p class="text-sm font-semibold text-slate-950">{{ strtoupper($item['from_status']) }} -> {{ strtoupper($item['to_status']) }}</p>
                <p class="mt-1 text-xs text-slate-500">{{ $item['changed_by'] }} - {{ $item['changed_at'] }}</p>
                @if(!empty($item['note']))<p class="mt-2 rounded-md bg-slate-50 p-3 text-sm leading-6 text-slate-700">{{ $item['note'] }}</p>@endif
            </li>
        @empty
            <li class="text-sm text-slate-500">Belum ada histori status.</li>
        @endforelse
    </ol>
</div>

