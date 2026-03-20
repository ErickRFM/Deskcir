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

        $path = ltrim(str_replace('\\', '/', $path), '/');
        $disk = $disk ?: 'public';

        if (str_starts_with($path, 'storage/')) {
            $path = substr($path, 8);
        }

        if ($disk === 'public') {
            return '/storage/' . ltrim(preg_replace('#^public/#', '', $path) ?? $path, '/');
        }

        if ($disk === 'local' && str_starts_with($path, 'public/')) {
            return '/storage/' . ltrim(substr($path, 7), '/');
        }

        try {
            return Storage::disk($disk)->url($path);
        } catch (\Throwable $exception) {
            return '/storage/' . ltrim(preg_replace('#^public/#', '', $path) ?? $path, '/');
        }
    }
}
