<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'phone' => $this->phone,
            'area' => $this->area,
            'flat_no' => $this->flat_no,
            'address_type' => $this->address_type,
            'address_line' => $this->address_line,
            'address_line2' => $this->address_line2,
            'post_code' => $this->post_code,
            'is_default' => (bool) $this->is_default,
            'longitude' => $this->longitude,
            'latitude' => $this->latitude,
            'company_name' => $this->company_name,
            'cui' => $this->cui,
            'trade_register_number' => $this->trade_register_number,
            'vat_payer' => $this->vat_payer

        ];
    }
}
