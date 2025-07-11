<?php

namespace App\Http\Controllers;

use App\Models\InnerAd;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function details(Request $request)
    {
        // ... existing code ...
        
        $innerAds = InnerAd::where('status', true)->latest()->get();
        \Log::info('inner ads');
        \Log::info($innerAds);
        
        return response()->json([
            'status' => true,
            'message' => 'Product details retrieved successfully',
            'data' => [
                'product' => $product,
                'inner_ads' => $innerAds,
                // ... other data ...
            ]
        ]);
    }
} 