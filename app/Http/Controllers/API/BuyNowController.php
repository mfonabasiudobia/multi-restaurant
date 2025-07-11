<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\BuyNowStoreRequest;
use App\Models\Product;
use App\Models\DeliveryWeight;
use App\Models\Order;
use Illuminate\Http\Request;

class BuyNowController extends Controller
{
    public function store(BuyNowStoreRequest $request)
    {

        try {
            $data = $request->validated();

            // Get product
            $product = Product::findOrFail($data['product_id']);

            // Calculate delivery charge based on weight
            $deliveryCharge = 0;
            if ($request->weight) {
                $deliveryWeight = DeliveryWeight::where('min_weight', '<=', $request->weight)
                    ->where('max_weight', '>=', $request->weight)
                    ->first();

                // If no weight charge found, use default or minimum charge
                if (!$deliveryWeight) {
                    // Try to find the minimum weight charge
                    $deliveryWeight = DeliveryWeight::orderBy('min_weight', 'asc')
                        ->first();
                }

                $deliveryCharge = $deliveryWeight ? $deliveryWeight->price : 0;

                // Log the delivery charge calculation for debugging
                \Log::info('Buy Now delivery charge calculation', [
                    'weight' => $request->weight,
                    'weight_charge' => $deliveryWeight ? $deliveryWeight->price : 'No weight charge found',
                    'delivery_charge' => $deliveryCharge
                ]);
            }

            // Create order with delivery charge
            $order = Order::create([
                // ... other order data
                'delivery_charge' => $deliveryCharge,
                'total_amount' => ($product->discount_price ?: $product->price) + $deliveryCharge,
                // ... other fields
            ]);

            return response()->json([
                'message' => __('Order placed successfully'),
                'data' => $order
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
