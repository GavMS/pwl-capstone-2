<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        try {
            // Call Node.js Backend API
            $apiUrl = env('NODEJS_API_URL', 'http://localhost:5000');
            $response = Http::post("{$apiUrl}/api/auth/login", [
                'email' => $request->email,
                'password' => $request->password
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                // Store token and user data in session
                Session::put('token', $data['token']);
                Session::put('user', $data['user']);

                // Redirect ke dashboard sesuai role
                $role = $data['user']['role'] ?? '';
                $routeMap = [
                    'Administrator'        => 'dashboard.admin',
                    'Kepala Laboratorium'  => 'dashboard.kalab',
                    'Ketua Program Studi'  => 'dashboard.kaprodi',
                    'Staf Administrasi'    => 'dashboard.stafadmin',
                    'Staf Laboratorium'    => 'dashboard.staflab',
                ];

                $dashboardRoute = $routeMap[$role] ?? 'login';
                return redirect()->route($dashboardRoute)->with('success', 'Login berhasil!');
            } else {
                return back()->withErrors([
                    'email' => $response->json()['message'] ?? 'Login gagal. Kredensial tidak valid.'
                ])->withInput();
            }
        } catch (\Exception $e) {
            return back()->withErrors([
                'email' => 'Tidak bisa terhubung ke server autentikasi.'
            ])->withInput();
        }
    }

    public function logout()
    {
        Session::forget('token');
        Session::forget('user');
        return redirect()->route('login');
    }

    public function dashboard()
    {
        if (!Session::has('token')) {
            return redirect()->route('login');
        }

        return view('dashboard', ['user' => Session::get('user')]);
    }
}
