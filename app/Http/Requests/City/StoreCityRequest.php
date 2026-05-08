<?php

namespace App\Http\Requests\City;

use Illuminate\Foundation\Http\FormRequest;

class StoreCityRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'province_name' => ['required', 'string', 'max:255'],
            'island_name' => ['nullable', 'string', 'max:255'],
            'is_foreign' => ['required', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
