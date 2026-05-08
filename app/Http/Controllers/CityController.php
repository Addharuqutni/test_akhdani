<?php

namespace App\Http\Controllers;

use App\Http\Requests\City\StoreCityRequest;
use App\Http\Requests\City\UpdateCityRequest;
use App\Models\City;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CityController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q'));
        $cities = City::query()
            ->when($q, fn ($query) => $query->where('name', 'like', "%{$q}%")->orWhere('province_name', 'like', "%{$q}%"))
            ->latest()
            ->paginate(15);

        return view('cities.index', compact('cities', 'q'));
    }

    public function store(StoreCityRequest $request): RedirectResponse
    {
        City::query()->create($request->validated());
        return back()->with('success', 'City created');
    }

    public function update(UpdateCityRequest $request, City $city): RedirectResponse
    {
        $city->update($request->validated());
        return back()->with('success', 'City updated');
    }

    public function deactivate(City $city): RedirectResponse
    {
        $city->update(['is_active' => false]);
        return back()->with('success', 'City deactivated');
    }
}
