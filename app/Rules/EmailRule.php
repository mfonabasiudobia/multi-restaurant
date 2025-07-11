<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class EmailRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
     
        \Log::info('EmailRule', [filter_var($value, FILTER_VALIDATE_EMAIL)]);
        if (! filter_var($value, FILTER_VALIDATE_EMAIL)) {
            \Log::info('EmailRule1', ['fail']);
            $fail('The :attribute must be a valid email address.');
        }
    }
}
