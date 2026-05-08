<?php

namespace App\Services\BusinessTrip;

use App\Enums\BusinessTripStatus;
use App\Models\BusinessTripRequest;
use App\Models\City;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BusinessTripSubmissionService
{
    public function __construct(
        private readonly BusinessTripDurationService $durationService,
        private readonly DistanceCalculatorService $distanceService,
        private readonly BusinessTripAllowanceService $allowanceService,
        private readonly BusinessTripStatusHistoryService $historyService,
    ) {}

    public function createDraft(User $employee, array $payload): BusinessTripRequest
    {
        return DB::transaction(function () use ($employee, $payload) {
            $calc = $this->buildCalculation($payload);

            $request = BusinessTripRequest::query()->create(array_merge($payload, $calc, [
                'employee_id' => $employee->id,
                'request_number' => $this->generateRequestNumber(),
                'status' => BusinessTripStatus::DRAFT->value,
            ]));

            $this->historyService->initial($request, $employee->id);

            return $request;
        });
    }

    public function updateDraft(BusinessTripRequest $request, array $payload): BusinessTripRequest
    {
        $calc = $this->buildCalculation($payload);
        $request->update(array_merge($payload, $calc));

        return $request->refresh();
    }

    public function submit(BusinessTripRequest $request, int $actorId): BusinessTripRequest
    {
        if ($request->status !== BusinessTripStatus::DRAFT->value) {
            throw new \DomainException('Hanya draft yang bisa disubmit.');
        }

        return DB::transaction(function () use ($request, $actorId) {
            $from = $request->status;
            $request->update([
                'status' => BusinessTripStatus::SUBMITTED->value,
                'submitted_at' => now(),
            ]);

            $this->historyService->log($request, $from, BusinessTripStatus::SUBMITTED->value, $actorId, 'Submit pengajuan');

            return $request->refresh();
        });
    }

    public function cancel(BusinessTripRequest $request, int $actorId, ?string $note = null): BusinessTripRequest
    {
        if (!in_array($request->status, [BusinessTripStatus::DRAFT->value, BusinessTripStatus::SUBMITTED->value], true)) {
            throw new \DomainException('Pengajuan tidak bisa dibatalkan.');
        }

        return DB::transaction(function () use ($request, $actorId, $note) {
            $from = $request->status;
            $request->update(['status' => BusinessTripStatus::CANCELLED->value]);
            $this->historyService->log($request, $from, BusinessTripStatus::CANCELLED->value, $actorId, $note);
            return $request->refresh();
        });
    }

    private function buildCalculation(array $payload): array
    {
        $origin = City::query()->active()->findOrFail($payload['origin_city_id']);
        $destination = City::query()->active()->findOrFail($payload['destination_city_id']);

        $departure = Carbon::parse($payload['departure_date']);
        $return = Carbon::parse($payload['return_date']);

        $duration = $this->durationService->calculate($departure, $return);
        $distanceKm = $this->distanceService->haversineKm($origin->latitude, $origin->longitude, $destination->latitude, $destination->longitude);
        $allowance = $this->allowanceService->calculate($origin, $destination, $distanceKm, $duration);

        return array_merge([
            'duration_days' => $duration,
            'distance_km' => $distanceKm,
        ], $allowance);
    }

    private function generateRequestNumber(): string
    {
        $datePart = now()->format('Ymd');

        for ($attempt = 0; $attempt < 10; $attempt++) {
            $sequence = str_pad((string) random_int(1, 9999), 4, '0', STR_PAD_LEFT);
            $requestNumber = "PD-{$datePart}-{$sequence}";

            if (!BusinessTripRequest::query()->where('request_number', $requestNumber)->exists()) {
                return $requestNumber;
            }
        }

        return 'PD-'.$datePart.'-'.strtoupper(bin2hex(random_bytes(3)));
    }
}
