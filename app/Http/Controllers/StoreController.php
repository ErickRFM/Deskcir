<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->q;

        $categories = Category::with(['products' => function ($q) use ($query) {
            if ($query) {
                $q->where(function ($sub) use ($query) {
                    $sub->where('name', 'like', "%{$query}%")
                        ->orWhere('description', 'like', "%{$query}%");
                });
            }

            $q->with('images');
        }])->get();

        $totalResults = 0;

        if ($query) {
            foreach ($categories as $cat) {
                $totalResults += $cat->products->count();
            }
        }

        return view('store.index', compact('categories', 'totalResults', 'query'));
    }

    public function category($slug)
    {
        $category = Category::where('slug', $slug)
            ->with('products')
            ->firstOrFail();

        return view('store.category', compact('category'));
    }

    public function show($id)
    {
        $product = Product::with(['images', 'category'])->findOrFail($id);

        $relatedProducts = Product::with('images')
            ->where('category_id', $product->category_id)
            ->whereKeyNot($product->id)
            ->latest('id')
            ->take(8)
            ->get();

        return view('store.show', compact('product', 'relatedProducts'));
    }

    public function cart()
    {
        $cart = session()->get('cart', []);
        return view('store.cart', compact('cart'));
    }

    public function addToCart(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $cart = session()->get('cart', []);
        $qty = max(1, (int) $request->input('qty', 1));

        $cart[$id] = [
            'name' => $product->name,
            'price' => $product->price,
            'qty' => ($cart[$id]['qty'] ?? 0) + $qty,
        ];

        session()->put('cart', $cart);

        if ((int) $request->input('buy_now', 0) === 1) {
            return redirect('/checkout');
        }

        return back()->with('success', 'Producto agregado al carrito.');
    }
}
