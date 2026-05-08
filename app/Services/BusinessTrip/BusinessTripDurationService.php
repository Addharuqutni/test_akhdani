<?php

namespace App\Services\BusinessTrip;

use Carbon\CarbonInterface;

class BusinessTripDurationService
{
    public function calculate(CarbonInterface $departureDate, CarbonInterface $returnDate): int
    {
        return max(1, $departureDate->diffInDays($returnDate) + 1);
    }
}
