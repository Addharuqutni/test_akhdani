@props(['label' => 'Kota','name' => 'city_id','options' => []])
<div>
    <label for="{{ $name }}" class="field-label">{{ $label }}</label>
    <select id="{{ $name }}" wire:model.live="{{ $name }}" class="field-control">
        <option value="">Pilih kota</option>
        @foreach($options as $city)
            <option value="{{ $city['id'] }}">{{ $city['name'] }} - {{ $city['province_name'] }}</option>
        @endforeach
    </select>
    @error($name)<p class="mt-1.5 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
</div>

