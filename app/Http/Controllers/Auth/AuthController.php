<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Proses login user.
     * 
     * Function ini akan:
     * 1. Validasi input (username dan password wajib diisi)
     * 2. Cek kredensial di database
     * 3. Cek apakah user aktif (is_active = true)
     * 4. Jika valid, buat session baru
     * 5. Regenerate session ID untuk keamanan
     * 6. Redirect ke dashboard
     * 
     * Keamanan:
     * - Hanya user dengan is_active = true yang bisa login
     * - Session ID di-regenerate untuk mencegah session fixation
     * - Password di-hash dengan bcrypt
     * 
     * @param Request $request HTTP request dengan username dan password
     * @return RedirectResponse Redirect ke dashboard jika berhasil, kembali ke login jika gagal
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if (!Auth::attempt($credentials + ['is_active' => true])) {
            return back()->withErrors(['username' => 'Username/password tidak valid']);
        }

        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    /**
     * Proses logout user.
     * 
     * Function ini akan:
     * 1. Logout user (hapus authentication)
     * 2. Invalidate session (hapus semua data session)
     * 3. Regenerate CSRF token untuk keamanan
     * 4. Redirect ke halaman login
     * 
     * Keamanan:
     * - Session di-invalidate untuk mencegah session hijacking
     * - CSRF token di-regenerate untuk mencegah CSRF attacks
     * 
     * @param Request $request HTTP request
     * @return RedirectResponse Redirect ke halaman login
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
