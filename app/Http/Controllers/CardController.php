<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Card;

class CardController extends Controller
{

public function saveCard(Request $r)
{

$r->validate([
'mp_id' => 'required',
'brand' => 'required',
'last4' => 'required'
]);

Card::create([
'user_id' => auth()->id(),
'mp_id'   => $r->mp_id,
'brand'   => $r->brand,
'last4'   => $r->last4
]);

return back()->with('success','Tarjeta guardada');
}



public function delete($id)
{
Card::where('id',$id)
->where('user_id',auth()->id())
->delete();

return back();
}



public function index()
{
$cards = Card::where('user_id',auth()->id())->get();

return view('profile.cards',compact('cards'));
}

}