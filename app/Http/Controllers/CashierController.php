<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\WalletTransaction;
use Carbon\Carbon;

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
}
