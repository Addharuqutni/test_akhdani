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
    /**
     * Tampilkan daftar kota dengan fitur pencarian.
     * 
     * Function ini akan:
     * 1. Ambil query parameter 'q' untuk pencarian (opsional)
     * 2. Query kota dari database
     * 3. Jika ada pencarian, filter berdasarkan nama kota ATAU nama provinsi
     * 4. Urutkan dari yang terbaru
     * 5. Pagination 15 items per halaman
     * 
     * Contoh pencarian:
     * - /cities?q=Jakarta → Cari kota yang mengandung "Jakarta"
     * - /cities?q=Jawa → Cari kota di provinsi yang mengandung "Jawa"
     * 
     * @param Request $request HTTP request dengan query parameter 'q' (opsional)
     * @return View View dengan daftar kota (variables: $cities, $q)
     */
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q'));
        $cities = City::query()
            ->when($q, fn ($query) => $query->where('name', 'like', "%{$q}%")->orWhere('province_name', 'like', "%{$q}%"))
            ->latest()
            ->paginate(15);

        return view('cities.index', compact('cities', 'q'));
    }

    /**
     * Simpan kota baru ke database.
     * 
     * Function ini akan:
     * 1. Validasi input (via StoreCityRequest)
     * 2. Simpan kota baru ke database
     * 3. Redirect kembali dengan success message
     * 
     * Data yang disimpan:
     * - name (nama kota)
     * - province_name (nama provinsi)
     * - latitude (koordinat GPS)
     * - longitude (koordinat GPS)
     * - is_active (default: true)
     * 
     * @param StoreCityRequest $request Request dengan data yang sudah tervalidasi
     * @return RedirectResponse Redirect kembali dengan success message
     */
    public function store(StoreCityRequest $request): RedirectResponse
    {
        City::query()->create($request->validated());
        return back()->with('success', 'City created');
    }

    /**
     * Update data kota yang sudah ada.
     * 
     * Function ini akan:
     * 1. Validasi input (via UpdateCityRequest)
     * 2. Update data kota di database
     * 3. Redirect kembali dengan success message
     * 
     * Data yang bisa diupdate:
     * - name (nama kota)
     * - province_name (nama provinsi)
     * - latitude (koordinat GPS)
     * - longitude (koordinat GPS)
     * - is_active (status aktif/nonaktif)
     * 
     * @param UpdateCityRequest $request Request dengan data yang sudah tervalidasi
     * @param City $city Kota yang akan diupdate (route model binding)
     * @return RedirectResponse Redirect kembali dengan success message
     */
    public function update(UpdateCityRequest $request, City $city): RedirectResponse
    {
        $city->update($request->validated());
        return back()->with('success', 'City updated');
    }

    /**
     * Nonaktifkan kota (soft deactivate).
     * 
     * Function ini akan:
     * 1. Set is_active = false
     * 2. Redirect kembali dengan success message
     * 
     * Note: 
     * - Kota tidak dihapus dari database (soft deactivate)
     * - Kota yang nonaktif tidak bisa dipilih saat create pengajuan
     * - Pengajuan yang sudah ada tetap valid
     * 
     * @param City $city Kota yang akan dinonaktifkan (route model binding)
     * @return RedirectResponse Redirect kembali dengan success message
     */
    public function deactivate(City $city): RedirectResponse
    {
        $city->update(['is_active' => false]);
        return back()->with('success', 'City deactivated');
    }
}
