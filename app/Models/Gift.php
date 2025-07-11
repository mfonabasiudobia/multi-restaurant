<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use App\Traits\HasCdnUrl;

class Gift extends Model
{
    use HasFactory, HasCdnUrl;

    protected $guarded = ['id'];

    /**
     * Get the shop that owns the Gift
     */
    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class, 'order_gifts', 'gift_id', 'order_id')->withPivot('address_id', 'sender_name', 'receiver_name', 'price', 'note')->withTimestamps();
    }

    public function carts(): HasMany
    {
        return $this->hasMany(Cart::class, 'gift_id');
    }

    /**
     * get active records
     */
    public function scopeIsActive($query)
    {
        return $query->where('is_active', 1);
    }

    /**
     * get media model for this user.
     */
    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class);
    }

    /**
     * Generate a thumbnail for the media, if available, or use the default image.
     */
    public function thumbnail(): Attribute
    {
        $thumbnail = asset('default/default.jpg');
        if ($this->media) {
            $thumbnail = $this->transformUrl($this->media->src);
        }

        return Attribute::make(
            get: fn () => $thumbnail
        );
    }
}
