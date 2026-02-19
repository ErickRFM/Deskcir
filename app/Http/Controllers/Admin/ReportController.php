<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use PDF;

class ReportController extends Controller
{

public function dashboard()
{
    $total = Order::sum('total');

    $pedidos = Order::count();

    $ticket = $pedidos > 0 ? $total / $pedidos : 0;

    $clientes = User::count();

    $entregados = Order::where('status','entregado')->count();

    // ventas últimos 30 días
    $dias = Order::selectRaw('DATE(created_at) as dia, SUM(total) as total')
        ->where('created_at','>=',Carbon::now()->subDays(30))
        ->groupBy('dia')
        ->get();

    // productos más vendidos
    $top = Product::withCount('orderItems')
        ->orderBy('order_items_count','desc')
        ->take(5)
        ->get();

    return view('admin.reports.dashboard',compact(
        'total','pedidos','ticket','clientes','entregados','dias','top'
    ));
}

public function sales()
{
    $orders = Order::with('user')->latest()->paginate(20);

    return view('admin.reports.sales',compact('orders'));
}

public function products()
{
    $products = Product::withCount('orderItems')->get();

    return view('admin.reports.products',compact('products'));
}

public function clients()
{
    $users = User::withCount('orders')->get();

    return view('admin.reports.clients',compact('users'));
}

public function excel()
{
    $orders = Order::with('user')->get();

    return Excel::download(new \App\Exports\OrdersExport($orders),'ventas.xlsx');
}

public function pdf()
{
    $orders = Order::with('user')->get();

    $pdf = PDF::loadView('admin.reports.pdf',compact('orders'));

    return $pdf->download('ventas.pdf');
}

}