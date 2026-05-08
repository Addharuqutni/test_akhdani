<?php

namespace App\Http\Controllers;

use App\Enums\BusinessTripStatus;
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

    public function indexMy(Request $request): View
    {
        $rows = BusinessTripRequest::query()
            ->where('employee_id', $request->user()->id)
            ->with(['originCity', 'destinationCity'])
            ->latest()
            ->paginate(15);

        return view('business-trips.my-list', compact('rows'));
    }

    public function show(BusinessTripRequest $businessTripRequest): View
    {
        $this->authorize('view', $businessTripRequest);
        $businessTripRequest->load(['employee', 'originCity', 'destinationCity', 'statusHistories.actor']);
        return view('business-trips.detail', ['trip' => $businessTripRequest]);
    }

    public function storeDraft(StoreBusinessTripDraftRequest $request): RedirectResponse
    {
        $trip = $this->submissionService->createDraft($request->user(), $request->validated());
        return redirect()->route('trips.detail', $trip)->with('success', 'Draft created');
    }

    public function updateDraft(UpdateBusinessTripDraftRequest $request, BusinessTripRequest $businessTripRequest): RedirectResponse
    {
        if ($businessTripRequest->status !== BusinessTripStatus::DRAFT->value) {
            abort(422, 'Hanya draft yang bisa diupdate');
        }

        $this->authorize('update', $businessTripRequest);
        $this->submissionService->updateDraft($businessTripRequest, $request->validated());

        return back()->with('success', 'Draft updated');
    }

    public function submit(BusinessTripRequest $businessTripRequest): RedirectResponse
    {
        $this->authorize('update', $businessTripRequest);
        $this->submissionService->submit($businessTripRequest, auth()->id());

        return back()->with('success', 'Request submitted');
    }

    public function cancel(Request $request, BusinessTripRequest $businessTripRequest): RedirectResponse
    {
        $this->authorize('update', $businessTripRequest);
        $this->submissionService->cancel($businessTripRequest, (int) $request->user()->id, $request->string('note')->toString());

        return back()->with('success', 'Request cancelled');
    }
}
