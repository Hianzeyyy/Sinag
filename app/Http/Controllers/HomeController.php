<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     * Siguraduhin na ang naka-login lang ang makaka-access.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     * Traffic Control: Dito idinedesisyon kung sa Admin o Student dashboard pupunta.
     */
    public function index()
    {
        // 1. Kunin ang kasalukuyang user
        $user = Auth::user();

        // 2. I-check kung may role ang user para iwas error sa strtolower()
        if (!$user->role) {
            // Kung walang role, i-logout at ibalik sa login na may error
            Auth::logout();
            return redirect()->route('login')->with('error', 'Account role not assigned. Please contact IT.');
        }

        // 3. Linisin ang role check (Case-insensitive at tanggal ang extra spaces)
        $role = strtolower(trim((string) $user->role));

        // 4. Redirect Logic base sa Role
        if ($role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        if ($role === 'student' || $role === 'security' || $role === 'employee') {
            return redirect()->route('student.dashboard');
        }

        Auth::logout();
        return redirect()->route('login')->with('error', 'Unsupported account role. Please contact IT.');
    }
}