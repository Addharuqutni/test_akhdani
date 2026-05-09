<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Tampilkan daftar semua user.
     * 
     * Function ini akan:
     * 1. Query semua user dari database
     * 2. Eager load relasi 'role' untuk menghindari N+1 query
     * 3. Urutkan dari yang terbaru
     * 4. Pagination 15 items per halaman
     * 
     * Note: Hanya admin yang bisa akses halaman ini (dihandle di middleware).
     * 
     * @return View View dengan daftar user (variable: $users)
     */
    public function index(): View
    {
        $users = User::query()->with('role')->latest()->paginate(15);
        return view('users.index', compact('users'));
    }

    /**
     * Simpan user baru ke database.
     * 
     * Function ini akan:
     * 1. Validasi input (via StoreUserRequest)
     * 2. Hash password otomatis (via model cast)
     * 3. Simpan user baru ke database
     * 4. Redirect kembali dengan success message
     * 
     * Data yang disimpan:
     * - name (nama lengkap)
     * - username (untuk login)
     * - email (opsional)
     * - password (akan di-hash otomatis)
     * - role_id (role: admin, sdm, pegawai)
     * - is_active (default: true)
     * 
     * @param StoreUserRequest $request Request dengan data yang sudah tervalidasi
     * @return RedirectResponse Redirect kembali dengan success message
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        User::query()->create($request->validated());
        return back()->with('success', 'User created');
    }

    /**
     * Update data user yang sudah ada.
     * 
     * Function ini akan:
     * 1. Validasi input (via UpdateUserRequest)
     * 2. Jika password kosong, tidak update password (tetap pakai password lama)
     * 3. Jika password diisi, hash password baru otomatis (via model cast)
     * 4. Update data user di database
     * 5. Redirect kembali dengan success message
     * 
     * Data yang bisa diupdate:
     * - name (nama lengkap)
     * - username (untuk login)
     * - email (opsional)
     * - password (opsional, kosongkan jika tidak ingin ubah)
     * - role_id (role: admin, sdm, pegawai)
     * - is_active (status aktif/nonaktif)
     * 
     * @param UpdateUserRequest $request Request dengan data yang sudah tervalidasi
     * @param User $user User yang akan diupdate (route model binding)
     * @return RedirectResponse Redirect kembali dengan success message
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $payload = $request->validated();
        if (empty($payload['password'])) {
            unset($payload['password']);
        }

        $user->update($payload);
        return back()->with('success', 'User updated');
    }

    /**
     * Toggle status aktif/nonaktif user.
     * 
     * Function ini akan:
     * 1. Toggle is_active (true → false atau false → true)
     * 2. Redirect kembali dengan success message
     * 
     * Efek jika user dinonaktifkan:
     * - User tidak bisa login
     * - Jika sedang login, akan di-logout otomatis (via middleware)
     * - Data user tetap ada di database
     * 
     * @param User $user User yang akan di-toggle statusnya (route model binding)
     * @return RedirectResponse Redirect kembali dengan success message
     */
    public function toggleActive(User $user): RedirectResponse
    {
        $user->update(['is_active' => !$user->is_active]);
        return back()->with('success', 'User status updated');
    }
}
