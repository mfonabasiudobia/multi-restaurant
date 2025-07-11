<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class LocalizationManage
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get locale from session or default to app.locale
        $locale = session()->get('locale', config('app.locale'));
        
        // Set the application locale
        App::setLocale($locale);

        // Don't modify the request or response for admin routes
        if ($request->is('admin/*')) {
            return $next($request);
        }

        // For non-admin routes, you can add any special handling here
        return $next($request);
    }

    /**
     * Parse the Accept-Language header to extract the primary locale.
     *
     * @param  string $acceptLanguage
     * @return string
     */
    private function parseAcceptLanguage(string $acceptLanguage): string
    {
        // Split by comma to separate locales and weights
        $locales = explode(',', $acceptLanguage);

        // Extract the first locale without weight (e.g., "en_GB" from "en_GB;q=0.9")
        foreach ($locales as $locale) {
            $locale = explode(';', $locale)[0]; // Remove the quality score part (e.g., ";q=0.9")
            if (preg_match('/^[a-zA-Z_]+$/', $locale)) {
                return $locale;
            }
        }

        // Fallback to default app locale
        return config('app.locale');
    }
}