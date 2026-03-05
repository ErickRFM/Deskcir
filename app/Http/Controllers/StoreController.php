<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    /**
     * Mostrar tienda principal
     * Categorías con productos + búsqueda
     */
    /** */
public function index(Request $request)
{
    $query = $request->q;

    $categories = Category::with(['products' => function($q) use ($query){

        if($query){
            $q->where(function($sub) use ($query){
                $sub->where('name','like',"%{$query}%")
                    ->orWhere('description','like',"%{$query}%");
            });
        }

        // 👇 seguimos cargando imágenes normalmente
        $q->with('images');

    }])->get();

    // contador de resultados reales
    $totalResults = 0;

    if($query){
        foreach($categories as $cat){
            $totalResults += $cat->products->count();
        }
    }

    return view('store.index', compact('categories','totalResults','query'));
}

    /**
     * Mostrar productos por categoría
     */
    public function category($slug)
    {
        $category = Category::where('slug', $slug)
            ->with('products')
            ->firstOrFail();

        return view('store.category', compact('category'));
    }

    /**
     * Mostrar detalle de producto
     */
    public function show($id)
    {
        $product = Product::findOrFail($id);
        return view('store.show', compact('product'));
    }

    /**
     * Ver carrito
     */
    public function cart()
    {
        $cart = session()->get('cart', []);
        return view('store.cart', compact('cart'));
    }

    /**
     * Agregar producto al carrito
     */
    public function addToCart($id)
    {
        $product = Product::findOrFail($id);

        $cart = session()->get('cart', []);

        $cart[$id] = [
            'name'  => $product->name,
            'price' => $product->price,
            'qty'   => ($cart[$id]['qty'] ?? 0) + 1,
        ];

        session()->put('cart', $cart);

        return back();
    }
}