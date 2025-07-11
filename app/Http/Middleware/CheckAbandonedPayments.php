<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Enums\OrderStatus;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CheckAbandonedPayments
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Only check for authenticated users
        if (auth()->check() && auth()->user()->customer) {
            $customerId = auth()->user()->customer->id;
            
            // Find pending orders for this customer
            $cutoffTime = Carbon::now()->subMinutes(30);
            
            $pendingOrders = Order::where('customer_id', $customerId)
                ->where('order_status', OrderStatus::PENDING->value)
                ->where('payment_status', 'pending')
                ->where('created_at', '<', $cutoffTime)
                ->get();
                
            foreach ($pendingOrders as $order) {
                // Mark as abandoned
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
                
                Log::info('Automatically marked order as abandoned', ['order_id' => $order->id]);
            }
        }
        
        return $next($request);
    }
} 