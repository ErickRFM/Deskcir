<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect('/cart')->with('error', 'El carrito esta vacio.');
        }

        $summary = $this->buildSummary($cart, 'shipping');

        $cards = Card::query()
            ->where('user_id', auth()->id())
            ->orderByDesc('is_default')
            ->orderByDesc('id')
            ->get();

        $pickupPoints = $this->pickupPoints();

        return view('checkout', compact('cart', 'summary', 'cards', 'pickupPoints'));
    }

    public function store(Request $request)
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return back()->with('error', 'Carrito vacio.');
        }

        $validated = $request->validate([
            'delivery_type' => 'required|in:shipping,pickup',
            'address' => 'required_if:delivery_type,shipping|nullable|string|max:255',
            'city' => 'required_if:delivery_type,shipping|nullable|string|max:120',
            'postal_code' => 'required_if:delivery_type,shipping|nullable|string|max:20',
            'phone' => 'required|string|max:30',
            'pickup_point' => 'required_if:delivery_type,pickup|nullable|string|max:120',
            'delivery_notes' => 'nullable|string|max:500',
            'payment_method' => 'required|in:card_saved,card_new,transfer,cash,wallet,bitcoin',
            'saved_card_id' => 'nullable|integer',
            'card_number' => 'nullable|string|max:25',
            'card_holder' => 'nullable|string|max:120',
            'card_exp_month' => 'nullable|integer|min:1|max:12',
            'card_exp_year' => 'nullable|integer|min:2024|max:2100',
            'card_cvv' => 'nullable|string|max:4',
            'save_card' => 'nullable|boolean',
            'make_default_card' => 'nullable|boolean',
        ]);

        $summary = $this->buildSummary($cart, $validated['delivery_type']);
        $user = auth()->user();

        $selectedCard = null;
        $paymentMethod = $validated['payment_method'];

        if ($paymentMethod === 'card_saved') {
            $selectedCard = Card::query()
                ->where('id', $validated['saved_card_id'] ?? 0)
                ->where('user_id', $user->id)
                ->first();

            if (! $selectedCard) {
                return back()->withInput()->with('error', 'Selecciona una tarjeta guardada valida.');
            }

            if ($request->boolean('make_default_card')) {
                Card::where('user_id', $user->id)->update(['is_default' => false]);
                $selectedCard->update(['is_default' => true]);
            }
        }

        $cardNumberRaw = null;
        if ($paymentMethod === 'card_new') {
            $request->validate([
                'card_number' => 'required|string|min:13|max:25',
                'card_holder' => 'required|string|max:120',
                'card_exp_month' => 'required|integer|min:1|max:12',
                'card_exp_year' => 'required|integer|min:2024|max:2100',
                'card_cvv' => 'required|string|min:3|max:4',
            ]);

            $cardNumberRaw = preg_replace('/\D+/', '', (string) $request->input('card_number'));

            if ($request->boolean('save_card')) {
                if ($request->boolean('make_default_card')) {
                    Card::where('user_id', $user->id)->update(['is_default' => false]);
                }

                $selectedCard = Card::create([
                    'user_id' => $user->id,
                    'mp_id' => 'manual_' . Str::upper(Str::random(12)),
                    'brand' => $this->detectBrand($request->input('card_number')),
                    'last4' => substr($cardNumberRaw, -4),
                    'alias' => 'Tarjeta ' . substr($cardNumberRaw, -4),
                    'exp_month' => (int) $request->input('card_exp_month'),
                    'exp_year' => (int) $request->input('card_exp_year'),
                    'is_default' => $request->boolean('make_default_card'),
                ]);
            }
        }

        if ($paymentMethod === 'wallet' && (float) $user->wallet_balance < (float) $summary['total']) {
            return back()->withInput()->with('error', 'Saldo insuficiente en billetera para completar la compra.');
        }

        $address = $validated['delivery_type'] === 'shipping'
            ? $validated['address']
            : 'Punto de entrega: ' . ($validated['pickup_point'] ?? 'No especificado');

        $city = $validated['delivery_type'] === 'shipping' ? $validated['city'] : 'Entrega en punto';
        $postalCode = $validated['delivery_type'] === 'shipping' ? $validated['postal_code'] : '00000';

        $paymentStatus = in_array($paymentMethod, ['wallet', 'card_saved', 'card_new'], true)
            ? 'paid'
            : 'pending';

        $paymentReference = match ($paymentMethod) {
            'wallet' => 'WLT-' . Str::upper(Str::random(8)),
            'card_saved', 'card_new' => 'CARD-' . Str::upper(Str::random(8)),
            'transfer' => 'TRF-' . Str::upper(Str::random(8)),
            'cash' => 'CASH-' . Str::upper(Str::random(8)),
            'bitcoin' => 'CRYPTO-' . Str::upper(Str::random(8)),
            default => null,
        };

        $paymentMeta = [
            'channel' => $paymentMethod,
        ];

        if ($paymentMethod === 'card_saved' && $selectedCard) {
            $paymentMeta['card_brand'] = $selectedCard->brand;
            $paymentMeta['card_last4'] = $selectedCard->last4;
        }

        if ($paymentMethod === 'card_new') {
            $paymentMeta['card_brand'] = $this->detectBrand((string) $request->input('card_number'));
            $paymentMeta['card_last4'] = $cardNumberRaw ? substr($cardNumberRaw, -4) : null;
        }

        if ($paymentMethod === 'transfer') {
            $paymentMeta['bank'] = 'SPEI';
        }

        if ($paymentMethod === 'cash') {
            $paymentMeta['note'] = 'Pago contra entrega o en punto';
        }

        if ($paymentMethod === 'bitcoin') {
            $paymentMeta['network'] = 'BTC/ETH/USDT';
        }

        $orderId = null;

        DB::transaction(function () use (
            $user,
            $paymentMethod,
            $selectedCard,
            $validated,
            $summary,
            $cart,
            $address,
            $city,
            $postalCode,
            $paymentStatus,
            $paymentReference,
            $paymentMeta,
            &$orderId
        ) {
            $order = Order::create([
                'user_id' => $user->id,
                'payment_method' => $paymentMethod,
                'card_id' => $selectedCard?->id,
                'status' => 'pendiente',
                'address' => $address,
                'city' => $city,
                'postal_code' => $postalCode,
                'phone' => $validated['phone'],
                'subtotal' => $summary['subtotal'],
                'shipping_fee' => $summary['shipping_fee'],
                'service_fee' => $summary['service_fee'],
                'discount' => $summary['discount'],
                'wallet_used' => $paymentMethod === 'wallet' ? $summary['total'] : 0,
                'delivery_type' => $validated['delivery_type'],
                'pickup_point' => $validated['pickup_point'] ?? null,
                'delivery_notes' => $validated['delivery_notes'] ?? null,
                'tracking_code' => 'DSK-' . date('ymd') . '-' . strtoupper(Str::random(6)),
                'total' => $summary['total'],
            ]);

            foreach ($cart as $productId => $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => (int) $productId,
                    'qty' => (int) ($item['qty'] ?? 1),
                    'price' => (float) ($item['price'] ?? 0),
                ]);
            }

            Payment::create([
                'order_id' => $order->id,
                'method' => $paymentMethod,
                'status' => $paymentStatus,
                'amount' => $summary['total'],
                'reference' => $paymentReference,
                'paid_at' => $paymentStatus === 'paid' ? now() : null,
                'meta' => $paymentMeta,
            ]);

            if ($paymentMethod === 'wallet') {
                $user->wallet_balance = (float) $user->wallet_balance - (float) $summary['total'];
                $user->save();

                WalletTransaction::create([
                    'user_id' => $user->id,
                    'order_id' => $order->id,
                    'type' => 'purchase',
                    'amount' => (float) $summary['total'],
                    'reference' => $order->tracking_code,
                    'status' => 'completed',
                ]);
            }

            $orderId = $order->id;
        });

        session()->forget('cart');

        return redirect()->route('checkout.show', ['id' => $orderId])
            ->with('success', 'Compra registrada correctamente.');
    }

    public function show($id)
    {
        $order = Order::query()
            ->with(['items.product', 'card', 'payment'])
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        $statusSteps = [
            'pendiente' => 1,
            'en camino' => 2,
            'entregado' => 3,
            'cancelado' => 4,
        ];

        return view('checkout-tracking', compact('order', 'statusSteps'));
    }

    private function buildSummary(array $cart, string $deliveryType): array
    {
        $subtotal = 0;

        foreach ($cart as $item) {
            $subtotal += ((float) ($item['price'] ?? 0)) * ((int) ($item['qty'] ?? 1));
        }

        $shippingFee = $deliveryType === 'shipping' ? 79.00 : 0.00;
        $serviceFee = 15.00;
        $discount = 0.00;
        $total = max(0, $subtotal + $shippingFee + $serviceFee - $discount);

        return [
            'subtotal' => round($subtotal, 2),
            'shipping_fee' => round($shippingFee, 2),
            'service_fee' => round($serviceFee, 2),
            'discount' => round($discount, 2),
            'total' => round($total, 2),
        ];
    }

    private function detectBrand(string $number): string
    {
        $clean = preg_replace('/\D+/', '', $number);

        if (preg_match('/^4/', $clean)) {
            return 'Visa';
        }

        if (preg_match('/^5[1-5]/', $clean)) {
            return 'Mastercard';
        }

        if (preg_match('/^3[47]/', $clean)) {
            return 'Amex';
        }

        return 'Tarjeta';
    }

    private function pickupPoints(): array
    {
        return [
            'Deskcir Centro - Reforma 120',
            'Deskcir Norte - Plaza Tech Monterrey',
            'Deskcir Sur - Galerias Coapa',
            'Deskcir Express - Punto Parque Delta',
        ];
    }
}
