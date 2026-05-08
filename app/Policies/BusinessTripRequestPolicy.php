<?php

namespace App\Policies;

use App\Models\BusinessTripRequest;
use App\Models\User;

class BusinessTripRequestPolicy
{
    public function view(User $user, BusinessTripRequest $trip): bool
    {
        if ($user->hasRole('admin') || $user->hasRole('sdm')) {
            return true;
        }

        return $trip->employee_id === $user->id;
    }

    public function update(User $user, BusinessTripRequest $trip): bool
    {
        return $trip->employee_id === $user->id && in_array($trip->status, ['draft', 'submitted'], true);
    }
}
