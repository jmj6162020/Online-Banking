<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class RestoreProductController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(string $id)
    {
        Product::withTrashed()->find($id)->restore();

        return redirect(route('admin.products.index'));
    }
}
