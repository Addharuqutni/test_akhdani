<?php

namespace App\Models;

use App\Enums\RoleCode;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code'];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function matches(string|RoleCode $code): bool
    {
        return $this->code === ($code instanceof RoleCode ? $code->value : $code);
    }
}
