<?php

namespace App\Livewire\BusinessTrips;

use App\Enums\AllowanceRuleType;
use App\Models\City;
use App\Services\BusinessTrip\BusinessTripAllowanceService;
use App\Services\BusinessTrip\BusinessTripDurationService;
use App\Services\BusinessTrip\BusinessTripSubmissionService;
use App\Services\BusinessTrip\DistanceCalculatorService;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Livewire\Component;

class TripForm extends Component
{
    public string $purpose = '';
    public string $departure_date = '';
    public string $return_date = '';
    public ?int $origin_city_id = null;
    public ?int $destination_city_id = null;

    public array $cities = [];

    public array $summary = [
        'duration_days' => 0,
        'distance_km' => 0,
        'allowance_rule_type' => '-',
        'allowance_rule_label' => '-',
        'allowance_currency' => 'IDR',
        'allowance_per_day' => 0,
        'allowance_total' => 0,
    ];

    public function mount(): void
    {
        $this->cities = City::query()->active()->orderBy('name')->get(['id', 'name', 'province_name'])->toArray();
    }

    protected function rules(): array
    {
        return [
            'purpose' => ['required', 'string', 'min:5'],
            'departure_date' => ['required', 'date'],
            'return_date' => ['required', 'date', 'after_or_equal:departure_date'],
            'origin_city_id' => ['required', Rule::exists('cities', 'id')->where(fn ($query) => $query->where('is_active', true))],
            'destination_city_id' => ['required', Rule::exists('cities', 'id')->where(fn ($query) => $query->where('is_active', true))],
        ];
    }

    public function saveDraft(BusinessTripSubmissionService $submissionService): void
    {
        $trip = $submissionService->createDraft(auth()->user(), $this->validate());
        session()->flash('success', 'Draft pengajuan berhasil disimpan.');
        $this->redirectRoute('trips.detail', ['id' => $trip->id], navigate: true);
    }

    public function submit(BusinessTripSubmissionService $submissionService): void
    {
        $trip = $submissionService->createDraft(auth()->user(), $this->validate());
        $submissionService->submit($trip, (int) auth()->id());
        session()->flash('success', 'Pengajuan berhasil disubmit dan menunggu review SDM.');
        $this->redirectRoute('trips.detail', ['id' => $trip->id], navigate: true);
    }

    public function updated($property): void
    {
        if (in_array($property, ['departure_date', 'return_date', 'origin_city_id', 'destination_city_id'], true)) {
            $this->previewCalculation();
        }
    }

    private function previewCalculation(): void
    {
        if (!$this->departure_date || !$this->return_date || !$this->origin_city_id || !$this->destination_city_id) {
            return;
        }

        try {
            $origin = City::query()->findOrFail($this->origin_city_id);
            $destination = City::query()->findOrFail($this->destination_city_id);

            $duration = app(BusinessTripDurationService::class)->calculate(
                Carbon::parse($this->departure_date),
                Carbon::parse($this->return_date)
            );

            $distance = app(DistanceCalculatorService::class)->haversineKm(
                $origin->latitude,
                $origin->longitude,
                $destination->latitude,
                $destination->longitude
            );

            $allowance = app(BusinessTripAllowanceService::class)->calculate($origin, $destination, $distance, $duration);
            $allowance['allowance_rule_label'] = AllowanceRuleType::labelOf($allowance['allowance_rule_type'] ?? null);

            $this->summary = array_merge([
                'duration_days' => $duration,
                'distance_km' => $distance,
            ], $allowance);
        } catch (\Throwable) {
            // ignore preview failure
        }
    }

    public function render()
    {
        return view('livewire.business-trips.trip-form')->layout('layouts.app', ['title' => 'Form Pengajuan']);
    }
}
