<?php

namespace App\Providers;

use App\Models\BusinessTripRequest;
use App\Policies\BusinessTripRequestPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        BusinessTripRequest::class => BusinessTripRequestPolicy::class,
    ];

    public function boot(): void
    {
        //
    }
}
