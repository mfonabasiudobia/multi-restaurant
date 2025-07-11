<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'name' => 'required_if:address_type,home|string|max:255',
            'phone' => 'required|string|max:255',
            'area' => 'required|string|max:255',
            'flat_no' => 'nullable|string|max:255',
            'post_code' => 'required|string|max:255',
            'address_line' => 'required|string|max:255',
            'address_type' => 'required|in:home,office',
            'is_default' => 'boolean',
            'country' => 'required|string|max:255'
        ];

        // Only require company fields if address type is office
        if ($this->address_type === 'office') {
            $rules['company_name'] = 'required|string|max:255';
            //  $rules['cui'] = 'required|string|max:255';
            $rules['trade_register_number'] = 'required|string|max:255';
            $rules['vat_payer'] = 'required|string|max:255';
        }

        return $rules;
    }

    public function messages(): array
    {
        $request = request();
        if ($request->is('api/*')) {
            $lan = $request->header('accept-language') ?? 'en';
            app()->setLocale($lan);
        }

        return [
            'name.required_if' => __('The name field is required for individual addresses'),
            'name.max' => __('The name may not be greater than 255 characters'),
            'name.string' => __('The name must be a string'),
            'phone.required' => __('The phone field is required'),
            'phone.max' => __('The phone may not be greater than 255 characters'),
            'area.required' => __('The area field is required'),
            'area.max' => __('The area may not be greater than 255 characters'),
            'address_type.required' => __('The address type field is required'),
            'address_type.max' => __('The address type may not be greater than 255 characters'),
            'post_code.required' => __('The postal code field is required'),
            'post_code.max' => __('The post code may not be greater than 255 characters'),
            'flat_no.max' => __('The flat no may not be greater than 255 characters'),
            'address_line.required' => __('The address line field is required'),
            'address_line.max' => __('The address line may not be greater than 255 characters'),
            'company_name.required' => __('The company name field is required for business addresses'),
            // 'cui.required' => __('The CUI field is required for business addresses'),
            'country.required' => __('The country field is required'),
            'trade_register_number.required' => __('The trade register number field is required'),
            'vat_payer.required' => __('The vat payer field is required'),
        ];
    }
}
