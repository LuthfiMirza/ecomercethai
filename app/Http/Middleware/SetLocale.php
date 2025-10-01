<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        $available = ['en', 'th'];
        $locale = session('locale');

        if (! $locale) {
            $browser = substr((string) $request->getPreferredLanguage($available), 0, 2);
            $locale = in_array($browser, $available, true) ? $browser : config('app.locale', 'en');
        }

        app()->setLocale(in_array($locale, $available, true) ? $locale : 'en');

        return $next($request);
    }
}
