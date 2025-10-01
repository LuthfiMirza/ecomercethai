<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\ChatController as AdminChatController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\Admin\PaymentProfileController as AdminPaymentProfileController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $featuredProducts = \App\Models\Product::where('is_active', true)
        ->orderByDesc('created_at')
        ->take(3)
        ->get();

    $catalogProducts = \App\Models\Product::where('is_active', true)
        ->orderByDesc('created_at')
        ->get();

    $banners = \App\Models\Banner::active()->get();

    return view('home', [
        'featuredProducts' => $featuredProducts,
        'catalogProducts' => $catalogProducts,
        'banners' => $banners,
    ]);
})->name('home');

Route::get('/compare', function () {
    return view('compare');
});

Route::post('/locale', function (Request $request) {
    $locale = $request->input('locale');
    if (! in_array($locale, ['en', 'th'], true)) {
        $locale = 'en';
    }
    session(['locale' => $locale]);

    return back();
})->name('locale.set');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);

    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::get('/account', AccountController::class)->name('account');

Route::view('/catalog', 'pages.catalog');
Route::view('/product', 'pages.product');
Route::middleware('auth')->group(function () {
    Route::view('/cart', 'pages.cart')->name('cart');
    Route::view('/wishlist', 'pages.wishlist')->name('wishlist');
});
Route::view('/checkout', 'pages.checkout');

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

        Route::resource('users', AdminUserController::class)->only(['index', 'edit', 'update', 'destroy']);
        Route::resource('products', AdminProductController::class);
        Route::resource('payments', AdminPaymentController::class)->only(['index', 'show', 'destroy']);
        Route::resource('payment-profiles', AdminPaymentProfileController::class);
        Route::resource('banners', BannerController::class);

        Route::get('chat', [AdminChatController::class, 'index'])->name('chat.index');
        Route::post('chat/send', [AdminChatController::class, 'send'])->name('chat.send');
    });
});
