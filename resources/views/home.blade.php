@extends('layouts.app')

@section('content')
<!-- Hero Section / Slider -->
<div class="hero-slider relative h-[600px] overflow-hidden">
    <div class="container mx-auto px-4 h-full flex items-center">
        <div class="w-full md:w-1/2">
            <h1 class="text-5xl font-bold text-cream mb-4">Gaming PC Ultra</h1>
            <p class="text-2xl text-accent mb-8">$1,999.99</p>
            <button class="bg-accent text-white px-8 py-3 rounded-lg hover:bg-accent/90 transition">SHOP NOW</button>
        </div>
    </div>
    <!-- Slider Navigation -->
    <button class="absolute left-4 top-1/2 -translate-y-1/2 text-cream hover:text-accent">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
        </svg>
    </button>
    <button class="absolute right-4 top-1/2 -translate-y-1/2 text-cream hover:text-accent">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
    </button>
</div>

<!-- Category Icons -->
<div class="bg-primary py-12">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-2 md:grid-cols-7 gap-8">
            @foreach(['CPU', 'GPU', 'RAM', 'SSD', 'HDD', 'PSU', 'Monitor', 'Fan', 'Mouse', 'Keyboard', 'Headset', 'USB', 'OS', 'Smartphone'] as $category)
            <div class="category-icon text-center cursor-pointer">
                <div class="w-16 h-16 mx-auto bg-secondary rounded-full flex items-center justify-center mb-2 hover:bg-secondary/90 transition-colors">
                    <i class="fas fa-microchip text-2xl text-white"></i>
                </div>
                <p class="text-secondary text-sm">{{ $category }}</p>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Hot Categories (added) -->
<section class="container mx-auto px-6 py-10">
  <h2 class="text-2xl font-bold text-gray-900 mb-6">Hot Categories</h2>
  <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
    @foreach(['GPU','CPU','SSD','Monitor','Keyboard','Mouse','Headset','Motherboard','RAM','PSU','Cooler','Laptop'] as $cat)
      <a href="/catalog?category={{ urlencode($cat) }}" class="group rounded-xl border bg-white hover:shadow-soft transition p-4 flex flex-col items-center text-center">
        <img loading="lazy" src="https://source.unsplash.com/120x120/?{{ urlencode($cat) }}" alt="{{ $cat }}" class="w-16 h-16 object-cover rounded-md"/>
        <span class="mt-2 text-sm font-medium text-gray-800">{{ $cat }}</span>
      </a>
    @endforeach
  </div>
</section>

<!-- Product Filter -->
<!-- <div class="container mx-auto px-4 py-8">
    <div class="flex flex-wrap gap-4 items-center">
        <select class="bg-primary text-accent border border-accent rounded px-4 py-2">
            <option>Product</option>
        </select>
        <select class="bg-primary text-accent border border-accent rounded px-4 py-2">
            <option>Brand</option>
        </select>
        <select class="bg-primary text-accent border border-accent rounded px-4 py-2">
            <option>Price</option>
        </select>
        <button class="bg-accent text-white px-6 py-2 rounded hover:bg-accent/90">Filter</button>
    </div>
</div> -->

<!-- Rekomendasi Produk Section -->
<!-- <div class="bg-primary py-12 mt-8 pixel-border">
    <div class="container mx-auto px-4">
        <h2 class="pixel-heading text-center mb-8">Rekomendasi Produk</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @php
            $recommendedProducts = [
                ['name' => 'Gaming Laptop ROG', 'price' => '1,499.99', 'image' => 'https://via.placeholder.com/300x200?text=Gaming+Laptop'],
                ['name' => 'Mechanical Keyboard', 'price' => '199.99', 'image' => 'https://via.placeholder.com/300x200?text=Keyboard'],
                ['name' => 'Gaming Mouse', 'price' => '89.99', 'image' => 'https://via.placeholder.com/300x200?text=Mouse']
            ];
            @endphp

            @foreach($recommendedProducts as $product)
            <div class="pixel-card overflow-hidden">
                <img src="{{ $product['image'] }}" alt="{{ $product['name'] }}" class="w-full h-48 object-cover">
                <div class="p-4">
                    <h3 class="font-bold text-secondary">{{ $product['name'] }}</h3>
                    <p class="text-gray-600 mt-2">${{ $product['price'] }}</p>
                    <button class="pixel-button w-full mt-4">Buy Now</button>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div> -->

