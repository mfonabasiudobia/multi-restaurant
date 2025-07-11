<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class ShopLanguage
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // If no locale is set in session, default to Romanian
        $locale = session()->get('locale', 'ro');
        App::setLocale($locale);
        session()->put('locale', $locale);
        
        return $next($request);
    }
} 