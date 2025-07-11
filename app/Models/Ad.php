<?php

namespace App\Models;

use App\Traits\HasCdnUrl;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;


class Ad extends Model
{
    use HasFactory, HasCdnUrl;

    protected $guarded = ['id'];

    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'media_id');
    }

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
    public function redirectLink(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->attributes['redirectLink'] ?? null
        );
    }
}
