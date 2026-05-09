<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'username', 'email', 'password', 'role_id', 'is_active',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Relasi ke tabel roles.
     * 
     * Setiap user memiliki 1 role (admin, sdm, atau pegawai).
     * 
     * @return BelongsTo Relasi belongsTo ke Role model
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Relasi ke pengajuan perjalanan dinas yang dibuat oleh user ini.
     * 
     * Satu user (pegawai) bisa membuat banyak pengajuan.
     * 
     * @return HasMany Relasi hasMany ke BusinessTripRequest model (sebagai employee)
     */
    public function businessTripRequests(): HasMany
    {
        return $this->hasMany(BusinessTripRequest::class, 'employee_id');
    }

    /**
     * Relasi ke pengajuan yang di-approve oleh user ini.
     * 
     * Satu user (SDM) bisa approve banyak pengajuan.
     * 
     * @return HasMany Relasi hasMany ke BusinessTripRequest model (sebagai approver)
     */
    public function approvedRequests(): HasMany
    {
        return $this->hasMany(BusinessTripRequest::class, 'approved_by');
    }

    /**
     * Cek apakah user memiliki role tertentu.
     * 
     * Helper method untuk cek role user dengan aman (null-safe).
     * 
     * Cara penggunaan:
     * - $user->hasRole('admin') → true jika user adalah admin
     * - $user->hasRole('sdm') → true jika user adalah SDM
     * - $user->hasRole('pegawai') → true jika user adalah pegawai
     * 
     * @param string $code Kode role (admin, sdm, pegawai)
     * @return bool True jika user memiliki role tersebut, false jika tidak
     */
    public function hasRole(string $code): bool
    {
        return optional($this->role)->code === $code;
    }
}
