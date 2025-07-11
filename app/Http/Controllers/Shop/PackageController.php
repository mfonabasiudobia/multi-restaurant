<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\PaymentGateway;
use App\Models\BankDetail;
use App\Models\Payment;
use App\Models\PackagePayment;
use App\Models\PaymentProof;
use App\Repositories\MediaRepository;
use App\Http\Controllers\Gateway\Stripe\ProcessController as StripeProcessor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Models\ShopPackage;

class PackageController extends Controller
{
    public function payment()
    {
        $shop = auth()->user()->shop;
        $currentPackage = $shop->package ?? null;
        $packages = Package::where('is_active', true)->get();
        $paymentGateways = PaymentGateway::where('is_active', true)->get();
        $bankDetails = BankDetail::first();
        
        // Get payment history with correct relationships
        $paymentHistory = PackagePayment::with([
            'package',
            'payment',
            'payment.paymentProof'
        ])
        ->where('shop_id', $shop->id)
        ->latest()
        ->get();

        return view('shop.package.payment', compact(
            'currentPackage',
            'packages',
            'paymentGateways',
            'bankDetails',
            'paymentHistory'
        ));
    }

    public function processPayment(Request $request)
    {
        try {
            \Log::info('Payment Request:', [
                'all' => $request->all(),
                'payment_method' => $request->payment_method,
                'package_id' => $request->package_id
            ]);

            $request->validate([
                'package_id' => 'required|exists:packages,id',
                'payment_method' => 'required|in:BANK,STRIPE,PAYPAL',
                'payment_proof' => 'required_if:payment_method,BANK|file|mimes:pdf,jpg,jpeg,png|max:2048'
            ]);

            $package = Package::findOrFail($request->package_id);
            $shop = auth()->user()->shop;

            if ($request->payment_method === 'STRIPE') {
                try {
                    // Get Stripe gateway
                    $stripeGateway = PaymentGateway::where('name', 'stripe')
                        ->where('is_active', true)
                        ->first();

                    \Log::info('Stripe Gateway:', [
                        'gateway_exists' => (bool)$stripeGateway,
                        'config_exists' => !empty($stripeGateway?->config),
                        'gateway' => $stripeGateway
                    ]);

                    if (!$stripeGateway || !$stripeGateway->config) {
                        throw new \Exception('Stripe payment is not properly configured');
                    }

                    $stripeConfig = json_decode($stripeGateway->config);

                    // Set Stripe API Key
                    Stripe::setApiKey($stripeConfig->secret_key);

                    DB::beginTransaction();

                    try {
                        // Create payment record first
                        $payment = Payment::create([
                            'shop_id' => $shop->id,
                            'package_id' => $package->id,
                            'payment_method' => 'STRIPE',
                            'amount' => $package->price,
                            'currency' => 'USD',
                            'status' => 'pending'
                        ]);

                        \Log::info('Created Payment Record:', [
                            'payment_id' => $payment->id,
                            'shop_id' => $shop->id,
                            'package_id' => $package->id,
                            'amount' => $package->price
                        ]);

                        // Create Stripe session
                        $session = Session::create([
                            'payment_method_types' => ['card'],
                            'line_items' => [[
                                'price_data' => [
                                    'currency' => 'usd',
                                    'unit_amount' => (int)($package->price * 100),
                                    'product_data' => [
                                        'name' => $package->name,
                                        'description' => "Package subscription for {$shop->name}",
                                    ],
                                ],
                                'quantity' => 1,
                            ]],
                            'mode' => 'payment',
                            'success_url' => route('shop.package.stripe.success') . '?session_id={CHECKOUT_SESSION_ID}',
                            'cancel_url' => route('shop.package.stripe.cancel'),
                        ]);

                        // Update payment with stripe session id
                        $payment->stripe_session_id = $session->id;
                        $payment->save();

                        \Log::info('Updated Payment with Session ID:', [
                            'payment_id' => $payment->id,
                            'session_id' => $session->id
                        ]);

                        // Create package payment record
                        PackagePayment::create([
                            'payment_id' => $payment->id,
                            'shop_id' => $shop->id,
                            'package_id' => $package->id,
                            'amount' => $package->price,
                            'status' => 'pending'
                        ]);

                        DB::commit();

                        \Log::info('Stripe Session Created:', [
                            'session_id' => $session->id,
                            'payment_id' => $payment->id
                        ]);

                        return response()->json(['url' => $session->url]);
                    } catch (\Exception $e) {
                        DB::rollBack();
                        throw $e;
                    }
                } catch (\Exception $e) {
                    \Log::error('Stripe Process Error:', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    throw $e;
                }
            }

            DB::beginTransaction();
            try {
                // Create base payment record
                $payment = Payment::create([
                    'shop_id' => $shop->id,
                    'amount' => $package->price,
                    'payment_method' => $request->payment_method,
                    'status' => 'pending'
                ]);

                // Create package payment record
                $packagePayment = PackagePayment::create([
                    'payment_id' => $payment->id,
                    'shop_id' => $shop->id,
                    'package_id' => $package->id,
                    'amount' => $package->price,
                    'status' => 'pending'
                ]);

                // Handle payment proof for bank transfers
                if ($request->payment_method === 'BANK' && $request->hasFile('payment_proof')) {
                    $file = $request->file('payment_proof');
                    $path = $file->store('payments/proof', 'public');
                    
                    PaymentProof::create([
                        'user_id' => auth()->id(),
                        'payment_id' => $payment->id,
                        'file_path' => $path
                    ]);
                }

                DB::commit();
                return back()->with('success', 'Payment submitted successfully. Please wait for admin verification.');

            } catch (\Exception $e) {
                DB::rollBack();
                return back()->with('error', 'Payment processing failed. Please try again.');
            }
        } catch (\Exception $e) {
            \Log::error('Payment Processing Error:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => $e->getMessage()
            ], 422);
        }
    }

