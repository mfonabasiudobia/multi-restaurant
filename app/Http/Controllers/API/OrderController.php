<?php

namespace App\Http\Controllers\API;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Events\ProductApproveEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\OrderRequest;
use App\Http\Resources\OrderDetailsResource;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Repositories\NotificationRepository;
use App\Repositories\OrderRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use App\Models\PaymentGateway;
use Stripe\Checkout\Session;
use App\Enums\PaymentStatus;

class OrderController extends Controller
{
    /**
     * Display a listing of the orders with status filter and pagination options.
     *
     * @param  Request  $request  The HTTP request
     * @return Some_Return_Value json Response
     *
     * @throws Some_Exception_Class If something goes wrong
     */
    public function index(Request $request)
    {
        $orderStatus = $request->order_status;

        $page = $request->page;
        $perPage = $request->per_page;
        $skip = ($page * $perPage) - $perPage;

        $customer = auth()->user()->customer;
        $orders = $customer->orders()
            ->when($orderStatus, function ($query) use ($orderStatus) {
                return $query->where('order_status', $orderStatus);
            })->latest('id');

        $total = $orders->count();

        // paginate
        $orders = $orders->when($perPage && $page, function ($query) use ($perPage, $skip) {
            return $query->skip($skip)->take($perPage);
        })->get();

        // return
        return $this->json('orders', [
            'total' => $total,
            'status_wise_orders' => [
                'all' => $customer->orders()->count(),
                'pending' => $customer->orders()->where('order_status', OrderStatus::PENDING->value)->count(),
                'confirm' => $customer->orders()->where('order_status', OrderStatus::CONFIRM->value)->count(),
                // 'processing' => $customer->orders()->where('order_status', OrderStatus::PROCESSING->value)->count(),
                // 'on_the_way' => $customer->orders()->where('order_status', OrderStatus::ON_THE_WAY->value)->count(),
                'delivered' => $customer->orders()->where('order_status', OrderStatus::DELIVERED->value)->count(),
                'cancelled' => $customer->orders()->where('order_status', OrderStatus::CANCELLED->value)->count(),
            ],
            'orders' => OrderResource::collection($orders),
        ]);
    }

    /**
     * Store a newly created order in storage.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(OrderRequest $request)
    {



        try {
            \Log::info('in controller', [$request->all()]);

            $isBuyNow = $request->is_buy_now ?? false;

            // Get carts
            $carts = auth()->user()->customer->carts()
                ->whereIn('shop_id', $request->shop_ids)
                ->where('is_buy_now', $isBuyNow);

            // Filter by selected products if provided
            if ($request->has('selected_products') && is_array($request->selected_products) && count($request->selected_products) > 0) {
                $carts = $carts->whereIn('product_id', $request->selected_products);
            }

            $carts = $carts->get();

            \Log::info('carts', [$carts]);

            if ($carts->isEmpty()) {
                \Log::info('carts is empty');
                return $this->json('Sorry shop cart is empty', [], 422);
            }

            // Process payment
            $paymentUrl = null;

            // Log the delivery charge and payable amount from the request
            \Log::info('Delivery charge from request:', [$request->delivery_charge]);
            \Log::info('Payable amount from request:', [$request->payable_amount]);

            // Handle card payments (map to the appropriate payment gateway)
            if ($request->payment_method === 'card') {
                // Use the payment_type to determine the actual payment method
                $paymentMethodName = strtoupper($request->payment_type === 'card' ? 'STRIPE' : $request->payment_type);
                \Log::info('Payment method for card payment', ['payment_method' => $paymentMethodName]);


                $paymentMethods = PaymentMethod::cases();
                $index = array_search($paymentMethodName, array_column(PaymentMethod::cases(), 'name'));

                if ($index === false) {
                    \Log::error('Payment method not found', ['payment_method' => $paymentMethodName]);
                    return $this->json('Invalid payment method', [], 422);
                }

                $paymentMethod = $paymentMethods[$index];

                // Ensure that the order is returned
                $payment = OrderRepository::storeByrequestFromCart($request, $paymentMethod, $carts);

                // Assuming you need the first order for payment processing
                $order = $payment->orders()->first();

                if ($paymentMethodName === 'STRIPE' && $order) {

                    \Log::info('Processing Stripe payment');
                    $paymentUrl = $this->payment($order, $request);
                }
            } else {
                // Handle other payment methods (CASH, BANK, etc.)
                $paymentMethodName = strtoupper($request->payment_method);
                \Log::info('Payment method', ['payment_method' => $paymentMethodName]);

                $paymentMethods = PaymentMethod::cases();
                $index = array_search($paymentMethodName, array_column(PaymentMethod::cases(), 'name'));

                if ($index === false) {
                    \Log::error('Payment method not found', ['payment_method' => $paymentMethodName]);
                    return $this->json('Invalid payment method', [], 422);
                }

                $paymentMethod = $paymentMethods[$index];

                // Ensure that the order is returned
                $payment = OrderRepository::storeByrequestFromCart($request, $paymentMethod, $carts);
            }

            return $this->json('Order created successfully', [
                'order_payment_url' => $paymentUrl,
                'order_id' => $order->id ?? null,
            ]);
        } catch (\Exception $e) {
            \Log::info('in error', [$e->getMessage()]);
            \Log::info('in error', [$e->getTrace()]);
            return response()->json([
                'message' => $e->getMessage()
            ], 422);
        }
    }



    /**
     * Again order
     */
    public function reOrder(Request $request)
    {
        // Validate the request
        $request->validate([
            'order_id' => 'required|exists:orders,id',
        ]);

        // Find the order
        $order = Order::find($request->order_id);

        if ($order->order_status->value == OrderStatus::DELIVERED->value) {

            // Check product quantity
            foreach ($order->products as $product) {
                if ($product->quantity < $product->pivot->quantity) {
                    return $this->json('Sorry, your product quantity out of stock', [], 422);
                }
            }

            // create payment
            $paymentMethod = $order->payments()?->latest('id')->first()->payment_method ?? 'cash';
            $payment = Payment::create([
                'amount' => $order->payable_amount,
                'payment_method' => $paymentMethod,
            ]);

            // re-order
            $order = OrderRepository::reOrder($order);

            // attach payment to order
            $payment->orders()->attach($order->id);

            // payment url
            $paymentUrl = null;
            if ($paymentMethod != 'cash') {
                $paymentUrl = route('order.payment', ['payment' => $payment, 'gateway' => $payment->payment_method]);
            }

            // return
            return $this->json('Re-order created successfully', [
                'order_payment_url' => $paymentUrl,
                'order' => OrderResource::make($order),
            ]);
        }

        return $this->json('Sorry, You can not  re-order because order is not delivered', [], 422);
    }

