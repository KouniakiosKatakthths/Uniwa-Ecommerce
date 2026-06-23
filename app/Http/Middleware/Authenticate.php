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
        if ($request->expectsJson()) {
            return null;
        }

        $appUrl = rtrim(config('app.url'), '/');
        $query = $request->getQueryString();

        session()->put(
            'url.intended',
            $appUrl . '/' . ltrim($request->path(), '/') . ($query ? '?' . $query : '')
        );

        return route('login');
    }
}
