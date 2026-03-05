<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Order;
use App\Models\Ticket;

class HistoryController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        $ticketCount = Ticket::where('user_id', $userId)->count();
        $appointmentCount = Appointment::where('user_id', $userId)->count();
        $orderCount = Order::where('user_id', $userId)->count();

        $tickets = Ticket::where('user_id', $userId)
            ->latest()
            ->take(10)
            ->get();

        $appointments = Appointment::with(['technician:id,name', 'ticket:id,subject'])
            ->where('user_id', $userId)
            ->orderByDesc('date')
            ->orderByDesc('time')
            ->take(10)
            ->get();

        $orders = Order::where('user_id', $userId)
            ->latest()
            ->take(10)
            ->get();

        return view('client.history', compact(
            'ticketCount',
            'appointmentCount',
            'orderCount',
            'tickets',
            'appointments',
            'orders'
        ));
    }
}