    /**
     * Show the order details.
     *
     * @param  Request  $request  The request object
     */
    public function show(Request $request)
    {
        // Validate the request
        $request->validate([
            'order_id' => 'required|exists:orders,id',
        ]);

        // Find the order
        $order = Order::find($request->order_id);
        \Log::info('Order details request', ['order_id' => $request->order_id]);
        \Log::info('Order found', ['order' => $order]);

        // Create the resource
        $orderResource = OrderDetailsResource::make($order);
        \Log::info('Order resource created', ['resource' => $orderResource]);

        return $this->json('order details', [
            'order' => $orderResource,
        ]);
    }

    /**
     * Cancel the order.
     */
    public function cancel(Request $request)
    {
        // Validate the request
        $request->validate([
            'order_id' => 'required|exists:orders,id',
        ]);

        // Find the order
        $order = Order::find($request->order_id);

        if ($order->order_status->value == OrderStatus::PENDING->value) {

            // update order status
            $order->update([
                'order_status' => OrderStatus::CANCELLED->value,
            ]);

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

            return $this->json('Order cancelled successfully', [
                'order' => OrderResource::make($order),
            ]);
        }

        return $this->json('Sorry, order cannot be cancelled because it is not pending', [], 422);
    }

    public function payment(Order $order, Request $request)
    {


        try {
            \Log::info('payment', [$order]);

            // Check if order is already paid
            if ($order->payment_status === 'paid') {
                return $this->json('Order is already paid', [
                    'order_id' => $order->id,
                    'redirect_url' => url('/payment-success?order_id=' . $order->id)
                ]);
            }

            // Retrieve Stripe configuration from the database
            $stripeGateway = PaymentGateway::where('name', 'stripe')
                ->where('is_active', true)
                ->first();

            \Log::info('stripeGateway', [$stripeGateway]);
            if (!$stripeGateway || !$stripeGateway->config) {
                throw new \Exception('Stripe payment is not properly configured');
            }

            $stripeConfig = json_decode($stripeGateway->config);
            \Log::info('stripeConfig', [$stripeConfig]);

            // Set Stripe API Key
            Stripe::setApiKey($stripeConfig->secret_key);

            // Create a checkout session
            $checkoutSession = Session::create([
                //  'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'ron',
                        'product_data' => [
                            'name' => 'Order #' . $order->order_code,
                            'description' => 'Payment for order including shipping charge',
                        ],
                        'unit_amount' => (int) ($order->payable_amount * 100), // Convert to cents
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => url('/payment-success?session_id={CHECKOUT_SESSION_ID}&order_id=' . $order->id),
                'cancel_url' => url('/payment-cancel?order_id=' . $order->id),
                'metadata' => [
                    'order_id' => $order->id,
                ],
            ]);



            // Update the order with the session ID
            $order->update([
                'session_id' => $checkoutSession->id
            ]);

            // Create or update payment record
            $payment = $order->payments()->updateOrCreate(
                ['order_id' => $order->id],
                [
                    'amount' => $order->payable_amount,
                    'method' => 'stripe',
                    'status' => 'pending',
                    'transaction_id' => $checkoutSession->id,
                ]
            );

            return $this->json('Payment session created', [
                'order_payment_url' => $checkoutSession->url,
                'session_id' => $checkoutSession->id,
                'apiKey' => $stripeConfig->published_key,
                'orderId' => $order->id,
                'amount' => $order->payable_amount,
                'delivery_charge' => $order->delivery_charge,
                'total_amount' => $order->total_amount,
                'coupon_discount' => $order->coupon_discount,
                'stripe_redirect_url' =>  $checkoutSession->url
            ]);
        } catch (\Exception $e) {
            \Log::error('Payment error: ' . $e->getMessage());
            return $this->json('Payment processing failed: ' . $e->getMessage(), [], 500);
        }
    }