    public function checkStatus()
    {
        $shop = auth()->user()->shop;
        $package = $shop->package;

        $showModal = false;
        $message = '';

        if (!$package) {
            $showModal = true;
            $message = 'Please subscribe to a package to continue.';
        } elseif (!$package->is_paid) {
            $showModal = true;
            $message = 'Please complete your package payment.';
        } elseif ($package->products_used >= $package->product_limit) {
            $showModal = true;
            $message = 'You have reached your product limit. Please upgrade your package.';
        }

        Log::info('Checking package status', [
            'shop_id' => $shop->id,
            'show_modal' => $showModal,
            'package_status' => $package ? [
                'is_paid' => $package->is_paid,
                'products_used' => $package->products_used,
                'product_limit' => $package->product_limit
            ] : null
        ]);

        return response()->json([
            'show_modal' => $showModal,
            'message' => $message,
            'package' => $package
        ]);
    }

    // Add these new methods for Stripe webhooks
    public function stripeSuccess(Request $request)
    {
        try {
            \Log::info('Stripe Success Callback:', [
                'request_session_id' => $request->session_id,
                'all_params' => $request->all()
            ]);

            if (!$request->session_id) {
                throw new \Exception('No session ID provided');
            }

            // Find the payment record
            $payment = Payment::where('stripe_session_id', $request->session_id)
                ->where('status', 'pending')
                ->first();

            \Log::info('Payment Record Search:', [
                'session_id' => $request->session_id,
                'payment_found' => (bool)$payment,
                'payment_details' => $payment
            ]);

            // Also check for any recent pending payments
            $recentPayments = Payment::where('status', 'pending')
                ->where('created_at', '>=', now()->subHours(1))
                ->get();

            \Log::info('Recent Pending Payments:', [
                'count' => $recentPayments->count(),
                'payments' => $recentPayments
            ]);

            if (!$payment) {
                throw new \Exception('Invalid or already processed payment');
            }

            // Get Stripe gateway and config
            $stripeGateway = PaymentGateway::where('name', 'stripe')
                ->where('is_active', true)
                ->first();

            if (!$stripeGateway || !$stripeGateway->config) {
                throw new \Exception('Stripe payment is not properly configured');
            }

            $stripeConfig = json_decode($stripeGateway->config);

            // Verify the payment with Stripe
            $stripe = new \Stripe\StripeClient($stripeConfig->secret_key);
            $session = $stripe->checkout->sessions->retrieve($request->session_id);
            \Log::info($session);

            \Log::info('Stripe Session Retrieved:', [
                'session_id' => $session->id,
                'payment_status' => $session->payment_status,
                'customer' => $session->customer
            ]);

            if ($session->payment_status === 'paid') {
                DB::beginTransaction();

                try {
                    // Update payment status
                    $payment->update(['status' => 'completed']);

                    // Update package payment status
                    $packagePayment = PackagePayment::where('payment_id', $payment->id)->first();
                    
                    if (!$packagePayment) {
                        throw new \Exception('Package payment record not found');
                    }

                    $packagePayment->update([
                        'status' => 'approved',
                        'expires_at' => now()->addDays($packagePayment->package->duration_days)
                    ]);

                    // Update shop package
                    ShopPackage::updateOrCreate(
                        ['shop_id' => $packagePayment->shop_id],
                        [
                            'package_id' => $packagePayment->package_id,
                            'product_limit' => $packagePayment->package->product_limit,
                            'package_price' => $packagePayment->amount,
                            'is_paid' => true,
                            'expires_at' => $packagePayment->expires_at
                        ]
                    );

                    DB::commit();
                    return redirect()->route('shop.package.payment')
                        ->with('success', 'Payment completed successfully!');
                } catch (\Exception $e) {
                    DB::rollBack();
                    \Log::error('Transaction Error:', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    throw $e;
                }
            }

            throw new \Exception('Payment not completed');
        } catch (\Exception $e) {
            \Log::error('Stripe Success Error:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'session_id' => $request->session_id,
                'payment_exists' => isset($payment),
                'request_data' => $request->all()
            ]);

            return redirect()->route('shop.package.payment')
                ->with('error', 'Payment verification failed: ' . $e->getMessage());
        }
    }

    public function stripeCancel()
    {
        return redirect()->route('shop.package.payment')
            ->with('error', 'Payment was cancelled.');
    }
} 