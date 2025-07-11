<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        \Log::info('Admin authentication redirect', [
            'request' => $request->all()
        ]);
        return $request->expectsJson() ? null : route('admin.login');
    }
}
