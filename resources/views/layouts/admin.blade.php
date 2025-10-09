<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }"
      x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))"
      :class="{'dark': darkMode}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>Admin Panel - Toko Thailand</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Prevent FOUC: set initial dark class before Alpine mounts -->
    <script>
        (function () {
            try {
                const isDark = localStorage.getItem('darkMode') === 'true';
                if (isDark) document.documentElement.classList.add('dark');
            } catch (e) {}
        })();
    </script>
    <style>[x-cloak]{display:none!important;}</style>

    <!-- Vite: Tailwind CSS + Alpine.js + Chart.js (bundled) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Custom CSS for Admin Panel -->
    <style>
        /* Define CSS variables for dark mode */
        :root {
            --background: 0 0% 100%;
            --foreground: 222.2 47.4% 11.2%;
            
            --muted: 210 40% 96.1%;
            --muted-foreground: 215.4 16.3% 46.9%;
            
            --popover: 0 0% 100%;
            --popover-foreground: 222.2 47.4% 11.2%;
            
            --border: 214.3 31.8% 91.4%;
            --input: 214.3 31.8% 91.4%;
            
            --card: 0 0% 100%;
            --card-foreground: 222.2 47.4% 11.2%;
            
            --primary: 222.2 47.4% 11.2%;
            --primary-foreground: 210 40% 98%;
            
            --secondary: 210 40% 96.1%;
            --secondary-foreground: 222.2 47.4% 11.2%;
            
            --accent: 210 40% 96.1%;
            --accent-foreground: 222.2 47.4% 11.2%;
            
            --destructive: 0 100% 50%;
            --destructive-foreground: 210 40% 98%;
            
            --ring: 215 20.2% 65.1%;
            
            --radius: 0.5rem;
        }
         
        .dark {
            --background: 224 71% 4%;
            --foreground: 213 31% 91%;
            
            --muted: 223 47% 11%;
            --muted-foreground: 215.4 16.3% 56.9%;
            
            --accent: 216 34% 17%;
            --accent-foreground: 210 40% 98%;
            
            --popover: 224 71% 4%;
            --popover-foreground: 215 20.2% 65.1%;
            
            --border: 216 34% 17%;
            --input: 216 34% 17%;
            
            --card: 224 71% 4%;
            --card-foreground: 213 31% 91%;
            
            --primary: 210 40% 98%;
            --primary-foreground: 222.2 47.4% 1.2%;
            
            --secondary: 222.2 47.4% 11.2%;
            --secondary-foreground: 210 40% 98%;
            
            --destructive: 0 63% 31%;
            --destructive-foreground: 210 40% 98%;
            
            --ring: 216 34% 17%;
            
            --radius: 0.5rem;
        }
        
        /* Sidebar transition */
        .sidebar-transition {
            transition: all 0.3s ease;
        }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #1f2937;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #4b5563;
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #6b7280;
        }
        
        /* Neon button effect */
        .neon-button {
            background-color: #00ff99;
            color: #000;
            transition: all 0.3s ease;
            border-radius: 1rem; /* xl rounded */
        }
        
        .neon-button:hover {
            box-shadow: 0 0 10px #00ff99, 0 0 20px #00ff99, 0 0 30px #00ff99;
        }
        
        /* Input field styling */
        .input-field {
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            color: #0f172a;
            border-radius: 0.5rem; /* rounded-lg */
        }
        
        .dark .input-field {
            background-color: #1e293b;
            border: 1px solid #334155;
            color: #e2e8f0;
        }
        
        .input-field:focus {
            border-color: #00ff99;
            box-shadow: 0 0 0 3px rgba(0, 255, 153, 0.3);
        }
        
        .input-field::placeholder {
            color: #94a3b8;
        }
        
        /* Card styling */
        .admin-card {
            border-radius: 1rem; /* xl rounded */
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        
        .dark .admin-card {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.3), 0 2px 4px -1px rgba(0, 0, 0, 0.2);
            background-color: #1e293b; /* dark background */
        }
        
        /* Box dark background for dark mode */
        .dark .dark\:bg-boxdark {
            background-color: #1e293b;
        }
        
        .dark .dark\:border-strokedark {
            border-color: #334155;
        }
    </style>
    
    @yield('head')
