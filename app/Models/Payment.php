<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Payment extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $fillable = [
        'status',
        'shop_id',
        'package_id',
        'proof_id',
        'payment_method',
        'amount',
        'currency',
        'stripe_session_id',
        'is_paid',
        'payment_token'
    ];

    protected $casts = [
        'is_paid' => 'boolean',
        'amount' => 'decimal:2'
    ];

    /**
     * Get all of the orders for the Payment
     */
    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class, 'order_payments', 'payment_id', 'order_id');
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function proof()
    {
        return $this->belongsTo(Media::class, 'proof_id');
    }

    public function paymentProof()
    {
        return $this->hasOne(PaymentProof::class);
    }
}
