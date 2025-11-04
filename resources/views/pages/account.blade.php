@extends('layouts.app')

@section('content')
<main id="main" class="container py-10" role="main">
    @auth
        <section class="max-w-4xl mx-auto space-y-6">
            <header class="bg-white/90 dark:bg-neutral-900/80 border border-neutral-200 dark:border-neutral-800 rounded-2xl shadow-soft p-8">
                <h1 class="text-2xl font-semibold text-neutral-900 dark:text-neutral-100">
                    <span data-i18n="account.greeting_prefix">Hello</span> {{ auth()->user()->name }}
                </h1>
                <p class="mt-2 text-sm text-neutral-600 dark:text-neutral-300">{{ auth()->user()->email }}</p>
                <div class="mt-6 flex flex-wrap gap-3">
                    <a href="{{ route('cart') }}" class="px-4 py-2 rounded-lg border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-sm font-medium text-neutral-700 dark:text-neutral-200 hover:border-accent-500" data-i18n="account.open_cart">Open cart</a>
                    <a href="{{ route('wishlist') }}" class="px-4 py-2 rounded-lg border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-sm font-medium text-neutral-700 dark:text-neutral-200 hover:border-accent-500" data-i18n="account.open_wishlist">View wishlist</a>
                </div>
            </header>

            <!-- Orders Section -->
            @if($orders->count() > 0)
            <div class="bg-white/90 dark:bg-neutral-900/80 border border-neutral-200 dark:border-neutral-800 rounded-2xl shadow-soft p-8">
                <h2 class="text-xl font-semibold text-neutral-900 dark:text-neutral-100 mb-6">Order History</h2>
                <div class="space-y-4">
                    @foreach($orders as $order)
                    <div class="border border-neutral-200 dark:border-neutral-700 rounded-lg p-4 hover:border-accent-500 transition">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <h3 class="font-semibold text-neutral-900 dark:text-neutral-100">Order #{{ $order->id }}</h3>
                                    <span class="inline-block px-2 py-1 rounded text-xs font-medium
                                        @if($order->status === 'completed')
                                            bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                        @elseif($order->status === 'pending')
                                            bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                        @elseif($order->status === 'cancelled')
                                            bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                        @else
                                            bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                        @endif
                                    ">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </div>
                                <p class="text-sm text-neutral-600 dark:text-neutral-400">
                                    {{ $order->created_at->format('d M Y H:i') }}
                                </p>
                                <p class="text-sm text-neutral-600 dark:text-neutral-400 mt-1">
                                    {{ $order->orderItems->count() }} item(s) - Total: <span class="font-semibold">฿{{ number_format($order->total_amount, 2) }}</span>
                                </p>
                            </div>
                            <div class="flex flex-col gap-2">
                                <a href="{{ route('orders.show', ['locale' => app()->getLocale(), 'order' => $order->id]) }}" 
                                   class="px-4 py-2 rounded-lg bg-accent-500 hover:bg-accent-600 text-white text-sm font-medium text-center transition">
                                    View Details
                                </a>
                                @if($order->track_token)
                                <a href="{{ route('orders.track', ['locale' => app()->getLocale(), 'token' => $order->track_token]) }}" 
                                   class="px-4 py-2 rounded-lg border border-accent-500 text-accent-500 hover:bg-accent-50 dark:hover:bg-accent-900/20 text-sm font-medium text-center transition">
                                    <i class="fa-solid fa-location-dot mr-2"></i>Track Order
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @else
            <div class="bg-white/90 dark:bg-neutral-900/80 border border-neutral-200 dark:border-neutral-800 rounded-2xl shadow-soft p-8 text-center">
                <p class="text-neutral-600 dark:text-neutral-400 mb-4">You haven't placed any orders yet.</p>
                <a href="{{ route('catalog') }}" class="inline-block px-4 py-2 rounded-lg bg-accent-500 hover:bg-accent-600 text-white text-sm font-medium transition">
                    Start Shopping
                </a>
            </div>
            @endif

            <!-- Account Settings Section -->
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
                <p class="text-sm text-neutral-600 dark:text-neutral-300 mb-6" data-i18n="account.login_block_subtitle">Access your cart wishlist and order history.</p>
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
