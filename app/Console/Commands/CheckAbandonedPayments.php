<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Enums\OrderStatus;
use Carbon\Carbon;

class CheckAbandonedPayments extends Command
{
    protected $signature = 'payments:check-abandoned';
    protected $description = 'Check for abandoned payments and update their status';

    public function handle()
    {
        // Find orders that are pending for more than 30 minutes
        $cutoffTime = Carbon::now()->subMinutes(30);
        
        $pendingOrders = Order::where('order_status', OrderStatus::PENDING->value)
            ->where('created_at', '<', $cutoffTime)
            ->where('payment_status', '!=', 'paid')
            ->get();
            
        $count = 0;
        
        foreach ($pendingOrders as $order) {
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
            
            $count++;
        }
        
        $this->info("Updated $count abandoned payment orders");
        
        return 0;
    }
} 