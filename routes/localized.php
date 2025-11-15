<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ChatController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\OrderController as FrontOrderController;
use App\Http\Controllers\Admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\Admin\PaymentProfileController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\PromoController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\MegaMenuController;
use App\Http\Controllers\OrderTrackingController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\SocialLoginController;
use App\Http\Controllers\ChatMessageController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $featuredProducts = \App\Models\Product::with('category')
        ->where('is_active', true)
        ->orderByDesc('created_at')
        ->take(3)
        ->get();

    $catalogProducts = \App\Models\Product::with('category')
        ->where('is_active', true)
        ->orderByDesc('created_at')
        ->take(12)
        ->get();

    $banners = \App\Models\Banner::active()->get();

    return view('home', [
        'featuredProducts' => $featuredProducts,
        'catalogProducts' => $catalogProducts,
        'banners' => $banners,
    ]);
})->name('home');

Route::get('/faqs', function () {
    $faqs = collect(trans('faq.groups', []))
        ->map(function ($group) {
            return [
                'category' => $group['category'] ?? '',
                'items' => collect($group['items'] ?? [])->map(function ($item) {
                    return [
                        'question' => $item['question'] ?? '',
                        'answer' => $item['answer'] ?? '',
                    ];
                })->all(),
            ];
        })
        ->all();

    return view('pages.faqs', compact('faqs'));
})->name('faqs');

Route::get('/mega-menu-preview', MegaMenuController::class)->name('mega-menu.preview');

// Public order tracking by secret token (no auth)
Route::get('/t/{token}', [OrderTrackingController::class, 'show'])->name('orders.track');
Route::get('/t/{token}/status', [OrderTrackingController::class, 'status'])->name('orders.track.status');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->middleware('throttle:5,1');

    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);

    Route::get('/auth/google/redirect', [SocialLoginController::class, 'redirect'])->name('social.google.redirect');
    Route::get('/auth/google/callback', [SocialLoginController::class, 'callback'])->name('social.google.callback');
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::get('/account', AccountController::class)->name('account');

// Catalog & Products
Route::get('/catalog', [\App\Http\Controllers\CatalogController::class, 'index'])->name('catalog');
Route::get('/product/{slug}', [\App\Http\Controllers\CatalogController::class, 'show'])->name('product.show');

