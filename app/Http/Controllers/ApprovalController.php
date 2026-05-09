<?php

namespace App\Http\Controllers;

use App\Enums\BusinessTripStatus;
use App\Http\Requests\Approval\ApprovalDecisionRequest;
use App\Models\BusinessTripRequest;
use App\Services\BusinessTrip\BusinessTripApprovalService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ApprovalController extends Controller
{
    public function __construct(private readonly BusinessTripApprovalService $approvalService) {}

    /**
     * Tampilkan daftar pengajuan yang menunggu approval (approval queue).
     * 
     * Function ini akan:
     * 1. Query pengajuan dengan status SUBMITTED (menunggu approval)
     * 2. Eager load relasi (employee, kota asal, kota tujuan)
     * 3. Urutkan berdasarkan waktu submit (yang paling lama duluan)
     * 4. Pagination 15 items per halaman
     * 
     * Note: Hanya user dengan role SDM yang bisa akses halaman ini (dihandle di middleware).
     * 
     * @return View View dengan daftar pengajuan yang menunggu approval (variable: $rows)
     */
    public function queue(): View
    {
        $rows = BusinessTripRequest::query()
            ->with(['employee', 'originCity', 'destinationCity'])
            ->where('status', BusinessTripStatus::SUBMITTED->value)
            ->latest('submitted_at')
            ->paginate(15);

        return view('approvals.queue', compact('rows'));
    }

    /**
     * Tampilkan detail pengajuan untuk proses approval.
     * 
     * Function ini akan:
     * 1. Load relasi lengkap (employee, kota, history status dengan actor)
     * 2. Tampilkan view detail untuk SDM review
     * 
     * Di halaman ini SDM bisa:
     * - Melihat detail lengkap pengajuan
     * - Approve pengajuan
     * - Reject pengajuan dengan alasan
     * 
     * @param BusinessTripRequest $businessTripRequest Pengajuan yang akan direview (route model binding)
     * @return View View detail pengajuan untuk approval (variable: $trip)
     */
    public function show(BusinessTripRequest $businessTripRequest): View
    {
        $businessTripRequest->load(['employee', 'originCity', 'destinationCity', 'statusHistories.actor']);
        return view('approvals.detail', ['trip' => $businessTripRequest]);
    }

    /**
     * Approve (setujui) pengajuan perjalanan dinas.
     * 
     * Function ini akan:
     * 1. Validasi input (via ApprovalDecisionRequest)
     * 2. Panggil service untuk approve pengajuan
     * 3. Ubah status dari SUBMITTED → APPROVED
     * 4. Set waktu approval dan ID approver
     * 5. Simpan catatan approval (opsional)
     * 6. Redirect ke approval queue
     * 
     * Setelah approved:
     * - Pegawai bisa melakukan perjalanan dinas
     * - Pengajuan tidak bisa diubah lagi
     * 
     * @param ApprovalDecisionRequest $request Request dengan data yang sudah tervalidasi
     * @param BusinessTripRequest $businessTripRequest Pengajuan yang akan di-approve
     * @return RedirectResponse Redirect ke approval queue dengan success message
     * @throws BusinessTripException Jika status bukan SUBMITTED (dihandle di service)
     */
    public function approve(ApprovalDecisionRequest $request, BusinessTripRequest $businessTripRequest): RedirectResponse
    {
        $this->approvalService->approve($businessTripRequest, (int) auth()->id(), $request->input('approval_note'));
        return redirect()->route('approvals.queue')->with('success', 'Request approved');
    }

    /**
     * Reject (tolak) pengajuan perjalanan dinas.
     * 
     * Function ini akan:
     * 1. Validasi input (via ApprovalDecisionRequest)
     * 2. Validasi tambahan: rejection_reason WAJIB diisi
     * 3. Panggil service untuk reject pengajuan
     * 4. Ubah status dari SUBMITTED → REJECTED
     * 5. Set waktu rejection dan ID approver
     * 6. Simpan alasan penolakan (WAJIB)
     * 7. Simpan catatan tambahan (opsional)
     * 8. Redirect ke approval queue
     * 
     * Setelah rejected:
     * - Pegawai bisa melihat alasan penolakan
     * - Pegawai bisa membuat pengajuan baru
     * 
     * @param ApprovalDecisionRequest $request Request dengan data yang sudah tervalidasi
     * @param BusinessTripRequest $businessTripRequest Pengajuan yang akan di-reject
     * @return RedirectResponse Redirect ke approval queue dengan success message
     * @throws \Illuminate\Validation\ValidationException Jika rejection_reason tidak diisi
     * @throws BusinessTripException Jika status bukan SUBMITTED (dihandle di service)
     */
    public function reject(ApprovalDecisionRequest $request, BusinessTripRequest $businessTripRequest): RedirectResponse
    {
        $request->validate(['rejection_reason' => ['required', 'string', 'max:2000']]);
        $this->approvalService->reject(
            $businessTripRequest,
            (int) auth()->id(),
            $request->input('rejection_reason'),
            $request->input('approval_note')
        );

        return redirect()->route('approvals.queue')->with('success', 'Request rejected');
    }
}
