<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::latest()->get();

        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::all();

        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        // ================= VALIDACIÓN =================
        $request->validate(
        [
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',

            'images.*'    => 'image|mimes:jpg,jpeg,png,webp|max:2048',
        ],
        [
            'name.required'        => 'El nombre es obligatorio',
            'price.required'       => 'El precio es obligatorio',
            'stock.required'       => 'El stock es obligatorio',

            'category_id.required' => 'Debes seleccionar una categoría',
            'category_id.exists'   => 'La categoría seleccionada no es válida',

            'images.*.image'       => 'Cada archivo debe ser una imagen',
            'images.*.mimes'       => 'Formatos permitidos: jpg, png, webp',
            'images.*.max'         => 'Máximo 2MB por imagen',
        ]);

        // ================= CREAR PRODUCTO =================
        $product = Product::create([

            'name'        => $request->name,
            'description' => $request->description,
            'price'       => $request->price,
            'stock'       => $request->stock,
            'category_id' => $request->category_id,

            // 🔥 SLUG ÚNICO
            'slug' => Str::slug($request->name) . '-' . time(),
        ]);

        // ================= GUARDAR IMÁGENES =================
        if ($request->hasFile('images')) {

            foreach ($request->file('images') as $img) {

                $path = $img->store('products', 'public');

                $product->images()->create([
                    'path' => $path
                ]);
            }
        }

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Producto creado correctamente');
    }

    // ================= EDIT =================
    public function edit($id)
    {
        $product = Product::findOrFail($id);

        $categories = Category::all();

        return view('admin.products.edit',
            compact('product','categories'));
    }

    // ================= UPDATE =================
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'category_id' => 'required|exists:categories,id'
        ],[
            'name.required' => 'El nombre es obligatorio',
            'price.required' => 'El precio es obligatorio',
            'category_id.required' => 'Selecciona una categoría',
            'category_id.exists' => 'La categoría no es válida'
        ]);

        $product->update([
            'name'        => $request->name,
            'description' => $request->description,
            'price'       => $request->price,
            'stock'       => $request->stock,
            'category_id' => $request->category_id
        ]);

        return redirect()
            ->route('admin.products.index')
            ->with('success','Producto actualizado correctamente');
    }
}