<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class SupabaseAuthController extends Controller
{
    private $supabaseUrl;
    private $supabaseKey;

    public function __construct()
    {
        $this->supabaseUrl = rtrim(env('SUPABASE_URL'), '/');
        $this->supabaseKey = env('SUPABASE_ANON_KEY');
    }

    public function showLoginForm()
    {
        return view('pages.auth.auth', ['mode' => 'login']);
    }

    public function showRegisterForm()
    {
        return view('pages.auth.auth', ['mode' => 'register']);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $response = Http::withHeaders([
            'apikey' => $this->supabaseKey,
            'Authorization' => 'Bearer ' . $this->supabaseKey,
        ])->post($this->supabaseUrl . '/auth/v1/token?grant_type=password', [
            'email' => $request->email,
            'password' => $request->password,
        ]);

        if ($response->successful()) {
            $data = $response->json();
            
            // Simpan data user ke dalam session
            Session::put('supabase_user', [
                'access_token' => $data['access_token'],
                'user' => $data['user']
            ]);

            return redirect()->route('dashboard')->with('success', 'Berhasil login!');
        }

        return back()->with('error', $response->json()['error_description'] ?? 'Email atau password salah');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed'
        ]);

        $response = Http::withHeaders([
            'apikey' => $this->supabaseKey,
            'Authorization' => 'Bearer ' . $this->supabaseKey,
        ])->post($this->supabaseUrl . '/auth/v1/signup', [
            'email' => $request->email,
            'password' => $request->password,
            'data' => [
                'name' => $request->name,
                'role' => 'user' // default role
            ]
        ]);

        if ($response->successful()) {
            // Bisa langsung login atau minta verifikasi email tergantung setting Supabase
            // Kita asumsikan langsung login jika tidak ada email confirmation
            $data = $response->json();
            
            if (isset($data['session'])) {
                Session::put('supabase_user', [
                    'access_token' => $data['session']['access_token'],
                    'user' => $data['user']
                ]);
                return redirect()->route('dashboard')->with('success', 'Pendaftaran berhasil dan Anda telah login!');
            }
            
            return redirect()->route('login')->with('success', 'Pendaftaran berhasil! Silakan periksa email Anda untuk verifikasi atau langsung login.');
        }

        $responseData = $response->json();
        $errorMessage = $responseData['msg'] ?? $responseData['message'] ?? $responseData['error_description'] ?? 'Terjadi kesalahan saat pendaftaran: ' . $response->body();
        return back()->with('error', $errorMessage);
    }

    public function logout()
    {
        // Opsional: invalidate token di supabase
        $user = Session::get('supabase_user');
        if ($user) {
            Http::withHeaders([
                'apikey' => $this->supabaseKey,
                'Authorization' => 'Bearer ' . $user['access_token'],
            ])->post($this->supabaseUrl . '/auth/v1/logout');
        }

        Session::forget('supabase_user');
        return redirect()->route('dashboard')->with('success', 'Berhasil logout!');
    }
}
