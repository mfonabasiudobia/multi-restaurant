<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ShopCreateRequest;
use App\Http\Requests\ShopPasswordResetRequest;
use App\Models\Notification;
use App\Models\Review;
use App\Models\Shop;
use App\Models\Package;
use App\Repositories\ShopRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Rules\PhoneNumber;

class ShopController extends Controller
{
    /**
     * Display a listing of the shops.
     */
    public function index()
    {
        $shops = Shop::withUser()
            ->latest()
            ->paginate(20);

        // Add logging to debug
        \Log::info('Shop list count: ' . $shops->count());

        return view('admin.shop.index', compact('shops'));
    }

    /**
     * Create a new shop.
     */
    public function create()
    {
        \Log::warning('View Create Shop');

        return view('admin.shop.create');
    }

    /**
     * Store a newly created shop.
     */
    public function store(ShopCreateRequest $request)
    {
        // store shop from shopRepository
        ShopRepository::storeByRequest($request);

        \Log::warning('Store Shop');

        return to_route('admin.shop.index')->withSuccess(__('Shop created successfully'));
    }

    /**
     * Display the specified shop.
     */
    public function show(Shop $shop)
    {
        Notification::where('url', '/admin/shops/' . $shop->id)->whereNull('shop_id')->where('is_read', false)->update(['is_read' => true]);

        // Load business info
        $businessInfo = $shop->businessInfo;
        $package = $shop->package;

        \Log::warning('Show Shop');

        return view('admin.shop.show', compact('shop', 'businessInfo', 'package'));
    }

    /**
     * Edit the shop.
     */
    public function edit(Shop $shop)
    {

        \Log::warning('View Edit Shop');

        return view('admin.shop.edit', compact('shop'));
    }

    /**
     * Update the shop.
     */
    public function update(Request $request, Shop $shop)
    {
        $request->validate([

            'shop_name' => 'required|string|min:3|max:255',
            'email' => 'required|email',
            'phone' => 'required',
            'address' => 'required|string',
            'first_name' => 'required|string|min:2|max:255',
            'last_name' => 'nullable|string|max:255',
            'gender' => ['nullable', 'string'],
            'description' => ['nullable', 'string', 'max:200'],

            'vat_number' => 'required|string|max:50',
            'business_location' => 'nullable|string|max:255',
            'company_name' => 'nullable|string|max:255',
        ]);


        if (app()->environment() == 'local' && $shop->user->email == 'shop@readyecommerce.com') {
            return back()->with('demoMode', 'You can not update the shop in demo mode');
        }

        // Update shop basic info
        ShopRepository::updateByRequest($shop, $request);

        // Update or create business info
        $shop->businessInfo()->updateOrCreate(
            ['shop_id' => $shop->id],
            [
                'vat_number' => $request->vat_number,
                'business_location' => $request->business_location,
                'company_name' => $request->company_name
            ]
        );

        \Log::warning('Update Shop');

        return to_route('admin.shop.index')->withSuccess(__('Shop updated successfully'));
    }

    /**
     * Toggle the status of the shop user.
     */
    public function statusToggle(Shop $shop)
    {
        if (app()->environment() == 'local' && $shop->user->email == 'shop@readyecommerce.com') {
            return back()->with('demoMode', 'You can not update status of the shop in demo mode');
        }

        $user = $shop->user;
        if ($user->hasRole('root')) {
            return back()->with('error', __('You can not update status of the root shop'));
        }

        \Log::warning('Update Shop Status');

        // Update the user status
        $shop->user()->update([
            'is_active' => !$shop->user->is_active,
        ]);

        // Create initial package when activating shop
        if ($shop->user->is_active) {
            // Get default package
            $defaultPackage = Package::where('is_active', true)->first();

            if ($defaultPackage) {
                $shop->package()->updateOrCreate(
                    ['shop_id' => $shop->id],
                    [
                        'package_id' => $defaultPackage->id,
                        'product_limit' => $defaultPackage->product_limit,
                        'package_price' => $defaultPackage->price,
                        'is_paid' => false,
                        'expires_at' => now()->addDays($defaultPackage->duration_days)
                    ]
                );
            }
        }

        return back()->withSuccess(__('Status updated successfully'));
    }
    /**
     * Deny the shop and send a message via email.
     */
    public function denyShop(Request $request, Shop $shop)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        if (app()->environment() == 'local' && $shop->user->email == 'shop@readyecommerce.com') {
            return back()->with('demoMode', 'You can not deny the shop in demo mode');
        }

        $user = $shop->user;
        if ($user->hasRole('root')) {
            return back()->with('error', __('You can not deny the root shop'));
        }

        // Store denial message in the database
        $shop->update([
            'denial_message' => $request->message,
            'denied_at' => now()
        ]);

        // Send the denial message via email
        try {
            \Mail::to($user->email)->send(new \App\Mail\DenyShopMail($request->message));
        } catch (\Exception $e) {
            \Log::error('Failed to send denial email: ' . $e->getMessage());
        }

        // Update the shop status
        $shop->user()->update([
            'is_active' => false,
        ]);

        return back()->withSuccess(__('Shop denied successfully'));
    }

    /**
     * Display the shop orders.
     */
    public function orders(Shop $shop)
    {
        $orders = $shop->orders()->paginate(20);

        return view('admin.shop.orders', compact('shop', 'orders'));
    }

    /**
     * Display the shop products.
     */
    public function products(Shop $shop)
    {
        $products = $shop->products()->paginate(20);

        return view('admin.shop.products', compact('shop', 'products'));
    }

    /**
     * Display the shop category.
     */
    public function categories(Shop $shop)
    {
        $categories = $shop->categories()->paginate(20);

        return view('admin.shop.category', compact('shop', 'categories'));
    }

    /**
     * Display the shop reviews.
     */
    public function reviews(Shop $shop)
    {
        $reviews = $shop->reviews()->withoutGlobalScopes()->latest('id')->paginate(20);

        return view('admin.shop.reviews', compact('shop', 'reviews'));
    }

    public function resetPassword(Shop $shop, ShopPasswordResetRequest $request)
    {
        if (app()->environment() == 'local' && $shop->user->email == 'shop@readyecommerce.com') {
            return back()->with('demoMode', 'You can not update status of the shop in demo mode');
        }

        // Update the user status
        $shop->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->withSuccess(__('Shop password reset successfully'));
    }

    public function toggleReview($reviewId)
    {
        $review = Review::withoutGlobalScopes()->find($reviewId);

        $review->update([
            'is_active' => ! $review->is_active,
        ]);

        $message = $review->is_active ? __('Review activated successfully') : __('Review deactivated successfully');

        return back()->withSuccess($message);
    }
}
