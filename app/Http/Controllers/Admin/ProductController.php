<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    // =============================
    // LISTADO
    // =============================
    public function index()
    {
        $products = Product::with('images','category')->get();

        return view('admin.products.index', compact('products'));
    }

    // =============================
    // CREAR
    // =============================
    public function create()
    {
        $categories = Category::all();

        return view('admin.products.create', compact('categories'));
    }
    public function deleteImage($id)
{
    $img = ProductImage::findOrFail($id);

    Storage::disk('public')->delete($img->path);

    $img->delete();

    return back()->with('success','Imagen eliminada');
}
    // =============================
    // GUARDAR
    // =============================
    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',

            'images.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ],[
            'name.required'        => 'El nombre es obligatorio',
            'price.required'       => 'El precio es obligatorio',
            'stock.required'       => 'El stock es obligatorio',
            'category_id.required' => 'Selecciona una categoría',
            'category_id.exists'   => 'La categoría no es válida',

            'images.*.image' => 'Cada archivo debe ser una imagen',
            'images.*.mimes' => 'Formatos permitidos: jpg, png, webp',
        ]);

        $slug = Str::slug($request->name) . '-' . time();

        $product = Product::create([
            'name'        => $request->name,
            'slug'        => $slug,
            'description' => $request->description,
            'price'       => $request->price,
            'stock'       => $request->stock,
            'category_id' => $request->category_id,
        ]);

        // 🔥 MÚLTIPLES IMÁGENES
        if ($request->hasFile('images')) {

            foreach ($request->file('images') as $img) {

                $path = $img->store('products', 'public');

                ProductImage::create([
                    'product_id' => $product->id,
                    'path'       => $path
                ]);
            }
        }

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Producto agregado correctamente 🔥');
    }

    // =============================
    // EDITAR (🔥 EL QUE TE FALTABA)
    // =============================
    public function edit($id)
    {
        $product = Product::with('images')->findOrFail($id);

        $categories = Category::all();

        return view('admin.products.edit',
            compact('product','categories'));
    }

    // =============================
    // ACTUALIZAR
    // =============================
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'name'        => 'required|string|max:255',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',

            'images.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ],[
            'name.required'        => 'El nombre es obligatorio',
            'category_id.required' => 'Selecciona una categoría',
        ]);

        $product->update([
            'name'        => $request->name,
            'description' => $request->description,
            'price'       => $request->price,
            'stock'       => $request->stock,
            'category_id' => $request->category_id,
        ]);

        // 🔥 AGREGAR NUEVAS IMÁGENES
        if ($request->hasFile('images')) {

            foreach ($request->file('images') as $img) {

                $path = $img->store('products', 'public');

                ProductImage::create([
                    'product_id' => $product->id,
                    'path'       => $path
                ]);
            }
        }

        return redirect()
            ->route('admin.products.index')
            ->with('success','Producto actualizado correctamente');
    }

    // =============================
    // ELIMINAR
    // =============================
    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        // 🔥 BORRAR IMÁGENES REALES
        foreach ($product->images as $img) {

            Storage::disk('public')->delete($img->path);

            $img->delete();
        }

        $product->delete();

        return back()
            ->with('success','Producto eliminado correctamente');
    }
}