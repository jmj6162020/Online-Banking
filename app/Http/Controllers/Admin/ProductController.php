<?php

namespace App\Http\Controllers\Admin;

use App\Actions\CreateProductAction;
use App\Actions\UpdateProductAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductCollection;
use App\Http\Resources\ProductResource;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    protected $createProductAction;

    protected $updateProductAction;

    public function __construct(
        CreateProductAction $createProductAction,
        UpdateProductAction $updateProductAction,
    ) {
        $this->createProductAction = $createProductAction;
        $this->updateProductAction = $updateProductAction;
    }

    public function index(Request $request): Response
    {
        $archived = request('archived', false);

        $products = Product::search(request('query'))
            ->get()
            ->where('campus', $request->user()->campus);

        if ($archived) {
            $products = Product::onlyTrashed()
                ->where(function ($query) {
                    $query->where('name', 'LIKE', '%' . request('query') . '%')
                        ->orWhere('sku', 'LIKE', '%' . request('query') . '%');
                })
                ->where('campus', $request->user()->campus)
                ->get();
        }

        if ($request->user()->hasRole('super-admin')) {
            $products = Product::search(request('query'))->get();
            if ($archived) {
                $products = Product::withTrashed()
                    ->where(function ($query) {
                        $query->where('name', 'LIKE', '%' . request('query') . '%')
                            ->orWhere('sku', 'LIKE', '%' . request('query') . '%');
                    })
                    ->get();
            }
        }

        return Inertia::render('Admin/Products/Index', [
            'products' => new ProductCollection($products),
            'archived' => $archived,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Products/Create', [
            'categories' => Category::all(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|integer|exists:categories,id',
            'name' => [
                'required',
                Rule::unique('products')->where(function ($query) use ($request) {
                    return $query
                        ->where('name', $request->input('name'))
                        ->where('sku', $request->input('sku'))
                        ->where('campus', $request->user()->campus ?? request('campus'));
                }),
            ],
            'sku' => [
                'required',
                Rule::unique('products')->where(function ($query) use ($request) {
                    return $query
                        ->where('name', $request->input('name'))
                        ->where('sku', $request->input('sku'))
                        ->where('campus', $request->user()->campus ?? request('campus'));
                }),
            ],
            'campus' => ['nullable', Rule::in(['Main', 'Morelos'])],
            'details' => 'nullable|string',
            'images' => 'required|array',
            'images.*' => 'file|mimetypes:image/*',
            'variants' => 'required|array',
            'variants.*.name' => 'required|string|max:255',
            'variants.*.price' => 'required|numeric|min:1',
            'variants.*.quantity' => 'required|integer|min:1',
        ]);

        $this->createProductAction->handle($validated);

        return redirect(route('admin.products.index'))
            ->with(['message' => 'Product created successfully.']);
    }

    public function edit(Product $product)
    {
        return Inertia::render('Admin/Products/Edit', [
            'product' => new ProductResource($product),
            'categories' => Category::all(),
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'category_id' => 'required|integer|exists:categories,id',
            'name' => [
                'required',
                Rule::unique('products')->ignore($product->id),
            ],
            'sku' => [
                'required',
                Rule::unique('products')->ignore($product->id),
            ],
            'details' => 'nullable|string',
            'images' => 'nullable|array',
            'images.*' => 'file|mimetypes:image/*',
            'variants' => 'required|array',
            'variants.*.name' => 'required|string|max:255',
            'variants.*.price' => 'required|numeric|min:1',
            'variants.*.quantity' => 'required|integer|min:1',
        ]);

        if ($product->images->count() === 0 && ! $request->images) {
            return back()->withErrors([
                'images' => 'The images field is required.',
            ]);
        }

        $this->updateProductAction->handle($validated, $product);

        return redirect(route('admin.products.index'))
            ->with(['message' => 'Product updated successfully.']);
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect(route('admin.products.index'))->with([
            'message' => 'Product archived successfully.',
        ]);
    }
}
