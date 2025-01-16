<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductCollection;
use App\Models\Category;
use Inertia\Inertia;
use Spatie\QueryBuilder\QueryBuilder;

class CategoryProductController extends Controller
{
    public function index(string $slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();

        $products = QueryBuilder::for($category->products())
            ->allowedFilters(['campus', 'name'])
            ->get();

        return Inertia::render('CategoryProducts/Index', [
            'products' => new ProductCollection($products),
            'category' => $category,
            'campus' => request('filter.campus', 'Main'),
            'name' => request('filter.name'),
        ]);
    }
}
