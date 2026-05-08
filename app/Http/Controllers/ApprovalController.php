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

    public function queue(): View
    {
        $rows = BusinessTripRequest::query()
            ->with(['employee', 'originCity', 'destinationCity'])
            ->where('status', BusinessTripStatus::SUBMITTED->value)
            ->latest('submitted_at')
            ->paginate(15);

        return view('approvals.queue', compact('rows'));
    }

    public function show(BusinessTripRequest $businessTripRequest): View
    {
        $businessTripRequest->load(['employee', 'originCity', 'destinationCity', 'statusHistories.actor']);
        return view('approvals.detail', ['trip' => $businessTripRequest]);
    }

    public function approve(ApprovalDecisionRequest $request, BusinessTripRequest $businessTripRequest): RedirectResponse
    {
        $this->approvalService->approve($businessTripRequest, (int) auth()->id(), $request->input('approval_note'));
        return redirect()->route('approvals.queue')->with('success', 'Request approved');
    }

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
