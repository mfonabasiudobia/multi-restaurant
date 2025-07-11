<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckShopPackage
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        // Allow root users to bypass package check
        if ($user && $user->hasRole('root')) {
            return $next($request);
        }

        // Check package for non-root shop users
        if ($user && $user->hasRole('shop')) {
            $shop = $user->shop;
            
            // Check if shop has an active package
            if (!$shop->package || !$shop->package->is_paid) {
                return response()->json([
                    'show_package_modal' => true,
                    'message' => 'Please subscribe to a package to continue',
                    'package' => $shop->package
                ]);
            }

            // Check if product limit is reached
            if ($shop->products()->count() >= $shop->package->product_limit) {
                return response()->json([
                    'show_package_modal' => true,
                    'message' => 'You have reached your product limit. Please upgrade your package.',
                    'package' => $shop->package
                ]);
            }
        }

        return $next($request);
    }
} 