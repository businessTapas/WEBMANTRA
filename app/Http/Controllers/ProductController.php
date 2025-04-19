<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    public function index() {
        $products = Product::where('status', 'active')->orderBy('id', 'DESC')->get();

        return view('productlist')->with('product_lists', $products);
    }
}
