<?php

namespace App\Services;

use App\Models\DeliveryWeight;

class CartService
{
    public function calculateDeliveryCharge($totalWeight)
    {
        $weightCharge = DeliveryWeight::active()
            ->where('min_weight', '<=', $totalWeight)
            ->where('max_weight', '>=', $totalWeight)
            ->first();

        return $weightCharge ? $weightCharge->price : 0;
    }
} 