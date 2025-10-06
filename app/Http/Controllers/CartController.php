<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        ]);

        $product = Product::findOrFail($request->product_id);
        
        $cartData = [
            'product_id' => $product->id,
            'quantity' => $request->quantity,
            'price' => $product->price,
        ];

        if (Auth::check()) {
            $cartData['user_id'] = Auth::id();
            
            $cart = Cart::where('user_id', Auth::id())
                ->where('product_id', $product->id)
                ->first();
                
            if ($cart) {
                $cart->quantity += $request->quantity;
                $cart->save();
            } else {
                Cart::create($cartData);
            }
        } else {
            $cartData['session_id'] = session()->getId();
            
            $cart = Cart::where('session_id', session()->getId())
                ->where('product_id', $product->id)
                ->first();
                
            if ($cart) {
                $cart->quantity += $request->quantity;
                $cart->save();
            } else {
                Cart::create($cartData);
            }
        }

        return response()->json([
            'success' => true,
            'message' => __('cart.added'),
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = $this->findCartItem($id);
        
        if (!$cart) {
            return response()->json(['success' => false, 'message' => __('cart.not_found')], 404);
        }

        $cart->quantity = $request->quantity;
        $cart->save();

        return response()->json([
            'success' => true,
            'message' => __('cart.updated'),
            'subtotal' => $cart->subtotal,
        ]);
    }

    public function remove($id)
    {
        $cart = $this->findCartItem($id);
        
        if (!$cart) {
            return response()->json(['success' => false, 'message' => __('cart.not_found')], 404);
        }

        $cart->delete();

        return response()->json([
            'success' => true,
            'message' => __('cart.removed'),
        ]);
    }

    public function clear()
    {
        if (Auth::check()) {
            Cart::where('user_id', Auth::id())->delete();
        } else {
            Cart::where('session_id', session()->getId())->delete();
        }

        return response()->json([
            'success' => true,
            'message' => __('cart.cleared'),
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
                $userCart = Cart::where('user_id', Auth::id())
                    ->where('product_id', $guestCart->product_id)
                    ->first();

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
}
