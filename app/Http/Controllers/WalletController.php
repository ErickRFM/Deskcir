<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class WalletController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $cards = Card::query()
            ->where('user_id', $user->id)
            ->orderByDesc('is_default')
            ->orderByDesc('id')
            ->get();

        $transactions = WalletTransaction::query()
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(15);

        return view('wallet.index', compact('user', 'transactions', 'cards'));
    }

    public function topup(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:50|max:50000',
        ]);

        $user = auth()->user();
        $amount = round((float) $validated['amount'], 2);

        DB::transaction(function () use ($user, $amount) {
            $user->wallet_balance = round((float) $user->wallet_balance + $amount, 2);
            $user->save();

            WalletTransaction::create([
                'user_id' => $user->id,
                'type' => 'topup',
                'amount' => $amount,
                'reference' => 'TOPUP-' . strtoupper(Str::random(8)),
                'status' => 'completed',
            ]);
        });

        return back()->with('success', 'Recarga aplicada correctamente.');
    }
}
