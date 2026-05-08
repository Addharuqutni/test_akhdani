<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BusinessTripRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_number', 'employee_id', 'purpose', 'departure_date', 'return_date',
        'origin_city_id', 'destination_city_id', 'duration_days', 'distance_km',
        'allowance_rule_type', 'allowance_currency', 'allowance_per_day', 'allowance_total',
        'status', 'submitted_at', 'approved_at', 'approved_by', 'rejected_at',
        'rejection_reason', 'approval_note',
    ];

    protected function casts(): array
    {
        return [
            'departure_date' => 'date',
            'return_date' => 'date',
            'submitted_at' => 'datetime',
            'approved_at' => 'datetime',
            'rejected_at' => 'datetime',
            'distance_km' => 'float',
            'allowance_per_day' => 'float',
            'allowance_total' => 'float',
        ];
    }

    public function employee(): BelongsTo { return $this->belongsTo(User::class, 'employee_id'); }
    public function approver(): BelongsTo { return $this->belongsTo(User::class, 'approved_by'); }
    public function originCity(): BelongsTo { return $this->belongsTo(City::class, 'origin_city_id'); }
    public function destinationCity(): BelongsTo { return $this->belongsTo(City::class, 'destination_city_id'); }
    public function statusHistories(): HasMany { return $this->hasMany(BusinessTripStatusHistory::class); }
}
