<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Traits\HasCdnUrl;

class GeneraleSetting extends Model
{
    use HasFactory, HasCdnUrl;

    protected $guarded = ['id'];

    protected $table = 'generate_settings';

    public function mediaLogo()
    {
        return $this->belongsTo(Media::class, 'logo_id');
    }

    public function mediaAppLogo()
    {
        return $this->belongsTo(Media::class, 'app_logo_id');
    }

    public function mediaFavicon()
    {
        return $this->belongsTo(Media::class, 'favicon_id');
    }

    public function mediaFooterLogo()
    {
        return $this->belongsTo(Media::class, 'footer_logo_id');
    }

    public function mediaFooterQr()
    {
        return $this->belongsTo(Media::class, 'footer_qrcode_id');
    }

    public function logo(): Attribute
    {
        $logo = asset('default/logo.png');
        if ($this->mediaLogo) {
            $logo = $this->transformUrl($this->mediaLogo->src);
        }
        return Attribute::make(get: fn () => $logo);
    }

    public function appLogo(): Attribute
    {
        $logo = asset('default/logo.png');
        if ($this->mediaAppLogo) {
            $logo = $this->transformUrl($this->mediaAppLogo->src);
        }
        return Attribute::make(get: fn () => $logo);
    }

    public function favicon(): Attribute
    {
        $favicon = asset('default/favicon.png');
        if ($this->mediaFavicon) {
            $favicon = $this->transformUrl($this->mediaFavicon->src);
        }
        return Attribute::make(get: fn () => $favicon);
    }

    public function footerLogo(): Attribute
    {
        $logo = asset('default/logo.png');
        if ($this->mediaFooterLogo) {
            $logo = $this->transformUrl($this->mediaFooterLogo->src);
        }
        return Attribute::make(get: fn () => $logo);
    }

    public function footerQr(): Attribute
    {
        $qr = asset('default/qr.png');
        if ($this->mediaFooterQr) {
            $qr = $this->transformUrl($this->mediaFooterQr->src);
        }
        return Attribute::make(get: fn () => $qr);
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function () {
            Cache::forget('generale_setting');
            self::clearOrderCache();
        });

        static::updated(function () {
            Cache::forget('generale_setting');
            self::clearOrderCache();
        });

        static::deleted(function () {
            Cache::forget('generale_setting');
            self::clearOrderCache();
        });
    }

    protected static function clearOrderCache()
    {
        $cacheKeys = [
            'admin_all_orders',
            'shop_all_orders',
        ];

        foreach (OrderStatus::cases() as $status) {
            $cacheKeys[] = 'admin_status_'.Str::camel($status->value);
            $cacheKeys[] = 'shop_status_'.Str::camel($status->value);
        }

        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }
    }
}
