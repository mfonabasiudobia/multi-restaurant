<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessInfo extends Model
{
    protected $fillable = [
        'shop_id',
        'vat_number',
        'business_location',
        'company_name'
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
} 