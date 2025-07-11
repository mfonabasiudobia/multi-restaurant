<?php

namespace App\Repositories;

use App\Http\Requests\ShopCreateRequest;
use App\Models\Shop;
use App\Models\User;
use App\Models\Package;
use App\Repositories\UserRepository;
use App\Repositories\MediaRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ShopRepository extends Repository
{
    /**
     * base method
     *
     * @method model()
     */
    public static function model()
    {
        return Shop::class;
    }

    /**
     * new shop creation by request.
     */
    public static function storeByRequest($request)
    {
        DB::beginTransaction();
        
        try {
            // Handle profile photo
            $mediaId = null;
            if ($request->hasFile('profile_photo')) {
                $media = MediaRepository::storeByRequest($request->profile_photo, 'users/profile', 'image');
                $mediaId = $media ? $media->id : null;
            }

            // Create user
            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'name' => $request->first_name . ' ' . $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'address' => $request->address ?? '',
                'media_id' => $mediaId,
                'is_active' => false
            ]);

            // Handle file uploads
            $thumbnail = null;
            if ($request->hasFile('shop_logo')) {
                $thumbnail = MediaRepository::storeByRequest($request->shop_logo, 'shops/logo', 'image');
            }

            $banner = null;
            if ($request->hasFile('shop_banner')) {
                $banner = MediaRepository::storeByRequest($request->shop_banner, 'shops/banner', 'image');
            }

            // Create shop
            $shop = Shop::create([
                'user_id' => $user->id,
                'name' => $request->shop_name,
                'address' => $request->address ?? '',
                'logo_id' => $thumbnail ? $thumbnail->id : null,
                'banner_id' => $banner ? $banner->id : null,
                'description' => $request->description,
                'status' => false
            ]);

            // Create business info
            $shop->businessInfo()->create([
                'vat_number' => $request->vat_number,
                'business_location' => $request->business_location,
                'company_name' => $request->company_name
            ]);

            // Update user with shop_id
            $user->update([
                'shop_id' => $shop->id
            ]);

            // Create initial package
            if ($request->package_id) {
                $package = Package::findOrFail($request->package_id);
                $shop->package()->create([
                    'package_id' => $package->id,
                    'product_limit' => $package->product_limit,
                    'package_price' => $package->price,
                    'is_paid' => false,
                    'expires_at' => now()->addDays($package->duration_days)
                ]);
            }

            // Assign shop role
            $user->assignRole('shop');

            DB::commit();
            return $shop;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * update shop by request.
     */
    public static function updateByRequest($shop, $request)
    {
        if (!$shop) {
            throw new \Exception('Shop not found');
        }

        DB::beginTransaction();
        try {
            // Handle profile photo
            if ($request->hasFile('profile_photo')) {
                // If no existing media, create new one
                if (!$shop->user->media) {
                    $media = MediaRepository::storeByRequest(
                        $request->profile_photo,
                        'users/profile',
                        'image'
                    );
                } else {
                    // Update existing media
                    $media = MediaRepository::updateByRequest(
                        $request->profile_photo,
                        'users/profile',
                        'image',
                        $shop->user->media
                    );
                }
                $shop->user->media_id = $media ? $media->id : null;
                $shop->user->save();
            }

            // Update user information using UserRepository
            if ($shop->user) {
                UserRepository::updateByRequest($request, $shop->user);
            }

            // Update shop information
            $shop->update([
                'name' => $request->shop_name,
                'address' => $request->address ?? $shop->address,
            ]);

            // Update shop logo if provided
            if ($request->hasFile('shop_logo')) {
                $thumbnail = MediaRepository::updateByRequest(
                    $request->shop_logo,
                    'shops/logo',
                    'image',
                    $shop->mediaLogo
                );
                $shop->update([
                    'logo_id' => $thumbnail ? $thumbnail->id : null
                ]);
            }

            // Update shop banner if provided
            if ($request->hasFile('shop_banner')) {
                $banner = MediaRepository::updateByRequest(
                    $request->shop_banner,
                    'shops/banner',
                    'image',
                    $shop->mediaBanner
                );
                $shop->update([
                    'banner_id' => $banner ? $banner->id : null
                ]);
            }

            DB::commit();
            return $shop;
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Failed to update shop', [
                'shop_id' => $shop->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public static function updateShopSetting(Shop $shop, $request): Shop
    {
        $openTime = $request->opening_time ? Carbon::parse($request->opening_time)->format('H:i:s') : $shop->opening_time;
        $closeTime = $request->closing_time ? Carbon::parse($request->closing_time)->format('H:i:s') : $shop->closing_time;
        // update shop
        self::update($shop, [
            'delivery_charge' => $request->delivery_charge ?? 0,
            'min_order_amount' => $request->min_order_amount ?? $shop->min_order_amount,
            'prefix' => $request->prefix ?? $shop->prefix,
            'opening_time' => $openTime,
            'closing_time' => $closeTime,
            'estimated_delivery_time' => $request->estimated_delivery_time ?? $shop->estimated_delivery_time,
            'off_day' => $request->off_day ? array_map(function ($value) {
                return strtolower($value);
            }, $request->off_day) : null,

        ]);

        return $shop;
    }

    public static function updateShopInfo(Shop $shop, $request): Shop
    {
        // shop logo
        $thumbnail = self::updateLogo($shop, $request);

        // shop banner
        $banner = self::updateBanner($shop, $request);

        // update shop
        self::update($shop, [
            'name' => $request->name,
            'logo_id' => $thumbnail ? $thumbnail->id : null,
            'banner_id' => $banner ? $banner->id : null,
            'address' => $request->address,
            'description' => $request->description,
        ]);

        return $shop;
    }

    /**
     * Update or create a logo for the shop.
     */
    private static function updateLogo($shop, $request)
    {
        $thumbnail = $shop?->mediaLogo;
        // if logo and thumbnail is not null
        if ($request->hasFile('shop_logo')) {
            // update logo from mediaRepository
            $thumbnail = MediaRepository::updateByRequest(
                $request->shop_logo,
                'shops/logo',
                'image',
                $thumbnail
            );
        }

        return $thumbnail;
    }

    /**
     * Update or create a banner for the shop.
     */
    private static function updateBanner($shop, $request)
    {
        $thumbnail = $shop?->mediaBanner;
        // if banner and thumbnail is not null
        if ($request->hasFile('shop_banner')) {
            // update banner from mediaRepository
            $thumbnail = MediaRepository::updateByRequest(
                $request->shop_banner,
                'shops/banner',
                'image',
                $thumbnail
            );
        }

        return $thumbnail;
    }
}
