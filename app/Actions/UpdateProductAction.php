<?php

namespace App\Actions;

use App\Models\Product;

class UpdateProductAction
{
    public function handle(array $data, Product $product)
    {
        $product->update([
            'name' => $data['name'],
            'campus' => request()->user()->campus,
            'sku' => $data['sku'],
            'details' => $data['details'],
            'category_id' => $data['category_id'],
        ]);

        if (isset($data['images'])) {
            foreach ($data['images'] as $image) {
                $path = $image->store('products', 'public');
                $product->images()->create(['path' => $path]);
            }
        }

        $product->variants()->delete();

        foreach ($data['variants'] as $variantData) {
            $product->variants()->upsert([
                'name' => $variantData['name'],
                'price' => $variantData['price'],
                'quantity' => $variantData['quantity']],
                ['product_id', 'name', 'price', 'quantity'],
                ['name', 'price', 'quantity'],
            );
        }

        return $product;
    }
}
