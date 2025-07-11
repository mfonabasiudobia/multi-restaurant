<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\PackagePayment;
use Illuminate\Support\Facades\DB;

class CheckPackageLimit
{
    public function handle(Request $request, Closure $next)
    {
        $shop = generaleSetting('shop');
        
        if (!$shop) {
            return redirect()->route('shop.package.payment')
                ->withError(__('Shop not found.'));
        }

        if (!$shop->package) {
            return redirect()->route('shop.package.payment')
                ->withError(__('Please subscribe to a package to continue.'));
        }

        if (!$shop->package->is_paid) {
            return redirect()->route('shop.package.payment')
                ->withError(__('Please complete your package payment to continue.'));
        }

        $approvedPayments = PackagePayment::where('shop_id', $shop->id)
            ->where('status', 'approved')
            ->whereNotNull('expires_at')
            ->where('expires_at', '>=', now())
            ->with('package')
            ->get();
        
        $totalLimit = $approvedPayments->sum(function($payment) {
            return $payment->package->product_limit ?? 0;
        });
        
        $productsCount = $shop->products()->count();
        
        if ($productsCount >= $totalLimit) {
            return redirect()->route('shop.package.payment')
                ->withError(__('You have reached your total product limit of ') . $totalLimit . 
                        __('. Please purchase an additional package.'));
        }

        return $next($request);
    }
} 