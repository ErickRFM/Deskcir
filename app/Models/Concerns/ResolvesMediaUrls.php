<?php

namespace App\Models\Concerns;

use Illuminate\Support\Facades\Storage;

trait ResolvesMediaUrls
{
    protected function resolveMediaUrl(?string $path, ?string $disk = null): ?string
    {
        if (blank($path)) {
            return null;
        }

        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        $disk = $disk ?: 'public';

        try {
            return Storage::disk($disk)->url($path);
        } catch (\Throwable $exception) {
            return asset('storage/' . ltrim($path, '/'));
        }
    }
}
