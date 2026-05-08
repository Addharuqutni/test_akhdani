<?php

namespace App\Livewire\Cities;

use App\Livewire\Concerns\WithListDefaults;
use App\Models\City;
use Livewire\Component;
use Livewire\WithPagination;

class CityManagement extends Component
{
    use WithListDefaults, WithPagination;

    public string $search = '';
    public ?int $editing_city_id = null;
    public string $name = '';
    public string $latitude = '';
    public string $longitude = '';
    public string $province_name = '';
    public string $island_name = '';
    public bool $is_foreign = false;
    public bool $is_active = true;

    protected array $rules = [
        'name' => ['required'], 'latitude' => ['required', 'numeric'], 'longitude' => ['required', 'numeric'],
        'province_name' => ['required'], 'island_name' => ['required'],
    ];

    public function save(): void
    {
        $payload = $this->validate();
        $payload['is_foreign'] = $this->is_foreign;
        $payload['is_active'] = $this->is_active;

        if ($this->editing_city_id) {
            City::query()->findOrFail($this->editing_city_id)->update($payload);
            session()->flash('success', 'Data kota berhasil diupdate.');
        } else {
            City::query()->create($payload);
            session()->flash('success', 'Data kota berhasil disimpan.');
        }

        $this->resetForm();
    }

    public function edit(int $id): void
    {
        $city = City::query()->findOrFail($id);
        $this->editing_city_id = $city->id;
        $this->name = $city->name;
        $this->latitude = (string) $city->latitude;
        $this->longitude = (string) $city->longitude;
        $this->province_name = $city->province_name;
        $this->island_name = (string) $city->island_name;
        $this->is_foreign = $city->is_foreign;
        $this->is_active = $city->is_active;
    }

    public function deactivate(int $id): void
    {
        City::query()->findOrFail($id)->update(['is_active' => false]);
        session()->flash('success', 'Data kota dinonaktifkan.');
    }

    public function resetForm(): void
    {
        $this->reset(['editing_city_id', 'name', 'latitude', 'longitude', 'province_name', 'island_name', 'is_foreign']);
        $this->is_active = true;
    }

    public function render()
    {
        $rows = City::query()
            ->when($this->search, fn ($query) => $query
                ->where(function ($subQuery) {
                    $subQuery
                        ->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('province_name', 'like', '%'.$this->search.'%');
                }))
            ->latest()
            ->paginate($this->perPage)
            ->through(fn (City $city) => [
                'id' => $city->id,
                'name' => $city->name,
                'province_name' => $city->province_name,
                'island_name' => $city->island_name,
                'is_foreign' => $city->is_foreign,
                'is_active' => $city->is_active,
            ]);

        return view('livewire.cities.city-management', compact('rows'))->layout('layouts.app', ['title' => 'Master Kota']);
    }
}

