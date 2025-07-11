<?php

namespace App\Http\Controllers\Shop;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Repositories\FlashSaleRepository;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Show the application dashboard.
     */
    public function index()
    {
        $shop = generaleSetting('shop');

        $totalOrder = $shop->orders()->count();
        $totalProduct = $shop->products()->count();
        $totalCategories = $shop->categories->count();
       
        $totalSize = $shop->sizes->count();
        $totalUnit = $shop->units->count();
        $package = $shop->package;
        $needsPayment = !$package || !$package->is_paid;
        $reachedLimit = $package && $shop->products()->count() >= $package->product_limit;

        $orderStatuses = OrderStatus::cases();

        $productObject = $shop->products();
        $orderObject = $shop->orders();

        $topSellingProducts = (clone $productObject)->whereHas('orders')->withCount('orders')->orderBy('orders_count', 'desc')->limit(8)->get();

        $topReviewProducts = (clone $productObject)->whereHas('reviews')->withAvg('reviews as average_rating', 'rating')->orderBy('average_rating', 'desc')->limit(8)->get();

        $latestOrders = (clone $orderObject)->latest('id')->limit(8)->get();

        $topFavorites = (clone $productObject)->whereHas('favorites')->withCount('favorites')->orderBy('favorites_count', 'desc')->limit(8)->get();

        $pendingWithdraw = $shop->withdraws()->where('status', 'pending')->sum('amount');
        $alreadyWithdraw = $shop->withdraws()->where('status', 'approved')->sum('amount');
        $deniedWithddraw = $shop->withdraws()->where('status', 'denied')->sum('amount');

        $totalWithdraw = $pendingWithdraw + $alreadyWithdraw;

        $totalPosSales = Order::withoutGlobalScopes()->where('shop_id', $shop->id)->where('pos_order', true)->where('order_status', OrderStatus::DELIVERED->value)->sum('payable_amount');

        $totalDeliveryCollected = (clone $orderObject)->where('order_status', OrderStatus::DELIVERED->value)->sum('delivery_charge');

        $flashSale = FlashSaleRepository::getIncoming();

        $user = auth()->user();
        if ($user && ! $user->is_active) {
           

            return view('shop.profile.edit', compact('shop'));
        }


        return view('shop.dashboard', compact('totalOrder', 'totalProduct', 'orderStatuses', 'topSellingProducts', 'topReviewProducts', 'latestOrders', 'topFavorites', 'totalCategories', 'totalSize', 'totalUnit', 'totalWithdraw', 'totalPosSales', 'totalDeliveryCollected', 'pendingWithdraw', 'alreadyWithdraw', 'deniedWithddraw', 'flashSale', 'package', 'needsPayment', 'reachedLimit'));
    }
}
