<?php

namespace App\Http\Controllers\Shop\Auth;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\AdminLoginRequest;
use App\Http\Requests\ShopCreateRequest;
use App\Models\GoogleReCaptcha;
use App\Models\Package;
use App\Models\Shop;
use App\Models\User;
use App\Repositories\ShopRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    /**
     * Show the application login page.
     */
    public function index()
    {
        \Log::info('Shop login page loaded');
        $GoogleReCaptcha = GoogleReCaptcha::first();

        return view('shop.auth.login', compact('GoogleReCaptcha'));
    }

    /**
     * Handle an authentication attempt.
     */
    public function login(Request $request)
    {
        \Log::info('Shop login attempt', ['email' => $request->email]);
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        \Log::info('Credentials validated', ['credentials' => $credentials]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            
            \Log::info('User authenticated', ['user_id' => $user->id]);

            if (!$user->hasRole('shop')) {
                Auth::logout();
                return back()->withErrors([
                    'email' => __('Invalid credentials for shop login'),
                ]);
            }

            // Check if shop exists
            $shop = Shop::where('user_id', $user->id)->first();
            if (!$shop) {
                \Log::error('No shop found for user', ['user_id' => $user->id]);
                Auth::logout();
                return back()->withErrors([
                    'email' => __('No shop found for this account'),
                ]);
            }

            // If shop is denied, redirect to edit page
            if ($shop->denied_at) {
                return redirect()->route('shop.register.edit')
                    ->with('denial_message', $shop->denial_message);
            }

            // Check if user is inactive (except for denied shops that need to edit)
            if (!$user->is_active && !$shop->denied_at) {
                Auth::logout();
                return back()->withErrors([
                    'email' => __('Your account is not active. Please wait for admin approval.'),
                ]);
            }

            return redirect()->intended(route('shop.dashboard.index'));
        }

        return back()->withErrors([
            'email' => __('The provided credentials do not match our records.'),
        ]);
    }

    /**
     * Check user exists or not and check password.
     */
    private function checkCredentials(AdminLoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();
        if ($user && Hash::check($request->password, $user->password)) {
            return $user;
        }

        return false;
    }

    public function create()
    {
        // Get active packages
        $packages = Package::where('is_active', true)->get();
        
        // Log packages for debugging
        \Log::info('Available packages:', ['count' => $packages->count(), 'packages' => $packages]);
        
        // Get denial message from session if exists
        $denialMessage = session('denial_message');

        // Set myShop to null for registration page
        $myShop = null;
        
        return view('shop.auth.create', compact('packages', 'denialMessage', 'myShop'));
    }

    public function store(ShopCreateRequest $request)
    {
        try {
            \Log::info('Shop registration attempt', [
                'request_data' => $request->except(['password', 'password_confirmation', 'profile_photo', 'shop_logo', 'shop_banner'])
            ]);

            // Validate package exists
            $package = Package::findOrFail($request->package_id);
            \Log::info('Selected package:', ['package' => $package]);

            $shop = ShopRepository::storeByRequest($request);

            \Log::info('Shop created successfully', [
                'shop_id' => $shop->id,
                'user_id' => $shop->user_id,
                'shop_name' => $shop->name
            ]);

            return to_route('shop.login')
                ->with('success', __('Your account is pending. Please wait for the admin to activate your account!'));
        } catch (\Exception $e) {
            \Log::error('Shop registration failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()
                ->withInput()
                ->withError(__('Registration failed: ') . $e->getMessage());
        }
    }
    public function update(ShopCreateRequest $request, Shop $shop)
    {
        \Log::info('Shop Update');
        \Log::info($request->all());
        if (app()->environment() == 'local' && $shop->user->email == '') {
            // Add your logic here
        }

        // Add your logic here for other environments or conditions
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request)
    {
        $user = auth()->user();

        // logout
        auth()->logout();

        // invalidate session
        $request->session()->invalidate();

        // regenerate session
        $request->session()->regenerateToken();

        $cacheKeys = [
            'admin_all_orders',
            'shop_all_orders',
        ];

        foreach (OrderStatus::cases() as $status) {
            $cacheKeys[] = 'admin_status_'.Str::camel($status->value);
            $cacheKeys[] = 'shop_status_'.Str::camel($status->value);
        }

        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }
        Cache::forget('user_permissions_'.$user->id);
        Cache::forget('user_non_permissions_'.$user->id);

        return to_route('shop.login')->withSuccess('Logout successfully');
    }
}
