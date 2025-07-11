<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class PhoneNumber implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Romanian format: +40 7xx xxx xxx or 07xx xxx xxx
        $romanianPattern = '/^(\+40|0)\s?7[0-9]{2}\s?[0-9]{3}\s?[0-9]{3}$/';
        
        // International format
        $internationalPattern = '/^\+[1-9]{1}[0-9]{3,14}$/';

        if (!preg_match($romanianPattern, $value) && !preg_match($internationalPattern, $value)) {
            $fail('The :attribute must be a valid Romanian or international phone number.');
        }
    }
} 