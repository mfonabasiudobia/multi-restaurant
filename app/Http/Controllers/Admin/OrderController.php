<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\Roles;
use App\Http\Controllers\Controller;
use App\Models\Driver;
use App\Models\GeneraleSetting;
use App\Models\Order;
use App\Models\User;
use App\Repositories\NotificationRepository;
use App\Repositories\OrderRepository;
use App\Services\NotificationServices;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a order list with filter status.
     */
    public function index($status = null)
    {
        $status = $status ? str_replace('_', ' ', $status) : '';

        $generaleSetting = GeneraleSetting::first();
        $shop = null;
        if ($generaleSetting?->shop_type == 'single') {
            $shop = User::role(Roles::ROOT->value)->first()?->shop;
        }

        $orders = OrderRepository::query()
            ->when($shop, function ($query) use ($shop) {
                return $query->where('shop_id', $shop->id);
            })
            ->when($status, function ($query) use ($status) {
                $query->where('order_status', $status);
            })->latest('id')->paginate(20);

        return view('admin.order.index', compact('orders', 'status'));
    }

    /**
     * Display the order details.
     */
    public function show(Order $order)
    {
        $orderStatus = OrderStatus::cases();
       \Log::info('Order status updated', ['order' => $order->id, 'status' => $order->order_status]);

        $riders = Driver::whereHas('user', function ($query) {
            return $query->where('is_active', true);
        })->get();
        $order->load('products', 'customer', 'shop');
        $products = $order->products;
        foreach ($products as $product) {
            $product->load('logistics');
        }
        
        $paymentProof = \DB::table('payment_proofs')->where('order_id', $order->id)->first();
        $paymentProofUrl = $paymentProof ? asset('storage/' . $paymentProof->file_path) : null;
        \Log::info('paymentProofUrl', ['paymentProofUrl' => $paymentProofUrl]);
        \Log::info('paymentProof', ['paymentProof' => $paymentProof]);

        return view('admin.order.show', compact('order', 'orderStatus', 'riders', 'paymentProofUrl','products'));
    }
    public function downloadPaymentProof(Order $order)
    {
        $paymentProof = \DB::table('payment_proofs')->where('order_id', $order->id)->first();
        if ($paymentProof) {
            return response()->download(storage_path('app/public/' . $paymentProof->file_path));
        }

        return redirect()->back()->with('error', 'Payment proof not found.');
    }

    /**
     * Update the order status.
     */
    public function statusChange(Order $order, Request $request)
    {
        $request->validate(['status' => 'required']);

        $order->update(['order_status' => $request->status]);

        $title = 'Order status updated';
        $message = 'Your order status updated to '.$request->status;
        $deviceKeys = $order->customer->user->devices->pluck('key')->toArray();

        if ($request->status == OrderStatus::CANCELLED->value) {
            foreach ($order->products as $product) {

                $qty = $product->pivot->quantity;

                $product->update(['quantity' => $product->quantity + $qty]);

                $flashsale = $product->flashSales?->first();
                $flashsaleProduct = null;

                if ($flashsale) {
                    $flashsaleProduct = $flashsale?->products()->where('id', $product->id)->first();

                    if ($flashsaleProduct && $product->pivot?->price) {
                        if ($flashsaleProduct->pivot->sale_quantity >= $qty && ($product->pivot?->price == $flashsaleProduct->pivot->price)) {
                            $flashsale->products()->updateExistingPivot($product->id, [
                                'sale_quantity' => $flashsaleProduct->pivot->sale_quantity - $qty,
                            ]);
                        }
                    }
                }
            }
        }

        try {
            NotificationServices::sendNotification($message, $deviceKeys, $title);
        } catch (\Throwable $th) {
        }

        $nofify = (object) [
            'title' => $title,
            'content' => $message,
            'user_id' => $order->customer->user_id,
            'type' => 'order',
        ];

        NotificationRepository::storeByRequest($nofify);

        return back()->with('success', __('Order status updated successfully.'));
    }

    /**
     * Update the payment status.
     */
    public function paymentStatusToggle(Order $order)
    {
        if ($order->payment_status->value == PaymentStatus::PAID->value) {
            return back()->with('error', __('When order is paid, payment status cannot be changed.'));
        }
        $order->update(['payment_status' => PaymentStatus::PAID->value]);

        $title = 'Payment status updated';
        $message = __('Your payment status updated to paid. order code: ').$order->prefix.$order->order_code;
        $deviceKeys = $order->customer->user->devices->pluck('key')->toArray();

        try {
            NotificationServices::sendNotification($message, $deviceKeys, $title);
        } catch (\Throwable $th) {
        }

        $nofify = (object) [
            'title' => $title,
            'content' => $message,
            'user_id' => $order->customer->user_id,
            'type' => 'order',
        ];

        NotificationRepository::storeByRequest($nofify);

        return back()->with('success', __('Payment status updated successfully'));
    }
}
