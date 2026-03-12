<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(Request $request): View
    {
        return view('auth.register', [
            'redirectTo' => $this->sanitizeRedirect($request->query('redirect_to')),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'string'],
            'redirect_to' => ['nullable', 'string', 'max:500'],
        ]);

        $roleRaw = Str::lower(trim((string) $request->input('role')));

        $aliases = [
            'admin' => ['admin', 'administrador', '1'],
            'technician' => ['technician', 'tecnico', '2'],
            'client' => ['client', 'cliente', '3'],
            'cashier' => ['cashier', 'caja', '4'],
        ];

        $canonicalRole = null;
        foreach ($aliases as $canonical => $validInputs) {
            if (in_array($roleRaw, $validInputs, true)) {
                $canonicalRole = $canonical;
                break;
            }
        }

        if (! $canonicalRole) {
            throw ValidationException::withMessages([
                'role' => 'El rol seleccionado no es valido.',
            ]);
        }

        $role = Role::query()->firstOrCreate(['name' => $canonicalRole]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => (int) $role->id,
        ]);

        event(new Registered($user));
        Auth::login($user);

        $redirectTo = $this->sanitizeRedirect($request->input('redirect_to'));
        if ($redirectTo) {
            return redirect($redirectTo);
        }

        return redirect(RouteServiceProvider::HOME);
    }

    private function sanitizeRedirect(?string $redirectTo): ?string
    {
        $redirectTo = trim((string) $redirectTo);

        if ($redirectTo === '' || ! str_starts_with($redirectTo, '/')) {
            return null;
        }

        return $redirectTo;
    }
}
