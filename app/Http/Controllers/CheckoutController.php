<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class CheckoutController extends Controller
{
    public function checkout(Request $request)
    {
        $json = File::get(storage_path('app/_district.json'));
        $states = json_decode($json, true)['states'];

        $cartData = $this->cartCalculation($request);
        if (Auth::check()) {
            $user = User::with('userDetails')->where('id', auth()->user()->id)->first();
        } else {
            $user = null;
        }


        return view('pages.checkout', array_merge([
            'states' => $states,
            'user' => $user,
        ], $cartData));
    }

    public function cartCalculation(Request $request)
    {
        $subtotal = 0;
        $product_details = collect([]);
        $cartCount = 0;

        if (Auth::check()) {
            $carts = Cart::where('user_id', auth()->user()->id)->get();
            $cartCount = count($carts);
            if ($cartCount > 0) {
                $product_details = Product::whereIn('id', $carts->pluck('product_id'))->get();
                foreach ($product_details as $product_detail) {
                    $cartItem = $carts->where('product_id', $product_detail->id)->first();
                    $product_detail->qty = $cartItem ? $cartItem->quantity : 1;

                    if ((float) $product_detail->discount > 0) {
                        $discount_amount = ($product_detail->price * $product_detail->discount) / 100;
                        $price = $product_detail->price - $discount_amount;
                    } else {
                        $price = $product_detail->price;
                    }
                    $subtotal += $price * $product_detail->qty;
                }
            }
        } else {
            $cart = $request->session()->get('cart', []);
            $cartCount = count($cart);
            if ($cartCount > 0) {
                $productIds = collect($cart)->pluck('product_id')->toArray();
                $product_details = Product::whereIn('id', $productIds)->get();
                foreach ($product_details as $product_detail) {
                    $cartItem = collect($cart)->firstWhere('product_id', $product_detail->id);
                    $product_detail->qty = $cartItem ? $cartItem['quantity'] : 1;

                    if ((float) $product_detail->discount > 0) {
                        $discount_amount = ($product_detail->price * $product_detail->discount) / 100;
                        $price = $product_detail->price - $discount_amount;
                    } else {
                        $price = $product_detail->price;
                    }
                    $subtotal += $price * $product_detail->qty;
                }
            }
        }
        $gst = $subtotal * .18;
        return compact('subtotal', 'cartCount', 'product_details', 'gst');
    }

    public function update(Request $request)
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
        }

        $cartData = $this->cartCalculation($request);

        if ($request->ajax()) {
            return view('pages.checkoutProduct', $cartData);
        }
    }

    public function delete(Request $request, $id)
    {
        $product_id = $id;

        if (Auth::check()) {
            $cart = Cart::where('product_id', $product_id)->where('user_id', auth()->user()->id)->first();
            if (!$cart) {
                return response()->json(['success' => false, 'message' => 'Product not found in cart']);
            } else {
                $cart->delete();
            }
        } else {
            $cart = $request->session()->get('cart', []);
            if (!isset($cart[$product_id])) {
                return response()->json(['success' => false, 'message' => 'Product not found in cart']);
            }
            unset($cart[$product_id]);
            $request->session()->put('cart', $cart);
        }

        $cartData = $this->cartCalculation($request);
        $cartCount = count($cartData['product_details'] ?? []);

        $html = view('pages.checkoutProduct', $cartData)->render();

        return response()->json([
            'success' => true,
            'html' => $html,
            'cartCount' => $cartCount
        ]);
    }
}
