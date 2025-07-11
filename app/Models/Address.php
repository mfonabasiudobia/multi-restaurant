<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'area',
        'flat_no',
        'post_code',
        'address_line',
        'address_type',
        'is_default',
        'company_name',
        'cui',
        'country',
        'trade_register_number',
        'vat_payer',
        'customer_id'

    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    /**
     * Get the customer that owns the address.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }


    public function getFullAddressAttribute()
    {


        return implode("\n", array_filter([

            implode("\n", array_filter([
                $this->address_line2 ? __("address_line") . ":" . $this->address_line : '',
                $this->address_line2 ?  __("address_line2") . ":" . $this->address_line2 : '',
                $this->area ?  __("area") . ":" .  $this->area : '',
                $this->flat_no ? __("flat_no") . ":" . $this->flat_no : '',

            ])),
            implode("\n", array_filter([


                $this->company_name ?  __("company_name") . ":" . $this->company_name : '',

                $this->trade_register_number ?  __("trade_register_number") . ":" . $this->trade_register_number : '',
                $this->vat_payer ? __("vat_payer") . ":" . $this->vat_payer : '',
            ])),
            implode("\n", array_filter([
                __("post_code") . ":" .  $this->post_code,
                __("country") . ":" . $this->country,

            ]))
        ]));
    }

    /**
     * Get all of the orders.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
