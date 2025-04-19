<?php

namespace App\Http\Middleware;

use App\Models\Cart;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class GlobalVariables
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Cart Count
        $globalCart = 0;
        if (Auth::check()) {
            $globalCart = Cart::where('user_id', auth()->id())->count();
        } else {
            $cartSession = $request->session()->get('cart', []);
            $globalCart = count($cartSession);
        }
        // Share with all views
        View::share('globalCart', $globalCart);

        return $next($request);
    }
}
