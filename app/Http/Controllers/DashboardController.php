<?php

namespace App\Http\Controllers;

use App\Enums\BusinessTripStatus;
use App\Models\BusinessTripRequest;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Tampilkan halaman dashboard dengan statistik pengajuan perjalanan dinas.
     * 
     * Function ini akan menampilkan statistik yang berbeda tergantung role user:
     * 
     * **Untuk Pegawai:**
     * - Total pengajuan milik pegawai tersebut
     * - Jumlah pengajuan yang sedang disubmit (menunggu approval)
     * - Jumlah pengajuan yang sudah diapprove
     * - Jumlah pengajuan yang ditolak
     * 
     * **Untuk Admin/SDM:**
     * - Total semua pengajuan di sistem
     * - Jumlah pengajuan yang menunggu approval
     * - Jumlah pengajuan yang sudah diapprove
     * - Jumlah pengajuan yang ditolak
     * - Total uang saku yang sudah diapprove (dalam rupiah)
     * 
     * Note: Menggunakan `clone $base` untuk menghindari query builder mutation.
     * 
     * @param Request $request HTTP request dengan user yang sedang login
     * @return View View dashboard dengan data statistik
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        if ($user->hasRole('pegawai')) {
            $base = BusinessTripRequest::query()->where('employee_id', $user->id);
            $stats = [
                'total' => (clone $base)->count(),
                'submitted' => (clone $base)->where('status', BusinessTripStatus::SUBMITTED->value)->count(),
                'approved' => (clone $base)->where('status', BusinessTripStatus::APPROVED->value)->count(),
                'rejected' => (clone $base)->where('status', BusinessTripStatus::REJECTED->value)->count(),
            ];
        } else {
            $base = BusinessTripRequest::query();
            $stats = [
                'total' => (clone $base)->count(),
                'submitted' => (clone $base)->where('status', BusinessTripStatus::SUBMITTED->value)->count(),
                'approved' => (clone $base)->where('status', BusinessTripStatus::APPROVED->value)->count(),
                'rejected' => (clone $base)->where('status', BusinessTripStatus::REJECTED->value)->count(),
                'approved_total_amount' => (clone $base)->where('status', BusinessTripStatus::APPROVED->value)->sum('allowance_total'),
            ];
        }

        return view('dashboard.index', compact('stats'));
    }
}
