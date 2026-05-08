<?php

namespace App\Services\BusinessTrip;

use App\Enums\BusinessTripStatus;
use App\Models\BusinessTripRequest;
use App\Models\BusinessTripStatusHistory;

class BusinessTripStatusHistoryService
{
    public function log(BusinessTripRequest $request, ?string $fromStatus, string $toStatus, int $changedBy, ?string $note = null): BusinessTripStatusHistory
    {
        return BusinessTripStatusHistory::query()->create([
            'business_trip_request_id' => $request->id,
            'from_status' => $fromStatus,
            'to_status' => $toStatus,
            'changed_by' => $changedBy,
            'note' => $note,
        ]);
    }

    public function initial(BusinessTripRequest $request, int $changedBy): void
    {
        $this->log($request, null, BusinessTripStatus::DRAFT->value, $changedBy, 'Draft dibuat');
    }
}
