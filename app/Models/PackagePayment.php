<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackagePayment extends Model
{
    protected $fillable = [
        'payment_id',
        'shop_id',
        'package_id',
        'amount',
        'status',
        'expires_at'
    ];

    protected $casts = [
        'expires_at' => 'datetime'
    ];

    public function payment()
    {
        return $this->belongsTo(Payment::class)->withDefault();
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class)->withDefault();
    }

    public function package()
    {
        return $this->belongsTo(Package::class)->withDefault();
    }
} 