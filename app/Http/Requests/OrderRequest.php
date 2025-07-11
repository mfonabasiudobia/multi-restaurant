<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'shop_ids' => 'required|array',
            'shop_ids.*' => 'required|exists:shops,id',
            'address_id' => 'required|exists:addresses,id',
            'note' => 'nullable|string',
            'payment_method' => 'required|string',
            'coupon_code' => 'nullable|string|max:50',
            'payment_proof' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048', // File validation
            'delivery_method' => 'nullable|in:gls,standard', // Made optional
        ];
    }

    public function messages(): array
    {
        \Log::info('OrderRequest', $this->all());
        $request = request();
        if ($request->is('api/*')) {
            $lan = $request->header('accept-language') ?? 'en';
            app()->setLocale($lan);
        }
       

        return [
            'shop_ids.required' => __('The shop field is required.'),
            'shop_ids.array' => __('The shop ids must be an array.'),
            'shop_ids.*.required' => __('The shop field is required.'),
            'shop_ids.*.exists' => __('The selected shop id is invalid.'),
            'address_id.required' => __('The address field is required.'),
            'address_id.exists' => __('The selected address id is invalid.'),
            'payment_method.required' => __('The payment method field is required.'),
            'payment_proof.mimes' => __('The payment proof must be a JPG, PNG, or PDF file.'),
            'payment_proof.max' => __('The payment proof file size must not exceed 2MB.')
        ];
    }
}