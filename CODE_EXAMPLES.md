# Code Examples - Toko Thailand

## 📝 View Examples

### 1. Catalog Page with Filters

```blade
<!-- resources/views/pages/catalog.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6">{{ __('Product Catalog') }}</h1>
    
    <!-- Filters -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="md:col-span-1">
            <form method="GET" action="{{ route('catalog') }}" class="space-y-4">
                <!-- Category Filter -->
                <div>
                    <label class="block font-semibold mb-2">{{ __('Category') }}</label>
                    <select name="category" class="w-full border rounded px-3 py-2">
                        <option value="">{{ __('All Categories') }}</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Brand Filter -->
                <div>
                    <label class="block font-semibold mb-2">{{ __('Brand') }}</label>
                    <select name="brand" class="w-full border rounded px-3 py-2">
                        <option value="">{{ __('All Brands') }}</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand }}" {{ request('brand') == $brand ? 'selected' : '' }}>
                                {{ $brand }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Price Range -->
                <div>
                    <label class="block font-semibold mb-2">{{ __('Price Range') }}</label>
                    <div class="flex gap-2">
                        <input type="number" name="min_price" placeholder="Min" 
                               value="{{ request('min_price') }}" 
                               class="w-1/2 border rounded px-3 py-2">
                        <input type="number" name="max_price" placeholder="Max" 
                               value="{{ request('max_price') }}" 
                               class="w-1/2 border rounded px-3 py-2">
                    </div>
                </div>
                
                <!-- Search -->
                <div>
                    <label class="block font-semibold mb-2">{{ __('Search') }}</label>
                    <input type="text" name="search" placeholder="Search products..." 
                           value="{{ request('search') }}" 
                           class="w-full border rounded px-3 py-2">
                </div>
                
                <button type="submit" class="w-full bg-orange-500 text-white py-2 rounded hover:bg-orange-600">
                    {{ __('Apply Filters') }}
                </button>
                
                <a href="{{ route('catalog') }}" class="block text-center text-sm text-gray-600 hover:underline">
                    {{ __('Clear Filters') }}
                </a>
            </form>
        </div>
        
        <!-- Products Grid -->
        <div class="md:col-span-3">
            <!-- Sort -->
            <div class="flex justify-between items-center mb-4">
                <p class="text-gray-600">{{ $products->total() }} {{ __('products found') }}</p>
                <select name="sort" onchange="window.location.href='?sort='+this.value+'&{{ http_build_query(request()->except('sort')) }}'" 
                        class="border rounded px-3 py-2">
                    <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>{{ __('Newest') }}</option>
                    <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>{{ __('Price: Low to High') }}</option>
                    <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>{{ __('Price: High to Low') }}</option>
                    <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>{{ __('Name: A-Z') }}</option>
                </select>
            </div>
            
            <!-- Products -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($products as $product)
                    <div class="border rounded-lg overflow-hidden hover:shadow-lg transition">
                        <a href="{{ route('product.show', $product->slug) }}">
                            <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" class="w-full h-48 object-cover">
                        </a>
                        <div class="p-4">
                            <h3 class="font-semibold mb-2">
                                <a href="{{ route('product.show', $product->slug) }}" class="hover:text-orange-500">
                                    {{ $product->name }}
                                </a>
                            </h3>
                            <p class="text-sm text-gray-600 mb-2">{{ $product->brand }}</p>
                            <p class="text-lg font-bold text-orange-500 mb-3">{{ format_price($product->price) }}</p>
                            <div class="flex gap-2">
                                <button onclick="addToCart({{ $product->id }})" 
                                        class="flex-1 bg-orange-500 text-white py-2 rounded hover:bg-orange-600">
                                    {{ __('Add to Cart') }}
                                </button>
                                <button onclick="addToWishlist({{ $product->id }})" 
                                        class="px-3 border border-orange-500 text-orange-500 rounded hover:bg-orange-50">
                                    <i class="fa fa-heart"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 text-center py-12">
                        <p class="text-gray-500">{{ __('No products found') }}</p>
                    </div>
                @endforelse
            </div>
            
            <!-- Pagination -->
            <div class="mt-8">
                {{ $products->links() }}
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
async function addToCart(productId) {
    try {
        const response = await fetch('/cart/add', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ product_id: productId, quantity: 1 })
        });
        
        const data = await response.json();
        if (data.success) {
            alert(data.message);
            // Update cart count in navbar
        }
    } catch (error) {
        console.error('Error:', error);
    }
}

async function addToWishlist(productId) {
    try {
        const response = await fetch('/wishlist/add', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ product_id: productId })
        });
        
        const data = await response.json();
        if (data.success) {
            alert(data.message);
        }
    } catch (error) {
        console.error('Error:', error);
    }
}
</script>
@endpush
@endsection
```

