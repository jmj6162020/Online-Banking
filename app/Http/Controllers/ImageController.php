<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    public function destroy(Image $image)
    {
        Storage::disk('public')->delete($image->path);

        $image->delete();

        return response()->noContent();
    }
}
