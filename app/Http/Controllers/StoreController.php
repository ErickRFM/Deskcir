<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    /**
     * Mostrar tienda principal
     * Categorías con productos
     */
    public function index()
    {
        $categories = Category::with('products.images')->get();

        return view('store.index', compact('categories'));
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