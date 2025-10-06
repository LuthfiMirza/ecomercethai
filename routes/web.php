<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::pattern('locale', 'en|th');

Route::get('/', function (Request $request) {
    $supported = supported_locales();

    $locale = session('locale');

    if (! $locale) {
        $preferred = substr((string) $request->getPreferredLanguage($supported), 0, 2);
        $locale = in_array($preferred, $supported, true) ? $preferred : config('app.locale', 'en');
    }

    if (! in_array($locale, $supported, true)) {
        $locale = 'en';
    }

    return redirect()->to(url($locale));
})->name('redirect.locale');

Route::prefix('{locale}')
    ->whereIn('locale', supported_locales())
    ->group(function () {
        require __DIR__.'/localized.php';
    });
