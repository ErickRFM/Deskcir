<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class ProductController extends Controller
{
    private function mediaDisk(): string
    {
        if (app()->environment(['local', 'testing'])) {
            return 'public';
        }

        $defaultDisk = (string) config('filesystems.default', 'local');

        if ($defaultDisk === 's3'
            && class_exists(\League\Flysystem\AwsS3V3\PortableVisibilityConverter::class)
            && $this->s3Configured()) {
            return 's3';
        }

        return 'public';
    }

    private function s3Configured(): bool
    {
        return ! blank(config('filesystems.disks.s3.key'))
            && ! blank(config('filesystems.disks.s3.secret'))
            && ! blank(config('filesystems.disks.s3.bucket'))
            && (! blank(config('filesystems.disks.s3.endpoint')) || ! blank(config('filesystems.disks.s3.url')));
    }

    private function storeProductImage(UploadedFile $img): array
    {
        $disks = array_values(array_unique([$this->mediaDisk(), 'public']));

        foreach ($disks as $disk) {
            $path = $this->storeProductImageOnDisk($img, $disk);

            if ($path !== null) {
                return [
                    'path' => $path,
                    'disk' => $disk,
                ];
            }
        }

        throw ValidationException::withMessages([
            'images' => 'No se pudo guardar la imagen del producto. Intenta nuevamente.',
        ]);
    }

    private function storeProductImageOnDisk(UploadedFile $img, string $disk): ?string
    {
        try {
            $manager = new ImageManager(new Driver());
            $image = $manager->read($img->getRealPath());

            if (method_exists($image, 'orient')) {
                $image = $image->orient();
            }

            $image = $image->scaleDown(width: 1600);
            $encoded = $image->toWebp(quality: 82);
            $path = 'products/' . Str::uuid()->toString() . '.webp';

            $stored = Storage::disk($disk)->put($path, (string) $encoded, [
                'visibility' => 'public',
                'ContentType' => 'image/webp',
            ]);

            if ($stored !== true) {
                throw new \RuntimeException("No se pudo escribir la imagen en el disco {$disk}.");
            }

            return $path;
        } catch (\Throwable $exception) {
            try {
                $storedPath = $img->storePublicly('products', $disk);

                return is_string($storedPath) && $storedPath !== ''
                    ? str_replace('\\', '/', $storedPath)
                    : null;
            } catch (\Throwable $fallbackException) {
                return null;
            }
        }
    }

    private function saveProductImages(Product $product, array $images): void
    {
        $storedImages = [];

        try {
            foreach ($images as $img) {
                $stored = $this->storeProductImage($img);
                $storedImages[] = $stored;

                ProductImage::create([
                    'product_id' => $product->id,
                    'path' => $stored['path'],
                    'disk' => $stored['disk'],
                ]);
            }
        } catch (\Throwable $exception) {
            foreach ($storedImages as $stored) {
                try {
                    Storage::disk($stored['disk'])->delete($stored['path']);
                } catch (\Throwable $cleanupException) {
                    continue;
                }
            }

            throw $exception;
        }
    }

    public function create()
    {
        $categories = $this->getAdminCategories();

        return view('admin.products.create', compact('categories'));
    }

    public function index()
    {
        $products = Product::query()
            ->with(['images', 'category'])
            ->orderByDesc('id')
            ->get();

        return view('admin.products.index', compact('products'));
    }

    public function show($id)
    {
        return redirect()->route('admin.products.edit', $id);
    }

    public function deleteImage($id)
    {
        $img = ProductImage::findOrFail($id);

        try {
            $img->deleteStoredFile();
            $img->delete();
        } catch (\Throwable $exception) {
            if (request()->expectsJson()) {
                return response()->json([
                    'ok' => false,
                    'message' => 'No se pudo eliminar la imagen en este momento.',
                ], 422);
            }

            return back()->with('error', 'No se pudo eliminar la imagen en este momento.');
        }

        if (request()->expectsJson()) {
            return response()->json([
                'ok' => true,
                'message' => 'Imagen eliminada correctamente.',
            ]);
        }

        return back()->with('success', 'Imagen eliminada');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'images.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ], [
            'name.required' => 'El nombre es obligatorio',
            'name.max' => 'El nombre no debe superar los 255 caracteres',
            'price.required' => 'El precio es obligatorio',
            'price.numeric' => 'El precio debe ser numerico',
            'price.min' => 'El precio no puede ser negativo',
            'stock.required' => 'El stock es obligatorio',
            'stock.integer' => 'El stock debe ser un numero entero',
            'stock.min' => 'El stock no puede ser negativo',
            'category_id.required' => 'Selecciona una categoria',
            'category_id.exists' => 'La categoria no es valida',
            'images.*.image' => 'Cada archivo debe ser una imagen',
            'images.*.mimes' => 'Formatos permitidos: jpg, png, webp',
            'images.*.max' => 'Cada imagen debe pesar maximo 5 MB',
        ]);

        $slug = Str::slug($request->name) . '-' . time();
        $product = null;

        try {
            DB::transaction(function () use ($request, $slug, &$product) {
                $product = Product::create([
                    'name' => $request->name,
                    'slug' => $slug,
                    'description' => $request->description,
                    'price' => $request->price,
                    'stock' => $request->stock,
                    'category_id' => $request->category_id,
                ]);

                if ($request->hasFile('images')) {
                    $this->saveProductImages($product, $request->file('images'));
                }
            });
        } catch (ValidationException $exception) {
            throw $exception;
        } catch (\Throwable $exception) {
            return back()
                ->withInput()
                ->withErrors(['images' => 'No se pudieron guardar las imagenes del producto.']);
        }

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Producto agregado correctamente');
    }

    public function edit($id)
    {
        $product = Product::with('images')->findOrFail($id);
        $categories = $this->getAdminCategories();

        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'images.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ], [
            'name.required' => 'El nombre es obligatorio',
            'name.max' => 'El nombre no debe superar los 255 caracteres',
            'price.required' => 'El precio es obligatorio',
            'price.numeric' => 'El precio debe ser numerico',
            'price.min' => 'El precio no puede ser negativo',
            'stock.required' => 'El stock es obligatorio',
            'stock.integer' => 'El stock debe ser un numero entero',
            'stock.min' => 'El stock no puede ser negativo',
            'category_id.required' => 'Selecciona una categoria',
            'category_id.exists' => 'La categoria no es valida',
            'images.*.image' => 'Cada archivo debe ser una imagen',
            'images.*.mimes' => 'Formatos permitidos: jpg, png, webp',
            'images.*.max' => 'Cada imagen debe pesar maximo 5 MB',
        ]);

        try {
            DB::transaction(function () use ($request, $product) {
                $product->update([
                    'name' => $request->name,
                    'description' => $request->description,
                    'price' => $request->price,
                    'stock' => $request->stock,
                    'category_id' => $request->category_id,
                ]);

                if ($request->hasFile('images')) {
                    $this->saveProductImages($product, $request->file('images'));
                }
            });
        } catch (ValidationException $exception) {
            throw $exception;
        } catch (\Throwable $exception) {
            return back()
                ->withInput()
                ->withErrors(['images' => 'No se pudieron guardar las imagenes del producto.']);
        }

        return redirect()
            ->route('admin.products.edit', $product->id)
            ->with('success', 'Producto actualizado correctamente');
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        if ($product->orderItems()->exists()) {
            return back()->with('error', 'No puedes eliminar un producto con ventas registradas. Editalo o dejalo sin stock.');
        }

        try {
            DB::transaction(function () use ($product) {
                foreach ($product->images as $img) {
                    $img->deleteStoredFile();
                    $img->delete();
                }

                $product->delete();
            });
        } catch (\Throwable $exception) {
            return back()->with('error', 'No se pudo eliminar el producto en este momento.');
        }

        return back()->with('success', 'Producto eliminado correctamente');
    }

    private function getAdminCategories()
    {
        $baseCategories = [
            'Gabinetes',
            'Laptops',
            'Accesorios',
            'Refacciones',
        ];

        foreach ($baseCategories as $name) {
            Category::query()->firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name]
            );
        }

        return Category::query()->orderBy('name')->get();
    }
}

