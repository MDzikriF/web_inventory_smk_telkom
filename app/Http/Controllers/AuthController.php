<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function index()
    {
        if (Auth::check()) {
            return Auth::user()->role === 'admin' ? redirect('/admin/dashboard') : redirect('/user/dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'login' => ['required'], 
            'password' => ['required'],
        ]);

        $credentials = [
            'nip' => $request->login,
            'password' => $request->password,
        ];

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            if (!Auth::user()->is_active) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return back()->withErrors([
                    'login' => 'Akun Anda dinonaktifkan oleh Admin.',
                ])->onlyInput('login');
            }

            $request->session()->regenerate();
            
            return Auth::user()->role === 'admin' 
                ? redirect()->intended('/admin/dashboard') 
                : redirect()->intended('/user/dashboard');
        }

        return back()->withErrors([
            'login' => 'NIP atau kata sandi yang Anda masukkan tidak tepat.',
        ])->onlyInput('login');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
