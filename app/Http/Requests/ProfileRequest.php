<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore(auth()->id())],
            'phone' => ['required', 'string', Rule::unique('users')->ignore(auth()->id())],
            'shop_name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'description' => 'nullable|string|max:1000',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'vat_number' => 'required|string|max:50',
            'business_location' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'min_order_amount' => 'required|numeric|min:0',
            'estimated_delivery_time' => 'required|numeric|min:1'
        ];
    }
} 