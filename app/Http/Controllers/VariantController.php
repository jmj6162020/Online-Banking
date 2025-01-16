<?php

namespace App\Http\Controllers;

use App\Models\Variant;

class VariantController extends Controller
{
    public function destroy(Variant $variant)
    {
        $variant->delete();

        return response()->noContent();
    }
}
