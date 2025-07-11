<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $fillable = [
        'name',
        'price',
        'product_limit',
        'duration_days',
        'is_active',
        'is_popular',
        'features'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_popular' => 'boolean',
        'features' => 'array',
        'price' => 'decimal:2'
    ];

    public function shopPackages()
    {
        return $this->hasMany(ShopPackage::class);
    }
} 