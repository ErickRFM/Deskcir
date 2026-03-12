<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(Request $request): View
    {
        return view('auth.login', [
            'redirectTo' => $this->sanitizeRedirect($request->query('redirect_to')),
        ]);
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $redirectTo = $this->sanitizeRedirect($request->input('redirect_to'));
        if ($redirectTo) {
            return redirect($redirectTo);
        }

        if (auth()->user()->role->name === 'admin') {
            return redirect('/admin');
        }

        if (auth()->user()->role->name === 'technician') {
            return redirect('/technician');
        }

        return redirect('/client');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/store');
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
