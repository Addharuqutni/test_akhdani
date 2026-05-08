<?php

namespace App\Livewire\Approvals;

use App\Enums\BusinessTripStatus;
use App\Livewire\Concerns\WithListDefaults;
use App\Models\BusinessTripRequest;
use Livewire\Component;
use Livewire\WithPagination;

class ApprovalQueue extends Component
{
    use WithListDefaults, WithPagination;

    public function render()
    {
        $rows = BusinessTripRequest::query()
            ->with(['employee', 'originCity', 'destinationCity'])
            ->where('status', BusinessTripStatus::SUBMITTED->value)
            ->latest('submitted_at')
            ->paginate($this->perPage)
            ->through(fn ($r) => [
                'id' => $r->id,
                'request_number' => $r->request_number,
                'employee' => $r->employee->name,
                'purpose' => $r->purpose,
                'period' => $r->departure_date->format('d M Y').' - '.$r->return_date->format('d M Y'),
                'route' => $r->originCity->name.' -> '.$r->destinationCity->name,
                'total' => $r->allowance_total,
                'currency' => $r->allowance_currency,
                'status' => $r->status,
            ]);

        return view('livewire.approvals.approval-queue', compact('rows'))->layout('layouts.app', ['title' => 'Approval Queue']);
    }
}
