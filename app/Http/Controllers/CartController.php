<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Wishlist;
use App\Models\Cart;
use Illuminate\Support\Str;
use Helper;

class CartController extends Controller
{
    protected $product = null;
    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    public function addToCart(Request $request)
    {
        // return $request->all();
        if (empty($request->slug)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid Product'
            ], 400);
        }
        $product = Product::where('slug', $request->slug)->first();
        // return $product;
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        } else {

            if (!Auth::check()) {
                $cart = $request->session()->get('cart', []);

                if (isset($cart[$product->id])) {
                    $cart[$product->id]['quantity'] += 1;
                } else {
                    $cart[$product->id] = [
                        'product_id' => $product->id,
                        'quantity' => 1,
                    ];
                }

                // Store updated cart in session
                $request->session()->put('cart', $cart);
                if (count($cart) > 0) {
                    $product_details = Product::whereIn('id', array_column($cart, 'product_id'))->get();

                    foreach ($product_details as $product_detail) {
                        $product_detail->qty = $cart[$product_detail->id]['quantity'] ?? 1;
                    }
                    $cartView = view('pages.cartview', compact('product_details'))->render();
                }

                return response()->json([
                    'success' => true,
                    // 'message' => 'Product added to cart',
                    // 'cart' => $cart,
                    // 'products' => $product_details,
                    'cartView' => $cartView,
                    'cart_count' => count($cart)
                ]);
            } else {
                $already_cart = Cart::where('product_id', $product->id)->where('user_id', auth()->user()->id)->first();
                if ($already_cart) {
                    $already_cart->update([
                        'quantity' => $already_cart->quantity + 1,
                        'amount' => $already_cart->amount + $product->price,
                    ]);
                } else {
                    $new_cart = Cart::create([
                        'product_id' => $product->id,
                        'user_id' => auth()->user()->id,
                        'price' => $product->price,
                        'quantity' => 1,
                        'amount' => $product->price * 1
                    ]);
                }

                $carts = Cart::where('user_id', auth()->user()->id)->get();
                $cartCount = count($carts);
                $product_details = collect([]);
                if ($cartCount > 0) {
                    $product_details = Product::whereIn('id', $carts->pluck('product_id'))->get();

                    foreach ($product_details as $product_detail) {
                        $cartItem = $carts->where('product_id', $product_detail->id)->first();
                        $product_detail->qty = $cartItem ? $cartItem->quantity : 1;
                    }
                }
                $cartView = view('pages.cartview', compact('product_details'))->render();
                return response()->json([
                    'success' => true,
                    // 'message' => 'Product added to cart',
                    // 'cart' => $cart,
                    // 'products' => $product_details,
                    'cartView' => $cartView,
                    'cart_count' => $cartCount
                ]);
            }
        }
    }

    public function cartView(Request $request)
    {
        if (!Auth::check()) {
            $cart = $request->session()->get('cart', []);
            $cartCount = count($cart);
            if ($cartCount > 0) {
                $product_details = Product::whereIn('id', array_column($cart, 'product_id'))->get();

                foreach ($product_details as $product_detail) {
                    $product_detail->qty = $cart[$product_detail->id]['quantity'] ?? 1;
                }
                $cartView = view('pages.cartview', compact('product_details'))->render();
                return response()->json([
                    'success' => true,
                    // 'message' => 'Product added to cart',
                    // 'cart' => $cart,
                    // 'products' => $product_details,
                    'cartView' => $cartView,
                    'cart_count' => count($cart)
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'empty_cart' => true,
                    'message' => 'Your cart is empty'
                ]);
            }
        } else {
            $carts = Cart::where('user_id', auth()->user()->id)->get();
            $cartCount = count($carts);
            $product_details = collect([]);
            if ($cartCount > 0) {
                $product_details = Product::whereIn('id', $carts->pluck('product_id'))->get();

                foreach ($product_details as $product_detail) {
                    $cartItem = $carts->where('product_id', $product_detail->id)->first();
                    $product_detail->qty = $cartItem ? $cartItem->quantity : 1;
                }
                $cartView = view('pages.cartview', compact('product_details'))->render();
                return response()->json([
                    'success' => true,
                    // 'message' => 'Product added to cart',
                    // 'cart' => $cart,
                    // 'products' => $product_details,
                    'cartView' => $cartView,
                    'cart_count' => $cartCount
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'empty_cart' => true,
                    'message' => 'Your cart is empty'
                ]);
            }
        }
    }

    public function singleAddToCart(Request $request)
    {
        $request->validate([
            'slug'      =>  'required',
            'quant'      =>  'required',
        ]);
        // dd($request->quant[1]);


        $product = Product::where('slug', $request->slug)->first();
        if ($product->stock < $request->quant[1]) {
            return back()->with('error', 'Out of stock, You can add other products.');
        }
        if (($request->quant[1] < 1) || empty($product)) {
            request()->session()->flash('error', 'Invalid Products');
            return back();
        }

        $already_cart = Cart::where('user_id', auth()->user()->id)->where('order_id', null)->where('product_id', $product->id)->first();

        // return $already_cart;

        if ($already_cart) {
            $already_cart->quantity = $already_cart->quantity + $request->quant[1];
            // $already_cart->price = ($product->price * $request->quant[1]) + $already_cart->price ;
            $already_cart->amount = ($product->price * $request->quant[1]) + $already_cart->amount;

            if ($already_cart->product->stock < $already_cart->quantity || $already_cart->product->stock <= 0) return back()->with('error', 'Stock not sufficient!.');

            $already_cart->save();
        } else {

            $cart = new Cart;
            $cart->user_id = auth()->user()->id;
            $cart->product_id = $product->id;
            $cart->price = ($product->price - ($product->price * $product->discount) / 100);
            $cart->quantity = $request->quant[1];
            $cart->amount = ($product->price * $request->quant[1]);
            if ($cart->product->stock < $cart->quantity || $cart->product->stock <= 0) return back()->with('error', 'Stock not sufficient!.');
            // return $cart;
            $cart->save();
        }
        request()->session()->flash('success', 'Product successfully added to cart.');
        return back();
    }

    public function cartDelete(Request $request, $id)
    {
        $product_id = $id;

        if (Auth::check()) {
            $cart = Cart::where('product_id', $product_id)->where('user_id', auth()->user()->id)->first();
            if (!$cart) {
                return response()->json(['success' => false, 'message' => 'Product not found in cart']);
            } else {
                $cart->delete();
            }
            $carts = Cart::where('user_id', auth()->user()->id)->get();
            $cartCount = count($carts);
            $product_details = collect([]);
            if ($cartCount > 0) {
                $product_details = Product::whereIn('id', $carts->pluck('product_id'))->get();

                foreach ($product_details as $product_detail) {
                    $cartItem = $carts->where('product_id', $product_detail->id)->first();
                    $product_detail->qty = $cartItem ? $cartItem->quantity : 1;
                }
                $cartView = view('pages.cartview', compact('product_details'))->render();
                return response()->json([
                    'success' => true,
                    'message' => 'Product deleted from cart',
                    // 'cart' => $cart,
                    // 'products' => $product_details,
                    'cartView' => $cartView,
                    'cart_count' => $cartCount
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'empty_cart' => true,
                    'message' => 'Your cart is empty',
                    'cart_count' => 0
                ]);
            }
        } else {
            $cart = $request->session()->get('cart', []);
            if (!isset($cart[$product_id])) {
                return response()->json(['success' => false, 'message' => 'Product not found in cart']);
            }
            unset($cart[$product_id]);
            $request->session()->put('cart', $cart);

            $cartCount = count($cart);
            if ($cartCount > 0) {
                $product_details = Product::whereIn('id', array_column($cart, 'product_id'))->get();

                foreach ($product_details as $product_detail) {
                    $product_detail->qty = $cart[$product_detail->id]['quantity'] ?? 1;
                }
                $cartView = view('pages.cartview', compact('product_details'))->render();
                return response()->json([
                    'success' => true,
                    'message' => 'Product deleted from cart',
                    // 'cart' => $cart,
                    // 'products' => $product_details,
                    'cartView' => $cartView,
                    'cart_count' => count($cart)
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'empty_cart' => true,
                    'message' => 'Your cart is empty',
                    'cart_count' => 0
                ]);
            }
        }
    }

    public function cartUpdate(Request $request)
    {
        $product_id = $request->product_id;
        $action = $request->action;

        if (Auth::check()) {
            $cart = Cart::where('user_id', auth()->id())
                ->where('product_id', $product_id)
                ->first();

            if (!$cart) {
                return response()->json(['success' => false, 'message' => 'Product not found in cart']);
            }

            $quantity = $cart->quantity;

            if ($action === 'decriment' && $quantity > 1) {
                $cart->update(['quantity' => --$quantity]);
            } elseif ($action === 'incriment' && $quantity < 10) {
                $cart->update(['quantity' => ++$quantity]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $action === 'decriment' ? 'Minimum quantity is 1' : 'Maximum quantity is 10'
                ]);
            }
            return response()->json(['success' => true, 'quantity' => $quantity]);
        } else {
            $cart = $request->session()->get('cart', []);

            if (!isset($cart[$product_id])) {
                return response()->json(['success' => false, 'message' => 'Product not found in cart']);
            }

            $quantity = $cart[$product_id]['quantity'];

            if ($action === 'decriment' && $quantity > 1) {
                $cart[$product_id]['quantity']--;
            } elseif ($action === 'incriment' && $quantity < 10) {
                $cart[$product_id]['quantity']++;
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $action === 'decriment' ? 'Minimum quantity is 1' : 'Maximum quantity is 10'
                ]);
            }

            $request->session()->put('cart', $cart);
            return response()->json(['success' => true, 'quantity' => $cart[$product_id]['quantity']]);
        }
    }

    public function checkout(Request $request)
    {

        return view('frontend.pages.checkout');
    }
}
