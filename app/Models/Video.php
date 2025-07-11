<?php

namespace App\Models;

use App\Traits\HasCdnUrl;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory, HasCdnUrl;

    protected $guarded = ['id'];

    /**
     * Get the product associated with the video.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the full URL of the video.
     */
    public function getUrlAttribute(): string
    {
        return $this->transformUrl($this->src) ?? asset('default/default_video.jpg');
    }

    /**
     * Get the full URL of the thumbnail.
     */
    public function getThumbnailUrlAttribute(): string
    {
        return $this->transformUrl($this->thumbnail) ?? asset('default/default_video_thumb.jpg');
    }
}