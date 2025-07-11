namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Order;
use App\Models\Payment;
use App\Enums\OrderStatus;
use Stripe\Stripe;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;
use App\Enums\PaymentStatus;

class StripeWebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        try {
            $stripeConfig = json_decode(setting('stripe_configuration'));
            if (!$stripeConfig || !isset($stripeConfig->webhook_secret)) {
                throw new \Exception('Stripe webhook secret is not configured');
            }

            $payload = @file_get_contents('php://input');
            $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
            $event = null;

            try {
                $event = \Stripe\Webhook::constructEvent(
                    $payload, $sig_header, $stripeConfig->webhook_secret
                );
            } catch(\UnexpectedValueException $e) {
                \Log::error('Invalid payload:', [$e->getMessage()]);
                return response()->json(['error' => 'Invalid payload'], 400);
            } catch(\Stripe\Exception\SignatureVerificationException $e) {
                \Log::error('Invalid signature:', [$e->getMessage()]);
                return response()->json(['error' => 'Invalid signature'], 400);
            }

            if ($event->type === 'checkout.session.completed') {
                $session = $event->data->object;
                
                // Find the order using payment intent
                $order = Order::where('payment_intent', $session->payment_intent)->first();
                
                if ($order) {
                    if ($session->payment_status === 'paid') {
                        $order->payment_status = PaymentStatus::PAID;
                        $order->save();
                        
                        \Log::info('Payment completed via webhook', [
                            'order_id' => $order->id,
                            'payment_intent' => $session->payment_intent
                        ]);
                    }
                } else {
                    \Log::error('Order not found for payment intent', [
                        'payment_intent' => $session->payment_intent
                    ]);
                }
            }

            return response()->json(['status' => 'success']);
            
        } catch (\Exception $e) {
            \Log::error('Webhook error:', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    /**
     * Handle checkout.session.completed event
     */
    private function handleCheckoutSessionCompleted($session)
    {
        Log::info('Processing checkout session completed', ['session_id' => $session->id]);
        
        try {
            // Find the order using metadata
            $orderId = $session->metadata->order_id ?? null;
            
            if (!$orderId) {
                Log::warning('No order ID found in session metadata', ['session_id' => $session->id]);
                return;
            }
            
            $order = Order::find($orderId);
            
            if (!$order) {
                Log::warning('Order not found', ['order_id' => $orderId]);
                return;
            }
            
            // Update order status
            $order->update([
                'order_status' => OrderStatus::CONFIRM->value,
                'payment_status' => 'paid'
            ]);
            
            // Update payment record if exists
            $payment = $order->payments()->latest()->first();
            if ($payment) {
                $payment->update([
                    'status' => 'completed',
                    'transaction_id' => $session->payment_intent ?? $session->id
                ]);
            }
            
            Log::info('Order payment completed successfully', [
                'order_id' => $order->id,
                'session_id' => $session->id
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error processing checkout session', [
                'error' => $e->getMessage(),
                'session_id' => $session->id
            ]);
        }
    }
    
    /**
     * Handle payment_intent.succeeded event
     */
    private function handlePaymentIntentSucceeded($paymentIntent)
    {
        Log::info('Processing payment intent succeeded', ['payment_intent_id' => $paymentIntent->id]);
        
        try {
            // Find the order using metadata
            $orderId = $paymentIntent->metadata->order_id ?? null;
            
            if (!$orderId) {
                Log::warning('No order ID found in payment intent metadata', ['payment_intent_id' => $paymentIntent->id]);
                return;
            }
            
            $order = Order::find($orderId);
            
            if (!$order) {
                Log::warning('Order not found', ['order_id' => $orderId]);
                return;
            }
            
            // Update order status
            $order->update([
                'order_status' => OrderStatus::CONFIRM->value,
                'payment_status' => 'paid'
            ]);
            
            // Update payment record if exists
            $payment = $order->payments()->latest()->first();
            if ($payment) {
                $payment->update([
                    'status' => 'completed',
                    'transaction_id' => $paymentIntent->id
                ]);
            }
            
            Log::info('Order payment completed successfully', [
                'order_id' => $order->id,
                'payment_intent_id' => $paymentIntent->id
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error processing payment intent', [
                'error' => $e->getMessage(),
                'payment_intent_id' => $paymentIntent->id
            ]);
        }
    }
    
    /**
     * Handle payment_intent.payment_failed event
     */
    private function handlePaymentIntentFailed($paymentIntent)
    {
        Log::info('Processing payment intent failed', ['payment_intent_id' => $paymentIntent->id]);
        
        try {
            // Find the order using metadata
            $orderId = $paymentIntent->metadata->order_id ?? null;
            
            if (!$orderId) {
                Log::warning('No order ID found in payment intent metadata', ['payment_intent_id' => $paymentIntent->id]);
                return;
            }
            
            $order = Order::find($orderId);
            
            if (!$order) {
                Log::warning('Order not found', ['order_id' => $orderId]);
                return;
            }
            
            // Update order status
            $order->update([
                'payment_status' => 'failed'
            ]);
            
            // Update payment record if exists
            $payment = $order->payments()->latest()->first();
            if ($payment) {
                $payment->update([
                    'status' => 'failed',
                    'transaction_id' => $paymentIntent->id
                ]);
            }
            
            Log::info('Order payment failed', [
                'order_id' => $order->id,
                'payment_intent_id' => $paymentIntent->id
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error processing failed payment intent', [
                'error' => $e->getMessage(),
                'payment_intent_id' => $paymentIntent->id
            ]);
        }
    }
    
    /**
     * Handle checkout.session.expired event
     */
    private function handleCheckoutSessionExpired($session)
    {
        Log::info('Processing checkout session expired', ['session_id' => $session->id]);
        
        try {
            // Find the order using metadata
            $orderId = $session->metadata->order_id ?? null;
            
            if (!$orderId) {
                Log::warning('No order ID found in session metadata', ['session_id' => $session->id]);
                return;
            }
            
            $order = Order::find($orderId);
            
            if (!$order) {
                Log::warning('Order not found', ['order_id' => $orderId]);
                return;
            }
            
            // Update order status
            $order->update([
                'payment_status' => 'expired'
            ]);
            
            // Update payment record if exists
            $payment = $order->payments()->latest()->first();
            if ($payment) {
                $payment->update([
                    'status' => 'expired'
                ]);
            }
            
            Log::info('Order payment expired', [
                'order_id' => $order->id,
                'session_id' => $session->id
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error processing expired session', [
                'error' => $e->getMessage(),
                'session_id' => $session->id
            ]);
        }
    }
} 