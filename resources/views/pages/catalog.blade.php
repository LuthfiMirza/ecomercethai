@extends('layouts.app')

@section('content')
<main id="main" class="container py-8 md:py-10" role="main">
  <h1 class="text-2xl font-semibold mb-4">Katalog</h1>
  <form method="get" class="grid grid-cols-1 lg:grid-cols-12 gap-6">
    <!-- Sidebar filter -->
    <div class="lg:col-span-3">
      <div class="hidden lg:block sticky top-24">
        <x-filter :categories="['CPU','GPU','RAM','SSD','Monitor','PSU','Casing']" :brands="['ASUS','MSI','Gigabyte','Corsair','Kingston']" />
      </div>
      <!-- Mobile filter button -->
      <div x-data="{open:false}" class="lg:hidden">
        <x-button @click.prevent="open=true" class="w-full">Filter</x-button>
        <div x-show="open" x-transition.opacity class="fixed inset-0 z-50">
          <div class="absolute inset-0 bg-black/40" @click="open=false"></div>
          <div class="absolute right-0 top-0 h-full w-[90vw] max-w-sm bg-white dark:bg-neutral-900 p-4 overflow-y-auto">
            <div class="flex items-center justify-between mb-2">
              <div class="font-medium">Filter</div>
              <button class="p-2" @click="open=false"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <x-filter :categories="['CPU','GPU','RAM','SSD','Monitor','PSU','Casing']" :brands="['ASUS','MSI','Gigabyte','Corsair','Kingston']" />
          </div>
        </div>
      </div>
    </div>

    <!-- Content -->
    <div class="lg:col-span-9">
      <div class="flex items-center justify-between mb-4">
        <x-sort />
        <div class="text-xs text-neutral-500">124 hasil</div>
      </div>
      <!-- Grid -->
      <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-3 gap-4">
        @for($i=0;$i<9;$i++)
          <x-product-card title="Produk {{ $i+1 }}" :price="rand(49,899)" :compare-at="rand(50,999)" :rating="rand(35,50)/10" :reviews="rand(5,300)" image="https://source.unsplash.com/600x600/?tech,{{ $i }}" />
        @endfor
      </div>

      <!-- Pagination demo (server-side paginator expected) -->
      <div class="mt-6">
        {{-- Example placeholder: replace with real $products paginator --}}
        {{-- <x-pagination :paginator="$products" /> --}}
      </div>
    </div>
  </form>
</main>
@endsection

