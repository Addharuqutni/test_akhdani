<?php

namespace App\Http\Controllers;

use App\Enums\BusinessTripStatus;
use App\Models\BusinessTripRequest;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
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
