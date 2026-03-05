<?php

namespace App\Http\Controllers\Technician;

use App\Http\Controllers\Controller;
use App\Models\Ticket;

class TechnicianController extends Controller
{

    public function index()
    {

        $tickets = Ticket::with('checklist')
            ->where('technician_id', auth()->id())
            ->latest()
            ->take(5)
            ->get();

        return view('technician.dashboard', [
            'tickets' => $tickets
        ]);

    }

}