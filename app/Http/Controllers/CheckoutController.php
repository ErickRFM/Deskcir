<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;

class CheckoutController extends Controller
{
    //  MOSTRAR VISTA DE CHECKOUT
    public function index()
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect('/cart')
                ->with('error', 'El carrito está vacío');
        }

        return view('checkout');
    }

    //  GUARDAR ORDEN
    public function store(Request $r)
    {
        // Validación básica
        $r->validate([
            'payment_method' => 'required',
            'address'        => 'required',
            'city'           => 'required',
            'postal_code'    => 'required',
            'phone'          => 'required'
        ]);

        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return back()->with('error','Carrito vacío');
        }

        // Calcular total
        $total = 0;

        foreach ($cart as $item) {
            $total += $item['price'] * $item['qty'];
        }

        // Crear orden
        Order::create([
            'user_id' => auth()->id(),
            'payment_method' => $r->payment_method,
            'address' => $r->address,
            'city' => $r->city,
            'postal_code' => $r->postal_code,
            'phone' => $r->phone,
            'total' => $total
        ]);

        // 🧹 Limpiar carrito
        session()->forget('cart');

        return redirect('/store')
            ->with('success','Compra realizada con éxito');
    }
}