<!-- Product Catalog with Ads -->
<!-- <div class="container mx-auto px-4 py-12">
    <h2 class="pixel-heading text-center mb-8">Our Products</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        @php
        $products = [
            ['name' => 'Gaming Monitor', 'price' => '399.99', 'image' => 'https://via.placeholder.com/300x200?text=Monitor'],
            ['name' => 'Gaming Chair', 'price' => '299.99', 'image' => 'https://via.placeholder.com/300x200?text=Chair'],
            ['name' => 'RTX 4080', 'price' => '899.99', 'image' => 'https://via.placeholder.com/300x200?text=GPU'],
            ['name' => 'Gaming Headset', 'price' => '129.99', 'image' => 'https://via.placeholder.com/300x200?text=Headset'],
            ['name' => 'SSD 1TB', 'price' => '149.99', 'image' => 'https://via.placeholder.com/300x200?text=SSD'],
            ['name' => 'RAM 32GB', 'price' => '199.99', 'image' => 'https://via.placeholder.com/300x200?text=RAM'],
            ['name' => 'Power Supply', 'price' => '179.99', 'image' => 'https://via.placeholder.com/300x200?text=PSU'],
            ['name' => 'CPU Cooler', 'price' => '89.99', 'image' => 'https://via.placeholder.com/300x200?text=Cooler']
        ];
        @endphp

        @foreach($products as $index => $product)
            @if($index > 0 && $index % 4 == 0)
                <div class="col-span-full pixel-border bg-white p-4 text-center">
                    <img src="{{ asset('images/ad-example.png') }}" 
                         alt="Advertisement" 
                         class="mx-auto"
                         onerror="this.onerror=null; this.src='data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAzMDAgMTAwIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZjBmMGYwIi8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCIgZm9udC1zaXplPSIyNCIgZmlsbD0iIzY2NiIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9XCIuM2VtXCI+QWR2ZXJ0aXNlbWVudDwvdGV4dD48L3N2Zz4='">
                    <h3 class="font-pixel text-xl text-gray-900 mt-4">SPECIAL OFFER!</h3>
                    <p class="text-gray-700 my-2">Limited Time Deal</p>
                    <button class="btn-accent mt-2">Shop Now</button>
                </div>
            @endif -->
            <!-- Product Card dalam Rekomendasi Produk Section -->
            <!-- <div class="bg-white border-2 border-black p-4 rounded-md hover:shadow-lg transition-all">
                <img src="{{ $product['image'] }}" alt="{{ $product['name'] }}" class="w-full h-48 object-cover mb-4">
                <div class="p-4">
                    <h3 class="font-bold text-gray-900">{{ $product['name'] }}</h3>
                    <p class="text-gray-700 mt-2">${{ $product['price'] }}</p>
                    <button class="w-full mt-4 bg-orange-500 text-white border-2 border-black py-2 px-4 rounded-md hover:bg-orange-600 transition-colors">Buy Now</button>
                </div>
            </div> -->
            
            <!-- Product Card dalam Product Catalog -->
            <!-- <div class="bg-white border-2 border-black p-4 rounded-md hover:shadow-lg transition-all">
                <img src="{{ $product['image'] }}" 
                     alt="{{ $product['name'] }}" 
                     class="w-full h-48 object-cover mb-4">
                <h3 class="font-bold text-gray-900">{{ $product['name'] }}</h3>
                <p class="text-gray-700 mt-2">${{ $product['price'] }}</p>
                <button class="w-full mt-4 bg-orange-500 text-white border-2 border-black py-2 px-4 rounded-md hover:bg-orange-600 transition-colors">Buy Now</button>
            </div>
        @endforeach
    </div>
</div> -->

<!-- Top Advertisement Banner -->
<div class="container mx-auto px-6 my-8">
    <div class="bg-white p-4 rounded-md text-center">
        <img src="{{ asset('image/iklan.jpg') }}" 
             alt="Top Advertisement" 
             class="mx-auto max-w-full h-auto">
    </div>
</div>