### 2. Product Detail Page with Schema.org

```blade
<!-- resources/views/pages/product.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Product Image -->
        <div>
            <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" class="w-full rounded-lg">
        </div>
        
        <!-- Product Info -->
        <div>
            <h1 class="text-3xl font-bold mb-2">{{ $product->name }}</h1>
            <p class="text-gray-600 mb-4">{{ $product->brand }}</p>
            <p class="text-3xl font-bold text-orange-500 mb-4">{{ format_price($product->price) }}</p>
            
            <div class="mb-6">
                <h3 class="font-semibold mb-2">{{ __('Description') }}</h3>
                <p class="text-gray-700">{{ $product->description }}</p>
            </div>
            
            <div class="mb-6">
                <p class="text-sm">
                    <span class="font-semibold">{{ __('Stock') }}:</span>
                    @if($product->stock > 0)
                        <span class="text-green-600">{{ $product->stock }} {{ __('available') }}</span>
                    @else
                        <span class="text-red-600">{{ __('Out of stock') }}</span>
                    @endif
                </p>
            </div>
            
            @if($product->stock > 0)
                <div class="flex gap-4 mb-6">
                    <input type="number" id="quantity" value="1" min="1" max="{{ $product->stock }}" 
                           class="w-20 border rounded px-3 py-2">
                    <button onclick="addToCart({{ $product->id }})" 
                            class="flex-1 bg-orange-500 text-white py-3 rounded-lg hover:bg-orange-600">
                        {{ __('Add to Cart') }}
                    </button>
                    <button onclick="addToWishlist({{ $product->id }})" 
                            class="px-6 border-2 border-orange-500 text-orange-500 rounded-lg hover:bg-orange-50">
                        <i class="fa fa-heart"></i>
                    </button>
                </div>
            @endif
        </div>
    </div>
    
    <!-- Related Products -->
    @if($relatedProducts->count() > 0)
        <div class="mt-12">
            <h2 class="text-2xl font-bold mb-6">{{ __('Related Products') }}</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($relatedProducts as $related)
                    <div class="border rounded-lg overflow-hidden hover:shadow-lg transition">
                        <a href="{{ route('product.show', $related->slug) }}">
                            <img src="{{ asset($related->image) }}" alt="{{ $related->name }}" class="w-full h-48 object-cover">
                        </a>
                        <div class="p-4">
                            <h3 class="font-semibold mb-2">
                                <a href="{{ route('product.show', $related->slug) }}">{{ $related->name }}</a>
                            </h3>
                            <p class="text-lg font-bold text-orange-500">{{ format_price($related->price) }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

<!-- Schema.org Markup -->
<script type="application/ld+json">
{
  "@context": "https://schema.org/",
  "@type": "Product",
  "name": "{{ $product->name }}",
  "image": "{{ asset($product->image) }}",
  "description": "{{ $product->description }}",
  "brand": {
    "@type": "Brand",
    "name": "{{ $product->brand }}"
  },
  "offers": {
    "@type": "Offer",
    "url": "{{ route('product.show', $product->slug) }}",
    "priceCurrency": "THB",
    "price": "{{ $product->price }}",
    "availability": "{{ $product->stock > 0 ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock' }}"
  }
}
</script>

@push('scripts')
<script>
async function addToCart(productId) {
    const quantity = document.getElementById('quantity').value;
    
    try {
        const response = await fetch('/cart/add', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ product_id: productId, quantity: parseInt(quantity) })
        });
        
        const data = await response.json();
        if (data.success) {
            alert(data.message);
        }
    } catch (error) {
        console.error('Error:', error);
    }
}

async function addToWishlist(productId) {
    try {
        const response = await fetch('/wishlist/add', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ product_id: productId })
        });
        
        const data = await response.json();
        alert(data.message);
    } catch (error) {
        console.error('Error:', error);
    }
}
</script>
@endpush
@endsection
```

