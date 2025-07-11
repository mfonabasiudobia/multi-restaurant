<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopPackage extends Model
{
    protected $fillable = [
        'shop_id',
        'package_id',
        'product_limit',
        'package_price',
        'is_paid',
        'expires_at'
    ];

    protected $casts = [
        'is_paid' => 'boolean',
        'expires_at' => 'datetime'
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }
} 