    public function getStripePublicKey()
    {
        try {
            $stripeGateway = PaymentGateway::where('name', 'stripe')
                ->where('is_active', true)
                ->first();

            if (!$stripeGateway || !$stripeGateway->config) {
                throw new \Exception('Stripe payment is not properly configured');
            }

            $stripeConfig = json_decode($stripeGateway->config);

            return response()->json([
                'publicKey' => $stripeConfig->public_key,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching Stripe public key: ' . $e->getMessage());
            return response()->json(['error' => 'Unable to fetch Stripe public key'], 500);
        }
    }

    /**
     * Verify payment status
     */
    public function verifyPayment(Request $request)
    {
        try {
            $request->validate([
                'session_id' => 'required|string',
            ]);

            $stripeGateway = PaymentGateway::where('name', 'stripe')
                ->where('is_active', true)
                ->first();

            if (!$stripeGateway || !$stripeGateway->config) {
                \Log::error('Stripe payment is not properly configured');
                throw new \Exception('Stripe payment is not properly configured');
            }

            $stripeConfig = json_decode($stripeGateway->config);
            Stripe::setApiKey($stripeConfig->secret_key);

            try {
                // Retrieve the session
                $session = \Stripe\Checkout\Session::retrieve($request->session_id);
                \Log::info('session', [$session]);
                \Log::info('session payment status', [$session->payment_status]);

                if ($session->payment_status === 'paid') {
                    // Find the order using metadata
                    $orderId = $session->metadata->order_id ?? null;
                    \Log::info('orderId', [$orderId]);

                    if ($orderId) {
                        $order = Order::find($orderId);

                        if ($order) {
                            \Log::info('order', [$order]);
                            \Log::info('order payment status', [$order->payment_status]);
                            // Update order status if not already updated by webhook
                            if ($order->payment_status !== PaymentStatus::PAID->value) {
                                $order->update([
                                    'order_status' => OrderStatus::CONFIRM->value,
                                    'payment_status' => PaymentStatus::PAID->value
                                ]);

                                // Update payment record if exists
                                $payment = $order->payments()->latest()->first();
                                \Log::info('payment', [$payment]);
                                if ($payment) {
                                    $payment->update([
                                        'status' => 'completed',
                                        'transaction_id' => $session->payment_intent
                                    ]);
                                }
                            }

                            return response()->json([
                                'status' => 'success',
                                'message' => 'Payment verified successfully',
                                'order_id' => $order->id
                            ]);
                        } else {
                            \Log::error('Order not found for session', ['session_id' => $request->session_id, 'order_id' => $orderId]);
                            throw new \Exception('Order not found for this payment session');
                        }
                    } else {
                        \Log::error('Order ID not found in session metadata', ['session_id' => $request->session_id]);
                        throw new \Exception('Order ID not found in payment session metadata');
                    }
                } else {
                    \Log::error('Payment not marked as paid in Stripe', ['session_id' => $request->session_id, 'payment_status' => $session->payment_status]);
                    throw new \Exception('Payment not completed. Status: ' . $session->payment_status);
                }
            } catch (\Stripe\Exception\ApiErrorException $e) {
                \Log::error('Stripe API error: ' . $e->getMessage(), ['session_id' => $request->session_id]);
                throw new \Exception('Error communicating with payment provider: ' . $e->getMessage());
            }
        } catch (\Exception $e) {
            \Log::error('Payment verification error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Payment verification failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle abandoned payment
     */
    public function abandonedPayment(Request $request)
    {
        try {
            $request->validate([
                'order_id' => 'required|exists:orders,id',
            ]);

            $order = Order::find($request->order_id);

            if (!$order) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Order not found'
                ], 404);
            }

            // Only update if the order is still in pending status
            if ($order->order_status === OrderStatus::PENDING->value) {
                // Update order status to abandoned
                $order->update([
                    'payment_status' => 'abandoned'
                ]);

                // Update payment record if exists
                $payment = $order->payments()->latest()->first();
                if ($payment) {
                    $payment->update([
                        'status' => 'abandoned'
                    ]);
                }

                \Log::info('Payment abandoned for order', ['order_id' => $order->id]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Payment marked as abandoned'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error handling abandoned payment: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Error handling abandoned payment'
            ], 500);
        }
    }

    /**
     * Check payment status
     */
    public function checkPaymentStatus(Order $order)
    {
        try {
            // Get the payment status
            $paymentStatus = $order->payment_status;

            // Get Stripe config for potential redirect
            $stripeGateway = PaymentGateway::where('name', 'stripe')
                ->where('is_active', true)
                ->first();

            $stripeKey = null;
            if ($stripeGateway && $stripeGateway->config) {
                $stripeConfig = json_decode($stripeGateway->config);
                $stripeKey = $stripeConfig->published_key;
            }

            // Get the payment method
            $paymentMethod = $order->payment_method;

            // Get the order details
            $orderDetails = [
                'id' => $order->id,
                'order_code' => $order->order_code,
                'total_amount' => $order->total_amount,
                'payable_amount' => $order->payable_amount,
                'created_at' => $order->created_at->format('Y-m-d H:i:s'),
            ];

            return response()->json([
                'status' => $paymentStatus,
                'order_status' => $order->order_status,
                'payment_method' => $paymentMethod,
                'stripe_key' => $stripeKey,
                'order_details' => $orderDetails,
                'can_pay_online' => in_array($paymentMethod, ['Online Payment', 'Stripe', 'Card Payment']) && $paymentStatus === 'Pending'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error checking payment status: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Error checking payment status'
            ], 500);
        }
    }

    /**
     * Refresh payment session for a pending order
     */
    public function refreshPaymentSession(Request $request)
    {
        try {
            $request->validate([
                'order_id' => 'required|exists:orders,id',
            ]);

            $order = Order::find($request->order_id);

            if (!$order) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Order not found'
                ], 404);
            }

            // Only create a new session if the order is still pending
            if ($order->order_status !== OrderStatus::PENDING->value) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Order is no longer pending'
                ], 400);
            }

            // Create a new Stripe session
            $stripeGateway = PaymentGateway::where('name', 'stripe')
                ->where('is_active', true)
                ->first();

            if (!$stripeGateway || !$stripeGateway->config) {
                throw new \Exception('Stripe payment is not properly configured');
            }

            $stripeConfig = json_decode($stripeGateway->config);
            Stripe::setApiKey($stripeConfig->secret_key);

            // Create a checkout session
            $checkoutSession = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'ron',
                        'product_data' => [
                            'name' => 'Order #' . $order->order_code,
                            'description' => 'Payment for order including shipping charge',
                        ],
                        'unit_amount' => (int) ($order->payable_amount * 100), // Convert to cents
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => url('/payment-success?session_id={CHECKOUT_SESSION_ID}&order_id=' . $order->id),
                'cancel_url' => url('/payment-cancel?order_id=' . $order->id),
                'metadata' => [
                    'order_id' => $order->id,
                ],
            ]);

            // Update the order with the new session ID
            $order->update([
                'session_id' => $checkoutSession->id
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Payment session refreshed',
                'sessionId' => $checkoutSession->id,
                'apiKey' => $stripeConfig->published_key,
                'amount' => $order->payable_amount, // Include the full payable amount
                'delivery_charge' => $order->delivery_charge, // Include the delivery charge
                'total_amount' => $order->total_amount, // Include the total amount
                'coupon_discount' => $order->coupon_discount // Include the coupon discount
            ]);
        } catch (\Exception $e) {
            \Log::error('Error refreshing payment session: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Error refreshing payment session: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update payment method for an existing order
     */
    public function updatePaymentMethod(Request $request)
    {
        try {
            $request->validate([
                'order_id' => 'required|exists:orders,id',
                'payment_method' => 'required|string',
                'payment_type' => 'required|string',
            ]);

            $order = Order::find($request->order_id);

            if (!$order) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Order not found'
                ], 404);
            }

            // Only update if the order is still pending
            if ($order->order_status !== OrderStatus::PENDING->value) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Order is no longer pending'
                ], 400);
            }

            // Update the payment method
            $order->update([
                'payment_method' => $request->payment_method,
                'payment_type' => $request->payment_type,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Payment method updated successfully'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error updating payment method: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Error updating payment method: ' . $e->getMessage()
            ], 500);
        }
    }
}
