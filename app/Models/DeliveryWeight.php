<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryWeight extends Model
{
    use HasFactory;

    protected $fillable = [
        'min_weight',
        'max_weight', 
        'price',
        'is_active'
    ];

    protected $casts = [
        'min_weight' => 'float',
        'max_weight' => 'float',
        'price' => 'float',
        'is_active' => 'boolean'
    ];

    /**
     * Scope a query to only include active records.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
} 