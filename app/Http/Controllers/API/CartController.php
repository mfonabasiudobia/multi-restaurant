<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CartRequest;
use App\Http\Requests\CheckoutRequest;
use App\Repositories\CartRepository;
use App\Repositories\ProductRepository;
use App\Models\DeliveryWeight;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $isBuyNow = request()->is_buy_now ?? false;



        $carts = auth()->user()->customer->carts();

        if ($isBuyNow) {
            $carts->where('is_buy_now', true);
        }

        $carts = $carts->get();


        $groupCart = $carts->groupBy('shop_id');
        $shopWiseProducts = CartRepository::ShopWiseCartProducts($groupCart);

        return $this->json('cart list', [
            'total' => $carts->count(),
            'cart_items' => $shopWiseProducts,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CartRequest $request)
    {


        $isBuyNow = $request->is_buy_now ?? false;

        $product = ProductRepository::find($request->product_id);

        $quantity = $request->quantity ?? 1;

        $customer = auth()->user()->customer;
        $cart = $customer->carts()->where('product_id', $product->id)->first();

        if ($isBuyNow) {

            $buyNowCart = $customer->carts()->where('is_buy_now', true)->first();

            if ($buyNowCart && $buyNowCart->product_id != $request->product_id) {
                $buyNowCart->delete();
            }
        }

        /* $prodyctqty = ['pqty' => $product->quantity, 'cartqty' => $cart?->quantity, 'quantity' => $quantity];
        echo "<pre>";
        print_r($prodyctqty);
        echo "</pre>";
        die();*/
        if (($product->quantity < $quantity) || ($product->quantity <= $cart?->quantity)) {
            return $this->json('Sorry! product cart quantity is limited. No more stock', [], 422);
        }

        // store or update cart
        CartRepository::storeOrUpdateByRequest($request, $product);

        $carts = $customer->carts()->where('is_buy_now', $isBuyNow)->get();

        $groupCart = $carts->groupBy('shop_id');
        $shopWiseProducts = CartRepository::ShopWiseCartProducts($groupCart);

        return $this->json('product added to cart', [
            'total' => $carts->count(),
            'cart_items' => $shopWiseProducts,
        ], 200);
    }

    /**
     * increase cart quantity
     */
    public function increment(CartRequest $request)
    {
        $isBuyNow = $request->is_buy_now ?? false;

        $product = ProductRepository::find($request->product_id);

        $customer = auth()->user()->customer;

        $cart = $customer->carts()?->where('product_id', $product->id)->where('is_buy_now', $isBuyNow)->first();

        if (! $cart) {
            return $this->json('Sorry product not found in cart', [], 422);
        }

        $quantity = $cart->quantity;

        $flashSale = $product->flashSales?->first();

        $flashSaleProduct = $flashSale?->products()->where('id', $product->id)->first();

        $productQty = $product->quantity;

        if ($flashSaleProduct) {
            $flashSaleQty = $flashSaleProduct->pivot->quantity - $flashSaleProduct->pivot->sale_quantity;

            if ($flashSaleQty > 0) {
                $productQty = $flashSaleQty;
            }
        }

        if ($productQty > $quantity) {
            $cart->update([
                'quantity' => $quantity + 1,
            ]);
        } else {
            return $this->json('Sorry! product cart quantity is limited. No more stock', [], 422);
        }

        $carts = $customer->carts()->where('is_buy_now', $isBuyNow)->get();
        $groupCart = $carts->groupBy('shop_id');
        $shopWiseProducts = CartRepository::ShopWiseCartProducts($groupCart);

        return $this->json('product quantity increased', [
            'total' => $carts->count(),
            'cart_items' => $shopWiseProducts,
        ], 200);
    }

    /**
     * decrease cart quantity
     * */
    public function decrement(CartRequest $request)
    {

        $isBuyNow = $request->is_buy_now ?? false;

        $product = ProductRepository::find($request->product_id);
        $customer = auth()->user()->customer;
        $cart = $customer->carts()?->where('product_id', $product->id)->where('is_buy_now', $isBuyNow)->first();

        if (! $cart) {
            return $this->json('Sorry product not found in cart', [], 422);
        }

        $message = 'product removed from cart';

        if ($cart->quantity > 1) {
            $cart->update([
                'quantity' => $cart->quantity - 1,
            ]);

            $message = 'product quantity decreased';
        } else {
            $cart->delete();
        }

        $carts = $customer->carts()->where('is_buy_now', $isBuyNow)->get();
        $groupCart = $carts->groupBy('shop_id');
        $shopWiseProducts = CartRepository::ShopWiseCartProducts($groupCart);

        return $this->json($message, [
            'total' => $carts->count(),
            'cart_items' => $shopWiseProducts,
        ], 200);
    }

    public function checkout(Request $request)
    {



        $isBuyNow = $request->is_buy_now ?? false;

        $shopIds = $request->shop_ids ?? [];
        $customer = auth()->user()->customer;

        $carts = $customer->carts();

        if ($isBuyNow) {
            $carts->where('is_buy_now', true);
        }
        // Filter by selected shops if provided

        if (!empty($shopIds)) {
            $carts = $carts->whereIn('shop_id', $shopIds);
        }

        // Filter by selected products if provided
        if ($request->has('selected_products') && is_array($request->selected_products) && count($request->selected_products) > 0) {
            $carts = $carts->whereIn('product_id', $request->selected_products);
        }
        /*  echo "<pre>";
        print_r($carts->toRawSql());
        die();*/
        $carts = $carts->get();



        $checkout = CartRepository::checkoutByRequest($request, $carts);

        $groupCart = $carts->groupBy('shop_id');
        $shopWiseProducts = CartRepository::ShopWiseCartProducts($groupCart);

        $message = 'Checkout information';

        $applyCoupon = false;

        if ($request->coupon_code && $checkout['coupon_discount'] > 0) {
            $applyCoupon = true;
            $message = 'Coupon applied';
        } elseif ($request->coupon_code) {
            $message = 'Coupon not applied';
        }

        // Get total weight from request or calculate from cart items
        $totalWeight = $request->total_weight ?? 0;

        // If total weight is not provided in request, calculate it from cart items
        if (!$totalWeight) {
            $totalWeight = 0;
            foreach ($carts as $cart) {
                // Get product weight from size
                $productWeight = 0;

                // If product has size, use size as weight
                if ($cart->size_id) {
                    $size = $cart->product->sizes()->where('id', $cart->size_id)->first();
                    if ($size) {
                        $productWeight = (float) $size->name;
                    }
                }

                // Add to total weight (weight * quantity)
                $totalWeight += ($productWeight * $cart->quantity);
            }
        }

        // Find delivery weight charge based on total weight
        $weightCharge = \App\Models\DeliveryWeight::active()
            ->where('min_weight', '<=', $totalWeight)
            ->where('max_weight', '>=', $totalWeight)
            ->first();

        // If no weight charge found, use default or minimum charge
        if (!$weightCharge) {
            // Try to find the minimum weight charge
            $weightCharge = \App\Models\DeliveryWeight::active()
                ->orderBy('min_weight', 'asc')
                ->first();
        }

        $deliveryCharge = $weightCharge ? $weightCharge->price : 0;

        // Log the delivery charge calculation for debugging
        \Log::info('Delivery charge calculation', [
            'total_weight' => $totalWeight,
            'weight_charge' => $weightCharge ? $weightCharge->price : 'No weight charge found',
            'delivery_charge' => $deliveryCharge
        ]);

        // Update checkout with calculated delivery charge
        $checkout['delivery_charge'] = $deliveryCharge;
        $checkout['payable_amount'] = ($checkout['total_amount'] - $checkout['coupon_discount']) + $deliveryCharge + (0);

        // Get weight unit
        $weightUnit = \App\Models\Unit::where('is_weight', true)
            ->whereNull('shop_id')
            ->first();

        return $this->json($message, [
            'checkout' => $checkout,
            'apply_coupon' => $applyCoupon,
            'checkout_items' => $shopWiseProducts,
            'total_weight' => $totalWeight,
            'delivery_charge' => $deliveryCharge,
            'weight_unit' => $weightUnit ? $weightUnit->name : 'KG',
        ]);
    }

    public function destroy(CartRequest $request)
    {
        $isBuyNow = $request->is_buy_now ?? false;

        $customer = auth()->user()->customer;

        $carts = $customer->carts()->where('product_id', $request->product_id)->get();



        if ($carts->isEmpty()) {
            return $this->json('Sorry product not found in cart', [], 422);
        }

        foreach ($carts as $cart) {
            $cart->delete();
        }

        $carts = $customer->carts()->where('is_buy_now', $isBuyNow)->get();
        $groupCart = $carts->groupBy('shop_id');
        $shopWiseProducts = CartRepository::ShopWiseCartProducts($groupCart);

        return $this->json('product removed from cart', [
            'total' => $carts->count(),
            'cart_items' => $shopWiseProducts,
        ], 200);
    }
}
