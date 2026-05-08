<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusinessTripStatusHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_trip_request_id', 'from_status', 'to_status', 'changed_by', 'note',
    ];

    public function request(): BelongsTo
    {
        return $this->belongsTo(BusinessTripRequest::class, 'business_trip_request_id');
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
