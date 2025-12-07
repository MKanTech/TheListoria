<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);
        
        $login_type = filter_var($request->input('email'), FILTER_VALIDATE_EMAIL)
            ? 'email' 
            : 'username';
        
        $credentials = [
            $login_type => $request->input('email'),
            'password' => $request->input('password'),
        ];
        
        $remember = $request->boolean('remember'); 
        
        if (Auth::attempt($credentials, $remember)) {
        $request->session()->regenerate();
        
            // Başarılı giriş sonrası ana sayfaya yönlendir
            return redirect()->intended(route('home', [], false));
        }

        return back()->withErrors([
        'email' => 'Girdiğiniz bilgileri kontrol edip tekrar deneyiniz.',
        ]);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}