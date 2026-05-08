<?php

use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\BusinessTripRequestController;
use App\Livewire\Approvals\ApprovalDetail;
use App\Livewire\Approvals\ApprovalQueue;
use App\Livewire\Auth\LoginPage;
use App\Livewire\BusinessTrips\MyTripList;
use App\Livewire\BusinessTrips\TripDetail;
use App\Livewire\BusinessTrips\TripForm;
use App\Livewire\Cities\CityManagement;
use App\Livewire\Dashboard\DashboardIndex;
use App\Livewire\Shared\SubmissionHistory;
use App\Livewire\Users\UserManagement;
use Illuminate\Support\Facades\Route;

Route::get('/login', LoginPage::class)->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth', 'active'])->group(function () {
    Route::get('/', DashboardIndex::class)->name('dashboard');

    Route::middleware('role:admin')->group(function () {
        Route::get('/users', UserManagement::class)->name('users.index');
        Route::get('/cities', CityManagement::class)->name('cities.index');
    });

    Route::middleware('role:pegawai,admin,sdm')->group(function () {
        Route::get('/business-trips/create', TripForm::class)->name('trips.form');
        Route::get('/business-trips/my', MyTripList::class)->name('trips.my-list');
        Route::get('/business-trips/{id}', TripDetail::class)->name('trips.detail');
        Route::get('/history', SubmissionHistory::class)->name('history.index');
    });

    Route::middleware('role:sdm')->group(function () {
        Route::get('/approvals', ApprovalQueue::class)->name('approvals.queue');
        Route::get('/approvals/{id}', ApprovalDetail::class)->name('approvals.detail');
    });

    Route::prefix('/actions')->group(function () {
        Route::post('/business-trips/draft', [BusinessTripRequestController::class, 'storeDraft'])->name('actions.trips.store-draft');
        Route::put('/business-trips/{businessTripRequest}/draft', [BusinessTripRequestController::class, 'updateDraft'])->name('actions.trips.update-draft');
        Route::post('/business-trips/{businessTripRequest}/submit', [BusinessTripRequestController::class, 'submit'])->name('actions.trips.submit');
        Route::post('/business-trips/{businessTripRequest}/cancel', [BusinessTripRequestController::class, 'cancel'])->name('actions.trips.cancel');

        Route::middleware('role:sdm')->group(function () {
            Route::post('/approvals/{businessTripRequest}/approve', [ApprovalController::class, 'approve'])->name('actions.approvals.approve');
            Route::post('/approvals/{businessTripRequest}/reject', [ApprovalController::class, 'reject'])->name('actions.approvals.reject');
        });
    });
});
