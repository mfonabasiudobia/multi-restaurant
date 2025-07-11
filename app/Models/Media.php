<?php

namespace App\Models;

use App\Traits\HasCdnUrl;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    use HasFactory, HasCdnUrl;

    protected $guarded = ['id'];

    public function srcUrl(): Attribute
    {
        $image = asset('default/default.jpg');

        if ($this->src) {
            $image = $this->transformUrl($this->src);
        }

        return Attribute::make(
            get: fn () => $image,
        );
    }
}
