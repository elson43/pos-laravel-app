<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    /**
     * Show the centralized login form.
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect(Auth::user()->getDashboardUrl());
        }

        return view('auth.login');
    }

    /**
     * Handle login authentication request.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Record login in activity log
            ActivityLog::log(
                'LOGIN',
                "Pengguna {$user->name} ({$user->role}) berhasil masuk ke sistem.",
                $user
            );

            return redirect()->intended($user->getDashboardUrl())
                ->with('success', "Selamat datang kembali, {$user->name}!");
        }

        return back()->withErrors([
            'email' => 'Email atau kata sandi yang Anda masukkan tidak sesuai.',
        ])->onlyInput('email');
    }

    /**
     * Show the public registration form.
     */
    public function showRegister()
    {
        if (Auth::check()) {
            return redirect(Auth::user()->getDashboardUrl());
        }

        return view('auth.register');
    }

    /**
     * Handle registration request with explicit role selection.
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'role' => ['required', 'string', Rule::in(['UKasir', 'UGudang', 'UAdmin'])],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.unique' => 'Email ini sudah terdaftar di sistem.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'role.required' => 'Peran (Role) wajib dipilih.',
            'role.in' => 'Pilihan peran tidak valid.',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        Auth::login($user);

        // Record registration & auto login in activity log
        ActivityLog::log(
            'REGISTER',
            "Pengguna baru {$user->name} mendaftar sebagai peran {$user->role}.",
            $user
        );

        return redirect($user->getDashboardUrl())
            ->with('success', "Registrasi berhasil! Selamat datang di POS Retail, {$user->name}.");
    }

    /**
     * Handle user logout.
     */
    public function logout(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            ActivityLog::log(
                'LOGOUT',
                "Pengguna {$user->name} ({$user->role}) keluar dari sistem.",
                $user
            );
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Anda telah berhasil keluar dari sistem.');
    }
}
