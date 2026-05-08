<?php

namespace App\Http\Controllers;

use App\Models\BusinessTripRequest;
use Illuminate\View\View;

class StatusHistoryController extends Controller
{
    public function byRequest(BusinessTripRequest $businessTripRequest): View
    {
        $this->authorize('view', $businessTripRequest);
        $histories = $businessTripRequest->statusHistories()->with('actor')->latest()->get();

        return view('history.by-request', compact('businessTripRequest', 'histories'));
    }
}
