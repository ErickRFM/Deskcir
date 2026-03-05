<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Models\Ticket;
use App\Models\Product;
use Carbon\Carbon;
use DB;

class AdminController extends Controller
{
    public function dashboard()
    {
        // ventas hoy
        $ventasHoy = Order::whereDate('created_at', Carbon::today())->sum('total');

        // ventas ayer
        $ventasAyer = Order::whereDate('created_at', Carbon::yesterday())->sum('total');

        // calcular crecimiento
        if ($ventasAyer > 0) {
            $crecimiento = round((($ventasHoy - $ventasAyer) / $ventasAyer) * 100, 2);
        } else {
            $crecimiento = $ventasHoy > 0 ? 100 : 0;
        }

        // total pedidos
        $pedidos = Order::count();

        // clientes
        $clientes = User::where('role_id',3)->count();

        // tickets abiertos
        $ticketsAbiertos = Ticket::where('status','abierto')->count();

        // ingresos del mes
        $ventasMes = Order::whereMonth('created_at', Carbon::now()->month)
        ->sum('total');

        // técnicos activos
        $tecnicosActivos = User::where('role_id',2)->count();

        // top productos
       $topProductos = Product::withCount('orderItems')
       ->orderBy('order_items_count','desc')
       ->limit(5)
    ->get();

        // tickets recientes
        $ticketsRecientes = Ticket::latest()
        ->limit(5)
        ->get();

        // últimas ventas
        $ultimasVentas = Order::with('user')
        ->latest()
        ->limit(5)
        ->get();

        // ventas últimos 7 días (grafica)
        $ventasGrafica = Order::select(
            DB::raw('DATE(created_at) as fecha'),
            DB::raw('SUM(total) as total')
        )
        ->where('created_at','>=',Carbon::now()->subDays(7))
        ->groupBy('fecha')
        ->orderBy('fecha')
        ->get();

        return view('admin.dashboard',[
            'ventasHoy'=>$ventasHoy,
            'pedidos'=>$pedidos,
            'clientes'=>$clientes,
            'ticketsAbiertos'=>$ticketsAbiertos,
            'crecimiento'=>$crecimiento,
            'ventasMes'=>$ventasMes,
            'tecnicosActivos'=>$tecnicosActivos,
            'topProductos'=>$topProductos,
            'ticketsRecientes'=>$ticketsRecientes,
            'ultimasVentas'=>$ultimasVentas,
            'ventasGrafica'=>$ventasGrafica
        ]);
    }
}