<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Ticket;
use App\Models\User;
use Carbon\Carbon;
use DB;

class AdminController extends Controller
{
    public function dashboard()
    {
        $ventasHoy = Order::whereDate('created_at', Carbon::today())->sum('total');
        $ventasAyer = Order::whereDate('created_at', Carbon::yesterday())->sum('total');

        if ($ventasAyer > 0) {
            $crecimiento = round((($ventasHoy - $ventasAyer) / $ventasAyer) * 100, 2);
        } else {
            $crecimiento = $ventasHoy > 0 ? 100 : 0;
        }

        $pedidos = Order::count();
        $clientes = User::whereHas('role', fn ($query) => $query->where('name', 'client'))->count();
        $ticketsAbiertos = Ticket::whereIn('status', ['abierto', 'en_proceso'])->count();
        $ventasMes = Order::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('total');
        $tecnicosActivos = User::whereHas('role', fn ($query) => $query->where('name', 'technician'))->count();
        $topProductos = Product::withCount('orderItems')
            ->orderByDesc('order_items_count')
            ->limit(5)
            ->get();
        $ticketsRecientes = Ticket::with('user')
            ->latest()
            ->limit(5)
            ->get();
        $ultimasVentas = Order::with('user')
            ->latest()
            ->limit(5)
            ->get();
        $ventasGrafica = Order::select(
                DB::raw('DATE(created_at) as fecha'),
                DB::raw('SUM(total) as total')
            )
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get();

        return view('admin.dashboard', [
            'ventasHoy' => $ventasHoy,
            'pedidos' => $pedidos,
            'clientes' => $clientes,
            'ticketsAbiertos' => $ticketsAbiertos,
            'crecimiento' => $crecimiento,
            'ventasMes' => $ventasMes,
            'tecnicosActivos' => $tecnicosActivos,
            'topProductos' => $topProductos,
            'ticketsRecientes' => $ticketsRecientes,
            'ultimasVentas' => $ultimasVentas,
            'ventasGrafica' => $ventasGrafica,
        ]);
    }
}
