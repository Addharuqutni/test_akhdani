<?php

namespace App\Livewire\Shared;

use App\Livewire\Concerns\WithListDefaults;
use App\Models\BusinessTripRequest;
use Livewire\Component;
use Livewire\WithPagination;

class SubmissionHistory extends Component
{
    use WithListDefaults, WithPagination;

    public function render()
    {
        $user = auth()->user();

        $rows = BusinessTripRequest::query()
            ->with('employee')
            ->when($user->hasRole('pegawai'), fn ($query) => $query->where('employee_id', $user->id))
            ->latest('updated_at')
            ->paginate($this->perPage)
            ->through(fn (BusinessTripRequest $trip) => [
                'id' => $trip->id,
                'request_number' => $trip->request_number,
                'employee' => $trip->employee->name,
                'status' => $trip->status,
                'updated_at' => $trip->updated_at->format('Y-m-d H:i'),
            ]);

        return view('livewire.shared.submission-history', compact('rows'))->layout('layouts.app', ['title' => 'Histori Pengajuan']);
    }
}

