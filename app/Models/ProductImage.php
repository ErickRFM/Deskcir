<?php

namespace App\Models;

use App\Models\Concerns\ResolvesMediaUrls;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ProductImage extends Model
{
    use HasFactory;
    use ResolvesMediaUrls;

    protected $fillable = [
        'product_id',
        'path',
        'disk',
    ];

    protected $appends = ['url'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function normalizedPath(): ?string
    {
        $path = trim((string) $this->path);

        if ($path === '') {
            return null;
        }

        $path = ltrim(str_replace('\\', '/', $path), '/');

        if (str_starts_with($path, 'storage/')) {
            $path = substr($path, 8);
        }

        return $path !== '' ? $path : null;
    }

    public function candidateDisks(): array
    {
        $defaultDisk = (string) config('filesystems.default', 'public');
        $candidates = [
            $this->disk,
            $defaultDisk,
            'public',
            'local',
        ];

        return array_values(array_unique(array_filter($candidates)));
    }

    public function storageDisk(): string
    {
        $path = $this->normalizedPath();

        if ($path === null) {
            return $this->disk ?: 'public';
        }

        foreach ($this->candidateDisks() as $disk) {
            try {
                if (Storage::disk($disk)->exists($path)) {
                    return $disk;
                }
            } catch (\Throwable $exception) {
                continue;
            }
        }

        return $this->disk ?: 'public';
    }

    public function storedFileExists(): bool
    {
        $path = $this->normalizedPath();

        if ($path === null) {
            return false;
        }

        foreach ($this->candidateDisks() as $disk) {
            try {
                if (Storage::disk($disk)->exists($path)) {
                    return true;
                }
            } catch (\Throwable $exception) {
                continue;
            }
        }

        return false;
    }

    public function deleteStoredFile(): void
    {
        $path = $this->normalizedPath();

        if ($path === null) {
            return;
        }

        foreach ($this->candidateDisks() as $disk) {
            try {
                if (Storage::disk($disk)->exists($path)) {
                    Storage::disk($disk)->delete($path);
                    return;
                }
            } catch (\Throwable $exception) {
                continue;
            }
        }

        Storage::disk($this->disk ?: 'public')->delete($path);
    }

    public function getUrlAttribute(): ?string
    {
        if (blank($this->path)) {
            return null;
        }

        if (filter_var($this->path, FILTER_VALIDATE_URL)) {
            return $this->path;
        }

        if (($this->disk ?: null) === 's3') {
            return $this->resolveMediaUrl($this->normalizedPath() ?? $this->path, 's3');
        }

        if ($this->exists) {
            return route('products.images.file', ['productImage' => $this->getKey()], false);
        }

        return $this->resolveMediaUrl($this->path, $this->disk);
    }
}
