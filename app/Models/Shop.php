<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use App\Models\Season;
use App\Models\Quality;
use App\Models\BusinessInfo;
use App\Models\ShopPackage;
use App\Traits\HasCdnUrl;


class Shop extends Model
{
    use HasFactory, HasCdnUrl;

    protected $guarded = ['id'];
    protected $fillable = [
        'name',
        'user_id',
        'phone',
        'address',
        'lat',
        'long',
        'logo_id', 
        'banner_id',
        'description',
        'status',
        'denial_message',
        'denied_at',
        'vat_number',
        'business_location',
        'company_name',
        'gender'
    ];

    protected $casts = [
        'denied_at' => 'datetime'
    ];

    /**
     * Get the shop user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function seasons(): HasMany
    {
        return $this->hasMany(Season::class);
    }

    public function qualities(): HasMany
    {
        return $this->hasMany(Quality::class);
    }

    /**
     * get emploees for this shop
     */
    public function employees(): HasMany
    {
        return $this->hasMany(User::class, 'shop_id');
    }

    /**
     * get withdraw model for this user.
     */
    public function withdraws(): HasMany
    {
        return $this->hasMany(Withdraw::class, 'shop_id');
    }

    /**
     * get gift model for this shop.
     */
    public function gifts(): HasMany
    {
        return $this->hasMany(Gift::class, 'shop_id');
    }

    /**
     * Get the logo media for the Shop.
     */
    public function mediaLogo(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'logo_id');
    }

    /**
     * Retrieve the media banner for this instance.
     */
    public function mediaBanner(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'banner_id');
    }

    /**
     * get all gallery images for this shop
     */
    public function galleries(): HasMany
    {
        return $this->hasMany(Gallery::class, 'shop_id');
    }

    /**
     * Get the logo for the Shop as an attribute.
     */
    public function logo(): Attribute
    {
        $logo = asset('default/logo.png');
        if ($this->mediaLogo) {
            $logo = $this->transformUrl($this->mediaLogo->src);
        }

        return Attribute::make(
            get: fn () => $logo
        );
    }

    /**
     * Get the banner for the Shop as an attribute.
     */
    public function banner(): Attribute
    {
        $banner = asset('default/default.jpg');
        if ($this->mediaBanner) {
            $banner = $this->transformUrl($this->mediaBanner->src);
        }

        return Attribute::make(
            get: fn () => $banner
        );
    }

    /**
     * Get all of the products for the Shop.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Retrieve the categories associated with the shop.
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'shop_categories');
    }

    /**
     * Retrieve the sub categories associated with the shop.
     */
    public function subCategories(): HasMany
    {
        return $this->hasMany(SubCategory::class);
    }

  
    /**
     * Get all of the coupons for the Shop.
     */
    public function coupons(): HasMany
    {
        return $this->hasMany(Coupon::class);
    }



    /**
     * Get the sizes for the shop.
     */
    public function sizes(): HasMany
    {
        return $this->hasMany(Size::class, 'shop_id');
    }

    /**
     * Get all of the units for the Shop.
     */
    public function units(): HasMany
    {
        return $this->hasMany(Unit::class);
    }

    /**
     * Get all of the orders for the Shop.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get all of the banners for the Shop.
     */
    public function banners(): HasMany
    {
        return $this->hasMany(Banner::class, 'shop_id');
    }

    /**
     * Scope a query to only include active shops.
     *
     * @param  Builder  $builder  The query builder
     * @return mixed
     */
    public function scopeIsActive(Builder $builder)
    {
        return $builder->whereHas('user', function ($query) {
            $query->where('is_active', 1);
        });
    }

    /**
     * Get all of the reviews for the Shop.
     *
     * @return HasMany.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'shop_id');
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
            get: fn () => (float) number_format($avgRating > 0 ? $avgRating : 5, 1, '.', ''),
        );
    }

    public function businessInfo()
    {
        return $this->hasOne(BusinessInfo::class);
    }

    public function package()
    {
        return $this->hasOne(ShopPackage::class);
    }

    public function scopeWithUser($query)
    {
        return $query->with('user');
    }
}
