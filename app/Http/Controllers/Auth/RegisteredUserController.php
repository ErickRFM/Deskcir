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
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(Request $request): View
    {
        return view('auth.register', [
            'redirectTo' => $this->sanitizeRedirect($request->query('redirect_to')),
            'roles' => $this->registrationRoles(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $roles = $this->registrationRoles();
        $selectedRole = Str::lower((string) $request->input('role', 'client'));

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['nullable', 'string', Rule::in($roles->pluck('name')->all())],
            'redirect_to' => ['nullable', 'string', 'max:500'],
        ]);

        $role = $roles->firstWhere('name', $selectedRole)
            ?? $roles->firstWhere('name', 'client')
            ?? Role::query()->firstOrCreate(['name' => 'client']);

        $user = User::create([
            'name' => (string) $request->name,
            'email' => (string) $request->email,
            'password' => Hash::make((string) $request->password),
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

    private function registrationRoles()
    {
        $roleOrder = ['client', 'technician', 'cashier', 'admin'];

        foreach ($roleOrder as $roleName) {
            Role::query()->firstOrCreate(['name' => $roleName]);
        }

        return Role::query()
            ->whereIn('name', $roleOrder)
            ->get()
            ->sortBy(fn (Role $role) => array_search($role->name, $roleOrder, true))
            ->values();
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