### 3. Checkout Page

```blade
<!-- resources/views/pages/checkout.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6">{{ __('Checkout') }}</h1>
    
    <form method="POST" action="{{ route('checkout.process') }}">
        @csrf
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Shipping Address -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold mb-4">{{ __('Shipping Address') }}</h2>
                    
                    @if($shippingAddresses->count() > 0)
                        <div class="space-y-3">
                            @foreach($shippingAddresses as $address)
                                <label class="flex items-start p-4 border rounded cursor-pointer hover:bg-gray-50">
                                    <input type="radio" name="shipping_address_id" value="{{ $address->id }}" 
                                           {{ $address->is_default ? 'checked' : '' }} 
                                           class="mt-1 mr-3" required>
                                    <div>
                                        <p class="font-semibold">{{ $address->name }}</p>
                                        <p class="text-sm text-gray-600">{{ $address->phone }}</p>
                                        <p class="text-sm text-gray-600">
                                            {{ $address->address_line1 }}
                                            @if($address->address_line2), {{ $address->address_line2 }}@endif
                                        </p>
                                        <p class="text-sm text-gray-600">
                                            {{ $address->city }}, {{ $address->state }} {{ $address->postal_code }}
                                        </p>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-600">{{ __('No shipping address found. Please add one.') }}</p>
                        <a href="#" class="text-orange-500 hover:underline">{{ __('Add Address') }}</a>
                    @endif
                </div>
                
                <!-- Payment Method -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold mb-4">{{ __('Payment Method') }}</h2>
                    
                    <div class="space-y-3">
                        <label class="flex items-center p-4 border rounded cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="payment_method" value="bank_transfer" checked class="mr-3" required>
                            <div>
                                <p class="font-semibold">{{ __('Bank Transfer') }}</p>
                                <p class="text-sm text-gray-600">{{ __('Transfer to our bank account') }}</p>
                            </div>
                        </label>
                        
                        <label class="flex items-center p-4 border rounded cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="payment_method" value="credit_card" class="mr-3" required>
                            <div>
                                <p class="font-semibold">{{ __('Credit Card') }}</p>
                                <p class="text-sm text-gray-600">{{ __('Pay with credit/debit card') }}</p>
                            </div>
                        </label>
                    </div>
                </div>
            </div>
            
            <!-- Right Column - Order Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow p-6 sticky top-4">
                    <h2 class="text-xl font-semibold mb-4">{{ __('Order Summary') }}</h2>
                    
                    <!-- Cart Items -->
                    <div class="space-y-3 mb-4">
                        @foreach($cartItems as $item)
                            <div class="flex justify-between text-sm">
                                <span>{{ $item->product->name }} x{{ $item->quantity }}</span>
                                <span>{{ format_price($item->subtotal) }}</span>
                            </div>
                        @endforeach
                    </div>
                    
                    <hr class="my-4">
                    
                    <!-- Coupon -->
                    <div class="mb-4">
                        <div class="flex gap-2">
                            <input type="text" name="coupon_code" id="coupon_code" 
                                   placeholder="{{ __('Coupon code') }}" 
                                   class="flex-1 border rounded px-3 py-2 text-sm">
                            <button type="button" onclick="applyCoupon()" 
                                    class="px-4 py-2 bg-gray-200 rounded text-sm hover:bg-gray-300">
                                {{ __('Apply') }}
                            </button>
                        </div>
                        <p id="coupon-message" class="text-sm mt-2"></p>
                    </div>
                    
                    <hr class="my-4">
                    
                    <!-- Totals -->
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span>{{ __('Subtotal') }}</span>
                            <span id="subtotal">{{ format_price($subtotal) }}</span>
                        </div>
                        <div class="flex justify-between" id="discount-row" style="display: none;">
                            <span>{{ __('Discount') }}</span>
                            <span id="discount" class="text-green-600">-{{ format_price(0) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>{{ __('Shipping') }}</span>
                            <span>{{ format_price(50) }}</span>
                        </div>
                        <hr>
                        <div class="flex justify-between font-bold text-lg">
                            <span>{{ __('Total') }}</span>
                            <span id="total">{{ format_price($subtotal + 50) }}</span>
                        </div>
                    </div>
                    
                    <button type="submit" class="w-full mt-6 bg-orange-500 text-white py-3 rounded-lg hover:bg-orange-600">
                        {{ __('Place Order') }}
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
async function applyCoupon() {
    const code = document.getElementById('coupon_code').value;
    const messageEl = document.getElementById('coupon-message');
    
    if (!code) {
        messageEl.textContent = '{{ __("Please enter a coupon code") }}';
        messageEl.className = 'text-sm mt-2 text-red-600';
        return;
    }
    
    try {
        const response = await fetch('/checkout/apply-coupon', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ coupon_code: code })
        });
        
        const data = await response.json();
        
        if (data.success) {
            messageEl.textContent = data.message;
            messageEl.className = 'text-sm mt-2 text-green-600';
            
            // Update discount display
            document.getElementById('discount-row').style.display = 'flex';
            document.getElementById('discount').textContent = '-' + formatPrice(data.discount_amount);
            
            // Update total
            const subtotal = {{ $subtotal }};
            const shipping = 50;
            const total = subtotal - data.discount_amount + shipping;
            document.getElementById('total').textContent = formatPrice(total);
        } else {
            messageEl.textContent = data.message;
            messageEl.className = 'text-sm mt-2 text-red-600';
        }
    } catch (error) {
        console.error('Error:', error);
        messageEl.textContent = '{{ __("An error occurred") }}';
        messageEl.className = 'text-sm mt-2 text-red-600';
    }
}

function formatPrice(amount) {
    return '฿' + amount.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
}
</script>
@endpush
@endsection
```

