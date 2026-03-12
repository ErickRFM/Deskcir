<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    private function mediaDisk(): string
    {
        $defaultDisk = (string) config('filesystems.default', 'public');

        if ($defaultDisk === 's3' && ! class_exists(\League\Flysystem\AwsS3V3\PortableVisibilityConverter::class)) {
            return 'public';
        }

        return $defaultDisk ?: 'public';
    }

    public function create()
    {
        $categories = $this->getAdminCategories();

        return view('admin.products.create', compact('categories'));
    }

    public function deleteImage($id)
    {
        $img = ProductImage::findOrFail($id);

        Storage::disk($img->disk ?: $this->mediaDisk())->delete($img->path);
        $img->delete();

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
            'price.required' => 'El precio es obligatorio',
            'stock.required' => 'El stock es obligatorio',
            'category_id.required' => 'Selecciona una categoria',
            'category_id.exists' => 'La categoria no es valida',
            'images.*.image' => 'Cada archivo debe ser una imagen',
            'images.*.mimes' => 'Formatos permitidos: jpg, png, webp',
        ]);

        $slug = Str::slug($request->name) . '-' . time();

        $product = Product::create([
            'name' => $request->name,
            'slug' => $slug,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock,
            'category_id' => $request->category_id,
        ]);

        if ($request->hasFile('images')) {
            $disk = $this->mediaDisk();

            foreach ($request->file('images') as $img) {
                $path = $img->store('products', $disk);

                ProductImage::create([
                    'product_id' => $product->id,
                    'path' => $path,
                    'disk' => $disk,
                ]);
            }
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
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'images.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ], [
            'name.required' => 'El nombre es obligatorio',
            'category_id.required' => 'Selecciona una categoria',
            'category_id.exists' => 'La categoria no es valida',
        ]);

        $product->update([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock,
            'category_id' => $request->category_id,
        ]);

        if ($request->hasFile('images')) {
            $disk = $this->mediaDisk();

            foreach ($request->file('images') as $img) {
                $path = $img->store('products', $disk);

                ProductImage::create([
                    'product_id' => $product->id,
                    'path' => $path,
                    'disk' => $disk,
                ]);
            }
        }

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Producto actualizado correctamente');
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        foreach ($product->images as $img) {
            Storage::disk($img->disk ?: $this->mediaDisk())->delete($img->path);
            $img->delete();
        }

        $product->delete();

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