<!-- Rekomendasi Produk Section -->
<div class="container mx-auto px-6">
    <h2 class="text-2xl font-bold text-gray-900 mb-8 text-center">Rekomendasi Produk</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @php
        $recommendedProducts = [
            ['name' => 'Gaming Laptop ROG', 'price' => '1,499.99', 'image' => '/image/monkeyHack.png'],
            ['name' => 'Mechanical Keyboard', 'price' => '199.99', 'image' => 'https://via.placeholder.com/300x200?text=Keyboard'],
            ['name' => 'Gaming Mouse', 'price' => '89.99', 'image' => 'https://via.placeholder.com/300x200?text=Mouse']
        ];
        @endphp

        @foreach($recommendedProducts as $product)
        <div class="bg-white rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300 border border-orange-100 overflow-hidden relative">
            <!-- Badge -->
            <div class="absolute top-2 right-2 bg-orange-500 text-white text-xs font-bold px-2 py-1 rounded-full">
                New
            </div>
            
            <!-- Product Image -->
            <div class="relative h-48 overflow-hidden">
                <img src="{{ $product['image'] }}" 
                     alt="{{ $product['name'] }}" 
                     class="w-full h-full object-cover transform hover:scale-105 transition-transform duration-300">
            </div>
            
            <!-- Product Info -->
            <div class="p-4 border-t border-orange-100">
                <h3 class="font-bold text-lg text-gray-800 mb-2">{{ $product['name'] }}</h3>
                
                <!-- Price and Rating -->
                <div class="flex justify-between items-center mb-4">
                    <span class="text-orange-500 font-bold text-xl">${{ $product['price'] }}</span>
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-yellow-400 fill-current" viewBox="0 0 20 20">
                            <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                        </svg>
                        <span class="text-gray-600 text-sm ml-1">4.5</span>
                    </div>
                </div>
                
                <!-- Buy Button -->
                <button class="w-full bg-orange-500 text-white py-2 rounded-lg font-semibold hover:bg-orange-600 transform hover:-translate-y-0.5 transition-all duration-200 flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    Buy Now
                </button>
                <button data-compare data-name="{{ $product['name'] }}" data-price="{{ $product['price'] }}" data-image="{{ $product['image'] }}" data-brand="BrandX" data-stock="In stock" data-variation="Default" data-rating="4.5" class="mt-2 w-full border border-orange-300 text-orange-600 py-2 rounded-lg font-medium hover:bg-orange-50 transition-colors">
                    <i class="fa-solid fa-code-compare mr-2"></i> Add to Compare
                </button>
            </div>
        </div>
        @endforeach
    </div>
</div>

<!-- Middle Advertisement Banner -->
<div class="container mx-auto px-6 my-12">
    <div class="bg-white border-2 border-orange-500 p-4 rounded-md text-center">
        <img src="{{ asset('images/ad-example.png') }}" 
             alt="Middle Advertisement" 
             class="mx-auto max-w-full h-auto"
             onerror="this.onerror=null; this.src='data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAzMDAgMTAwIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZjBmMGYwIi8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCIgZm9udC1zaXplPSIyNCIgZmlsbD0iIzY2NiIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9XCIuM2VtXCI+QWR2ZXJ0aXNlbWVudDwvdGV4dD48L3N2Zz4='">
    </div>
</div>

<!-- Rekomendasi Produk Section -->
<div class="container mx-auto px-6">
    <h2 class="text-2xl font-bold text-gray-900 mb-8 text-center">Rekomendasi Produk</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @php
        $recommendedProducts = [
            ['name' => 'Gaming Laptop ROG', 'price' => '1,499.99', 'image' => '/image/monkeyHack.png'],
            ['name' => 'Mechanical Keyboard', 'price' => '199.99', 'image' => 'https://via.placeholder.com/300x200?text=Keyboard'],
            ['name' => 'Gaming Mouse', 'price' => '89.99', 'image' => 'https://via.placeholder.com/300x200?text=Mouse']
        ];
        @endphp

        @foreach($recommendedProducts as $product)
        <div class="bg-white rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300 border border-orange-100 overflow-hidden relative">
            <!-- Badge -->
            <div class="absolute top-2 right-2 bg-orange-500 text-white text-xs font-bold px-2 py-1 rounded-full">
                New
            </div>
            
            <!-- Product Image -->
            <div class="relative h-48 overflow-hidden">
                <img src="{{ $product['image'] }}" 
                     alt="{{ $product['name'] }}" 
                     class="w-full h-full object-cover transform hover:scale-105 transition-transform duration-300">
            </div>
            
            <!-- Product Info -->
            <div class="p-4 border-t border-orange-100">
                <h3 class="font-bold text-lg text-gray-800 mb-2">{{ $product['name'] }}</h3>
                
                <!-- Price and Rating -->
                <div class="flex justify-between items-center mb-4">
                    <span class="text-orange-500 font-bold text-xl">${{ $product['price'] }}</span>
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-yellow-400 fill-current" viewBox="0 0 20 20">
                            <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                        </svg>
                        <span class="text-gray-600 text-sm ml-1">4.5</span>
                    </div>
                </div>
                
                <!-- Buy Button -->
                <button class="w-full bg-orange-500 text-white py-2 rounded-lg font-semibold hover:bg-orange-600 transform hover:-translate-y-0.5 transition-all duration-200 flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    Buy Now
                </button>
            </div>
        </div>
        @endforeach
    </div>