### 4. Bank Transfer Payment Page

```blade
<!-- resources/views/pages/payment/bank-transfer.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-2xl">
    <h1 class="text-3xl font-bold mb-6">{{ __('Bank Transfer Payment') }}</h1>
    
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-xl font-semibold mb-4">{{ __('Order Information') }}</h2>
        <div class="space-y-2">
            <p><span class="font-semibold">{{ __('Order ID') }}:</span> #{{ $order->id }}</p>
            <p><span class="font-semibold">{{ __('Total Amount') }}:</span> 
               <span class="text-2xl text-orange-500 font-bold">{{ format_price($order->total_amount) }}</span>
            </p>
        </div>
    </div>
    
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
        <h2 class="text-xl font-semibold mb-4">{{ __('Bank Account Details') }}</h2>
        <div class="space-y-3">
            <div>
                <p class="text-sm text-gray-600">{{ __('Bank Name') }}</p>
                <p class="font-semibold">Bangkok Bank</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">{{ __('Account Number') }}</p>
                <p class="font-semibold text-lg">123-4-56789-0</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">{{ __('Account Name') }}</p>
                <p class="font-semibold">Toko Thailand Co., Ltd.</p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-semibold mb-4">{{ __('Upload Payment Proof') }}</h2>
        
        <form method="POST" action="{{ route('payment.upload-proof', $order->id) }}" enctype="multipart/form-data">
            @csrf
            
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">{{ __('Payment Proof (Image)') }}</label>
                <input type="file" name="payment_proof" accept="image/*" required
                       class="w-full border rounded px-3 py-2">
                <p class="text-sm text-gray-600 mt-1">{{ __('Accepted formats: JPG, PNG. Max size: 2MB') }}</p>
            </div>
            
            <button type="submit" class="w-full bg-orange-500 text-white py-3 rounded-lg hover:bg-orange-600">
                {{ __('Upload Proof') }}
            </button>
        </form>
    </div>
    
    <div class="mt-6 text-center">
        <a href="{{ route('orders.show', $order->id) }}" class="text-orange-500 hover:underline">
            {{ __('View Order Details') }}
        </a>
    </div>
</div>
@endsection
```

## 🔧 Controller Examples

### Sending Order Confirmation Email

```php
// In CheckoutController::process() method, after DB::commit()

use App\Mail\OrderConfirmationMail;
use Illuminate\Support\Facades\Mail;

// Send order confirmation email
try {
    Mail::to(Auth::user()->email)->send(new OrderConfirmationMail($order));
} catch (\Exception $e) {
    \Log::error('Failed to send order confirmation email: ' . $e->getMessage());
}
```

