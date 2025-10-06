@extends('layouts.auth')

@section('content')
<section class="rounded-[34px] border-[3px] border-[#ffd9df] bg-white/80 dark:bg-neutral-900/85 shadow-[0_30px_80px_-25px_rgba(255,120,150,0.45)] backdrop-blur-xl overflow-hidden">
    <div class="grid md:grid-cols-[1.05fr_1fr]">
        <div class="hidden md:flex flex-col justify-between bg-gradient-to-b from-[#ffe5ef] via-[#ffeff6] to-[#ffe9ef] p-10 lg:p-12">
            <div class="flex items-center justify-between text-sm font-semibold text-rose-500">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2">
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-[#fff4f8] shadow-sm">
                        <i class="fa-solid fa-store"></i>
                    </span>
                    <span class="uppercase tracking-[0.32em] text-xs text-rose-400">TOKO THAILAND</span>
                </a>
                <span class="inline-flex h-9 items-center rounded-full bg-white/70 px-4 text-rose-400" data-i18n="login.badge_secure">Secure login</span>
            </div>
            <div class="flex-1 flex items-center justify-center">
                <img src="{{ asset('images/auth-illustration.svg') }}" alt="Login illustration" class="max-w-full drop-shadow-xl" loading="lazy">
            </div>
            <ul class="mt-6 flex flex-col gap-2 text-sm text-rose-500/80">
                <li class="inline-flex items-center gap-3"><span class="h-1.5 w-1.5 rounded-full bg-rose-400"></span><span data-i18n="login.highlights.fast_checkout">Save your favorites and check out faster.</span></li>
                <li class="inline-flex items-center gap-3"><span class="h-1.5 w-1.5 rounded-full bg-rose-400"></span><span data-i18n="login.highlights.order_tracking">Track orders anytime from your dashboard.</span></li>
                <li class="inline-flex items-center gap-3"><span class="h-1.5 w-1.5 rounded-full bg-rose-400"></span><span data-i18n="login.highlights.wishlist_sync">Sync wishlist items across your devices.</span></li>
            </ul>
        </div>

        <div class="relative bg-white/95 dark:bg-neutral-900/90">
            <a href="{{ route('home') }}" class="md:hidden absolute right-6 top-6 text-rose-400 hover:text-rose-500" aria-label="Close">
                <i class="fa-solid fa-xmark text-xl"></i>
            </a>
            <div class="p-8 sm:p-10 lg:p-12">
                <div class="mb-6 md:hidden overflow-hidden rounded-3xl border border-[#ffd6de] bg-gradient-to-br from-[#ffe8f1] to-[#ffd4df] p-6">
                    <img src="{{ asset('images/auth-illustration.svg') }}" alt="Login illustration" class="mx-auto h-48" loading="lazy">
                </div>
                <div class="mb-8 space-y-3 text-center md:text-left">
                    <h1 class="text-3xl font-semibold text-neutral-900 dark:text-neutral-100" data-i18n="login.heading">Welcome back</h1>
                    <p class="text-base text-neutral-600 dark:text-neutral-300" data-i18n="login.subtitle">Sign in to continue your shopping journey.</p>
                </div>
                <div class="space-y-8">
                    @include('auth.partials.login-form')
                    <div class="relative text-center text-sm text-neutral-500 before:absolute before:left-0 before:top-1/2 before:h-px before:w-full before:bg-neutral-200 before:content-[''] dark:text-neutral-400 dark:before:bg-neutral-800">
                        <span class="relative bg-white dark:bg-neutral-900 px-3" data-i18n="login.social_divider">Or continue with</span>
                    </div>
                    <div class="flex items-center justify-center gap-4">
                        <button type="button" class="h-12 w-12 rounded-full border border-[#ffd7de] bg-white shadow-sm text-[#ea4335] hover:border-[#ffb4c1]">
                            <i class="fa-brands fa-google text-lg"></i>
                        </button>
                        <button type="button" class="h-12 w-12 rounded-full border border-[#ffd7de] bg-white shadow-sm text-[#1877f2] hover:border-[#ffb4c1]">
                            <i class="fa-brands fa-facebook-f text-lg"></i>
                        </button>
                        <button type="button" class="h-12 w-12 rounded-full border border-[#ffd7de] bg-white shadow-sm text-neutral-900 hover:border-[#ffb4c1]">
                            <i class="fa-brands fa-apple text-xl"></i>
                        </button>
                    </div>
                    <p class="text-sm text-center text-neutral-500 dark:text-neutral-300">
                        <span data-i18n="login.no_account">Don’t have an account?</span>
                        <a href="{{ route('register') }}" class="font-semibold text-rose-500 hover:text-rose-400" data-i18n="login.no_account_cta">Create one</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>
@include('auth.partials.i18n-scripts')
@endsection
