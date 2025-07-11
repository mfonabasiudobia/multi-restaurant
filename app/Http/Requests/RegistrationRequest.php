<?php

namespace App\Http\Requests;

use App\Models\VerifyManage;
use App\Rules\EmailRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Cache;

class RegistrationRequest extends FormRequest
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
        $verifyManage = Cache::rememberForever('verify_manage', function () {
            return VerifyManage::first();
        });

        $emailRequird = 'nullable';

        // if ($verifyManage?->register_otp && ($verifyManage->register_otp_type == 'email' || $verifyManage->forgot_otp_type == 'email')) {
        //     $emailRequird = 'required';
        // }

        return [
            'name' => 'required|string|max:200',
            'phone' => 'required|unique:users,phone',
            'email' => [$emailRequird, 'email', new EmailRule, 'unique:users,email'],
            'password' => 'required|string|min:6',
        ];
    }

    public function messages(): array
    {
        $request = request();
        if ($request->is('api/*')) {
            \Log::info('Request language: ' . $request->header('accept-language'));
        
            // Get the Accept-Language header
            $acceptLanguage = $request->header('accept-language', 'en');
            
            // Parse and sanitize the locale
            $locale = $this->parseAndValidateLocale($acceptLanguage);
        
            \Log::info('Parsed locale: ' . $locale);
        
            // Set the validated locale
            app()->setLocale($locale);
        }

        return [
            'name.required' => __('The name field is required.'),
            'phone.required' => __('The phone field is required.'),
            'phone.unique' => __('Phone already exists.'),
            'password.required' => __('The password field is required.'),
            'password.min' => __('The password must be at least 6 characters.'),
            'email.required' => __('The email field is required.'),
            'email.email' => __('The email must be a valid email address.'),
            'email.unique' => __('The email has already been taken.'),
        ];
    }
    /**
 * Parse and validate the locale from the Accept-Language header.
 *
 * @param string $acceptLanguage
 * @return string
 */
private function parseAndValidateLocale(string $acceptLanguage): string
{
    // Default locale
    $defaultLocale = config('app.locale', 'en');

    // Supported locales
    $supportedLocales = config('app.supported_locales', ['en', 'en_GB']);

    // Extract the primary locale (e.g., "en_GB" from "en_GB,en;q=0.9")
    $locale = explode(',', $acceptLanguage)[0]; // Split on commas and take the first part
    $locale = explode(';', $locale)[0]; // Remove the quality value (e.g., ";q=0.9")

    // Validate the locale; fallback to default if invalid
    return in_array($locale, $supportedLocales) ? $locale : $defaultLocale;
}
}
