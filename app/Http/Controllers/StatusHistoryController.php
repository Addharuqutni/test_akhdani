<?php

namespace App\Http\Controllers;

use App\Models\BusinessTripRequest;
use Illuminate\View\View;

class StatusHistoryController extends Controller
{
    /**
     * Tampilkan history perubahan status untuk pengajuan tertentu.
     * 
     * Function ini akan:
     * 1. Cek authorization (apakah user berhak melihat pengajuan ini)
     * 2. Query semua history status dari pengajuan
     * 3. Eager load relasi 'actor' (user yang melakukan perubahan)
     * 4. Urutkan dari yang terbaru
     * 5. Tampilkan view dengan data history
     * 
     * History mencatat:
     * - Perubahan status (DRAFT → SUBMITTED → APPROVED/REJECTED/CANCELLED)
     * - Siapa yang melakukan perubahan
     * - Kapan perubahan dilakukan
     * - Catatan/alasan (jika ada)
     * 
     * @param BusinessTripRequest $businessTripRequest Pengajuan yang akan dilihat historynya (route model binding)
     * @return View View dengan data history (variables: $businessTripRequest, $histories)
     * @throws \Illuminate\Auth\Access\AuthorizationException Jika user tidak berhak akses
     */
    public function byRequest(BusinessTripRequest $businessTripRequest): View
    {
        $this->authorize('view', $businessTripRequest);
        $histories = $businessTripRequest->statusHistories()->with('actor')->latest()->get();

        return view('history.by-request', compact('businessTripRequest', 'histories'));
    }
}
