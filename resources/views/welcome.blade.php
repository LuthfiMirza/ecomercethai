<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Lungpaeit') }} - Your Tech Partner</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    
    <!-- Tailwind CSS v3.4 -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'saffron': {
                            50: '#fef7ed',
                            100: '#fdedd3',
                            200: '#fbd9a5',
                            300: '#f8c06d',
                            400: '#f5a332',
                            500: '#f28500',
                            600: '#e37400',
                            700: '#c45f00',
                            800: '#a04d00',
                            900: '#7c3a00',
                        },
                        'orange': {
                            50: '#fff7ed',
                            100: '#ffedd5',
                            200: '#fed7aa',
                            300: '#fdba74',
                            400: '#fb923c',
                            500: '#f97316',
                            600: '#ea580c',
                            700: '#c2410c',
                            800: '#9a3412',
                            900: '#7c2d12',
                        }
                    },
                    fontFamily: {
                        'sans': ['Inter', 'Figtree', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #f28500 0%, #f97316 50%, #fb923c 100%);
        }
        .space-bg {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #334155 100%);
        }
        .flash-sale-bg {
            background: linear-gradient(135deg, #f97316 0%, #ea580c 50%, #c2410c 100%);
        }
        .glow-text {
            text-shadow: 0 0 10px rgba(255, 255, 255, 0.8), 0 0 20px rgba(255, 255, 255, 0.6);
        }
        .countdown-box {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .product-card {
            transition: all 0.3s ease;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        .floating-animation {
            animation: float 3s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
    </style>
</head>
@php
    $brandName = config('app.name', 'Lungpaeit');
    $brandLogo = asset('image/logo.jpg');
@endphp

<body class="bg-gray-50 font-sans">
    <!-- Top Orange Header -->
    <div class="bg-orange-600 text-white py-2">
        <div class="max-w-7xl mx-auto px-4 flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="flex items-center gap-2">
                    <img src="{{ $brandLogo }}" alt="{{ $brandName }}" class="h-10 w-10 rounded-full object-cover shadow-sm" loading="lazy">
                    <h1 class="text-xl font-bold">🇹🇭 {{ $brandName }}</h1>
                </div>
            </div>
            <div class="flex items-center space-x-6">
                <div class="relative">
                    <input type="text" placeholder="Search Products, Categories, Brands" 
                           class="bg-white text-gray-800 px-4 py-2 rounded-lg w-80 focus:outline-none focus:ring-2 focus:ring-orange-300">
                    <svg class="absolute right-3 top-2.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <div class="flex items-center space-x-4">
                    <svg class="w-6 h-6 cursor-pointer hover:text-orange-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <svg class="w-6 h-6 cursor-pointer hover:text-orange-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Promotional Banner -->
    <div class="space-bg relative overflow-hidden">
        <div class="absolute inset-0">
            <!-- Space elements -->
            <div class="absolute top-10 left-20 w-32 h-32 bg-orange-500 rounded-full opacity-60 floating-animation"></div>
            <div class="absolute top-20 right-32 w-24 h-24 bg-saffron-400 rounded-full opacity-80 floating-animation" style="animation-delay: 1s;"></div>
            <div class="absolute bottom-20 left-1/3 w-16 h-16 bg-orange-400 rounded-full opacity-70 floating-animation" style="animation-delay: 2s;"></div>
        </div>
        
        <div class="relative max-w-7xl mx-auto px-4 py-16">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div class="text-white">
                    <div class="text-6xl font-bold glow-text mb-4">
                        สูงสุด 5,000
                    </div>
                    <div class="text-2xl mb-8">
                        1 - 30 ก.ย. 68
                    </div>
                    <div class="bg-orange-500 rounded-2xl p-6 inline-block">
                        <p class="text-xl font-semibold">ช็อปได้ที่ www.tokothailand.com</p>
                    </div>
                    <p class="text-sm mt-4 opacity-80">*เงื่อนไขเป็นตามที่บริษัทฯ กำหนด</p>
                </div>
                <div class="relative">
                    <div class="bg-gradient-to-r from-orange-500 to-saffron-600 rounded-2xl p-8 floating-animation">
                        <div class="text-center text-white">
                            <div class="text-4xl mb-4">🖥️</div>
                            <h3 class="text-2xl font-bold mb-2">Gaming PC Setup</h3>
                            <p class="text-lg">High Performance Gaming</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

                <!-- Service Flow Section -->
    <div class="bg-white py-8">
        <div class="max-w-7xl mx-auto px-4">
            <div class="bg-white rounded-3xl p-6 shadow-2xl border border-gray-200">
                <div class="flex items-center justify-center gap-0 overflow-x-auto">
                    <!-- Top-Rated Smartphones -->
                    <div class="flex-shrink-0">
                        <div class="bg-gray-50 rounded-l-2xl rounded-r-none p-4 text-center w-44 h-28 flex flex-col justify-center border border-gray-200 border-r-0">
                            <div class="w-10 h-12 mx-auto mb-2 bg-blue-500 rounded-lg relative">
                                <div class="absolute top-1 left-1 right-1 h-1 bg-white rounded-sm"></div>
                                <div class="absolute top-2 left-1 right-1 bottom-1 bg-white rounded-sm"></div>
                                <div class="absolute top-2.5 left-1.5 w-1 h-1 bg-blue-500 rounded-full"></div>
                                <div class="absolute top-2.5 right-1.5 w-1 h-1 bg-blue-500 rounded-full"></div>
                                <div class="absolute top-3 left-1.5 right-1.5 h-2 bg-blue-500 rounded-sm"></div>
                            </div>
                            <h4 class="font-bold text-xs text-gray-800 leading-tight">Top-Rated<br>Smartphones</h4>
                            <p class="text-xs text-gray-600 mt-1">Leading Brands Available</p>
                        </div>
                    </div>

                    <!-- Arrow -->
                    <div class="flex-shrink-0 w-6 h-28 bg-gray-50 flex items-center justify-center border-t border-b border-gray-200">
                        <div class="w-3 h-3 bg-orange-400 rounded-full"></div>
                    </div>

                    <!-- Notebook -->
                    <div class="flex-shrink-0">
                        <div class="bg-gray-50 rounded-none p-4 text-center w-44 h-28 flex flex-col justify-center border border-gray-200 border-r-0">
                            <div class="w-10 h-8 mx-auto mb-2 bg-gray-700 rounded-lg relative">
                                <div class="absolute top-0 left-0 right-0 h-5 bg-gray-800 rounded-t-lg"></div>
                                <div class="absolute bottom-0 left-0 right-0 h-1.5 bg-gray-600 rounded-b-lg"></div>
                                <div class="absolute top-1 left-1 right-1 h-3 bg-blue-100 rounded-sm"></div>
                            </div>
                            <h4 class="font-bold text-xs text-gray-800">Notebook</h4>
                            <p class="text-xs text-gray-600 mt-1">be all in one's<br>budget</p>
                        </div>
                    </div>

                    <!-- Arrow -->
                    <div class="flex-shrink-0 w-6 h-28 bg-gray-50 flex items-center justify-center border-t border-b border-gray-200">
                        <div class="w-3 h-3 bg-orange-400 rounded-full"></div>
                    </div>

                    <!-- Build/Assemble PC -->
                    <div class="flex-shrink-0">
                        <div class="bg-gray-50 rounded-none p-4 text-center w-44 h-28 flex flex-col justify-center border border-gray-200 border-r-0">
                            <div class="w-10 h-8 mx-auto mb-2 bg-gray-800 rounded-lg relative">
                                <div class="absolute top-1 left-1 w-1.5 h-1.5 bg-blue-400 rounded-full"></div>
                                <div class="absolute top-1 right-1 w-1.5 h-1.5 bg-red-400 rounded-full"></div>
                                <div class="absolute bottom-1 left-1 right-1 h-1 bg-gray-600 rounded-sm"></div>
                                <div class="absolute top-3 left-2 right-2 h-2 bg-gray-700 rounded-sm"></div>
                            </div>
                            <h4 class="font-bold text-xs text-gray-800 leading-tight">Build / Assemble<br>PC</h4>
                            <p class="text-xs text-gray-600 mt-1">Home Delivery within 5 Hours</p>
                        </div>
                    </div>

                    <!-- Arrow -->
                    <div class="flex-shrink-0 w-6 h-28 bg-gray-50 flex items-center justify-center border-t border-b border-gray-200">
                        <div class="w-3 h-3 bg-orange-400 rounded-full"></div>
                    </div>

                    <!-- Computer Sale -->
                    <div class="flex-shrink-0">
                        <div class="bg-gray-50 rounded-none p-4 text-center w-44 h-28 flex flex-col justify-center border border-gray-200 border-r-0">
                            <div class="w-10 h-8 mx-auto mb-2 bg-gray-200 rounded-lg relative border border-gray-300">
                                <div class="absolute top-1 left-1 right-1 h-4 bg-blue-600 rounded-sm"></div>
                                <div class="absolute bottom-1 left-2 right-2 h-1 bg-gray-400 rounded-sm"></div>
                            </div>
                            <h4 class="font-bold text-xs text-gray-800">Computer<br>Sale</h4>
                            <p class="text-xs text-gray-600 mt-1">with various<br>specifications</p>
                        </div>
                    </div>

                    <!-- Arrow -->
                    <div class="flex-shrink-0 w-6 h-28 bg-gray-50 flex items-center justify-center border-t border-b border-gray-200">
                        <div class="w-3 h-3 bg-orange-400 rounded-full"></div>
                    </div>

                    <!-- Solar Cell -->
                    <div class="flex-shrink-0">
                        <div class="bg-gray-50 rounded-none p-4 text-center w-44 h-28 flex flex-col justify-center border border-gray-200 border-r-0">
                            <div class="w-10 h-10 mx-auto mb-2 bg-orange-400 rounded-lg relative">
                                <div class="absolute inset-1 bg-yellow-300 rounded-lg"></div>
                                <div class="absolute top-1.5 left-1.5 right-1.5 h-1.5 bg-orange-500"></div>
                                <div class="absolute top-3 left-1.5 right-1.5 h-1.5 bg-orange-500"></div>
                                <div class="absolute top-4.5 left-1.5 right-1.5 h-1.5 bg-orange-500"></div>
                            </div>
                            <h4 class="font-bold text-xs text-gray-800">Solar<br>Cell</h4>
                            <p class="text-xs text-gray-600 mt-1">One-Stop<br>Service</p>
                        </div>
                    </div>

                    <!-- Arrow -->
                    <div class="flex-shrink-0 w-6 h-28 bg-gray-50 flex items-center justify-center border-t border-b border-gray-200">
                        <div class="w-3 h-3 bg-orange-400 rounded-full"></div>
                    </div>

                    <!-- Claim Check -->
                    <div class="flex-shrink-0">
                        <div class="bg-gray-50 rounded-r-2xl rounded-l-none p-4 text-center w-44 h-28 flex flex-col justify-center border border-gray-200">
                            <div class="w-10 h-10 mx-auto mb-2 bg-indigo-500 rounded-lg relative">
                                <div class="absolute top-1.5 left-1.5 w-6 h-1 bg-white rounded-sm"></div>
                                <div class="absolute top-3 left-1.5 w-4 h-1 bg-white rounded-sm"></div>
                                <div class="absolute top-4.5 left-1.5 w-3 h-1 bg-white rounded-sm"></div>
                                <div class="absolute top-6 right-1.5 w-1.5 h-1.5 bg-white rounded-full"></div>
                            </div>
                            <h4 class="font-bold text-xs text-gray-800">Claim<br>Check</h4>
                            <p class="text-xs text-gray-600 mt-1">Service<br>Inspection</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert/Information Bar -->
    <div class="bg-white py-4">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid md:grid-cols-2 gap-4 mb-4">
                <div class="bg-red-500 text-white p-4 rounded-lg flex items-center space-x-4">
                    <div class="text-3xl">⚠️</div>
                    <div>
                        <h3 class="font-bold text-lg">เตือนภัย มิจฉาชีพ</h3>
                        <p class="text-sm">แจ้งเงื่อนไขื่อนปัญชี</p>
                        <p class="text-sm">หลอกให้โอนเงิน</p>
                    </div>
                </div>
                <div class="bg-orange-500 text-white p-4 rounded-lg flex items-center space-x-4">
                    <div class="text-3xl">💰</div>
                    <div>
                        <h3 class="font-bold text-lg">แข็งคืนเงินค่าสินค้า</h3>
                        <p class="text-sm">ข้างในค่าไม่ได้คุณภาพ</p>
                        <p class="text-sm">Mobile Banking แถมดิน</p>
                    </div>
                </div>
            </div>
            <div class="bg-green-500 text-white p-4 rounded-lg text-center">
                <p class="font-semibold">ติดต่อสื่อสาร "{{ $brandName }}" ตัวจริง +66 2-123-4567</p>
            </div>
        </div>
    </div>

    <!-- Main Content Sections -->
    <div class="bg-white py-12">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid lg:grid-cols-3 gap-8">
                <!-- The Best Deals in Gaming -->
                <div class="lg:col-span-2">
                    <div class="bg-gradient-to-r from-saffron-600 to-saffron-800 text-white p-8 rounded-2xl mb-8">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-3xl font-bold">The Best Deals in Gaming</h2>
                            <div class="text-sm bg-white bg-opacity-20 px-3 py-1 rounded-full">
                                Aug 25 - Sep 7
                            </div>
                        </div>
                        <div class="grid md:grid-cols-2 gap-6">
                            <div class="bg-white bg-opacity-10 p-6 rounded-xl">
                                <div class="text-4xl mb-4">⚔️</div>
                                <h3 class="text-xl font-semibold mb-2">Gaming Setup</h3>
                                <p class="text-sm opacity-90">Premium gaming experience</p>
                            </div>
                            <div class="bg-white bg-opacity-10 p-6 rounded-xl">
                                <div class="text-4xl mb-4">💻</div>
                                <h3 class="text-xl font-semibold mb-2">Gaming Laptops</h3>
                                <p class="text-sm opacity-90">High performance mobile gaming</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Product Categories -->
                    <div class="mb-8">
                        <h3 class="text-2xl font-bold text-gray-800 mb-6">🔧 Hardware Categories</h3>
                        <div class="grid md:grid-cols-4 gap-4">
                            <div class="bg-white border border-orange-200 rounded-lg p-6 text-center hover:shadow-lg transition-shadow hover:border-orange-300">
                                <div class="text-3xl mb-3">🖥️</div>
                                <h4 class="font-semibold text-gray-800">Mainboard</h4>
                                <p class="text-sm text-gray-600 mt-2">Latest motherboards</p>
                            </div>
                            <div class="bg-white border border-orange-200 rounded-lg p-6 text-center hover:shadow-lg transition-shadow hover:border-orange-300">
                                <div class="text-3xl mb-3">⚡</div>
                                <h4 class="font-semibold text-gray-800">CPU</h4>
                                <p class="text-sm text-gray-600 mt-2">Intel & AMD processors</p>
                            </div>
                            <div class="bg-white border border-orange-200 rounded-lg p-6 text-center hover:shadow-lg transition-shadow hover:border-orange-300">
                                <div class="text-3xl mb-3">💾</div>
                                <h4 class="font-semibold text-gray-800">RAM</h4>
                                <p class="text-sm text-gray-600 mt-2">DDR4 & DDR5 memory</p>
                            </div>
                            <div class="bg-white border border-orange-200 rounded-lg p-6 text-center hover:shadow-lg transition-shadow hover:border-orange-300">
                                <div class="text-3xl mb-3">🪑</div>
                                <h4 class="font-semibold text-gray-800">Gaming Chair & Desk</h4>
                                <p class="text-sm text-gray-600 mt-2">Ergonomic furniture</p>
                            </div>
                        </div>
                    </div>

                    <!-- Others Category -->
                    <div class="mb-8">
                        <h3 class="text-2xl font-bold text-gray-800 mb-6">�� Other Categories</h3>
                        <div class="grid md:grid-cols-3 gap-4">
                            <div class="bg-white border border-orange-200 rounded-lg p-6 text-center hover:shadow-lg transition-shadow hover:border-orange-300">
                                <div class="text-3xl mb-3">🎧</div>
                                <h4 class="font-semibold text-gray-800">Audio Accessories</h4>
                                <p class="text-sm text-gray-600 mt-2">Headphones & speakers</p>
                            </div>
                            <div class="bg-white border border-orange-200 rounded-lg p-6 text-center hover:shadow-lg transition-shadow hover:border-orange-300">
                                <div class="text-3xl mb-3">🖱️</div>
                                <h4 class="font-semibold text-gray-800">Input Devices</h4>
                                <p class="text-sm text-gray-600 mt-2">Mice, keyboards, controllers</p>
                            </div>
                            <div class="bg-white border border-orange-200 rounded-lg p-6 text-center hover:shadow-lg transition-shadow hover:border-orange-300">
                                <div class="text-3xl mb-3">📱</div>
                                <h4 class="font-semibold text-gray-800">Mobile Accessories</h4>
                                <p class="text-sm text-gray-600 mt-2">Phone cases & chargers</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Battlefield 6 Promotion -->
                    <div class="bg-gradient-to-br from-orange-600 to-saffron-700 text-white p-6 rounded-xl">
                        <h3 class="text-xl font-bold mb-4">รับเกม Battlefield 6</h3>
                        <div class="bg-white bg-opacity-20 p-4 rounded-lg text-center">
                            <div class="text-4xl mb-2">🎮</div>
                            <p class="text-sm">Free with purchase</p>
                        </div>
                    </div>

                    <!-- FAQ Section -->
                    <div class="bg-white border border-orange-200 rounded-xl p-6">
                        <div class="text-center">
                            <div class="text-6xl mb-4">❓</div>
                            <h3 class="text-xl font-bold text-gray-800 mb-2">คำถามที่พบบ่อย</h3>
                            <p class="text-gray-600 text-sm">ทุกค่าถามเรามีคำตอบให้คุณแล้วที่นี่</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Brand Logos -->
    <div class="bg-gray-100 py-8">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex items-center justify-center space-x-8 overflow-x-auto">
                <div class="bg-white px-6 py-3 rounded-lg shadow-sm">Seagate</div>
                <div class="bg-white px-6 py-3 rounded-lg shadow-sm">MSI</div>
                <div class="bg-white px-6 py-3 rounded-lg shadow-sm">AMD</div>
                <div class="bg-white px-6 py-3 rounded-lg shadow-sm">HP</div>
                <div class="bg-white px-6 py-3 rounded-lg shadow-sm">WD</div>
                <div class="bg-white px-6 py-3 rounded-lg shadow-sm">Samsung</div>
                <div class="bg-white px-6 py-3 rounded-lg shadow-sm">HyperX</div>
            </div>
        </div>
    </div>

    <!-- FLASH SALE Section -->
    <div class="flash-sale-bg py-12">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-4xl font-bold text-white glow-text">FLASH SALE!</h2>
                <button class="bg-white text-orange-600 px-6 py-3 rounded-lg font-semibold hover:bg-gray-100 transition-colors">
                    View All
                </button>
            </div>
            
            <!-- Countdown Timer -->
            <div class="flex items-center justify-center space-x-4 mb-8">
                <div class="countdown-box px-6 py-4 rounded-lg text-center">
                    <div class="text-3xl font-bold text-white">12</div>
                    <div class="text-sm text-white opacity-80">Hours</div>
                </div>
                <div class="text-white text-2xl">:</div>
                <div class="countdown-box px-6 py-4 rounded-lg text-center">
                    <div class="text-3xl font-bold text-white">21</div>
                    <div class="text-sm text-white opacity-80">Minutes</div>
                </div>
                <div class="text-white text-2xl">:</div>
                <div class="countdown-box px-6 py-4 rounded-lg text-center">
                    <div class="text-3xl font-bold text-white">06</div>
                    <div class="text-sm text-white opacity-80">Seconds</div>
                </div>
            </div>

            <!-- Flash Sale Products -->
            <div class="grid md:grid-cols-4 gap-6">
                <div class="product-card bg-white rounded-xl p-6">
                    <div class="text-center">
                        <div class="text-4xl mb-4">❄️</div>
                        <h3 class="font-semibold text-gray-800 mb-2">CPU Cooler</h3>
                        <div class="text-sm text-gray-500 line-through mb-1">฿17,500</div>
                        <div class="text-2xl font-bold text-saffron-600 mb-2">฿12,500</div>
                        <div class="bg-red-500 text-white text-xs px-2 py-1 rounded-full inline-block mb-4">28% OFF</div>
                        <button class="w-full bg-saffron-600 text-white py-2 rounded-lg font-semibold hover:bg-saffron-700 transition-colors">
                            Buy Now
                        </button>
                    </div>
                </div>
                
                <div class="product-card bg-white rounded-xl p-6">
                    <div class="text-center">
                        <div class="text-4xl mb-4">💾</div>
                        <h3 class="font-semibold text-gray-800 mb-2">32GB RAM Kit</h3>
                        <div class="text-sm text-gray-500 line-through mb-1">฿8,900</div>
                        <div class="text-2xl font-bold text-saffron-600 mb-2">฿6,200</div>
                        <div class="bg-red-500 text-white text-xs px-2 py-1 rounded-full inline-block mb-4">30% OFF</div>
                        <button class="w-full bg-saffron-600 text-white py-2 rounded-lg font-semibold hover:bg-saffron-700 transition-colors">
                            Buy Now
                        </button>
                    </div>
                </div>
                
                <div class="product-card bg-white rounded-xl p-6">
                    <div class="text-center">
                        <div class="text-4xl mb-4">🖥️</div>
                        <h3 class="font-semibold text-gray-800 mb-2">Gaming PC Case</h3>
                        <div class="text-sm text-gray-500 line-through mb-1">฿4,500</div>
                        <div class="text-2xl font-bold text-saffron-600 mb-2">฿3,200</div>
                        <div class="bg-red-500 text-white text-xs px-2 py-1 rounded-full inline-block mb-4">29% OFF</div>
                        <button class="w-full bg-saffron-600 text-white py-2 rounded-lg font-semibold hover:bg-saffron-700 transition-colors">
                            Buy Now
                        </button>
                    </div>
                </div>
                
                <div class="product-card bg-white rounded-xl p-6">
                    <div class="text-center">
                        <div class="text-4xl mb-4">⌨️</div>
                        <h3 class="font-semibold text-gray-800 mb-2">RGB Keyboard</h3>
                        <div class="text-sm text-gray-500 line-through mb-1">฿3,200</div>
                        <div class="text-2xl font-bold text-saffron-600 mb-2">฿2,100</div>
                        <div class="bg-red-500 text-white text-xs px-2 py-1 rounded-full inline-block mb-4">34% OFF</div>
                        <button class="w-full bg-saffron-600 text-white py-2 rounded-lg font-semibold hover:bg-saffron-700 transition-colors">
                            Buy Now
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contact Information -->
    <div class="bg-white py-12">
        <div class="max-w-7xl mx-auto px-4">
            <h2 class="text-3xl font-bold text-center text-gray-800 mb-12">Contact Information</h2>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="bg-orange-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-2xl">💬</span>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Live Chat</h3>
                    <p class="text-gray-600 mb-4">24/7 customer support</p>
                    <button class="bg-saffron-600 text-white px-6 py-2 rounded-lg hover:bg-saffron-700 transition-colors">
                        Start Chat
                    </button>
                </div>
                <div class="text-center">
                    <div class="bg-orange-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-2xl">📞</span>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Phone Support</h3>
                    <p class="text-gray-600 mb-2">+66 2-123-4567</p>
                    <p class="text-sm text-gray-500">Mon-Fri: 9AM-6PM</p>
                </div>
                <div class="text-center">
                    <div class="bg-orange-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-2xl">📍</span>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Store Location</h3>
                    <p class="text-gray-600 mb-4">Bangkok, Thailand</p>
                    <button class="bg-saffron-600 text-white px-6 py-2 rounded-lg hover:bg-saffron-700 transition-colors">
                        View Map
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-12">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-2xl font-bold mb-4">🇹🇭 {{ $brandName }}</h3>
                    <p class="text-gray-400">Your trusted technology partner in Southeast Asia. Quality products, competitive prices, excellent service.</p>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Quick Links</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Home</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Products</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Categories</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Contact</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Payment Methods</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li>Credit/Debit Cards</li>
                        <li>PayPal</li>
                        <li>Bank Transfer</li>
                        <li>Cash on Delivery</li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Contact Info</h4>
                    <div class="text-gray-400 space-y-2">
                        <p>📍 Bangkok, Thailand</p>
                        <p>📞 +66 2-123-4567</p>
                        <p>📧 info@tokothailand.com</p>
                        <div class="flex space-x-4 mt-4">
                            <button class="bg-orange-600 px-4 py-2 rounded-lg hover:bg-orange-700 transition-colors">
                                📱 LINE
                            </button>
                            <button class="bg-saffron-600 px-4 py-2 rounded-lg hover:bg-saffron-700 transition-colors">
                                📧 Email
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; {{ date('Y') }} {{ $brandName }}. All rights reserved. | Laravel v{{ Illuminate\Foundation\Application::VERSION }}</p>
            </div>
        </div>
    </footer>

    <script>
        // Countdown Timer
        function updateCountdown() {
            const now = new Date().getTime();
            const endTime = now + (12 * 60 * 60 * 1000) + (21 * 60 * 1000) + (6 * 1000); // 12h 21m 6s from now
            
            const timer = setInterval(function() {
                const now = new Date().getTime();
                const distance = endTime - now;
                
                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);
                
                const hoursEl = document.querySelector('.countdown-box:nth-child(1) .text-3xl');
                const minutesEl = document.querySelector('.countdown-box:nth-child(3) .text-3xl');
                const secondsEl = document.querySelector('.countdown-box:nth-child(5) .text-3xl');
                
                if (hoursEl) hoursEl.textContent = hours.toString().padStart(2, '0');
                if (minutesEl) minutesEl.textContent = minutes.toString().padStart(2, '0');
                if (secondsEl) secondsEl.textContent = seconds.toString().padStart(2, '0');
                
                if (distance < 0) {
                    clearInterval(timer);
                    if (hoursEl) hoursEl.textContent = "00";
                    if (minutesEl) minutesEl.textContent = "00";
                    if (secondsEl) secondsEl.textContent = "00";
                }
            }, 1000);
        }
        
        // Initialize countdown when page loads
        document.addEventListener('DOMContentLoaded', updateCountdown);
        
        // Smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            });
        });
    </script>
</body>
</html>
