<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use App\Traits\HasCdnUrl;

class FlashSale extends Model
{
    use HasFactory, HasCdnUrl;

    protected $guarded = ['id'];

    /**
     * Get the products for the flash sale.
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'flash_sale_products')->withPivot('price', 'quantity', 'discount', 'sale_quantity');
    }

    /**
     * Get the media record associated with the model.
     */
    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class);
    }

    /**
     * Create a thumbnail for the media, with a default image if none is present.
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
