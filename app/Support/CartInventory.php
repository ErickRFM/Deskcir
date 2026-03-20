<?php

namespace App\Support;

use App\Models\Product;

class CartInventory
{
    public function refresh(array $cart): array
    {
        if ($cart === []) {
            return [
                'cart' => [],
                'alerts' => [],
                'changed' => false,
                'inventory_changed' => false,
            ];
        }

        $productIds = collect(array_keys($cart))
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0)
            ->values();

        $products = Product::query()
            ->with('images')
            ->whereIn('id', $productIds)
            ->get()
            ->keyBy(fn (Product $product) => (string) $product->id);

        $normalized = [];
        $alerts = [];
        $changed = false;
        $inventoryChanged = false;

        foreach ($cart as $productId => $item) {
            $productKey = (string) ((int) $productId);
            $product = $products->get($productKey);

            if (! $product) {
                $alerts[] = 'Retiramos un producto del carrito porque ya no existe.';
                $changed = true;
                $inventoryChanged = true;
                continue;
            }

            $stock = max(0, (int) $product->stock);

            if ($stock < 1) {
                $alerts[] = "{$product->name} ya no tiene stock y se retiro del carrito.";
                $changed = true;
                $inventoryChanged = true;
                continue;
            }

            $requestedQty = max(1, (int) ($item['qty'] ?? 1));
            $finalQty = min($requestedQty, $stock);

            if ($finalQty !== $requestedQty) {
                $unitLabel = $finalQty === 1 ? 'unidad' : 'unidades';
                $alerts[] = "Ajustamos {$product->name} a {$finalQty} {$unitLabel} por stock disponible.";
                $changed = true;
                $inventoryChanged = true;
            }

            $freshItem = $this->makeItem($product, $finalQty);
            $normalized[$productKey] = $freshItem;

            if (! $changed && $this->itemChanged($item, $freshItem)) {
                $changed = true;
            }
        }

        return [
            'cart' => $normalized,
            'alerts' => array_values(array_unique($alerts)),
            'changed' => $changed,
            'inventory_changed' => $inventoryChanged,
        ];
    }

    public function add(array $cart, Product $product, int $requestedQty): array
    {
        $sync = $this->refresh($cart);
        $cart = $sync['cart'];
        $stock = max(0, (int) $product->stock);

        if ($stock < 1) {
            return [
                'cart' => $cart,
                'level' => 'error',
                'message' => "{$product->name} no tiene stock disponible por ahora.",
            ];
        }

        $existingQty = (int) ($cart[(string) $product->id]['qty'] ?? 0);
        $remaining = max(0, $stock - $existingQty);

        if ($remaining < 1) {
            return [
                'cart' => $cart,
                'level' => 'error',
                'message' => "Ya alcanzaste el stock disponible de {$product->name}.",
            ];
        }

        $requestedQty = max(1, $requestedQty);
        $addedQty = min($requestedQty, $remaining);

        $cart[(string) $product->id] = $this->makeItem($product, $existingQty + $addedQty);

        $message = $addedQty < $requestedQty
            ? "Solo agregamos {$addedQty} unidad" . ($addedQty === 1 ? '' : 'es') . " de {$product->name} por stock disponible."
            : 'Producto agregado al carrito.';

        return [
            'cart' => $cart,
            'level' => 'success',
            'message' => $message,
        ];
    }

    private function makeItem(Product $product, int $qty): array
    {
        return [
            'name' => $product->name,
            'price' => (float) $product->price,
            'qty' => max(1, $qty),
            'image_url' => $product->image_url,
            'available_stock' => max(0, (int) $product->stock),
        ];
    }

    private function itemChanged(array $current, array $fresh): bool
    {
        return (string) ($current['name'] ?? '') !== (string) $fresh['name']
            || (float) ($current['price'] ?? 0) !== (float) $fresh['price']
            || (int) ($current['qty'] ?? 0) !== (int) $fresh['qty']
            || (string) ($current['image_url'] ?? '') !== (string) ($fresh['image_url'] ?? '')
            || (int) ($current['available_stock'] ?? -1) !== (int) $fresh['available_stock'];
    }
}
