<?php

namespace App\Livewire\BusinessTrips;

use App\Livewire\Concerns\WithListDefaults;
use App\Models\BusinessTripRequest;
use Livewire\Component;
use Livewire\WithPagination;

class MyTripList extends Component
{
    use WithListDefaults, WithPagination;

    public function render()
    {
        $trips = BusinessTripRequest::query()
            ->where('employee_id', auth()->id())
            ->with(['originCity', 'destinationCity'])
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.business-trips.my-trip-list', compact('trips'))->layout('layouts.app', ['title' => 'Daftar Pengajuan Saya']);
    }
}
