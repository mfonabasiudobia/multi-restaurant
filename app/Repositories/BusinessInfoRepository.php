<?php

namespace App\Repositories;

use App\Models\BusinessInfo;
use Illuminate\Support\Facades\DB;

class BusinessInfoRepository extends Repository
{
    public static function model()
    {
        return BusinessInfo::class;
    }

    public static function updateOrCreateByShop($shop, $request)
    {
        try {
            return $shop->businessInfo()->updateOrCreate(
                ['shop_id' => $shop->id],
                [
                    'vat_number' => $request->vat_number,
                    'business_location' => $request->business_location,
                    'company_name' => $request->company_name
                ]
            );
        } catch (\Exception $e) {
            \Log::error('Failed to update business info', [
                'shop_id' => $shop->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
} 