</div>

<!-- Horizontal Banner Between Sections -->
<div class="container mx-auto px-6 py-8">
    <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
        <img src="{{ asset('image/iklan.jpg') }}" alt="Wide Banner" class="w-full h-40 object-cover">
    </div>
</div>

<!-- Product Catalog Section -->
<div class="container mx-auto px-6">
    <h2 class="text-2xl font-bold text-gray-900 mb-8 text-center">Our Products</h2>
    @php
        $products = $products ?? [
            ['name' => 'Gaming Monitor', 'price' => '399.99', 'image' => 'https://via.placeholder.com/300x200?text=Monitor'],
            ['name' => 'Gaming Chair', 'price' => '299.99', 'image' => 'https://via.placeholder.com/300x200?text=Chair'],
            ['name' => 'RTX 4080', 'price' => '899.99', 'image' => 'https://via.placeholder.com/300x200?text=GPU'],
            ['name' => 'Gaming Headset', 'price' => '129.99', 'image' => 'https://via.placeholder.com/300x200?text=Headset'],
        ];
    @endphp
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach($products as $index => $product)
        @if($index < 3)
        <!-- Product Card Template -->
        <div class="bg-white rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300 border border-orange-100 overflow-hidden relative">
            <!-- Badge -->
            <div class="absolute top-2 right-2 bg-orange-500 text-white text-xs font-bold px-2 py-1 rounded-full">
                New
            </div>
            
            <!-- Product Image -->
            <div class="relative h-48 overflow-hidden">
                <img src="{{ $product['image'] }}" 
                     alt="{{ $product['name'] }}" 
                     class="w-full h-full object-cover transform hover:scale-105 transition-transform duration-300">
            </div>
            
            <!-- Product Info -->
            <div class="p-4 border-t border-orange-100">
                <h3 class="font-bold text-lg text-gray-800 mb-2">{{ $product['name'] }}</h3>
                
                <!-- Price and Rating -->
                <div class="flex justify-between items-center mb-4">
                    <span class="text-orange-500 font-bold text-xl">${{ $product['price'] }}</span>
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-yellow-400 fill-current" viewBox="0 0 20 20">
                            <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                        </svg>
                        <span class="text-gray-600 text-sm ml-1">4.5</span>
                    </div>
                </div>
                
                <!-- Buy Button -->
                <button class="w-full bg-orange-500 text-white py-2 rounded-lg font-semibold hover:bg-orange-600 transform hover:-translate-y-0.5 transition-all duration-200 flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    Buy Now
                </button>
                <button data-compare data-name="{{ $product['name'] }}" data-price="{{ $product['price'] }}" data-image="{{ $product['image'] }}" data-brand="BrandX" data-stock="In stock" data-variation="Default" data-rating="4.5" class="mt-2 w-full border border-orange-300 text-orange-600 py-2 rounded-lg font-medium hover:bg-orange-50 transition-colors">
                    <i class="fa-solid fa-code-compare mr-2"></i> Add to Compare
                </button>
            </div>
        </div>
        @endif
        @endforeach
    </div>
</div>

