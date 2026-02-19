<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PasswordController extends Controller
{
    public function update(Request $r)
    {
        $r->validate([
            'current_password' => ['required'],
            'password' => [
                'required',
                'confirmed',
                'min:8',
                'regex:/[A-Z]/',   // al menos una mayúscula
                'regex:/[0-9]/'    // al menos un número
            ]
        ],[
            'password.min' => 'Debe tener al menos 8 caracteres',
            'password.confirmed' => 'Las contraseñas no coinciden',
            'password.regex' => 'Debe incluir una mayúscula y un número'
        ]);

        // 🔥 VALIDAR CONTRASEÑA ACTUAL REAL
        if (!Hash::check($r->current_password, $r->user()->password)) {
            return back()->withErrors([
                'current_password' => 'La contraseña actual es incorrecta'
            ]);
        }

        // ACTUALIZAR
        $r->user()->update([
            'password' => Hash::make($r->password)
        ]);

        return back()->with('status','password-updated');
    }
}