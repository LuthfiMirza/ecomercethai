@extends('layouts.app')

@section('content')
<main id="main" class="container py-10" role="main">
    @auth
        <section class="max-w-3xl mx-auto space-y-6">
            <header class="bg-white/90 dark:bg-neutral-900/80 border border-neutral-200 dark:border-neutral-800 rounded-2xl shadow-soft p-8">
                <h1 class="text-2xl font-semibold text-neutral-900 dark:text-neutral-100">
                    <span data-i18n="account.greeting_prefix">Hello</span>, {{ auth()->user()->name }}
                </h1>
                <p class="mt-2 text-sm text-neutral-600 dark:text-neutral-300">{{ auth()->user()->email }}</p>
                <div class="mt-6 flex flex-wrap gap-3">
                    <a href="{{ route('cart') }}" class="px-4 py-2 rounded-lg border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-sm font-medium text-neutral-700 dark:text-neutral-200 hover:border-accent-500" data-i18n="account.open_cart">Open cart</a>
                    <a href="{{ route('wishlist') }}" class="px-4 py-2 rounded-lg border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-sm font-medium text-neutral-700 dark:text-neutral-200 hover:border-accent-500" data-i18n="account.open_wishlist">View wishlist</a>
                </div>
            </header>
            <div class="bg-white/90 dark:bg-neutral-900/80 border border-neutral-200 dark:border-neutral-800 rounded-2xl shadow-soft p-8">
                <h2 class="text-xl font-semibold text-neutral-900 dark:text-neutral-100 mb-4" data-i18n="account.settings_heading">Account settings</h2>
                <p class="text-sm text-neutral-600 dark:text-neutral-300 mb-6" data-i18n="account.settings_description">Manage your sessions and keep your account secure.</p>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-red-500 hover:bg-red-600 text-white text-sm font-semibold">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        <span data-i18n="account.logout">Logout</span>
                    </button>
                </form>
            </div>
        </section>
    @else
        <section class="grid gap-8 md:grid-cols-2">
            <div class="bg-white/90 dark:bg-neutral-900/80 border border-neutral-200 dark:border-neutral-800 rounded-2xl shadow-soft p-8">
                <h1 class="text-2xl font-semibold text-neutral-900 dark:text-neutral-100 mb-2" data-i18n="account.login_block_title">Sign in</h1>
                <p class="text-sm text-neutral-600 dark:text-neutral-300 mb-6" data-i18n="account.login_block_subtitle">Access your cart, wishlist, and order history.</p>
                @include('auth.partials.login-form')
            </div>
            <div class="bg-white/90 dark:bg-neutral-900/80 border border-neutral-200 dark:border-neutral-800 rounded-2xl shadow-soft p-8">
                <h2 class="text-2xl font-semibold text-neutral-900 dark:text-neutral-100 mb-2" data-i18n="account.register_block_title">Create account</h2>
                <p class="text-sm text-neutral-600 dark:text-neutral-300 mb-6" data-i18n="account.register_block_subtitle">Set up a new account for a more personalised experience.</p>
                @include('auth.partials.register-form')
            </div>
        </section>
    @endauth
</main>
@include('auth.partials.i18n-scripts')
@endsection
