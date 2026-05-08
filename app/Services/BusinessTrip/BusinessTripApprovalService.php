<?php

namespace App\Services\BusinessTrip;

use App\Enums\BusinessTripStatus;
use App\Models\BusinessTripRequest;
use Illuminate\Support\Facades\DB;

class BusinessTripApprovalService
{
    public function __construct(private readonly BusinessTripStatusHistoryService $historyService) {}

    public function approve(BusinessTripRequest $request, int $approverId, ?string $note = null): BusinessTripRequest
    {
        if ($request->status !== BusinessTripStatus::SUBMITTED->value) {
            throw new \DomainException('Hanya pengajuan submitted yang bisa di-approve.');
        }

        return DB::transaction(function () use ($request, $approverId, $note) {
            $from = $request->status;
            $request->update([
                'status' => BusinessTripStatus::APPROVED->value,
                'approved_at' => now(),
                'approved_by' => $approverId,
                'approval_note' => $note,
                'rejected_at' => null,
                'rejection_reason' => null,
            ]);

            $this->historyService->log($request, $from, BusinessTripStatus::APPROVED->value, $approverId, $note);

            return $request->refresh();
        });
    }

    public function reject(BusinessTripRequest $request, int $approverId, string $reason, ?string $note = null): BusinessTripRequest
    {
        if ($request->status !== BusinessTripStatus::SUBMITTED->value) {
            throw new \DomainException('Hanya pengajuan submitted yang bisa di-reject.');
        }

        return DB::transaction(function () use ($request, $approverId, $reason, $note) {
            $from = $request->status;
            $request->update([
                'status' => BusinessTripStatus::REJECTED->value,
                'rejected_at' => now(),
                'approved_by' => $approverId,
                'rejection_reason' => $reason,
                'approval_note' => $note,
            ]);

            $this->historyService->log($request, $from, BusinessTripStatus::REJECTED->value, $approverId, trim($reason.' '.$note));

            return $request->refresh();
        });
    }
}
