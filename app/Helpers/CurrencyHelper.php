<?php

if (!function_exists('money')) {
    /**
     * Format money based on locale and currency
     *
     * @param float $amount
     * @param string|null $currency
     * @return string
     */
    function money($amount, $currency = null)
    {
        $currency = $currency ?? config('app.currency', 'THB');
        $locale = app()->getLocale();

        $formatter = new NumberFormatter($locale, NumberFormatter::CURRENCY);
        
        return $formatter->formatCurrency($amount, $currency);
    }
}

if (!function_exists('format_price')) {
    /**
     * Simple price formatting
     *
     * @param float $amount
     * @return string
     */
    function format_price($amount)
    {
        $currency = config('app.currency', 'THB');
        
        if ($currency === 'THB') {
            return '฿' . number_format($amount, 2);
        } elseif ($currency === 'IDR') {
            return 'Rp ' . number_format($amount, 0, ',', '.');
        } else {
            return '$' . number_format($amount, 2);
        }
    }
}

if (! function_exists('supported_locales')) {
    function supported_locales(): array
    {
        return ['en', 'th'];
    }
}

if (! function_exists('localized_url')) {
    function localized_url(string $path = '', ?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        $available = supported_locales();

        if (! in_array($locale, $available, true)) {
            $locale = config('app.locale', 'en');
        }

        $path = ltrim($path, '/');

        return url($locale . ($path !== '' ? '/' . $path : ''));
    }
}

if (! function_exists('localized_route')) {
    function localized_route(string $name, array $parameters = [], bool $absolute = true): string
    {
        $parameters = array_merge(['locale' => app()->getLocale()], $parameters);

        return route($name, $parameters, $absolute);
    }
}
