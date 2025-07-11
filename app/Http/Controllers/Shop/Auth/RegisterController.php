<?php

namespace App\Http\Controllers\Shop\Auth;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Repositories\ShopRepository;
use App\Repositories\BusinessInfoRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class RegisterController extends Controller
{
    public function create()
    {
        // Get active packages
        $packages = Package::where('is_active', true)->get();
        
        \Log::info('Shop registration page loaded', [
            'available_packages' => $packages->count()
        ]);
        
        return view('shop.auth.create', [
            'packages' => $packages,
            'generaleSetting' => generaleSetting(),
            'directory' => app()->getLocale() == 'ar' ? 'rtl' : 'ltr'
        ]);
    }

    public function store(Request $request)
    {
        \Log::info('Register store method hit', [
            'all_data' => $request->all()
        ]);

        // Update validation rules to make business fields required
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|min:2|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string',
            'shop_name' => 'required|string|min:3|max:255',
            'address' => 'nullable|string',
            'password' => 'required|min:6|confirmed',
            'profile_photo' => 'required|image|mimes:jpg,jpeg,png,gif|max:2048',
            'shop_logo' => 'required|image|mimes:jpg,jpeg,png,gif|max:2048',
            'shop_banner' => 'required|image|mimes:jpg,jpeg,png,gif|max:2048',
            'description' => 'nullable|string|max:200',
            // Make business fields required
            'vat_number' => 'required|string|max:50',
            'business_location' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'gender' => 'nullable|string|in:male,female',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $shop = ShopRepository::storeByRequest($request);
            \Log::info('Shop created', ['shop' => $shop]);

            return response()->json([
                'success' => true,
                'message' => 'Mulțumim pentru înregistrare!',
                'redirect' => route('shop.login')
            ]);

        } catch (\Exception $e) {
            \Log::error('Shop registration failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function edit()
    {
        // Get the authenticated user's shop
        $myShop = auth()->user()->shop;
        
        if (!$myShop || !$myShop->denied_at) {
            return redirect()->route('shop.dashboard.index');
        }

        // Get active packages
        $packages = Package::where('is_active', true)->get();
        
        // Pass denial message from shop
        $denialMessage = $myShop->denial_message;
        
        return view('shop.auth.create', compact('packages', 'denialMessage', 'myShop'));
    }

    public function update(Request $request)
    {
        $shop = auth()->user()->shop;
        
        if (!$shop) {
            return redirect()->route('shop.dashboard.index')
                ->withErrors(['error' => 'Shop not found']);
        }

        if (!$shop->denied_at) {
            return redirect()->route('shop.dashboard.index')
                ->withErrors(['error' => 'Shop is not in denied state']);
        }

        try {
            DB::beginTransaction();

            // Build validation rules
            $rules = [
                'first_name' => 'required|string|min:2|max:100',
                'last_name' => 'nullable|string|max:155',
                'phone' => 'required|string',
                'shop_name' => 'required|string|min:3|max:255',
                'address' => 'nullable|string',
                'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
                'shop_logo' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
                'shop_banner' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
                'description' => 'nullable|string|max:200',
                'vat_number' => 'required|string|max:50',
                'business_location' => 'required|string|max:255',
                'company_name' => 'required|string|max:255',
                'gender' => 'nullable|string|in:male,female',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return back()
                    ->withErrors($validator)
                    ->withInput();
            }

            // Update shop and user information
            ShopRepository::updateByRequest($shop, $request);
            
            // Update business information
            BusinessInfoRepository::updateOrCreateByShop($shop, $request);

            // Clear denial status
            $shop->update([
                'denial_message' => null,
                'denied_at' => null
            ]);

            // Set user as inactive until admin approves again
            $shop->user()->update(['is_active' => false]);

            DB::commit();

            \Log::info('Shop updated successfully', ['shop_id' => $shop->id]);

            // Log out the user after successful update
            auth()->logout();

            return response()->json([
                'success' => true,
                'message' => 'Informațiile au fost actualizate!',
                'redirect' => route('shop.login'),
                'resubmit' => true
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Shop update failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'A apărut o eroare. Vă rugăm să încercați din nou.'
            ], 422);
        }
    }
} 