<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = $this->getCartItems();
        $subtotal = $cartItems->sum('subtotal');
        
        return view('pages.cart', compact('cartItems', 'subtotal'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'color' => 'nullable|string|max:80',
        ]);

        $product = Product::findOrFail($request->product_id);
        $selectedColor = $this->normalizeColor($request->input('color'));
        $availableColors = $product->available_colors;

        if ($selectedColor && ! empty($availableColors) && ! in_array($selectedColor, $availableColors, true)) {
            $message = __('product.color_invalid');
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                ], 422);
            }

            return back()
                ->withErrors(['color' => $message])
                ->withInput();
        }

        if (empty($availableColors)) {
            $selectedColor = null;
        }
        
        $cartData = [
            'product_id' => $product->id,
            'quantity' => $request->quantity,
            'price' => $product->price,
            'color' => $selectedColor,
        ];

        if (Auth::check()) {
            $cartData['user_id'] = Auth::id();
            
            $cart = $this->applyColorScope(
                Cart::where('user_id', Auth::id())->where('product_id', $product->id),
                $selectedColor
            )->first();
                
            if ($cart) {
                $cart->quantity += $request->quantity;
                $cart->save();
            } else {
                Cart::create($cartData);
            }
        } else {
            $cartData['session_id'] = session()->getId();
            
            $cart = $this->applyColorScope(
                Cart::where('session_id', session()->getId())->where('product_id', $product->id),
                $selectedColor
            )->first();
                
            if ($cart) {
                $cart->quantity += $request->quantity;
                $cart->save();
            } else {
                Cart::create($cartData);
            }
        }

        $payload = [
            'success' => true,
            'message' => __('cart.added'),
        ];

        if ($request->expectsJson()) {
            return response()->json(array_merge($payload, [
                'count' => $this->getCartItems()->sum('quantity'),
            ]));
        }

        return redirect()->back()->with('success', $payload['message']);
    }

    public function update(Request $request, string $locale, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = $this->findCartItem($id);
        
        if (!$cart) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => __('cart.not_found')], 404);
            }

            return redirect()->route('cart')->with('error', __('cart.not_found'));
        }

        $cart->quantity = $request->quantity;
        $cart->save();

        $payload = [
            'success' => true,
            'message' => __('cart.updated'),
            'subtotal' => $cart->subtotal,
        ];

        if ($request->expectsJson()) {
            return response()->json(array_merge($payload, [
                'count' => $this->getCartItems()->sum('quantity'),
            ]));
        }

        return redirect()->route('cart')->with('success', $payload['message']);
    }

    public function remove(string $locale, $id)
    {
        $cart = $this->findCartItem($id);
        
        if (!$cart) {
            if (request()->expectsJson()) {
                return response()->json(['success' => false, 'message' => __('cart.not_found')], 404);
            }

            return redirect()->route('cart')->with('error', __('cart.not_found'));
        }

        $cart->delete();

        $payload = [
            'success' => true,
            'message' => __('cart.removed'),
        ];

        if (request()->expectsJson()) {
            return response()->json(array_merge($payload, [
                'count' => $this->getCartItems()->sum('quantity'),
            ]));
        }

        return redirect()->route('cart')->with('success', $payload['message']);
    }

    public function clear()
    {
        if (Auth::check()) {
            Cart::where('user_id', Auth::id())->delete();
        } else {
            Cart::where('session_id', session()->getId())->delete();
        }

        $payload = [
            'success' => true,
            'message' => __('cart.cleared'),
        ];

        if (request()->expectsJson()) {
            return response()->json(array_merge($payload, [
                'count' => 0,
            ]));
        }

        return redirect()->route('cart')->with('success', $payload['message']);
    }

    public function summary()
    {
        $items = $this->getCartItems()->map(function (Cart $cart) {
            $product = $cart->product;
            $image = null;

            if ($product && $product->image) {
                $image = Str::startsWith($product->image, ['http://', 'https://'])
                    ? $product->image
                    : asset('storage/' . ltrim($product->image, '/'));
            }

            if (! $image) {
                $seed = urlencode($product->name ?? 'product');
                $image = "https://source.unsplash.com/160x160/?product,{$seed}";
            }

            return [
                'id' => $cart->id,
                'product_id' => $cart->product_id,
                'name' => $product->name ?? __('Product'),
                'quantity' => $cart->quantity,
                'price' => (float) $cart->price,
                'subtotal' => (float) $cart->subtotal,
                'image' => $image,
                'color' => $cart->color,
            ];
        });

        $count = $items->sum('quantity');
        $subtotal = $items->sum('subtotal');

        return response()->json([
            'success' => true,
            'items' => $items,
            'count' => $count,
            'subtotal' => $subtotal,
            'currency' => config('app.currency', 'THB'),
        ]);
    }

    private function getCartItems()
    {
        if (Auth::check()) {
            return Cart::with('product')->where('user_id', Auth::id())->get();
        } else {
            return Cart::with('product')->where('session_id', session()->getId())->get();
        }
    }

    private function findCartItem($id)
    {
        if (Auth::check()) {
            return Cart::where('id', $id)->where('user_id', Auth::id())->first();
        } else {
            return Cart::where('id', $id)->where('session_id', session()->getId())->first();
        }
    }

    public function migrateGuestCart()
    {
        if (Auth::check()) {
            $sessionId = session()->getId();
            $guestCarts = Cart::where('session_id', $sessionId)->get();

            foreach ($guestCarts as $guestCart) {
                $userCart = $this->applyColorScope(
                    Cart::where('user_id', Auth::id())->where('product_id', $guestCart->product_id),
                    $guestCart->color
                )->first();

                if ($userCart) {
                    $userCart->quantity += $guestCart->quantity;
                    $userCart->save();
                    $guestCart->delete();
                } else {
                    $guestCart->user_id = Auth::id();
                    $guestCart->session_id = null;
                    $guestCart->save();
                }
            }
        }
    }

    private function normalizeColor(?string $color): ?string
    {
        $value = trim((string) $color);

        return $value === '' ? null : $value;
    }

    private function applyColorScope($query, ?string $color)
    {
        $normalized = $this->normalizeColor($color);

        return $normalized === null
            ? $query->whereNull('color')
            : $query->where('color', $normalized);
    }
}
