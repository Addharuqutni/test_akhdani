<?php

namespace App\Services\BusinessTrip;

use App\Enums\BusinessTripStatus;
use App\Exceptions\BusinessTripException;
use App\Models\BusinessTripRequest;
use Illuminate\Support\Facades\DB;

class BusinessTripApprovalService
{
    public function __construct(private readonly BusinessTripStatusHistoryService $historyService) {}

    /**
     * Approve (setujui) pengajuan perjalanan dinas.
     * 
     * Function ini akan:
     * 1. Validasi bahwa status adalah SUBMITTED (sudah diajukan)
     * 2. Ubah status menjadi APPROVED
     * 3. Set waktu approval dan ID approver
     * 4. Simpan catatan approval (jika ada)
     * 5. Clear data rejection sebelumnya (jika ada)
     * 6. Catat history perubahan status
     * 
     * Setelah approved, pegawai bisa melakukan perjalanan dinas.
     * 
     * @param BusinessTripRequest $request Pengajuan yang akan di-approve
     * @param int $approverId ID user SDM yang meng-approve
     * @param string|null $note Catatan approval (opsional, misal: "Disetujui sesuai prosedur")
     * @return BusinessTripRequest Pengajuan yang sudah di-approve
     * @throws BusinessTripException Jika status bukan SUBMITTED
     */
    public function approve(BusinessTripRequest $request, int $approverId, ?string $note = null): BusinessTripRequest
    {
        if ($request->status !== BusinessTripStatus::SUBMITTED->value) {
            throw BusinessTripException::invalidStatus('Hanya pengajuan submitted yang bisa di-approve.');
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

    /**
     * Reject (tolak) pengajuan perjalanan dinas.
     * 
     * Function ini akan:
     * 1. Validasi bahwa status adalah SUBMITTED (sudah diajukan)
     * 2. Ubah status menjadi REJECTED
     * 3. Set waktu rejection dan ID approver
     * 4. Simpan alasan penolakan (WAJIB)
     * 5. Simpan catatan tambahan (opsional)
     * 6. Catat history perubahan status
     * 
     * Setelah rejected, pegawai bisa membuat pengajuan baru.
     * 
     * @param BusinessTripRequest $request Pengajuan yang akan di-reject
     * @param int $approverId ID user SDM yang me-reject
     * @param string $reason Alasan penolakan (WAJIB, misal: "Anggaran tidak mencukupi")
     * @param string|null $note Catatan tambahan (opsional)
     * @return BusinessTripRequest Pengajuan yang sudah di-reject
     * @throws BusinessTripException Jika status bukan SUBMITTED
     */
    public function reject(BusinessTripRequest $request, int $approverId, string $reason, ?string $note = null): BusinessTripRequest
    {
        if ($request->status !== BusinessTripStatus::SUBMITTED->value) {
            throw BusinessTripException::invalidStatus('Hanya pengajuan submitted yang bisa di-reject.');
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
