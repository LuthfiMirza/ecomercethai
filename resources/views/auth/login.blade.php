@extends('layouts.auth')

@section('content')
<section class="rounded-[32px] border-2 border-orange-100 bg-white/90 shadow-[0_40px_120px_-60px_rgba(255,112,67,0.55)] backdrop-blur-2xl overflow-hidden">
    <div class="grid md:grid-cols-[1.05fr_1fr]">
        <div class="hidden md:flex flex-col justify-between bg-gradient-to-b from-[#fff4ec] via-[#ffe8d8] to-[#ffe1c7] p-10 lg:p-12">
            <div class="flex items-center justify-between text-sm font-semibold text-orange-500">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2">
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-white shadow-sm overflow-hidden">
                        <img src="{{ asset('image/logo.jpg') }}" alt="{{ config('app.name', 'Lungpaeit') }}" class="h-full w-full object-cover" loading="lazy">
                    </span>
                    <span class="uppercase tracking-[0.32em] text-xs text-orange-400">{{ strtoupper(config('app.name', 'Lungpaeit')) }}</span>
                </a>
                <span class="inline-flex h-9 items-center rounded-full bg-white/80 px-4 text-orange-500" data-i18n="login.badge_secure">Secure login</span>
            </div>
            <div class="flex-1 flex items-center justify-center">
                <img src="{{ asset('image/logo.jpg') }}" alt="Store logo" class="max-w-full drop-shadow-xl" loading="lazy">
            </div>
            <ul class="mt-6 flex flex-col gap-2 text-sm text-orange-500/80">
                <li class="inline-flex items-center gap-3"><span class="h-1.5 w-1.5 rounded-full bg-orange-400"></span><span data-i18n="login.highlights.fast_checkout">Save your favorites and check out faster.</span></li>
                <li class="inline-flex items-center gap-3"><span class="h-1.5 w-1.5 rounded-full bg-orange-400"></span><span data-i18n="login.highlights.order_tracking">Track orders anytime from your dashboard.</span></li>
                <li class="inline-flex items-center gap-3"><span class="h-1.5 w-1.5 rounded-full bg-orange-400"></span><span data-i18n="login.highlights.wishlist_sync">Sync wishlist items across your devices.</span></li>
            </ul>
        </div>

        <div class="relative bg-white">
            <a href="{{ route('home') }}" class="md:hidden absolute right-6 top-6 text-orange-400 hover:text-orange-500" aria-label="Close">
                <i class="fa-solid fa-xmark text-xl"></i>
            </a>
            <div class="p-8 sm:p-10 lg:p-12">
                <div class="mb-6 md:hidden overflow-hidden rounded-3xl border border-orange-100 bg-gradient-to-br from-[#fff4ec] to-[#ffe3cf] p-6">
                    <img src="{{ asset('image/logo.jpg') }}" alt="Store logo" class="mx-auto h-48" loading="lazy">
                </div>
                <div class="mb-8 space-y-3 text-center md:text-left">
                    <h1 class="text-3xl font-semibold text-neutral-900" data-i18n="login.heading">Welcome back</h1>
                    <p class="text-base text-neutral-600" data-i18n="login.subtitle">Sign in to continue your shopping journey.</p>
                </div>
                @php($googleEnabled = config('services.google.client_id') && config('services.google.client_secret'))
                <div class="space-y-8">
                    @include('auth.partials.login-form')
                    @if($googleEnabled)
                    <div class="relative text-center text-sm text-neutral-500 before:absolute before:left-0 before:top-1/2 before:h-px before:w-full before:bg-neutral-200 before:content-['']">
                        <span class="relative bg-white px-3" data-i18n="login.social_divider">Or continue with</span>
                    </div>
                    <div class="flex items-center justify-center">
                        <a href="{{ localized_route('social.google.redirect') }}" class="inline-flex items-center gap-3 rounded-full border border-orange-100 bg-white px-6 py-3 text-sm font-semibold text-neutral-700 shadow-sm transition hover:border-orange-200" aria-label="Continue with Google">
                            <i class="fa-brands fa-google text-lg text-[#ea4335]"></i>
                            <span>Google</span>
                        </a>
                    </div>
                    @endif
                    <p class="text-sm text-center text-neutral-500">
                        <span data-i18n="login.no_account">Don’t have an account?</span>
                        <a href="{{ route('register') }}" class="font-semibold text-orange-500 hover:text-orange-400" data-i18n="login.no_account_cta">Create one</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>
@include('auth.partials.i18n-scripts')
@endsection
