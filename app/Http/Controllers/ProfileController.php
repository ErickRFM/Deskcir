<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        // 👉 Actualiza name + email con lo validado
        $user->fill($request->validated());

        // ===============================
        // 🚀 SUBIDA DE AVATAR CORRECTA
        // ===============================
        if ($request->hasFile('avatar')) {

            // 👉 BORRAR ANTERIOR SI EXISTE
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            // 👉 GUARDAR NUEVA
            $path = $request->file('avatar')
                ->store('avatars', 'public');

            $user->avatar = $path;
        }

        // 👉 Si cambió correo se desverifica
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')
            ->with('success', 'Perfil actualizado correctamente');
    }

    // =========================================
    // 🟢 NUEVO MÉTODO SOLO PARA AVATAR
    // =========================================
    public function avatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpg,png,jpeg,webp|max:4096'
        ]);

        $user = auth()->user();

        // 👉 BORRAR ANTERIOR
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        // 👉 GUARDAR NUEVA
        $path = $request->file('avatar')
            ->store('avatars','public');

        $user->avatar = $path;
        $user->save();

        return back()->with('success','Foto actualizada');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        // 👉 Borrar avatar al eliminar cuenta
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}