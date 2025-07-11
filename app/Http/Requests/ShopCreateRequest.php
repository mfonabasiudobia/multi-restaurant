<?php

namespace App\Http\Requests;

use App\Rules\EmailRule;
use App\Rules\PhoneNumber;
use Illuminate\Foundation\Http\FormRequest;

class ShopCreateRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $user = null;
        $required = 'required';
        if ($this->routeIs('admin.shop.update')) {
            $user = $this->shop?->user;
            $required = 'nullable';
        }

        // validation rules
        return [
            'name' => 'required|string|max:255',
            'shop_name' => 'required|string|min:3|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'phone' => ['required', 'string', new PhoneNumber],
            'address' => 'required|string',
            'first_name' => 'required|string|min:2|max:255',
            'last_name' => 'nullable|string|max:255',
            'Gender' => ['nullable', 'string'],
            'password_confirmation' => ['required', 'min:6'],
            'profile_photo' => 'required|image|mimes:jpg,jpeg,png,gif|max:2048',
            'shop_logo' => 'required|image|mimes:jpg,jpeg,png,gif|max:2048',
            'shop_banner' => 'required|image|mimes:jpg,jpeg,png,gif|max:2048',
            'description' => ['nullable', 'string', 'max:200'],
            'package_id' => 'required|exists:packages,id',
            'vat_number' => 'nullable|string|max:50',
            'business_location' => 'nullable|string|max:255',
            'company_name' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        $request = request();
        if ($request->is('api/*')) {
            $lan = $request->header('accept-language') ?? 'en';
            app()->setLocale($lan);
        }
        //  \Log::info('ShopCreateRequest messages', ['messages' => $this->messages()]);

        return [
            'first_name.required' => __('The first name field is required.'),
            'phone.required' => __('The phone field is required.'),
            'phone.unique' => __('The phone has already been taken.'),
            'email.required' => __('The email field is required.'),
            'email.unique' => __('The email has already been taken.'),
            'password.required' => __('The password field is required.'),
            'password.min' => __('The password must be at least 6 characters.'),
            'password.confirmed' => __('The password and confirmation password do not match.'),
            'profile_photo.image' => __('The profile photo must be an image.'),
            'profile_photo.max' => __('The profile photo must not be greater than 2 MB.'),
            'shop_name.required' => __('The shop name field is required.'),
            'shop_logo.image' => __('The shop logo must be an image.'),
            'shop_logo.max' => __('The shop logo must not be greater than 2 MB.'),
            'shop_banner.image' => __('The shop banner must be an image.'),
            'shop_banner.max' => __('The shop banner must not be greater than 2 MB.'),
            'description.max' => __('The description may not be greater than 200 characters.'),
            'password_confirmation.min' => __('The password confirmation must be at least 6 characters.'),
            'password_confirmation.required' => __('The password confirmation field is required.'),
            'address.max' => __('The address may not be greater than 255 characters.'),
        ];
    }
}
