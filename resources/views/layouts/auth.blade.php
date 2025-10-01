<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-line-id="{{ env('LINE_ID', 'tokothai') }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script>
      try {
        var saved = localStorage.getItem('theme');
        var isDark = saved ? saved === 'dark' : (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches);
        if (isDark) { document.documentElement.classList.add('dark'); }
        else { document.documentElement.classList.remove('dark'); }
      } catch (e) { /* no-op */ }
    </script>
    <title>{{ config('app.name', 'Toko Thailand') }} &mdash; {{ __('Account') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link rel="preconnect" href="https://unpkg.com">
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>[x-cloak]{display:none !important}</style>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        accent: { 500: '#FF7043', 600: '#f25d2e' },
                        secondary: { 500: '#8B5CF6' },
                    },
                    container: { center: true, padding: '1rem' },
                    boxShadow: { soft: '0 20px 45px rgba(15,23,42,0.12)' },
                },
            },
        };
    </script>
    <style>
        body {
            font-family: 'Figtree', sans-serif;
            background:
                linear-gradient(160deg, rgba(255, 228, 236, 0.95), rgba(255, 239, 248, 0.8)),
                radial-gradient(circle at 12% 18%, rgba(255,165,190,0.45) 0, transparent 60%),
                radial-gradient(circle at 80% 12%, rgba(255,210,128,0.35) 0, transparent 55%),
                #fff3f5;
        }
    </style>
</head>
<body class="min-h-screen bg-white text-neutral-800 dark:bg-neutral-950 dark:text-neutral-200">
    <script>
        window.App = Object.assign({}, window.App, {
            csrfToken: @json(csrf_token()),
            isAuthenticated: @json(auth()->check()),
            lang: localStorage.getItem('lang') || 'en',
            loginUrl: @json(route('login')),
            registerUrl: @json(route('register')),
        });
    </script>

    <main class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-8">
        <div class="w-full max-w-6xl">
            @yield('content')
        </div>
    </main>
</body>
</html>
