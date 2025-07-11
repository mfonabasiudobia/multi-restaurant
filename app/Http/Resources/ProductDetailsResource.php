<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Number;

class ProductDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $this->load(['orders', 'shop', 'sizes', 'unit', 'flashSales', 'vatTaxes']);

        $favorite = false;
        $user = Auth::guard('api')->user();
        if ($user) {
            $favoriteIDs = $user->customer->favorites()->pluck('product_id')->toArray();
            $favorite = in_array($this->id, $favoriteIDs);
        }

        $discountPercentage = $this->getDiscountPercentage($this->price, $this->discount_price);

        $shopDistance = null;

        if ($request->has('latitude') && $request->has('longitude')) {
            $shopDistance = getDistance([$request->latitude, $request->longitude], [$this->shop->latitude, $this->shop->longitude]);
        }

        $totalSold = $this->orders->sum('pivot.quantity');

        $flashsale = $this->flashSales?->first();
        $flashsaleProduct = null;
        $quantity = null;

        if ($flashsale) {

            $flashsaleProduct = $flashsale->products()->where('id', $this->id)->first();

            $quantity = $flashsaleProduct->pivot->quantity - $flashsaleProduct->pivot->sale_quantity;

            if ($quantity == 0) {
                $quantity = null;
                $flashsaleProduct = null;
            } else {
                $discountPercentage = $flashsale->pivot->discount;
            }
        }

        $price = $this->price;
        $discountPrice = $flashsaleProduct ? $flashsaleProduct->pivot->price : $this->discount_price;

        // calculate vat taxes
        $priceTaxAmount = 0;
        $discountTaxAmount = 0;
        foreach ($this->vatTaxes ?? [] as $tax) {
            if ($tax->percentage > 0) {
                $priceTaxAmount += $price * ($tax->percentage / 100);
                $discountPrice > 0 ? $discountTaxAmount += $discountPrice * ($tax->percentage / 100) : null;
            }
        }
        $videos = $this->processedVideos();


        $price = $price + $priceTaxAmount;
        $discountPrice = $discountPrice + $discountTaxAmount;

        $season = $this->season;



        $quality = $this->quality;



        return [
            'id' => $this->id,
            'name' => $this->name,
            'short_description' => $this->short_description,
            'price' => (float) number_format($price, 2, '.', ''),
            'discount_price' => (float) number_format($discountPrice, 2, '.', ''),
            'discount_percentage' => (float) number_format($discountPercentage, 2, '.', ''),

            'total_sold' => (string) number_format($totalSold, 0, '.', ','),
            'quantity' => (int) ($quantity ?? $this->quantity),
            'is_favorite' => (bool) $favorite,
            'taxes' => $this->vatTaxes,
            'thumbnails' => $this->thumbnails,
            'sizes' => SizeResource::collection($this->sizes),

            'unit' => $this->unit ? UnitResource::make($this->unit) : null,
            'shop' => [
                'id' => $this->shop->id,
                'name' => $this->shop->name,
                'logo' => $this->shop->logo,
                'rating' => (float) round($this->shop->averageRating, 1),
                'estimated_delivery_time' => (string) '24-48h',
                'delivery_charge' => (float) getDeliveryCharge(1),
            ],
            'flash_sale' => $flashsaleProduct ? FlashSaleResource::make($flashsale) : null,
            'description' => $this->description,
            'videos' => $videos,
            'season' => $season ? SeasonResource::make($season) : null,
            'quality' => $quality ? QualityResource::make($quality) : null,
        ];
    }
}
