<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\WalletTransaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class CashierController extends Controller
{
    public function dashboard()
    {
        $todaySales = Order::whereDate('created_at', Carbon::today())->sum('total');
        $todayOrders = Order::whereDate('created_at', Carbon::today())->count();
        $pendingOrders = Order::whereIn('status', ['pendiente', 'en camino'])->count();
        $todayTopups = WalletTransaction::where('type', 'topup')->whereDate('created_at', Carbon::today())->sum('amount');

        $recentOrders = Order::with('user')->latest()->limit(8)->get();
        $recentTopups = WalletTransaction::with('user')->where('type', 'topup')->latest()->limit(8)->get();
        $paymentMix = Order::selectRaw('payment_method, COUNT(*) as total')->groupBy('payment_method')->orderByDesc('total')->get();

        return view('cashier.dashboard', compact(
            'todaySales',
            'todayOrders',
            'pendingOrders',
            'todayTopups',
            'recentOrders',
            'recentTopups',
            'paymentMix'
        ));
    }

    public function profile()
    {
        $user = auth()->user();
        $todaySales = Order::whereDate('created_at', Carbon::today())->sum('total');
        $todayOrders = Order::whereDate('created_at', Carbon::today())->count();
        $pendingOrders = Order::whereIn('status', ['pendiente', 'en camino'])->count();
        $todayTopups = WalletTransaction::where('type', 'topup')->whereDate('created_at', Carbon::today())->sum('amount');

        return view('cashier.profile', compact('user', 'todaySales', 'todayOrders', 'pendingOrders', 'todayTopups'));
    }

    public function catalog(Request $request)
    {
        $search = trim((string) $request->get('q', ''));

        $products = Product::query()
            ->with(['images', 'category'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhereHas('category', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            })
            ->orderByDesc('id')
            ->limit(80)
            ->get();

        $categories = Category::query()->orderBy('name')->get();

        return view('cashier.catalog', compact('products', 'categories', 'search'));
    }

    public function storeProduct(Request $request)
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
                $path = $this->storeProductImage($img, $disk);

                ProductImage::create([
                    'product_id' => $product->id,
                    'path' => $path,
                    'disk' => $disk,
                ]);
            }
        }

        return redirect()
            ->route('cashier.catalog')
            ->with('success', 'Producto agregado correctamente');
    }

    private function mediaDisk(): string
    {
        $defaultDisk = (string) config('filesystems.default', 'public');

        if ($defaultDisk === 's3' && ! class_exists(\League\Flysystem\AwsS3V3\PortableVisibilityConverter::class)) {
            return 'public';
        }

        if ($defaultDisk === 's3' && ! $this->s3Configured()) {
            return 'public';
        }

        return $defaultDisk ?: 'public';
    }

    private function s3Configured(): bool
    {
        return ! blank(config('filesystems.disks.s3.key'))
            && ! blank(config('filesystems.disks.s3.secret'))
            && ! blank(config('filesystems.disks.s3.bucket'))
            && ! blank(config('filesystems.disks.s3.endpoint'));
    }

    private function storeProductImage(UploadedFile $img, string $disk): string
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

            Storage::disk($disk)->put($path, (string) $encoded, [
                'visibility' => 'public',
                'ContentType' => 'image/webp',
            ]);

            return $path;
        } catch (\Throwable $exception) {
            return $img->storePublicly('products', $disk);
        }
    }
}

