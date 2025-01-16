<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductCollection;
use App\Models\Product;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function __invoke()
    {
        $mainBestSellingProducts = new ProductCollection(
            Product::where('campus', 'Main')
                ->get()
                ->where('total_sold', '>', 0)
                ->sortBy('total_sold')
                ->take(10),
        );

        $morelosBestSellingProducts = new ProductCollection(
            Product::where('campus', 'Morelos')
                ->get()
                ->where('total_sold', '>', 0)
                ->sortBy('total_sold')
                ->take(10)
        );

        return Inertia::render('Home', [
            'mainBestSellingProducts' => $mainBestSellingProducts,
            'morelosBestSellingProducts' => $morelosBestSellingProducts,
        ]);
    }
}
