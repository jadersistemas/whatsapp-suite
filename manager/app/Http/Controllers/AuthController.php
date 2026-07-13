<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function showForm()
    {
        if (session('api_key_verified')) {
            return redirect()->route('dashboard');
        }

        return view('auth.apikey');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'api_key' => 'required|string',
        ]);

        $validKey = config('services.whatsapp.api_key');

        if (!$validKey) {
            return back()->withErrors(['api_key' => 'API Key não configurada no servidor.']);
        }

        if (hash_equals($validKey, $request->input('api_key'))) {
            session(['api_key_verified' => true]);
            return redirect()->intended(route('dashboard'));
        }

        return back()->withInput()->with('error', 'API Key inválida.');
    }

    public function logout(Request $request)
    {
        $request->session()->forget('api_key_verified');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('auth.apikey.form');
    }
}
