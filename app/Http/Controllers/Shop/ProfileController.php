<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileRequest;
use App\Http\Requests\PasswordRequest;
use App\Repositories\ShopRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Display the profile page.
     */
    public function index()
    {
        $shop = generaleSetting('shop');
        $businessInfo = $shop->businessInfo;
        return view('shop.profile.index', compact('shop', 'businessInfo'));
    }

    /**
     * Display the profile edit page.
     */
    public function edit()
    {
        $shop = generaleSetting('shop');
        $businessInfo = $shop->businessInfo;
        return view('shop.profile.edit', compact('shop', 'businessInfo'));
    }

    /**
     * Update the profile.
     */
    public function update(ProfileRequest $request)
    {
        $shop = generaleSetting('shop');
        $user = auth()->user();

        // Log for debugging
        \Log::info('Shop profile update request', [
            'has_profile_photo' => $request->hasFile('profile_photo'),
            'has_logo' => $request->hasFile('logo'),
            'user_id' => $user->id,
            'shop_id' => $shop->id
        ]);

        // Handle profile photo upload using MediaRepository with S3
        if ($request->hasFile('profile_photo')) {
            $thumbnail = $user->media;
            if ($thumbnail == null) {
                $thumbnail = \App\Repositories\MediaRepository::storeByRequest(
                    $request->profile_photo,
                    'users/profile'
                );
                \Log::info('Created new media for profile photo', ['media_id' => $thumbnail->id]);
            } else {
                $thumbnail = \App\Repositories\MediaRepository::updateByRequest(
                    $request->profile_photo,
                    'users/profile',
                    'image',
                    $thumbnail
                );
                \Log::info('Updated existing media for profile photo', ['media_id' => $thumbnail->id]);
            }
            $user->update(['media_id' => $thumbnail->id]);
        }

        // Update user information
        $user->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone
        ]);

        // Update shop logo using MediaRepository with S3
        if ($request->hasFile('logo')) {
            $logoMedia = $shop->mediaLogo;
            if ($logoMedia == null) {
                $logoMedia = \App\Repositories\MediaRepository::storeByRequest(
                    $request->logo,
                    'shops/logo'
                );
                \Log::info('Created new media for shop logo', ['media_id' => $logoMedia->id]);
            } else {
                $logoMedia = \App\Repositories\MediaRepository::updateByRequest(
                    $request->logo,
                    'shops/logo',
                    'image',
                    $logoMedia
                );
                \Log::info('Updated existing media for shop logo', ['media_id' => $logoMedia->id]);
            }
            $shop->update(['media_logo_id' => $logoMedia->id]);
        }

        // Update shop information
        $shop->update([
            'name' => $request->shop_name,
            'address' => $request->address,
            'description' => $request->description,
            'min_order_amount' => $request->min_order_amount,
            'estimated_delivery_time' => $request->estimated_delivery_time
        ]);

        // Update or create business information
        $shop->businessInfo()->updateOrCreate(
            ['shop_id' => $shop->id],
            [
                'vat_number' => $request->vat_number,
                'business_location' => $request->business_location,
                'company_name' => $request->company_name
            ]
        );

        return to_route('shop.profile.index')->withSuccess(__('Profile updated successfully'));
    }

    /**
     * Display the change password page.
     */
    public function changePassword()
    {
        return view('shop.profile.change-password');
    }

    /**
     * Update the password.
     */
    public function updatePassword(PasswordRequest $request)
    {
        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withError(__('Current password is incorrect'));
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return to_route('shop.profile.index')->withSuccess(__('Password updated successfully'));
    }
}
