<?php

namespace App\Repositories;


use App\Http\Requests\AddressRequest;
use App\Models\Address;

class AddressRepository extends Repository
{
    public static function model()
    {
        return Address::class;
    }

    public static function storeByRequest(AddressRequest $request): Address
    {
        $isDefault = $request->is_default ? true : false;
        $customer = auth()->user()->customer;

        $addresses = $customer?->addresses;

        if ($isDefault && ($addresses->count() > 0)) {
            $customer->addresses()->update(['is_default' => false]);
        }

        $data = [
            'customer_id' => auth()->user()->customer->id,
            'name' => $request->name,
            'phone' => $request->phone,
            'area' => $request->area,
            'flat_no' => $request->flat_no,
            'post_code' => $request->post_code,
            'address_line' => $request->address_line,
            'address_line2' => $request->address_line2,
            'address_type' => $request->address_type,
            'is_default' => $customer->addresses ? $isDefault : true,
            'longitude' => $request->longitude,
            'latitude' => $request->latitude,
            'country' => $request->country
        ];

        if ($request->address_type === 'office') {
            $data['company_name'] = $request->company_name;
            $data['cui'] = $request->cui;
        }

        return self::create($data);
    }

    public static function updateByRequest(AddressRequest $request, Address $address): Address
    {
        \Log::info($request->all());
        $isDefault = $request->is_default ? true : false;

        $customer = auth()->user()->customer;

        if ($isDefault) {
            $customer->addresses()->update(['is_default' => false]);
        }

        $data = [
            'name' => $request->name,
            'phone' => $request->phone,
            'area' => $request->area,
            'flat_no' => $request->flat_no,
            'post_code' => $request->post_code,
            'address_line' => $request->address_line,
            'address_line2' => $request->address_line2,
            'address_type' => $request->address_type,
            'is_default' => $isDefault,
            'country' => $request->country
        ];

        if ($request->address_type === 'office') {
            $data['company_name'] = $request->company_name;
            $data['cui'] = $request->cui;
            $data['trade_register_number'] = $request->trade_register_number;
            $data['vat_payer'] = $request->vat_payer;
        }

        $address->update($data);
        \Log::info($address);


        return $address;
    }
}
