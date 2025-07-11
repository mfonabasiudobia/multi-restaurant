<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'is_active' => 'boolean',
        'is_weight' => 'boolean'
    ];

    /**
     * Scope a query to only include active records.
     */
    public function scopeIsActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include weight units.
     */
    public function scopeIsWeight($query)
    {
        return $query->where('is_weight', true);
    }
}
