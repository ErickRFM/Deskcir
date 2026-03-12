<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function update(Request $request, Payment $payment)
    {
        $data = $request->validate([
            'status' => ['required', 'in:pending,paid,failed,cancelled'],
        ]);

        $paidAt = $data['status'] === 'paid'
            ? ($payment->paid_at ?? now())
            : null;

        $payment->update([
            'status' => $data['status'],
            'paid_at' => $paidAt,
        ]);

        return back()->with('success', 'Estado de pago actualizado.');
    }
}
