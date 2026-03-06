<?php

namespace App\Http\Controllers;

use App\Models\Card;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CardController extends Controller
{
    public function save(Request $request)
    {
        $validated = $request->validate([
            'mp_id' => 'nullable|string|max:120',
            'brand' => 'required|string|max:40',
            'last4' => 'required|string|size:4',
            'alias' => 'nullable|string|max:80',
            'exp_month' => 'nullable|integer|min:1|max:12',
            'exp_year' => 'nullable|integer|min:2024|max:2100',
            'is_default' => 'nullable|boolean',
        ]);

        if ($request->boolean('is_default')) {
            Card::where('user_id', auth()->id())->update(['is_default' => false]);
        }

        Card::create([
            'user_id' => auth()->id(),
            'mp_id' => $validated['mp_id'] ?? ('manual_' . Str::upper(Str::random(12))),
            'brand' => $validated['brand'],
            'last4' => $validated['last4'],
            'alias' => $validated['alias'] ?? ('Tarjeta ' . $validated['last4']),
            'exp_month' => $validated['exp_month'] ?? null,
            'exp_year' => $validated['exp_year'] ?? null,
            'is_default' => $request->boolean('is_default'),
        ]);

        return back()->with('success', 'Tarjeta guardada.');
    }

    public function saveCard(Request $request)
    {
        return $this->save($request);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'alias' => 'required|string|max:80',
            'exp_month' => 'nullable|integer|min:1|max:12',
            'exp_year' => 'nullable|integer|min:2024|max:2100',
            'is_default' => 'nullable|boolean',
        ]);

        $card = Card::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        if ($request->boolean('is_default')) {
            Card::where('user_id', auth()->id())->update(['is_default' => false]);
        }

        $card->update([
            'alias' => $validated['alias'],
            'exp_month' => $validated['exp_month'] ?? null,
            'exp_year' => $validated['exp_year'] ?? null,
            'is_default' => $request->boolean('is_default'),
        ]);

        return back()->with('success', 'Tarjeta actualizada.');
    }

    public function delete($id)
    {
        Card::where('id', $id)
            ->where('user_id', auth()->id())
            ->delete();

        return back()->with('success', 'Tarjeta eliminada.');
    }

    public function clear()
    {
        Card::where('user_id', auth()->id())->delete();

        return back()->with('success', 'Tarjetas eliminadas.');
    }

    public function index()
    {
        $cards = Card::where('user_id', auth()->id())
            ->orderByDesc('is_default')
            ->orderByDesc('id')
            ->get();

        return view('profile.cards', compact('cards'));
    }
}
