@props(['label','name','type' => 'text'])
<div>
    <label for="{{ $name }}" class="field-label">{{ $label }}</label>
    <input type="{{ $type }}" id="{{ $name }}" wire:model.live="{{ $name }}" class="field-control" />
    @error($name)<p class="mt-1.5 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
</div>