<!-- Bottom Advertisement Banner -->
<div class="container mx-auto px-6 my-12">
    <div class="bg-white border-2 border-orange-500 p-4 rounded-md text-center">
        <img src="{{ asset('images/ad-example.png') }}" 
             alt="Bottom Advertisement" 
             class="mx-auto max-w-full h-auto"
             onerror="this.onerror=null; this.src='data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAzMDAgMTAwIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZjBmMGYwIi8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCIgZm9udC1zaXplPSIyNCIgZmlsbD0iIzY2NiIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9XCIuM2VtXCI+QWR2ZXJ0aXNlbWVudDwvdGV4dD48L3N2Zz4='">
    </div>
</div>

<!-- Newsletter & Ads Combo Section -->
<div class="bg-orange-50 py-12">
    <div class="container mx-auto px-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
            <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                <img src="{{ asset('image/iklan.jpg') }}" alt="Newsletter Ad" class="w-full h-64 object-cover">
            </div>
            <div class="text-center lg:text-left">
                <h3 class="text-2xl font-bold text-gray-800 mb-4">Berlangganan Newsletter</h3>
                <p class="text-gray-600 mb-6">Dapatkan info terbaru dan penawaran spesial langsung di inbox Anda!</p>
                <div class="flex flex-col sm:flex-row gap-4">
                    <input type="email" placeholder="Masukkan email Anda" class="flex-1 px-4 py-2 border border-orange-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                    <button class="bg-orange-500 text-white px-6 py-2 rounded-lg hover:bg-orange-600 transition-colors">Subscribe</button>
</div>
</div>

<!-- Product Tabs with Skeleton -->
<section class="container mx-auto px-6 py-12" x-data="{tab:'latest',loading:false, set(t){this.tab=t; this.loading=true; setTimeout(()=>this.loading=false, 600);} }">
  <div class="flex items-center justify-between mb-4">
    <h2 class="text-2xl font-bold text-gray-900">Best Picks</h2>
    <div class="flex gap-2 text-sm">
      <button @click="set('latest')" :class="tab==='latest' ? 'bg-accent-500 text-white' : 'border'" class="px-3 py-1.5 rounded-md">Latest</button>
      <button @click="set('popular')" :class="tab==='popular' ? 'bg-accent-500 text-white' : 'border'" class="px-3 py-1.5 rounded-md">Popular</button>
      <button @click="set('top')" :class="tab==='top' ? 'bg-accent-500 text-white' : 'border'" class="px-3 py-1.5 rounded-md">Top‑selling</button>
    </div>
  </div>
  <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
    <template x-if="loading">
      <template x-for="i in 8" :key="i">
        <div class="rounded-2xl border bg-white p-4 animate-pulse">
          <div class="aspect-square bg-neutral-200 rounded-lg"></div>
          <div class="mt-3 h-4 bg-neutral-200 rounded"></div>
          <div class="mt-2 h-4 bg-neutral-200 rounded w-1/2"></div>
        </div>
      </template>
    </template>
    <template x-if="!loading">
      <template x-for="i in 8" :key="i">
        <div>
          <x-product-card title="Sample Product" :price="199" :compare-at="249" rating="4.5" reviews="123" image="https://source.unsplash.com/600x600/?tech" badge="Hot" />
        </div>
      </template>
    </template>
  </div>
</section>

<!-- Value Props -->
<section class="container mx-auto px-6 py-10">
  <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
    @foreach([
      ['Free Shipping','fa-truck-fast'],
      ['Next-day Delivery','fa-bolt'],
      ['60‑day Returns','fa-rotate-left'],
      ['Expert CS','fa-headset'],
      ['Exclusive Brands','fa-gem'],
    ] as $vp)
      <div class="rounded-xl border bg-white p-5 text-center">
        <i class="fa-solid {{ $vp[1] }} text-accent-600 text-2xl"></i>
        <div class="mt-2 text-sm font-medium">{{ $vp[0] }}</div>
      </div>
    @endforeach
  </div>
</section>

