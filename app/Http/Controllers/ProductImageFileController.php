<?php

namespace App\Http\Controllers;

use App\Models\ProductImage;
use Illuminate\Support\Facades\Storage;

class ProductImageFileController extends Controller
{
    public function __invoke(ProductImage $productImage)
    {
        $path = $productImage->normalizedPath();

        abort_if(blank($path), 404);

        foreach ($productImage->candidateDisks() as $disk) {
            try {
                if (Storage::disk($disk)->exists($path)) {
                    return Storage::disk($disk)->response(
                        $path,
                        null,
                        ['Cache-Control' => 'public, max-age=86400']
                    );
                }
            } catch (\Throwable $exception) {
                continue;
            }
        }

        abort(404);
    }
}
