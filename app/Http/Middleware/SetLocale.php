<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        // Get locale from session or cookie, default to Romanian
        $locale = session('locale', cookie('locale', 'ro'));
        
        // Ensure locale is supported, default to Romanian if not
        if (!in_array($locale, ['ro', 'en'])) {
            $locale = 'ro';
        }

        // Set the application locale
        App::setLocale($locale);
        
        return $next($request);
    }
} 