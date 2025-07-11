<?php

namespace App\Models;

use App\Traits\HasCdnUrl;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use HasFactory, HasCdnUrl;

    protected $guarded = ['id'];

    protected $appends = ['thumbnails'];
    protected $casts = [
        'is_delivery_via_gls' => 'boolean',
        'weight' => 'float',
    ];

    protected $fillable = [
        'shop_id',
        'name',
        'code',
        'short_description',
        'description',
        'price',
        'discount_price',
        'weight',
        'is_active',
        'is_approve',
        'quantity',
        'min_order_quantity',
        'buy_price',
        'is_new',
        // Logistics fields
        'bag_number',
        'location',
        'row',
        // Relations
        'season_id',
        'quality_id',
        'unit_id',
        'size_id',
        'sub_category_id',
        'video_id'
    ];

    protected $with = ['videos']; // Eager load videos by default

    /**
     * Get the videos associated with the product.
     */
    public function videos(): HasMany
    {
        return $this->hasMany(Video::class);
    }

    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }



    public function quality()
    {
        return $this->belongsTo(Quality::class);
    }

    /**
     * Get the processed videos associated with the product.
     */
    public function processedVideos(): Collection
    {
        return $this->videos()->get()->map(function ($video) {
            $video->src = $video->url;
            $video->thumbnail = $video->thumbnail_url;
            return $video;
        });
    }


    /**
     * Retrieve the shop that this model belongs to.
     *
     * @return BelongsTo The shop that this model belongs to.
     */
    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    /**
     * get the vat taxes that owns the product.
     */
    public function vatTaxes(): BelongsToMany
    {
        return $this->belongsToMany(VatTax::class, 'product_vat_taxes')->where('is_active', true);
    }

    /**
     * Retrieve the categories associated with the current model.
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'product_categories');
    }

    /**
     * Retrieve the subcategories associated with the current model.
     */
    public function subcategories(): BelongsToMany
    {
        return $this->belongsToMany(SubCategory::class, 'product_subcategories', 'product_id', 'sub_category_id');
    }

    /**
     * Retrieve the flash sales associated with the model.
     *
     * @return BelongsToMany The flash sales associated with the model.
     */
    public function flashSales(): BelongsToMany
    {
        $currentDate = Carbon::now()->format('Y-m-d');
        $currentTime = Carbon::now()->format('H:i:s');

        return $this->belongsToMany(FlashSale::class, 'flash_sale_products', 'product_id', 'flash_sale_id')
            ->withPivot('price', 'quantity', 'discount', 'sale_quantity')
            ->where('status', 1)
            ->where(function ($query) use ($currentDate, $currentTime) {
                $query->where('start_date', '<', $currentDate)
                    ->orWhere(function ($query) use ($currentDate, $currentTime) {
                        $query->where('start_date', '=', $currentDate)
                            ->where('start_time', '<=', $currentTime);
                    });
            })
            ->where(function ($query) use ($currentDate, $currentTime) {
                $query->where('end_date', '>', $currentDate)
                    ->orWhere(function ($query) use ($currentDate, $currentTime) {
                        $query->where('end_date', '=', $currentDate)
                            ->where('end_time', '>=', $currentTime);
                    });
            })->latest('id');
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
        $defaultThumbnail = asset('default/default.jpg');
        $thumbnail = $defaultThumbnail;

        if ($this->medias()->exists()) {
            $firstMedia = $this->medias()->first();
            if ($firstMedia && $firstMedia->src) {
                $thumbnail = $this->transformUrl($firstMedia->src);
            }
        }else if ($this->videos()->exists()) {
            $firstMedia = $this->videos()->first();
            if ($firstMedia && $firstMedia->thumbnail) {
                $thumbnail = $this->transformUrl($firstMedia->thumbnail);
            }
        }

        return new Attribute(
            get: fn() => $thumbnail
        );
    }

    /**
     * Get the medias for the product.
     */
    public function medias(): BelongsToMany
    {
        return $this->belongsToMany(Media::class, 'product_thumbnails');
    }

    /**
     * Get the thumbnails attribute.
     */
    protected function thumbnails(): Attribute
    {
        return Attribute::make(
            get: function () {
                return $this->additionalThumbnails();
            }
        );
    }

    /**
     * Generate additional thumbnails for the medias.
     */
    public function additionalThumbnails(): Collection
    {
        $thumbnails = collect([]);
        foreach ($this->medias as $media) {
            if ($media->src) {
                $thumbnails[] = (object) [
                    'id' => $media->id,
                    'thumbnail' => $this->transformUrl($media->src)
                ];
            }
        }
        return $thumbnails;
    }

    /**
     * Retrieves the reviews associated with this object.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany The reviews associated with this object.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Calculates the average rating of the reviews.
     *
     * @return Attribute The average rating attribute.
     */
    public function averageRating(): Attribute
    {
        $avgRating = $this->reviews()->avg('rating');

        return new Attribute(
            get: fn() => (float) number_format($avgRating > 0 ? $avgRating : 5, 1, '.', '')
        );
    }




    /**
     * sizes function.
     */
    public function sizes(): BelongsToMany
    {
        return $this->belongsToMany(Size::class, 'product_sizes')->withPivot('price', 'product_id');
    }

    /**
     * get the brand that owns the product.
     */

    /**
     * get the units that owns the product.
     */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    /**
     * Retrieve the orders associated with the model.
     */
    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class, 'order_products')->withPivot('quantity', 'color', 'unit', 'size', 'is_gift', 'price');
    }

    /**
     * Filter the given builder by active status.
     *
     * @param  Builder  $builder  The builder to filter.
     * @return Builder The filtered builder.
     */
    public function scopeIsActive(Builder $builder)
    {
        return $builder->where('is_active', true)->where('is_approve', true)->whereHas('shop', function ($query) {
            return $query->whereHas('user', function ($query) {
                $query->where('is_active', 1);
            });
        });
    }

    /**
     * Calculate the discount percentage based on the given price and discount price.
     */
    public static function getDiscountPercentage($price, $discountPrice)
    {
        return $discountPrice ? ($price - $discountPrice) * 100 / $price : 0;
    }

    /**
     * get the favorites from the model.
     */
    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function logistics()
    {
        return $this->hasOne(Logistics::class, 'bag_number', 'bag_number');
    }
}
