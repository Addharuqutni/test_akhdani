<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'latitude', 'longitude', 'province_name', 'island_name', 'is_foreign', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
            'is_foreign' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Scope untuk filter hanya kota yang aktif.
     * 
     * Scope ini digunakan untuk query kota yang is_active = true.
     * Kota yang nonaktif tidak akan muncul di dropdown saat create pengajuan.
     * 
     * Cara penggunaan:
     * - City::active()->get() → Ambil semua kota aktif
     * - City::active()->where('name', 'Jakarta')->first() → Cari Jakarta yang aktif
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query Query builder
     * @return \Illuminate\Database\Eloquent\Builder Query builder dengan filter is_active = true
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
