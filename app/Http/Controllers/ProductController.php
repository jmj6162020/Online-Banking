<?php

namespace App\Http\Controllers;

use App\Http\Resources\CartItemCollection;
use App\Http\Resources\ProductCollection;
use App\Http\Resources\ProductResource;
use App\Models\CartItem;
use App\Models\Product;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function index()
    {
        return Inertia::render('CategoryProducts/Index', [
            'products' => new ProductCollection(Product::all()),
        ]);
    }

    public function show(Product $product)
    {
        $cartItems = CartItem::currentSession()->get();

        return Inertia::render('Products/Show', [
            'product' => new ProductResource($product),
            'cartItems' => new CartItemCollection($cartItems),
            'name' => request('filter.name'),
        ]);
    }
}
