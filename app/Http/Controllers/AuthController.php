<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            if (Auth::user()->isAdmin()) {
                return redirect('/admin');
            } elseif (Auth::user()->isStudent()) {
                return redirect()->route('student.exam-sessions');
            }
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($credentials)) {
            return back()->withErrors([
                'email' => 'Email hoặc mật khẩu không đúng',
            ]);
        }

        $request->session()->regenerate();
        if (Auth::user()->isAdmin()) {
            return redirect('/admin');
        }
        // Redirect student đến trang chọn phiên thi
        return redirect()->route('student.exam-sessions');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
