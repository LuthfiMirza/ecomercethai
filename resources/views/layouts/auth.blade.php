<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      data-line-id="{{ env('LINE_ID', 'tokothai') }}"
      data-currency="{{ config('app.currency', 'THB') }}"
      data-locale="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Lungpaeit') }} &mdash; {{ __('Account') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('image/logo.jpg') }}">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link rel="preconnect" href="https://unpkg.com">
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    @viteReactRefresh
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak]{display:none !important}</style>
</head>
<body class="bg-[#fff9f5] text-neutral-800">
    <script>
        window.App = Object.assign({}, window.App, {
            csrfToken: @json(csrf_token()),
            isAuthenticated: @json(auth()->check()),
            lang: localStorage.getItem('lang') || '{{ app()->getLocale() }}',
            locale: '{{ app()->getLocale() }}',
            loginUrl: @json(route('login')),
            registerUrl: @json(route('register')),
        });
    </script>

    <x-navbar />

    <main class="pt-28 pb-12">
        <div class="container mx-auto px-4">
            <div class="relative grid gap-8 lg:grid-cols-[1.1fr_0.9fr]">
                <div class="absolute inset-0 -z-10 rounded-[36px] bg-gradient-to-br from-orange-50 via-white to-amber-50 shadow-[0_40px_120px_-60px_rgba(255,112,67,0.8)]"></div>
                <div class="lg:col-span-2">
                    @yield('content')
                </div>
            </div>
        </div>
    </main>

    <x-footer />
</body>
</html>
