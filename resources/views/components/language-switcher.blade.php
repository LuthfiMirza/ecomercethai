@php
    $currentLocale = app()->getLocale();
    $path = request()->path();
    $stripped = preg_replace('#^(?:en|th)(?:/)?#', '', $path);
    $normalized = ltrim($stripped, '/');
    $query = request()->getQueryString();

    $buildLocaleUrl = static function (string $locale) use ($normalized, $query) {
        $base = localized_url($normalized, $locale);

        return $query ? $base . '?' . $query : $base;
    };
@endphp

<div x-data="{
    lang: '{{ $currentLocale }}',
    set(code) {
        this.lang = code;
        localStorage.setItem('lang', code);
        document.dispatchEvent(new CustomEvent('i18n:change', { detail: { lang: code } }));
    }
}" class="inline-flex items-center rounded-full border border-neutral-200 bg-white px-1.5 py-1 text-xs font-semibold shadow-sm dark:border-neutral-700 dark:bg-neutral-800" role="group" aria-label="Language switcher">
  <a href="{{ $buildLocaleUrl('en') }}"
     @click="set('en')"
     class="inline-flex items-center gap-2 rounded-full px-3 py-1.5 transition"
     :class="lang === 'en' ? 'bg-accent-500 text-white shadow' : 'text-neutral-600 hover:bg-neutral-100 dark:text-neutral-200 dark:hover:bg-neutral-700'">
    <span aria-hidden="true">🇬🇧</span>
    <span class="font-semibold">EN</span>
  </a>

  <a href="{{ $buildLocaleUrl('th') }}"
     @click="set('th')"
     class="inline-flex items-center gap-2 rounded-full px-3 py-1.5 transition"
     :class="lang === 'th' ? 'bg-accent-500 text-white shadow' : 'text-neutral-600 hover:bg-neutral-100 dark:text-neutral-200 dark:hover:bg-neutral-700'">
    <span aria-hidden="true">🇹🇭</span>
    <span class="font-semibold">TH</span>
  </a>
</div>
