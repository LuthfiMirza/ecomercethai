<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
});

Route::get('/compare', function(){
    return view('compare');
});

// Demo routes for new pages (no controller logic changed)
Route::view('/catalog', 'pages.catalog');
Route::view('/product', 'pages.product');
Route::view('/cart', 'pages.cart');
Route::view('/checkout', 'pages.checkout');
