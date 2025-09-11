@extends('layouts.app')

@section('content')
<main id="main" class="container py-8 md:py-10" role="main">
  <article itemscope itemtype="https://schema.org/Product">
    <meta itemprop="name" content="RTX 4070 Ti" />

    <div class="grid md:grid-cols-2 gap-8">
      <!-- Gallery -->
      <div class="space-y-3">
        <div class="relative overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-800">
          <img loading="lazy" src="https://images.unsplash.com/photo-1587202372616-b0434d16fc5f?q=80&w=1200&auto=format&fit=crop" alt="RTX 4070 Ti" class="w-full h-auto"/>
          <div class="absolute inset-0 pointer-events-none hidden md:block" style="background: radial-gradient(transparent, transparent 60%, rgba(0,0,0,.08));"></div>
        </div>
        <div class="grid grid-cols-4 gap-3">
          @foreach([1,2,3,4] as $i)
            <img loading="lazy" src="https://source.unsplash.com/300x300/?gpu,{{ $i }}" alt="RTX 4070 Ti Thumbnail {{ $i }}" class="w-full h-auto rounded-lg border border-transparent hover:border-accent-500 cursor-pointer"/>
          @endforeach
        </div>
      </div>

      <!-- Info -->
      <div>
        <h1 class="text-2xl md:text-3xl font-semibold">RTX 4070 Ti</h1>
        <div class="mt-2 flex items-center gap-2 text-amber-500">
          <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-regular fa-star"></i>
          <a href="#reviews" class="text-xs text-neutral-500">(128 ulasan)</a>
        </div>
        <div class="mt-4 flex items-center gap-3">
          <div class="text-2xl font-semibold">฿ 42,999.00</div>
          <div class="text-sm text-neutral-500 line-through">฿ 45,999.00</div>
          <x-badge variant="success">-7%</x-badge>
        </div>

        <div class="mt-4 text-sm text-neutral-600 dark:text-neutral-300">Stok: <span class="text-green-600">Tersedia</span></div>

        <!-- Variasi -->
        <div class="mt-6">
          <div class="text-sm font-medium">Variasi</div>
          <div class="mt-2 flex gap-2">
            @foreach(['ASUS','MSI','Gigabyte'] as $v)
              <label class="px-3 py-1.5 rounded-md border cursor-pointer hover:border-accent-500">
                <input type="radio" name="variant" value="{{ $v }}" class="sr-only"/> {{ $v }}
              </label>
            @endforeach
          </div>
        </div>

        <div class="mt-6 flex gap-3">
          <x-button class="flex-1" aria-label="Tambah ke Keranjang">Tambah ke Keranjang</x-button>
          <x-button variant="outline" aria-label="Tambah ke Wishlist"><i class="fa-regular fa-heart mr-2"></i>Wishlist</x-button>
        </div>

        <!-- Trust badges -->
        <div class="mt-6 grid grid-cols-3 gap-3 text-center text-xs">
          <div class="rounded-md border p-3"><i class="fa-solid fa-truck-fast text-accent-600"></i><div>Pengiriman Cepat</div></div>
          <div class="rounded-md border p-3"><i class="fa-solid fa-shield-halved text-accent-600"></i><div>Garansi Resmi</div></div>
          <div class="rounded-md border p-3"><i class="fa-solid fa-rotate-left text-accent-600"></i><div>Retur Mudah</div></div>
        </div>
      </div>
    </div>

    <!-- Tabs: Deskripsi, Spesifikasi, Ulasan -->
    <section class="mt-12">
      <div class="border-b flex gap-6 text-sm">
        <a href="#desc" class="py-2 border-b-2 border-accent-500">Deskripsi</a>
        <a href="#spec" class="py-2">Spesifikasi</a>
        <a href="#reviews" class="py-2">Ulasan</a>
      </div>
      <div id="desc" class="prose dark:prose-invert max-w-none mt-6">
        <p>GPU kelas atas dengan performa luar biasa untuk gaming 4K dan kreator.</p>
      </div>
      <div id="spec" class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
        <div class="flex justify-between border-b py-2"><span>CUDA Cores</span><span>7680</span></div>
        <div class="flex justify-between border-b py-2"><span>VRAM</span><span>12GB GDDR6X</span></div>
        <div class="flex justify-between border-b py-2"><span>TDP</span><span>285W</span></div>
        <div class="flex justify-between border-b py-2"><span>Output</span><span>HDMI 2.1, 3x DP 1.4a</span></div>
      </div>
      <div id="reviews" class="mt-10 space-y-4">
        @for($i=0;$i<3;$i++)
        <div class="border rounded-md p-4">
          <div class="flex items-center gap-2 text-amber-500 text-sm"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-regular fa-star"></i></div>
          <div class="mt-1 text-sm text-neutral-700 dark:text-neutral-300">Ulasan yang sangat membantu! Kualitas mantap.</div>
        </div>
        @endfor
      </div>
    </section>

    <!-- Related products -->
    <section class="mt-12">
      <h2 class="text-xl font-semibold mb-4">Produk Terkait</h2>
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @for($i=0;$i<4;$i++)
          <x-product-card title="Produk {{ $i+1 }}" :price="rand(49,899)" :rating="rand(35,50)/10" :reviews="rand(5,300)" image="https://source.unsplash.com/600x600/?pc,{{ $i }}" />
        @endfor
      </div>
    </section>
  </article>
</main>
@endsection

