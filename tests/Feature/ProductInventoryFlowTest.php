<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ProductInventoryFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_image_route_serves_legacy_local_files(): void
    {
        Storage::fake('local');

        $category = Category::create([
            'name' => 'Laptops',
            'slug' => 'laptops',
        ]);

        $product = Product::create([
            'name' => 'Laptop demo',
            'slug' => 'laptop-demo',
            'description' => 'Equipo de prueba',
            'price' => 15000,
            'stock' => 3,
            'category_id' => $category->id,
        ]);

        Storage::disk('local')->put('products/demo-image.txt', 'deskcir-image');

        $image = ProductImage::create([
            'product_id' => $product->id,
            'path' => 'products/demo-image.txt',
            'disk' => null,
        ]);

        $response = $this->get(route('products.images.file', $image));

        $response->assertOk();
        $this->assertSame('deskcir-image', $response->streamedContent());
    }

    public function test_admin_product_upload_uses_public_disk_in_local_environment(): void
    {
        Storage::fake('public');

        $adminRole = Role::query()->firstOrCreate(['name' => 'admin']);
        $admin = User::factory()->create(['role_id' => $adminRole->id]);
        $category = Category::create([
            'name' => 'Laptops',
            'slug' => 'laptops',
        ]);

        $response = $this->actingAs($admin)->post('/admin/products', [
            'name' => 'Laptop con imagen',
            'description' => 'Equipo de prueba',
            'price' => 19999.99,
            'stock' => 5,
            'category_id' => $category->id,
            'images' => [
                UploadedFile::fake()->image('equipo.jpg', 1200, 900),
            ],
        ]);

        $response->assertRedirect(route('admin.products.index'));

        $image = ProductImage::query()->latest('id')->first();

        $this->assertNotNull($image);
        $this->assertSame('public', $image->disk);
        Storage::disk('public')->assertExists($image->normalizedPath());
    }

    public function test_checkout_reduces_product_stock_after_purchase(): void
    {
        $user = User::factory()->create();
        $category = Category::create([
            'name' => 'Accesorios',
            'slug' => 'accesorios',
        ]);

        $product = Product::create([
            'name' => 'Mouse gamer',
            'slug' => 'mouse-gamer',
            'description' => 'Mouse RGB',
            'price' => 999.99,
            'stock' => 3,
            'category_id' => $category->id,
        ]);

        $response = $this->actingAs($user)
            ->withSession([
                'cart' => [
                    (string) $product->id => [
                        'name' => $product->name,
                        'price' => (float) $product->price,
                        'qty' => 2,
                    ],
                ],
            ])
            ->post('/checkout', [
                'delivery_type' => 'shipping',
                'address' => 'Calle 123',
                'city' => 'Monterrey',
                'postal_code' => '64000',
                'phone' => '8112345678',
                'payment_method' => 'cash',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
        ]);
        $this->assertDatabaseHas('order_items', [
            'product_id' => $product->id,
            'qty' => 2,
        ]);
        $this->assertSame(1, (int) $product->fresh()->stock);
    }

    public function test_checkout_redirects_to_cart_when_stock_changes(): void
    {
        $user = User::factory()->create();
        $category = Category::create([
            'name' => 'Refacciones',
            'slug' => 'refacciones',
        ]);

        $product = Product::create([
            'name' => 'SSD 1TB',
            'slug' => 'ssd-1tb',
            'description' => 'Unidad solida',
            'price' => 1200,
            'stock' => 1,
            'category_id' => $category->id,
        ]);

        $response = $this->actingAs($user)
            ->withSession([
                'cart' => [
                    (string) $product->id => [
                        'name' => $product->name,
                        'price' => (float) $product->price,
                        'qty' => 2,
                    ],
                ],
            ])
            ->post('/checkout', [
                'delivery_type' => 'shipping',
                'address' => 'Calle 123',
                'city' => 'Monterrey',
                'postal_code' => '64000',
                'phone' => '8112345678',
                'payment_method' => 'cash',
            ]);

        $response
            ->assertRedirect('/cart')
            ->assertSessionHas('error');

        $response->assertSessionHas("cart.{$product->id}.qty", 1);
        $this->assertSame(1, (int) $product->fresh()->stock);
        $this->assertSame(0, Order::count());
    }

    public function test_admin_cannot_delete_product_with_sales_history(): void
    {
        $adminRole = Role::query()->firstOrCreate(['name' => 'admin']);
        $clientRole = Role::query()->firstOrCreate(['name' => 'client']);

        $admin = User::factory()->create(['role_id' => $adminRole->id]);
        $buyer = User::factory()->create(['role_id' => $clientRole->id]);
        $category = Category::create([
            'name' => 'Gabinetes',
            'slug' => 'gabinetes',
        ]);

        $product = Product::create([
            'name' => 'Gabinete Pro',
            'slug' => 'gabinete-pro',
            'description' => 'Gabinete ATX',
            'price' => 1899,
            'stock' => 4,
            'category_id' => $category->id,
        ]);

        $order = Order::create([
            'user_id' => $buyer->id,
            'payment_method' => 'cash',
            'status' => 'pendiente',
            'address' => 'Calle 123',
            'city' => 'Monterrey',
            'postal_code' => '64000',
            'phone' => '8112345678',
            'subtotal' => 1899,
            'shipping_fee' => 79,
            'service_fee' => 15,
            'discount' => 0,
            'wallet_used' => 0,
            'delivery_type' => 'shipping',
            'pickup_point' => null,
            'delivery_notes' => null,
            'tracking_code' => 'DSK-TEST-001',
            'total' => 1993,
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'qty' => 1,
            'price' => 1899,
        ]);

        $response = $this->actingAs($admin)
            ->delete(route('admin.products.destroy', $product));

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertNotNull($product->fresh());
    }
}
