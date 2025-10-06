<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        $available = supported_locales();

        $locale = $request->route('locale');

        if (! $locale) {
            $segment = $request->segment(1);
            if (in_array($segment, $available, true)) {
                $locale = $segment;
            }
        }

        if (! $locale) {
            $locale = session('locale');
        }

        if (! $locale) {
            $browser = substr((string) $request->getPreferredLanguage($available), 0, 2);
            $locale = in_array($browser, $available, true) ? $browser : config('app.locale', 'en');
        }

        $locale = in_array($locale, $available, true) ? $locale : 'en';

        app()->setLocale($locale);
        session(['locale' => $locale]);
        URL::defaults(['locale' => $locale]);

        return $next($request);
    }
}
