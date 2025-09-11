@extends('layouts.app')

@section('content')
<main id="main" class="container py-8 md:py-10" role="main">
  <h1 class="text-2xl font-semibold mb-6">Keranjang</h1>
  <div class="grid lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-4">
      @for($i=0;$i<2;$i++)
      <div class="flex gap-4 border rounded-lg p-4 bg-white dark:bg-neutral-900">
        <img src="https://source.unsplash.com/100x100/?product,{{ $i }}" alt="Produk {{ $i+1 }}" class="w-24 h-24 object-cover rounded-md"/>
        <div class="flex-1">
          <div class="font-medium">Produk {{ $i+1 }}</div>
          <div class="text-sm text-neutral-500">Varian: Default</div>
          <div class="mt-3 flex items-center gap-3">
            <input type="number" min="1" value="1" class="w-16 rounded-md border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-sm"/>
            <button class="text-red-600 text-sm">Hapus</button>
          </div>
        </div>
        <div class="font-semibold whitespace-nowrap">฿ {{ number_format(rand(59,299),2) }}</div>
      </div>
      @endfor
    </div>
    <aside class="space-y-3">
      <div class="border rounded-lg p-4 bg-white dark:bg-neutral-900">
        <div class="font-semibold mb-2">Ringkasan</div>
        <div class="text-sm flex justify-between"><span>Subtotal</span><span>฿ 1,099.00</span></div>
        <div class="text-sm flex justify-between"><span>Pengiriman</span><span>฿ 0</span></div>
        <div class="text-sm flex justify-between"><span>Pajak</span><span>—</span></div>
        <div class="mt-2 border-t pt-2 font-semibold flex justify-between"><span>Total</span><span>฿ 1,099.00</span></div>
        <x-button href="{{ url('/checkout') }}" class="w-full mt-4">Checkout</x-button>
      </div>
    </aside>
  </div>
</main>
@endsection

