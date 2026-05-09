<?php

namespace App\Http\Controllers;

use App\Enums\BusinessTripStatus;
use App\Exceptions\BusinessTripException;
use App\Http\Requests\BusinessTrip\CancelBusinessTripRequest;
use App\Http\Requests\BusinessTrip\StoreBusinessTripDraftRequest;
use App\Http\Requests\BusinessTrip\UpdateBusinessTripDraftRequest;
use App\Models\BusinessTripRequest;
use App\Services\BusinessTrip\BusinessTripSubmissionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BusinessTripRequestController extends Controller
{
    public function __construct(private readonly BusinessTripSubmissionService $submissionService) {}

    /**
     * Tampilkan daftar pengajuan perjalanan dinas milik user yang sedang login.
     * 
     * Function ini akan:
     * 1. Query pengajuan milik user (berdasarkan employee_id)
     * 2. Eager load relasi (kota, employee, approver) untuk menghindari N+1 query
     * 3. Urutkan dari yang terbaru
     * 4. Pagination 15 items per halaman
     * 
     * @param Request $request HTTP request dengan user yang sedang login
     * @return View View dengan daftar pengajuan (variable: $rows)
     */
    public function indexMy(Request $request): View
    {
        $rows = BusinessTripRequest::query()
            ->where('employee_id', $request->user()->id)
            ->with(['originCity', 'destinationCity', 'employee', 'approver'])
            ->latest()
            ->paginate(15);

        return view('business-trips.my-list', compact('rows'));
    }

    /**
     * Tampilkan detail pengajuan perjalanan dinas.
     * 
     * Function ini akan:
     * 1. Cek authorization (apakah user berhak melihat pengajuan ini)
     * 2. Load relasi lengkap (employee, kota, history status)
     * 3. Tampilkan view detail
     * 
     * @param BusinessTripRequest $businessTripRequest Pengajuan yang akan ditampilkan (route model binding)
     * @return View View detail pengajuan (variable: $trip)
     * @throws \Illuminate\Auth\Access\AuthorizationException Jika user tidak berhak akses
     */
    public function show(BusinessTripRequest $businessTripRequest): View
    {
        $this->authorize('view', $businessTripRequest);
        $businessTripRequest->load(['employee', 'originCity', 'destinationCity', 'statusHistories.actor']);
        return view('business-trips.detail', ['trip' => $businessTripRequest]);
    }

    /**
     * Simpan draft pengajuan perjalanan dinas baru.
     * 
     * Function ini akan:
     * 1. Validasi input (via StoreBusinessTripDraftRequest)
     * 2. Panggil service untuk create draft
     * 3. Redirect ke halaman detail draft yang baru dibuat
     * 
     * Data yang dihitung otomatis:
     * - Nomor request (PD-YYYYMMDD-0001)
     * - Durasi perjalanan (hari)
     * - Jarak tempuh (km)
     * - Uang saku (rupiah)
     * 
     * @param StoreBusinessTripDraftRequest $request Request dengan data yang sudah tervalidasi
     * @return RedirectResponse Redirect ke halaman detail draft
     */
    public function storeDraft(StoreBusinessTripDraftRequest $request): RedirectResponse
    {
        $trip = $this->submissionService->createDraft($request->user(), $request->validated());
        return redirect()->route('trips.detail', $trip)->with('success', 'Draft created');
    }

    /**
     * Update draft pengajuan yang sudah ada.
     * 
     * Function ini akan:
     * 1. Validasi bahwa status adalah DRAFT
     * 2. Cek authorization (apakah user pemilik draft)
     * 3. Validasi input (via UpdateBusinessTripDraftRequest)
     * 4. Update draft dengan data baru
     * 5. Recalculate durasi, jarak, dan uang saku
     * 
     * Note: Hanya draft (status DRAFT) yang bisa diupdate.
     * 
     * @param UpdateBusinessTripDraftRequest $request Request dengan data yang sudah tervalidasi
     * @param BusinessTripRequest $businessTripRequest Draft yang akan diupdate
     * @return RedirectResponse Redirect kembali dengan success message
     * @throws BusinessTripException Jika status bukan DRAFT
     * @throws \Illuminate\Auth\Access\AuthorizationException Jika user bukan pemilik
     */
    public function updateDraft(UpdateBusinessTripDraftRequest $request, BusinessTripRequest $businessTripRequest): RedirectResponse
    {
        if ($businessTripRequest->status !== BusinessTripStatus::DRAFT->value) {
            throw BusinessTripException::invalidStatus('Hanya draft yang bisa diupdate');
        }

        $this->authorize('update', $businessTripRequest);
        $this->submissionService->updateDraft($businessTripRequest, $request->validated());

        return back()->with('success', 'Draft updated');
    }

    /**
     * Submit draft pengajuan untuk diproses approval oleh SDM.
     * 
     * Function ini akan:
     * 1. Cek authorization (apakah user pemilik draft)
     * 2. Ubah status dari DRAFT → SUBMITTED
     * 3. Set waktu submit
     * 4. Catat history
     * 
     * Setelah submit:
     * - Pengajuan masuk ke approval queue SDM
     * - Tidak bisa diedit lagi
     * - Hanya bisa dibatalkan atau menunggu approval
     * 
     * @param BusinessTripRequest $businessTripRequest Draft yang akan disubmit
     * @return RedirectResponse Redirect kembali dengan success message
     * @throws \Illuminate\Auth\Access\AuthorizationException Jika user bukan pemilik
     * @throws BusinessTripException Jika status bukan DRAFT (dihandle di service)
     */
    public function submit(BusinessTripRequest $businessTripRequest): RedirectResponse
    {
        $this->authorize('update', $businessTripRequest);
        $this->submissionService->submit($businessTripRequest, auth()->id());

        return back()->with('success', 'Request submitted');
    }

    /**
     * Batalkan pengajuan perjalanan dinas.
     * 
     * Function ini akan:
     * 1. Validasi input note (via CancelBusinessTripRequest)
     * 2. Cek authorization (apakah user pemilik pengajuan)
     * 3. Ubah status menjadi CANCELLED
     * 4. Simpan catatan pembatalan
     * 
     * Yang bisa dibatalkan:
     * - Pengajuan dengan status DRAFT
     * - Pengajuan dengan status SUBMITTED (belum diapprove/reject)
     * 
     * Yang tidak bisa dibatalkan:
     * - Pengajuan yang sudah APPROVED
     * - Pengajuan yang sudah REJECTED
     * 
     * @param CancelBusinessTripRequest $request Request dengan note yang sudah tervalidasi
     * @param BusinessTripRequest $businessTripRequest Pengajuan yang akan dibatalkan
     * @return RedirectResponse Redirect kembali dengan success message
     * @throws \Illuminate\Auth\Access\AuthorizationException Jika user bukan pemilik
     * @throws BusinessTripException Jika status tidak bisa dibatalkan (dihandle di service)
     */
    public function cancel(CancelBusinessTripRequest $request, BusinessTripRequest $businessTripRequest): RedirectResponse
    {
        $this->authorize('update', $businessTripRequest);
        $this->submissionService->cancel(
            $businessTripRequest, 
            $request->user()->id, 
            $request->validated('note')
        );

        return back()->with('success', 'Request cancelled');
    }
}
