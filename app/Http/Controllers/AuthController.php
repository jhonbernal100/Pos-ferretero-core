<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return Auth::user()->rol === 'superadmin'
                ? redirect('/admin/dashboard')
                : redirect('/ventas/crear');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt([
            'email'    => $request->email,
            'password' => $request->password,
            'activo'   => true,
        ], $request->remember)) {
            $request->session()->regenerate();

            return Auth::user()->rol === 'superadmin'
                ? redirect('/admin/dashboard')
                : redirect('/ventas/crear');
        }

        return back()->withErrors([
            'email' => 'Credenciales incorrectas o cuenta desactivada.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}