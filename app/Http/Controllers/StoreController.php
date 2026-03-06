<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function index(Request $request)
    {
        $query = trim((string) $request->input('q', ''));
        $hasSearch = $query !== '';
        $quick = (string) $request->input('quick', '');
        $minPrice = $request->filled('min_price') ? (float) $request->input('min_price') : null;
        $maxPrice = $request->filled('max_price') ? (float) $request->input('max_price') : null;
        $categoryId = $request->filled('category') ? (int) $request->input('category') : null;
        $availability = (string) $request->input('availability', 'all');
        $sort = (string) $request->input('sort', 'newest');
        $hasImage = $request->boolean('has_image', false);

        if (!in_array($availability, ['all', 'in_stock', 'out_of_stock'], true)) {
            $availability = 'all';
        }

        if (!in_array($sort, ['newest', 'oldest', 'price_asc', 'price_desc', 'name_asc', 'name_desc'], true)) {
            $sort = 'newest';
        }

        if (!$hasSearch) {
            $quick = '';
            $minPrice = null;
            $maxPrice = null;
            $categoryId = null;
            $availability = 'all';
            $sort = 'newest';
            $hasImage = false;
        }

        $validQuick = ['offers', 'sale', 'defective', 'popular'];
        if (!in_array($quick, $validQuick, true)) {
            $quick = '';
        }

        if ($minPrice !== null && $maxPrice !== null && $minPrice > $maxPrice) {
            [$minPrice, $maxPrice] = [$maxPrice, $minPrice];
        }

        $avgPrice = (float) (Product::query()->avg('price') ?? 0);

        $productFilters = function ($builder) use (
            $query,
            $quick,
            $minPrice,
            $maxPrice,
            $availability,
            $hasImage,
            $avgPrice
        ) {
            if ($query !== '') {
                $builder->where(function ($sub) use ($query) {
                    $sub->where('name', 'like', "%{$query}%")
                        ->orWhere('description', 'like', "%{$query}%");
                });
            }

            if ($quick === 'offers') {
                $threshold = $avgPrice > 0 ? round($avgPrice * 0.9, 2) : null;
                if ($threshold !== null) {
                    $builder->where('price', '<=', $threshold)->where('stock', '>', 0);
                }
            } elseif ($quick === 'sale') {
                $builder->where(function ($sub) {
                    $sub->where('name', 'like', '%rebaja%')
                        ->orWhere('name', 'like', '%oferta%')
                        ->orWhere('name', 'like', '%promo%')
                        ->orWhere('description', 'like', '%rebaja%')
                        ->orWhere('description', 'like', '%oferta%')
                        ->orWhere('description', 'like', '%descuento%')
                        ->orWhere('description', 'like', '%promo%');
                });
            } elseif ($quick === 'defective') {
                $builder->where(function ($sub) {
                    $sub->where('name', 'like', '%defect%')
                        ->orWhere('name', 'like', '%detalle%')
                        ->orWhere('name', 'like', '%usad%')
                        ->orWhere('name', 'like', '%open box%')
                        ->orWhere('description', 'like', '%defect%')
                        ->orWhere('description', 'like', '%detalle%')
                        ->orWhere('description', 'like', '%usad%')
                        ->orWhere('description', 'like', '%open box%');
                });
            }

            if ($minPrice !== null) {
                $builder->where('price', '>=', $minPrice);
            }

            if ($maxPrice !== null) {
                $builder->where('price', '<=', $maxPrice);
            }

            if ($availability === 'in_stock') {
                $builder->where('stock', '>', 0);
            } elseif ($availability === 'out_of_stock') {
                $builder->where('stock', '<=', 0);
            }

            if ($hasImage) {
                $builder->where(function ($sub) {
                    $sub->whereHas('images')
                        ->orWhereNotNull('image');
                });
            }
        };

        if (!$hasSearch) {
            $categories = Category::query()
                ->orderBy('name')
                ->whereHas('products')
                ->with(['products' => function ($builder) {
                    $builder->with('images')->orderByDesc('id');
                }])
                ->get();
        } else {
            $categoriesQuery = Category::query()->orderBy('name');

            if ($categoryId) {
                $categoriesQuery->whereKey($categoryId);
            }

            $categories = $categoriesQuery
                ->whereHas('products', $productFilters)
                ->with(['products' => function ($builder) use ($productFilters, $sort, $quick) {
                    $productFilters($builder);
                    $builder->with('images');

                    if ($quick === 'popular') {
                        $builder->withCount('orderItems')->orderByDesc('order_items_count')->orderByDesc('id');
                        return;
                    }

                    if ($sort === 'oldest') {
                        $builder->orderBy('id');
                    } elseif ($sort === 'price_asc') {
                        $builder->orderBy('price');
                    } elseif ($sort === 'price_desc') {
                        $builder->orderByDesc('price');
                    } elseif ($sort === 'name_asc') {
                        $builder->orderBy('name');
                    } elseif ($sort === 'name_desc') {
                        $builder->orderByDesc('name');
                    } else {
                        $builder->orderByDesc('id');
                    }
                }])
                ->get();
        }

        $totalResults = $categories->sum(fn ($category) => $category->products->count());

        $categoryOptions = Category::query()
            ->withCount('products')
            ->orderBy('name')
            ->get();

        $priceRange = Product::query()
            ->selectRaw('MIN(price) as min_price, MAX(price) as max_price')
            ->first();

        $popularProducts = Product::query()
            ->with('images')
            ->withCount('orderItems')
            ->orderByDesc('order_items_count')
            ->orderByDesc('id')
            ->take(6)
            ->get();

        $filters = [
            'q' => $query,
            'quick' => $quick,
            'min_price' => $minPrice,
            'max_price' => $maxPrice,
            'category' => $categoryId,
            'availability' => $availability,
            'sort' => $sort,
            'has_image' => $hasImage,
        ];

        $activeFilters = collect($filters)->filter(function ($value, $key) {
            if ($key === 'availability') {
                return $value !== 'all';
            }

            if ($key === 'sort') {
                return $value !== 'newest';
            }

            if (is_bool($value)) {
                return $value;
            }

            return $value !== null && $value !== '';
        })->count();

        return view('store.index', compact(
            'categories',
            'categoryOptions',
            'totalResults',
            'priceRange',
            'filters',
            'activeFilters',
            'popularProducts'
        ));
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

