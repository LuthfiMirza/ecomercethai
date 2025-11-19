<?php

use Illuminate\Support\Str;

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
    function format_price($amount, ?string $currency = null)
    {
        $currency = $currency ?? config('app.currency', 'THB');
        
        if ($currency === 'THB') {
            return '฿' . number_format($amount, 2);
        }

        if (function_exists('money')) {
            return money($amount, $currency);
        }

        return $currency . ' ' . number_format($amount, 2);
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
    function localized_route(string $name, array $parameters = [], bool $absolute = false): string
    {
        $parameters = array_merge(['locale' => app()->getLocale()], $parameters);

        return route($name, $parameters, $absolute);
    }
}

if (! function_exists('humanize_label')) {
    /**
     * Convert snake/slug strings into human readable labels with UTF-8 safety.
     */
    function humanize_label(?string $value, string $fallback = ''): string
    {
        $value = trim((string) ($value ?? ''));

        if ($value === '') {
            return $fallback;
        }

        $stringable = Str::of($value)
            ->replace(['_', '-'], ' ')
            ->squish();

        $normalized = (string) $stringable;

        if ($normalized === '') {
            return $fallback;
        }

        if (preg_match('/[A-Za-z]/', $normalized)) {
            return (string) $stringable->ucfirst();
        }

        return $normalized;
    }
}
