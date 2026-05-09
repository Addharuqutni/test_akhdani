<?php

namespace App\Services\BusinessTrip;

use App\Enums\BusinessTripStatus;
use App\Exceptions\BusinessTripException;
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

    /**
     * Membuat draft pengajuan perjalanan dinas baru.
     * 
     * Function ini akan:
     * 1. Menghitung durasi, jarak, dan uang saku otomatis
     * 2. Generate nomor request unik (format: PD-YYYYMMDD-0001)
     * 3. Menyimpan data ke database dengan status DRAFT
     * 4. Mencatat history awal pengajuan
     * 
     * @param User $employee Pegawai yang membuat pengajuan
     * @param array $payload Data pengajuan (tujuan, tanggal, kota, dll)
     * @return BusinessTripRequest Draft pengajuan yang baru dibuat
     * @throws \Throwable Jika terjadi error saat transaksi database
     */
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

    /**
     * Update draft pengajuan yang sudah ada.
     * 
     * Function ini akan:
     * 1. Recalculate durasi, jarak, dan uang saku berdasarkan data baru
     * 2. Update data pengajuan di database
     * 3. Return data terbaru
     * 
     * Note: Hanya draft yang bisa diupdate (validasi di controller)
     * 
     * @param BusinessTripRequest $request Draft yang akan diupdate
     * @param array $payload Data baru untuk update
     * @return BusinessTripRequest Draft yang sudah diupdate
     */
    public function updateDraft(BusinessTripRequest $request, array $payload): BusinessTripRequest
    {
        $calc = $this->buildCalculation($payload);
        $request->update(array_merge($payload, $calc));

        return $request->refresh();
    }

    /**
     * Submit draft pengajuan untuk diproses approval.
     * 
     * Function ini akan:
     * 1. Validasi bahwa status adalah DRAFT
     * 2. Ubah status menjadi SUBMITTED
     * 3. Set waktu submit (submitted_at)
     * 4. Catat history perubahan status
     * 
     * Setelah submit, pengajuan akan masuk ke approval queue SDM.
     * 
     * @param BusinessTripRequest $request Draft yang akan disubmit
     * @param int $actorId ID user yang melakukan submit
     * @return BusinessTripRequest Pengajuan yang sudah disubmit
     * @throws BusinessTripException Jika status bukan DRAFT
     */
    public function submit(BusinessTripRequest $request, int $actorId): BusinessTripRequest
    {
        if ($request->status !== BusinessTripStatus::DRAFT->value) {
            throw BusinessTripException::invalidStatus('Hanya draft yang bisa disubmit.');
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

    /**
     * Batalkan pengajuan perjalanan dinas.
     * 
     * Function ini akan:
     * 1. Validasi bahwa status adalah DRAFT atau SUBMITTED
     * 2. Ubah status menjadi CANCELLED
     * 3. Catat alasan pembatalan di history
     * 
     * Pengajuan yang sudah APPROVED/REJECTED tidak bisa dibatalkan.
     * 
     * @param BusinessTripRequest $request Pengajuan yang akan dibatalkan
     * @param int $actorId ID user yang membatalkan
     * @param string|null $note Catatan/alasan pembatalan (opsional)
     * @return BusinessTripRequest Pengajuan yang sudah dibatalkan
     * @throws BusinessTripException Jika status tidak bisa dibatalkan
     */
    public function cancel(BusinessTripRequest $request, int $actorId, ?string $note = null): BusinessTripRequest
    {
        if (!in_array($request->status, [BusinessTripStatus::DRAFT->value, BusinessTripStatus::SUBMITTED->value], true)) {
            throw BusinessTripException::invalidStatus('Pengajuan tidak bisa dibatalkan.');
        }

        return DB::transaction(function () use ($request, $actorId, $note) {
            $from = $request->status;
            $request->update(['status' => BusinessTripStatus::CANCELLED->value]);
            $this->historyService->log($request, $from, BusinessTripStatus::CANCELLED->value, $actorId, $note);
            return $request->refresh();
        });
    }

    /**
     * Hitung otomatis durasi, jarak, dan uang saku perjalanan.
     * 
     * Function ini akan:
     * 1. Ambil data kota asal dan tujuan dari database
     * 2. Hitung durasi perjalanan (jumlah hari)
     * 3. Hitung jarak menggunakan rumus Haversine (berdasarkan koordinat)
     * 4. Hitung uang saku berdasarkan aturan yang berlaku
     * 
     * @param array $payload Data pengajuan (origin_city_id, destination_city_id, departure_date, return_date)
     * @return array Array berisi: duration_days, distance_km, allowance_rule_type, allowance_per_day, allowance_total
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Jika kota tidak ditemukan
     */
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

    /**
     * Generate nomor request unik dengan format: PD-YYYYMMDD-0001
     * 
     * Function ini menggunakan database locking untuk mencegah race condition.
     * Nomor akan sequential per hari (reset setiap hari baru).
     * 
     * Contoh:
     * - PD-20260509-0001 (request pertama hari ini)
     * - PD-20260509-0002 (request kedua hari ini)
     * - PD-20260510-0001 (request pertama besok, reset ke 0001)
     * 
     * Note: Function ini dipanggil dalam DB::transaction() untuk keamanan.
     * 
     * @return string Nomor request unik
     */
    private function generateRequestNumber(): string
    {
        $datePart = now()->format('Ymd');

        // Use database lock to prevent race condition
        $lastRequest = BusinessTripRequest::query()
            ->whereDate('created_at', today())
            ->lockForUpdate()
            ->latest('id')
            ->first();

        $sequence = $lastRequest 
            ? ((int) substr($lastRequest->request_number, -4)) + 1 
            : 1;
        
        $sequence = str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);

        return "PD-{$datePart}-{$sequence}";
    }
}
