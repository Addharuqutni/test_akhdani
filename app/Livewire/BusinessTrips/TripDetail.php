<?php

namespace App\Livewire\BusinessTrips;

use App\Enums\AllowanceRuleType;
use App\Models\BusinessTripRequest;
use Livewire\Component;

class TripDetail extends Component
{
    public int $id;
    public array $trip = [];
    public array $history = [];

    public function mount(int $id): void
    {
        $row = BusinessTripRequest::query()
            ->with(['employee', 'originCity', 'destinationCity', 'statusHistories.actor'])
            ->findOrFail($id);

        $user = auth()->user();
        if (!$user->hasRole('admin') && !$user->hasRole('sdm') && $row->employee_id !== $user->id) {
            abort(403, 'Akses ditolak.');
        }

        $this->trip = [
            'employee' => $row->employee->name,
            'request_number' => $row->request_number,
            'purpose' => $row->purpose,
            'departure_date' => $row->departure_date->format('Y-m-d'),
            'return_date' => $row->return_date->format('Y-m-d'),
            'duration_days' => $row->duration_days,
            'origin' => $row->originCity->name,
            'destination' => $row->destinationCity->name,
            'distance_km' => $row->distance_km,
            'allowance_rule_type' => $row->allowance_rule_type,
            'allowance_rule_label' => AllowanceRuleType::labelOf($row->allowance_rule_type),
            'allowance_currency' => $row->allowance_currency,
            'allowance_per_day' => $row->allowance_per_day,
            'allowance_total' => $row->allowance_total,
            'status' => $row->status,
        ];

        $this->history = $row->statusHistories->map(fn ($h) => [
            'from_status' => $h->from_status ?? '-',
            'to_status' => $h->to_status,
            'changed_by' => $h->actor->name,
            'changed_at' => $h->created_at->format('Y-m-d H:i'),
            'note' => $h->note,
        ])->toArray();
    }

    public function render()
    {
        return view('livewire.business-trips.trip-detail')->layout('layouts.app', ['title' => 'Detail Pengajuan']);
    }
}
