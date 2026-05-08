<?php

namespace App\Http\Requests\BusinessTrip;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBusinessTripDraftRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'purpose' => ['required', 'string', 'min:5'],
            'departure_date' => ['required', 'date'],
            'return_date' => ['required', 'date', 'after_or_equal:departure_date'],
            'origin_city_id' => ['required', Rule::exists('cities', 'id')->where(fn ($q) => $q->where('is_active', true))],
            'destination_city_id' => ['required', Rule::exists('cities', 'id')->where(fn ($q) => $q->where('is_active', true))],
        ];
    }
}
