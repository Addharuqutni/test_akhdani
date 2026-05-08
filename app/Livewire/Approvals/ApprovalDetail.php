<?php

namespace App\Livewire\Approvals;

use App\Enums\AllowanceRuleType;
use App\Models\BusinessTripRequest;
use App\Services\BusinessTrip\BusinessTripApprovalService;
use Livewire\Component;

class ApprovalDetail extends Component
{
    public int $id;
    public string $approval_note = '';
    public array $trip = [];
    public array $history = [];

    protected array $rules = [
        'approval_note' => ['nullable', 'string', 'max:500'],
    ];

    public function mount(int $id): void
    {
        $this->loadTrip($id);
    }

    public function approve(BusinessTripApprovalService $service): void
    {
        $this->validate();
        $row = BusinessTripRequest::query()->findOrFail($this->id);
        $service->approve($row, (int) auth()->id(), $this->approval_note ?: null);
        session()->flash('success', 'Pengajuan disetujui SDM.');
        $this->redirectRoute('approvals.queue', navigate: true);
    }

    public function reject(BusinessTripApprovalService $service): void
    {
        $this->validate();
        $row = BusinessTripRequest::query()->findOrFail($this->id);
        $service->reject($row, (int) auth()->id(), 'Ditolak oleh SDM', $this->approval_note ?: null);
        session()->flash('success', 'Pengajuan ditolak SDM.');
        $this->redirectRoute('approvals.queue', navigate: true);
    }

    private function loadTrip(int $id): void
    {
        $this->id = $id;
        $row = BusinessTripRequest::query()->with(['employee', 'statusHistories.actor'])->findOrFail($id);

        $this->trip = [
            'request_number' => $row->request_number,
            'employee' => $row->employee->name,
            'purpose' => $row->purpose,
            'duration_days' => $row->duration_days,
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
        return view('livewire.approvals.approval-detail')->layout('layouts.app', ['title' => 'Approval Detail']);
    }
}
