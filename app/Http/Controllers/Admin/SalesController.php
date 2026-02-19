<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

class SalesController extends Controller
{
    public function index()
    {
        $orders = Order::with('user')
            ->orderBy('created_at','desc')
            ->paginate(20);

        return view('admin.sales.index', compact('orders'));
    }

    public function updateStatus(Request $r, $id)
    {
        $r->validate([
            'status' => 'required'
        ]);

        $order = Order::findOrFail($id);

        $order->update([
            'status' => $r->status
        ]);

        return back()->with('success','Estado actualizado');
    }
}