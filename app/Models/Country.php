<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'currency_code',
        'tax_rate',
        'tax_settings',
        'is_active',
    ];

    protected $casts = [
        'tax_settings' => 'array',
        'is_active' => 'boolean',
        'tax_rate' => 'decimal:2',
    ];

    /**
     * Get all tenants for this country.
     */
    public function tenants(): HasMany
    {
        return $this->hasMany(Tenant::class);
    }
}