// Cart
Route::get('/cart', [\App\Http\Controllers\CartController::class, 'index'])->name('cart');
Route::get('/cart/summary', [\App\Http\Controllers\CartController::class, 'summary'])->name('cart.summary');
Route::post('/cart/add', [\App\Http\Controllers\CartController::class, 'add'])->name('cart.add');
Route::put('/cart/{id}', [\App\Http\Controllers\CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/{id}', [\App\Http\Controllers\CartController::class, 'remove'])->name('cart.remove');
Route::delete('/cart', [\App\Http\Controllers\CartController::class, 'clear'])->name('cart.clear');

// Wishlist
Route::middleware('auth')->group(function () {
    Route::get('/wishlist', [\App\Http\Controllers\WishlistController::class, 'index'])->name('wishlist');
    Route::post('/wishlist/add', [\App\Http\Controllers\WishlistController::class, 'add'])->name('wishlist.add');
    Route::delete('/wishlist/{id}', [\App\Http\Controllers\WishlistController::class, 'remove'])->name('wishlist.remove');
    Route::delete('/wishlist', [\App\Http\Controllers\WishlistController::class, 'clear'])->name('wishlist.clear');
    Route::get('/media/payment-proof/{orderId}', [\App\Http\Controllers\MediaController::class, 'paymentProof'])
        ->whereNumber('orderId')
        ->name('media.payment-proof');
});

// Checkout
Route::middleware('auth')->group(function () {
    Route::get('/checkout', [\App\Http\Controllers\CheckoutController::class, 'index'])->name('checkout');
    Route::post('/checkout/process', [\App\Http\Controllers\CheckoutController::class, 'process'])->name('checkout.process');
    Route::post('/checkout/address', [\App\Http\Controllers\CheckoutController::class, 'storeAddress'])->name('checkout.address.store');
    Route::delete('/checkout/address/{address}', [\App\Http\Controllers\CheckoutController::class, 'destroyAddress'])->name('checkout.address.destroy');
    Route::post('/checkout/apply-coupon', [\App\Http\Controllers\CheckoutController::class, 'applyCoupon'])->name('checkout.apply-coupon');
    Route::get('/checkout/success/{order}', [\App\Http\Controllers\CheckoutController::class, 'success'])->name('checkout.success');
});

// Payment
Route::middleware('auth')->group(function () {
    Route::get('/payment/bank-transfer/{order}', [\App\Http\Controllers\PaymentController::class, 'bankTransfer'])->name('payment.bank-transfer');
    Route::post('/payment/bank-transfer/{order}/upload', [\App\Http\Controllers\PaymentController::class, 'uploadProof'])->name('payment.upload-proof');
});

// Orders
Route::middleware('auth')->group(function () {
    Route::get('/orders/{order}', [FrontOrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{order}/status', [FrontOrderController::class, 'status'])->name('orders.status');
});

Route::middleware('auth')->group(function () {
    Route::get('/chat/messages', [ChatMessageController::class, 'index'])->name('chat.messages.index');
    Route::post('/chat/messages', [ChatMessageController::class, 'store'])->name('chat.messages.store');
});

// Contact
Route::get('/contact', [\App\Http\Controllers\ContactController::class, 'index'])->name('contact');
Route::post('/contact', [\App\Http\Controllers\ContactController::class, 'send'])->name('contact.send');

Route::redirect('/dashboard', '/admin/dashboard')
    ->middleware('auth')
    ->name('dashboard');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminController::class, 'login'])->name('login.post');
    Route::post('/logout', [AdminController::class, 'logout'])->name('logout');

    Route::middleware(['auth', 'admin'])->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/dashboard/metrics', [AdminController::class, 'metrics'])->name('dashboard.metrics');

        Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

        Route::get('/products', [ProductController::class, 'index'])->name('products.index');
        Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
        Route::post('/products', [ProductController::class, 'store'])->name('products.store');
        Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');
        Route::get('/products/{id}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::put('/products/{id}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('products.destroy');
        Route::get('/products-export/csv', [ProductController::class, 'exportCsv'])->name('products.export.csv');
        Route::get('/products-export/excel', [ProductController::class, 'exportExcel'])->name('products.export.excel');
        Route::get('/products-export/pdf', [ProductController::class, 'exportPdf'])->name('products.export.pdf');
        Route::post('/products-import', [ProductController::class, 'import'])->name('products.import');

        Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
        Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
        Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::get('/categories/{id}', [CategoryController::class, 'show'])->name('categories.show');
        Route::get('/categories/{id}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
        Route::put('/categories/{id}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy');

        Route::get('/banners', [BannerController::class, 'index'])->name('banners.index');
        Route::get('/banners/create', [BannerController::class, 'create'])->name('banners.create');
        Route::post('/banners', [BannerController::class, 'store'])->name('banners.store');
        Route::get('/banners/{id}', [BannerController::class, 'show'])->name('banners.show');
        Route::get('/banners/{id}/edit', [BannerController::class, 'edit'])->name('banners.edit');
        Route::put('/banners/{id}', [BannerController::class, 'update'])->name('banners.update');
        Route::delete('/banners/{id}', [BannerController::class, 'destroy'])->name('banners.destroy');

        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/poll', [OrderController::class, 'poll'])->name('orders.poll');
        Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
        Route::get('/orders/{id}/edit', [OrderController::class, 'edit'])->name('orders.edit');
        Route::put('/orders/{id}', [OrderController::class, 'update'])->name('orders.update');
        Route::patch('/orders/{id}/status', [OrderController::class, 'updateStatus'])->name('orders.update_status');
        Route::patch('/orders/{id}/payment-status', [OrderController::class, 'updatePaymentStatus'])->name('orders.update_payment');
        Route::post('/orders/{id}/apply-coupon', [OrderController::class, 'applyCoupon'])->name('orders.apply_coupon');
        Route::get('/orders-export/csv', [OrderController::class, 'exportCsv'])->name('orders.export.csv');
        Route::get('/orders-export/excel', [OrderController::class, 'exportExcel'])->name('orders.export.excel');
        Route::get('/orders-export/pdf', [OrderController::class, 'exportPdf'])->name('orders.export.pdf');
        Route::post('/orders-import', [OrderController::class, 'import'])->name('orders.import');
        Route::get('/orders/{id}/invoice', [OrderController::class, 'invoice'])->name('orders.invoice');
        Route::get('/orders/{id}/invoice/pdf', [OrderController::class, 'invoicePdf'])->name('orders.invoice.pdf');

        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{id}', [UserController::class, 'show'])->name('users.show');
        Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::post('/users/{id}/ban', [UserController::class, 'ban'])->name('users.ban');
        Route::post('/users/{id}/activate', [UserController::class, 'activate'])->name('users.activate');

        Route::get('/promos', [PromoController::class, 'index'])->name('promos.index');
        Route::get('/promos/create', [PromoController::class, 'create'])->name('promos.create');
        Route::post('/promos', [PromoController::class, 'store'])->name('promos.store');
        Route::get('/promos/{id}', [PromoController::class, 'show'])->name('promos.show');
        Route::get('/promos/{id}/edit', [PromoController::class, 'edit'])->name('promos.edit');
        Route::put('/promos/{id}', [PromoController::class, 'update'])->name('promos.update');
        Route::delete('/promos/{id}', [PromoController::class, 'destroy'])->name('promos.destroy');

        Route::get('/payments', [AdminPaymentController::class, 'index'])->name('payments.index');
        Route::get('/payments/{id}', [AdminPaymentController::class, 'show'])->name('payments.show');
        Route::delete('/payments/{id}', [AdminPaymentController::class, 'destroy'])->name('payments.destroy');

        Route::get('/payment-profiles', [PaymentProfileController::class, 'index'])->name('payment-profiles.index');
        Route::get('/payment-profiles/create', [PaymentProfileController::class, 'create'])->name('payment-profiles.create');
        Route::post('/payment-profiles', [PaymentProfileController::class, 'store'])->name('payment-profiles.store');
        Route::get('/payment-profiles/{id}', [PaymentProfileController::class, 'show'])->name('payment-profiles.show');
        Route::get('/payment-profiles/{id}/edit', [PaymentProfileController::class, 'edit'])->name('payment-profiles.edit');
        Route::put('/payment-profiles/{id}', [PaymentProfileController::class, 'update'])->name('payment-profiles.update');
        Route::delete('/payment-profiles/{id}', [PaymentProfileController::class, 'destroy'])->name('payment-profiles.destroy');

        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/export/pdf', [ReportController::class, 'exportPdf'])->name('reports.export.pdf');
        Route::get('/reports/export/excel', [ReportController::class, 'exportExcel'])->name('reports.export.excel');
        Route::get('/reports/metrics', [ReportController::class, 'metrics'])->name('reports.metrics');

        Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
        Route::get('/chat/conversations', [ChatController::class, 'conversations'])->name('chat.conversations');
        Route::get('/chat/conversations/{user}', [ChatController::class, 'messages'])->name('chat.conversations.show');
        Route::get('/chat/unread', [ChatController::class, 'unread'])->name('chat.unread');
        Route::post('/chat/messages', [ChatController::class, 'send'])->name('chat.send');
    });
});