### Sending Order Status Update Email

```php
// In Admin\OrderController::updateStatus() method

use App\Mail\OrderStatusUpdateMail;
use Illuminate\Support\Facades\Mail;

public function updateStatus(Request $request, $id)
{
    $request->validate([
        'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
    ]);
    
    $order = Order::findOrFail($id);
    $oldStatus = $order->status;
    $order->status = $request->status;
    $order->save();
    
    // Send email notification
    try {
        Mail::to($order->user->email)->send(
            new OrderStatusUpdateMail($order, $request->status)
        );
    } catch (\Exception $e) {
        \Log::error('Failed to send status update email: ' . $e->getMessage());
    }
    
    return back()->with('success', __('Order status updated successfully'));
}
```

## 📧 Queue Email Example

```php
// Instead of send(), use queue()
Mail::to($user->email)->queue(new OrderConfirmationMail($order));

// Or with delay
Mail::to($user->email)->later(now()->addMinutes(5), new OrderConfirmationMail($order));
```

## 🧪 Test Examples

### Testing Cart Functionality

```php
public function test_user_can_add_multiple_products_to_cart(): void
{
    $user = User::factory()->create();
    $product1 = Product::factory()->create(['price' => 100]);
    $product2 = Product::factory()->create(['price' => 200]);

    $this->actingAs($user)
        ->postJson('/cart/add', ['product_id' => $product1->id, 'quantity' => 2])
        ->assertStatus(200);

    $this->actingAs($user)
        ->postJson('/cart/add', ['product_id' => $product2->id, 'quantity' => 1])
        ->assertStatus(200);

    $this->assertDatabaseCount('carts', 2);
    
    $cartTotal = Cart::where('user_id', $user->id)->get()->sum('subtotal');
    $this->assertEquals(400, $cartTotal); // (100*2) + (200*1)
}
```

## 🎨 JavaScript Examples

### Complete Cart Management

```javascript
// resources/js/cart.js

class CartManager {
    constructor() {
        this.csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    }
    
    async add(productId, quantity = 1) {
        try {
            const response = await fetch('/cart/add', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken
                },
                body: JSON.stringify({ product_id: productId, quantity })
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.showNotification(data.message, 'success');
                this.updateCartCount();
            } else {
                this.showNotification(data.message, 'error');
            }
            
            return data;
        } catch (error) {
            console.error('Cart error:', error);
            this.showNotification('An error occurred', 'error');
        }
    }
    
    async update(cartId, quantity) {
        try {
            const response = await fetch(`/cart/${cartId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken
                },
                body: JSON.stringify({ quantity })
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.showNotification(data.message, 'success');
                this.updateCartDisplay();
            }
            
            return data;
        } catch (error) {
            console.error('Cart error:', error);
        }
    }
    
    async remove(cartId) {
        if (!confirm('Remove this item from cart?')) return;
        
        try {
            const response = await fetch(`/cart/${cartId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': this.csrfToken
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.showNotification(data.message, 'success');
                this.updateCartDisplay();
            }
            
            return data;
        } catch (error) {
            console.error('Cart error:', error);
        }
    }
    
    updateCartCount() {
        // Update cart count badge in navbar
        const badge = document.querySelector('#cart-count');
        if (badge) {
            const currentCount = parseInt(badge.textContent) || 0;
            badge.textContent = currentCount + 1;
        }
    }
    
    updateCartDisplay() {
        // Reload cart page or update cart drawer
        window.location.reload();
    }
    
    showNotification(message, type = 'info') {
        // Simple alert for now, can be replaced with toast notification
        alert(message);
    }
}

// Initialize
const cart = new CartManager();

// Make it globally available
window.cart = cart;
```

## 🔐 Middleware Example

### Custom Rate Limiter

```php
// In routes/web.php

use Illuminate\Support\Facades\RateLimiter;

RateLimiter::for('contact', function (Request $request) {
    return Limit::perHour(3)->by($request->ip());
});

// Then use in route
Route::post('/contact', [ContactController::class, 'send'])
    ->middleware('throttle:contact');
```

---

These examples should help you complete the implementation. Adjust styling and functionality as needed for your specific requirements.
