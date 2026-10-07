<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Support\Facades\Password;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    public function showResetForm(Request $request, $token = null)
    {
        $email = (string) $request->query('email');
        $user = $email !== '' ? User::where('email', $email)->first() : null;

        if (!$token || !$user || !Password::broker()->tokenExists($user, $token)) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'This password reset link is invalid or has expired.']);
        }

        return view('auth.passwords.reset', compact('token', 'email'));
    }

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/home';
}