<!-- Blog Teasers -->
<section class="container mx-auto px-6 py-10">
  <h2 class="text-2xl font-bold text-gray-900 mb-6">From Our Blog</h2>
  <div class="grid md:grid-cols-3 gap-6">
    @for($i=0;$i<3;$i++)
      <article class="rounded-2xl border bg-white overflow-hidden hover:shadow-soft transition">
        <img loading="lazy" src="https://source.unsplash.com/800x480/?electronics,{{ $i }}" alt="Blog {{ $i+1 }}" class="w-full h-40 object-cover"/>
        <div class="p-4">
          <h3 class="font-semibold text-gray-900">Artikel Tech {{ $i+1 }}</h3>
          <p class="text-sm text-gray-600 mt-1">Tips memilih komponen untuk performa optimal.</p>
          <a href="#" class="text-sm text-accent-600 hover:text-accent-700">Read more</a>
        </div>
      </article>
    @endfor
  </div>
</section>
        </div>
    </div>
</div>
<!-- Modern Extras: Features, Testimonials, Trending -->
<section class="py-14 bg-gradient-to-b from-white to-orange-50">
    <div class="container mx-auto px-6">
        <!-- Features -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
            <div class="bg-white rounded-xl shadow-md p-6 text-center">
                <i class="fa-solid fa-truck-fast text-3xl text-orange-500"></i>
                <h3 class="mt-3 font-semibold text-gray-900">Fast Shipping</h3>
                <p class="text-gray-600 text-sm">Same-day processing for in-stock items.</p>
            </div>
            <div class="bg-white rounded-xl shadow-md p-6 text-center">
                <i class="fa-solid fa-shield-halved text-3xl text-orange-500"></i>
                <h3 class="mt-3 font-semibold text-gray-900">Secure Warranty</h3>
                <p class="text-gray-600 text-sm">Official warranty and easy claims.</p>
            </div>
            <div class="bg-white rounded-xl shadow-md p-6 text-center">
                <i class="fa-solid fa-headset text-3xl text-orange-500"></i>
                <h3 class="mt-3 font-semibold text-gray-900">Pro Support</h3>
                <p class="text-gray-600 text-sm">Expert advice for your build.</p>
            </div>
        </div>

        <!-- Testimonials -->
        <div class="mb-12">
            <h2 class="text-2xl font-bold text-gray-900 mb-6 text-center">What Customers Say</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white rounded-xl shadow p-6">
                    <div class="flex items-center gap-3 mb-3">
                        <img class="w-10 h-10 rounded-full" src="https://i.pravatar.cc/100?img=12" alt=""/>
                        <div>
                            <div class="font-semibold text-gray-900">Ananda</div>
                            <div class="text-xs text-gray-500">Bangkok</div>
                        </div>
                    </div>
                    <p class="text-gray-700 text-sm">Pengiriman cepat dan customer service membantu sekali. Rekomendasi!</p>
                </div>
                <div class="bg-white rounded-xl shadow p-6">
                    <div class="flex items-center gap-3 mb-3">
                        <img class="w-10 h-10 rounded-full" src="https://i.pravatar.cc/100?img=32" alt=""/>
                        <div>
                            <div class="font-semibold text-gray-900">Budi</div>
                            <div class="text-xs text-gray-500">Chiang Mai</div>
                        </div>
                    </div>
                    <p class="text-gray-700 text-sm">Barang original, harga kompetitif, packing rapi. Mantap!</p>
                </div>
                <div class="bg-white rounded-xl shadow p-6">
                    <div class="flex items-center gap-3 mb-3">
                        <img class="w-10 h-10 rounded-full" src="https://i.pravatar.cc/100?img=25" alt=""/>
                        <div>
                            <div class="font-semibold text-gray-900">Somsak</div>
                            <div class="text-xs text-gray-500">Phuket</div>
                        </div>
                    </div>
                    <p class="text-gray-700 text-sm">Pilihan lengkap untuk rakit PC. Akan belanja lagi.</p>
                </div>
            </div>
        </div>

        <!-- Trending Categories -->
        <div>
            <h2 class="text-2xl font-bold text-gray-900 mb-6 text-center">Trending Categories</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach(['Graphics Card','CPU','SSD','Monitor'] as $cat)
                <div class="relative group overflow-hidden rounded-2xl shadow bg-white">
                    <img src="https://source.unsplash.com/600x400/?{{ urlencode($cat) }}" alt="{{ $cat }}" class="w-full h-36 object-cover group-hover:scale-105 transition-transform"/>
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                    <div class="absolute bottom-2 left-3 text-white font-semibold">{{ $cat }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    
</section>
@endsection
