<?php

namespace App\Http\Controllers;

use App\Support\CartInventory;

class CartController extends Controller
{
    public function index(CartInventory $cartInventory)
    {
        $sync = $cartInventory->refresh(session()->get('cart', []));
        $cart = $sync['cart'];

        session()->put('cart', $cart);

        $cartAlerts = $sync['alerts'];

        return view('store.cart', compact('cart', 'cartAlerts'));
    }

    public function remove($id)
    {
        $cart = session()->get('cart', []);

        unset($cart[$id]);

        session()->put('cart', $cart);

        return back()->with('success','Producto eliminado');
    }
}
