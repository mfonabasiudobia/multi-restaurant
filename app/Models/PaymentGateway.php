<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use App\Traits\HasCdnUrl;

class PaymentGateway extends Model
{
    use HasFactory, HasCdnUrl;

    protected $guarded = ['id'];

    /**
     * Get media
     */
    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class);
    }

    /**
     * Get logo
     */
    public function logo(): Attribute
    {
        $logo = asset('assets/gateway').'/'.$this->alias.'.png';
        if ($this->media) {
            $logo = $this->transformUrl($this->media->src);
        }

        return Attribute::make(
            get: fn () => $logo
        );
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function () {
            Cache::forget('payment_gateway');
        });

        static::updated(function () {
            Cache::forget('payment_gateway');
        });

        static::deleted(function () {
            Cache::forget('payment_gateway');
        });
    }
}
