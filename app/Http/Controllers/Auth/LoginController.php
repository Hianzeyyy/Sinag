<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Default redirection.
     * Note: Mas mangingibabaw ang authenticated() function sa baba.
     */
    protected $redirectTo = '/student/dashboard';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Role-Based Redirection Protocol
     * Dito natin sinisiguro na ang GAD Admin ay mapupunta sa Admin Panel.
     */
    protected function authenticated(Request $request, $user)
    {
        if ($request->boolean('remember')) {
            $request->session()->put('remembered_login', true);
            Cookie::queue(cookie(
                'remembered_email',
                $user->email,
                60 * 24 * 30,
                '/',
                null,
                $request->isSecure(),
                true,
                false,
                'lax'
            ));
        } else {
            $request->session()->put('remembered_login', false);
            Cookie::queue(Cookie::forget('remembered_email'));
        }

        $status = strtolower(trim((string) ($user->account_status ?? 'active')));

        // Backward compatibility for legacy records that still use "approved".
        if ($status === 'approved') {
            $status = 'active';
            $user->account_status = 'active';
            $user->save();
        }

        if ($status !== 'active') {
            auth()->logout();

            if ($status === 'rejected') {
                return redirect()->route('login')->with('error', 'Your registration was rejected. ' . ($user->rejection_reason ?: 'Please contact the GAD office for assistance.'));
            }

            return redirect()->route('login')->with('status', 'Your account is pending admin verification. You can sign in once approved.');
        }

        // Linisin ang role check at gawing null-safe para iwas login crash
        $role = strtolower(trim((string) $user->role));

        if ($role === '') {
            auth()->logout();

            return redirect()->route('login')
                ->with('error', 'Account role not assigned. Please contact IT.');
        }

        if ($role === 'admin') {
            return redirect()->route('admin.dashboard')
                             ->with('status', 'Welcome back, Administrator!'); 
        }

        if ($role === 'student' || $role === 'security' || $role === 'employee') {
            return redirect()->route('student.dashboard')
                             ->with('status', 'Logged in successfully as ' . ($user->cloak_alias ?: $user->name)); 
        }

        auth()->logout();

        return redirect()->route('login')
            ->with('error', 'Unsupported account role. Please contact IT.');
    }

    public function logout(Request $request)
    {
        $keepUsername = $request->session()->get('remembered_login', false);

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if (!$keepUsername) {
            Cookie::queue(Cookie::forget('remembered_email'));
        }

        return redirect()->route('splash');
    }
}