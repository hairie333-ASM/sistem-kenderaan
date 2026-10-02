<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        $demoUsers = User::with('driver')->orderBy('role')->get();

        return view('auth.login', compact('demoUsers'));
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            AuditLog::record('Log Masuk', 'Keselamatan', 'Pengguna berjaya log masuk ke dalam sistem.');

            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'Maklumat log masuk yang dimasukkan tidak sah.',
        ])->onlyInput('email');
    }

    /**
     * Fast 1-click switcher for demonstration and role evaluation
     */
    public function switchUser(int $id): RedirectResponse
    {
        $user = User::findOrFail($id);
        Auth::login($user);
        request()->session()->regenerate();

        AuditLog::record('Tukar Peranan Demonstrasi', 'Keselamatan', 'Pengguna beralih kepada akaun '.$user->name.' ('.strtoupper($user->role).').');

        return redirect()->route('dashboard')->with('success', 'Berjaya beralih ke akaun: '.$user->name.' ['.strtoupper($user->role).']');
    }

    public function logout(Request $request): RedirectResponse
    {
        AuditLog::record('Log Keluar', 'Keselamatan', 'Pengguna telah log keluar.');
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('info', 'Anda telah berjaya log keluar.');
    }
}
