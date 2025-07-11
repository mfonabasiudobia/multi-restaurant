<?php

namespace App\Models;

use App\Traits\HasCdnUrl;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class InnerAd extends Model
{
    use HasCdnUrl;

    protected $fillable = [
        'title',
        'image',
        'link',
        'status'
    ];

    protected $casts = [
        'status' => 'boolean'
    ];

    public function getImageAttribute($value)
    {
        return $this->transformUrl($value);
    }

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('d M Y');
    }
} 