</head>
<body class="bg-admin-gradient min-h-screen text-slate-800 dark:text-slate-100 font-sans">
    <div class="flex min-h-screen bg-transparent" x-data="{ sidebarOpen: true }">
        <!-- Sidebar -->
        <aside 
            class="w-72 soft-glass text-slate-600 fixed inset-y-6 left-6 rounded-3xl px-5 py-6 transform md:translate-x-0 transition-transform duration-300 ease-in-out z-30 shadow-[0_30px_60px_-35px_rgba(15,23,42,0.4)] dark:text-slate-100"
            :class="{'-translate-x-full': !sidebarOpen}"
        >
            <div class="flex items-center justify-between px-2 pb-6">
                <div class="flex items-center gap-3">
                    <div class="icon-circle bg-gradient-to-tr from-sky-500 to-indigo-500 text-white font-semibold shadow-[0_18px_35px_-18px_rgba(56,132,255,0.85)]">TT</div>
                    <div>
                        <p class="text-base font-semibold text-slate-700 dark:text-white">Toko Thailand</p>
                        <p class="text-xs text-slate-400 dark:text-slate-400/80">Admin Workspace</p>
                    </div>
                </div>
                <button 
                    @click="sidebarOpen = false" 
                    class="md:hidden icon-circle text-slate-500 hover:text-slate-900 dark:text-slate-200"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <nav class="mt-6 space-y-2">
                <a href="{{ localized_route('admin.dashboard') }}" class="admin-nav-link {{ request()->routeIs('admin.dashboard') ? 'admin-nav-link-active text-white' : '' }}">
                    <span class="admin-nav-icon">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    </span>
                    <span class="font-medium">Dashboard</span>
                </a>

                <a href="{{ localized_route('admin.products.index') }}" class="admin-nav-link {{ request()->routeIs('admin.products.*') ? 'admin-nav-link-active text-white' : '' }}">
                    <span class="admin-nav-icon">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    </span>
                    <span class="font-medium">Products</span>
                </a>

                <a href="{{ localized_route('admin.categories.index') }}" class="admin-nav-link {{ request()->routeIs('admin.categories.*') ? 'admin-nav-link-active text-white' : '' }}">
                    <span class="admin-nav-icon">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                    </span>
                    <span class="font-medium">Categories</span>
                </a>

                <a href="{{ localized_route('admin.orders.index') }}" class="admin-nav-link {{ request()->routeIs('admin.orders.*') ? 'admin-nav-link-active text-white' : '' }}">
                    <span class="admin-nav-icon">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    </span>
                    <span class="font-medium">Orders</span>
                </a>

                <a href="{{ localized_route('admin.users.index') }}" class="admin-nav-link {{ request()->routeIs('admin.users.*') ? 'admin-nav-link-active text-white' : '' }}">
                    <span class="admin-nav-icon">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </span>
                    <span class="font-medium">Users</span>
                </a>

                <a href="{{ localized_route('admin.promos.index') }}" class="admin-nav-link {{ request()->routeIs('admin.promos.*') ? 'admin-nav-link-active text-white' : '' }}">
                    <span class="admin-nav-icon">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-2.21 0-4 1.79-4 4s1.79 4 4 4m0-8V4m0 12v4m8-8h4M4 12H0"></path></svg>
                    </span>
                    <span class="font-medium">Promos</span>
                </a>

                <a href="{{ localized_route('admin.reports.index') }}" class="admin-nav-link {{ request()->routeIs('admin.reports.*') ? 'admin-nav-link-active text-white' : '' }}">
                    <span class="admin-nav-icon">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3v18m-7-7h18"></path></svg>
                    </span>
                    <span class="font-medium">Reports</span>
                </a>
            </nav>
        </aside>
        
        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden md:ml-[22rem] lg:ml-[23rem] px-6 pb-10">
            <!-- Header -->
            <header class="soft-glass rounded-3xl mt-6 shadow-[0_30px_55px_-35px_rgba(15,23,42,0.4)]">
                <div class="flex flex-col gap-6 p-6 md:flex-row md:items-center md:justify-between">
                    <div class="flex items-center gap-4">
                        <button 
                            @click="sidebarOpen = !sidebarOpen" 
                            class="icon-circle md:hidden text-slate-500 hover:text-slate-900 dark:text-slate-200"
                        >
                            <svg class="w-6 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                        </button>
                        
                        <h1 class="text-2xl font-semibold text-slate-700 dark:text-white">@yield('header')</h1>
                        <!-- Header Search (md+) -->
                        <div class="hidden md:block">
                            <form action="#" method="GET">
                                <div class="relative text-slate-600 dark:text-slate-300">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                    </span>
                                    <input type="text" name="q" placeholder="Search..." class="block w-72 rounded-2xl border border-white/60 bg-white/80 py-2.5 pl-11 pr-4 text-sm placeholder-slate-400 shadow-inner focus:border-sky-300 focus:ring-4 focus:ring-sky-200/70 outline-none backdrop-blur-xl dark:border-slate-700 dark:bg-slate-900/60">
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-4">
                        @php
                            $inlineNotifications = collect([
                                ['type' => 'success', 'title' => __('Success'), 'message' => session('success')],
                                ['type' => 'info', 'title' => __('Info'), 'message' => session('status')],
                                ['type' => 'error', 'title' => __('Error'), 'message' => session('error')],
                            ])
                            ->filter(fn ($item) => filled($item['message']))
                            ->map(function ($item) {
                                $message = $item['message'];
                                if (is_iterable($message)) {
                                    $message = collect($message)->flatten()->first();
                                }
                                $item['message'] = trim((string) $message);
                                return $item;
                            })
                            ->filter(fn ($item) => $item['message'] !== '')
                            ->values();
                        @endphp

                        <!-- Notification bell -->
                        <div class="relative" x-data="{ open: false }" @keydown.escape.window="open = false">
                            <button
                                type="button"
                                class="relative icon-circle text-slate-500 hover:text-slate-900 dark:text-slate-200"
                                aria-label="Notifications"
                                @click="open = !open"
                            >
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                                @if($inlineNotifications->isNotEmpty())
                                    <span class="absolute -top-1 -right-1 inline-flex h-4 w-4 items-center justify-center rounded-full bg-rose-500 text-[10px] font-semibold text-white shadow-[0_8px_16px_-8px_rgba(225,29,72,0.8)]">{{ $inlineNotifications->count() }}</span>
                                @endif
                            </button>

                            <div
                                x-show="open"
                                x-cloak
                                x-transition.origin.top.right
                                @click.outside="open = false"
                                class="absolute right-0 top-full mt-4 w-80 soft-card rounded-2xl border border-white/60 bg-white shadow-xl backdrop-blur-xl dark:border-slate-700 dark:bg-slate-900/95"
                                style="z-index: 60;"
                            >
                                <div class="border-b border-white/60 px-5 py-4 dark:border-slate-700">
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-sm font-semibold text-slate-600 dark:text-slate-200">{{ __('Notifications') }}</h3>
                                        <button class="text-xs font-medium text-sky-500 hover:text-sky-600" @click="open = false">{{ __('Close') }}</button>
                                    </div>
                                </div>
                                <div class="max-h-64 overflow-y-auto divide-y divide-slate-100 dark:divide-slate-800">
                                    @forelse($inlineNotifications as $item)
                                        <div class="flex items-start gap-3 px-5 py-4">
                                            <span @class([
                                                'inline-flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full text-white',
                                                'bg-emerald-500' => $item['type'] === 'success',
                                                'bg-sky-500' => $item['type'] === 'info',
                                                'bg-rose-500' => $item['type'] === 'error',
                                                'bg-amber-500' => $item['type'] === 'warning',
                                            ])>
                                                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2">
                                                    @switch($item['type'])
                                                        @case('success')
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 10l3 3 7-7" />
                                                            @break
                                                        @case('error')
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l8 8M6 14L14 6" />
                                                            @break
                                                        @case('warning')
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 9v3m0 4h.01M10 3a7 7 0 110 14 7 7 0 010-14z" />
                                                            @break
                                                        @default
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 11-0.001 20.001A10 10 0 0112 2z" />
                                                    @endswitch
                                                </svg>
                                            </span>
                                            <div class="flex-1 text-sm leading-5 text-slate-700 dark:text-slate-200">
                                                <p class="font-medium text-slate-800 dark:text-slate-100">{{ $item['title'] }}</p>
                                                <p class="mt-1 text-sm text-slate-600 dark:text-slate-200">{{ $item['message'] }}</p>
                                                <p class="mt-2 text-xs uppercase tracking-wide text-slate-400 dark:text-slate-400">{{ now()->diffForHumans(null, null, false, 1) }}</p>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="px-5 py-6 text-center text-sm text-slate-500 dark:text-slate-300">
                                            {{ __('You are all caught up!') }}
                                        </div>
                                    @endforelse
                                </div>
                                @if($inlineNotifications->isNotEmpty())
                                    <div class="border-t border-white/60 bg-slate-50/80 px-5 py-3 text-center text-xs font-medium text-slate-500 dark:border-slate-700 dark:bg-slate-900/60 dark:text-slate-300">
                                        {{ __('Keep up the great work!') }}
                                    </div>
                                @endif
                            </div>
                        </div>
                        @include('components.dark-mode-toggle', ['class' => 'icon-circle text-slate-500 hover:text-slate-900 dark:text-slate-200'])
                        
                        <!-- Profile Dropdown -->
                        <div class="relative" x-data="{ dropdownOpen: false }" @keydown.escape.window="dropdownOpen = false">
                            <button type="button" @click="dropdownOpen = !dropdownOpen" class="flex items-center gap-3 rounded-2xl bg-white/60 px-3 py-2 text-slate-500 shadow-[inset_0_1px_0_rgba(255,255,255,0.6)] transition hover:bg-white/80 dark:bg-slate-900/60 dark:text-slate-200">
                                <img class="h-8 w-8 rounded-full object-cover" src="https://ui-avatars.com/api/?name={{ urlencode(optional(Auth::user())->name ?? 'User') }}&background=random" alt="{{ optional(Auth::user())->name ?? 'User' }}">
                                <span class="ml-2 text-sm hidden md:block">{{ optional(Auth::user())->name }}</span>
                                <svg class="ml-1 w-4 h-4 hidden md:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                            
                            <div x-show="dropdownOpen" x-cloak @click.outside="dropdownOpen = false" class="absolute right-0 mt-3 w-52 soft-card rounded-2xl overflow-hidden z-20" x-transition.origin.top.right>
                                <a href="{{ localized_route('admin.profile.show') }}" class="block px-4 py-2 text-sm text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700">Your Profile</a>
                                <a href="#" class="block px-4 py-2 text-sm text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700">Settings</a>
                                <div class="border-t border-slate-200 dark:border-slate-700"></div>
                                <form action="{{ localized_route('admin.logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full text-left block px-4 py-2 text-sm text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700">
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>
            
            <!-- Notifications (Toasts) -->
            @if(session('success'))
                @include('components.notification', ['open' => true, 'type' => 'success', 'message' => session('success')])
            @endif
            @if(session('status'))
                @include('components.notification', ['open' => true, 'type' => 'info', 'message' => session('status')])
            @endif
            @if(session('error'))
                @include('components.notification', ['open' => true, 'type' => 'error', 'message' => session('error')])
            @endif
            @if(isset($errors) && $errors->any())
                @include('components.notification', ['open' => true, 'type' => 'error', 'message' => $errors->all()])
            @endif
            
            <!-- Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-transparent">
                <div class="container mx-auto px-0 pt-8 pb-12 form-modern space-y-8">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>
    
    @yield('scripts')
</body>
</html>
