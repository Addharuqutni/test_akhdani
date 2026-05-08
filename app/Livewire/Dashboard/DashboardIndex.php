<?php

namespace App\Livewire\Dashboard;

use App\Enums\BusinessTripStatus;
use App\Models\BusinessTripRequest;
use Livewire\Component;

class DashboardIndex extends Component
{
    public array $stats = [];

    public function mount(): void
    {
        $user = auth()->user();

        if ($user->hasRole('pegawai')) {
            $base = BusinessTripRequest::query()->where('employee_id', $user->id);
            $this->stats = [
                ['label' => 'Total Pengajuan Saya', 'value' => (clone $base)->count()],
                ['label' => 'Menunggu Review', 'value' => (clone $base)->where('status', BusinessTripStatus::SUBMITTED->value)->count()],
                ['label' => 'Approved', 'value' => (clone $base)->where('status', BusinessTripStatus::APPROVED->value)->count()],
                ['label' => 'Rejected', 'value' => (clone $base)->where('status', BusinessTripStatus::REJECTED->value)->count()],
            ];
            return;
        }

        $base = BusinessTripRequest::query();
        $this->stats = [
            ['label' => 'Total Pengajuan', 'value' => (clone $base)->count()],
            ['label' => 'Submitted', 'value' => (clone $base)->where('status', BusinessTripStatus::SUBMITTED->value)->count()],
            ['label' => 'Approved', 'value' => (clone $base)->where('status', BusinessTripStatus::APPROVED->value)->count()],
            ['label' => 'Rejected', 'value' => (clone $base)->where('status', BusinessTripStatus::REJECTED->value)->count()],
            ['label' => 'Total Nominal Disetujui', 'value' => 'IDR '.number_format((float) (clone $base)->where('status', BusinessTripStatus::APPROVED->value)->sum('allowance_total'), 0, ',', '.')],
        ];
    }

    public function render()
    {
        return view('livewire.dashboard.dashboard-index')->layout('layouts.app', ['title' => 'Dashboard']);
    }
}
