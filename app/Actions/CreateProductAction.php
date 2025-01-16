<?php

namespace App\Actions;

use App\Models\Category;

class CreateProductAction
{
    public function handle(array $data)
    {
        $category = Category::find($data['category_id']);

        $product = $category->products()->create([
            'name' => $data['name'],
            'campus' => $data['campus'] ?? request()->user()->campus,
            'sku' => $data['sku'],
            'details' => $data['details'],
        ]);

        foreach ($data['images'] as $image) {
            $path = $image->store('products', 'public');
            $product->images()->create(['path' => $path]);
        }

        foreach ($data['variants'] as $variantData) {
            $product->variants()->create([
                'name' => $variantData['name'],
                'price' => $variantData['price'],
                'quantity' => $variantData['quantity'],
            ]);
        }

        return $product;
    }
}
