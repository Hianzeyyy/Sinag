<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;
use Laravel\Socialite\Two\User as GoogleSocialiteUser;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Default redirection.
     * Note: Mas mangingibabaw ang authenticated() function sa baba.
     */
    protected $redirectTo = '/student/dashboard';

    private const MAX_PASSWORD_ATTEMPTS = 3;
    private const EXEMPT_PASSWORD_ATTEMPT_EMAILS = [
        'admin@psu.edu.ph',
        'gadcoordinator@psu.edu.ph',
    ];

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm()
    {
        return response()
            ->view('auth.login')
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache');
    }

    public function redirectToGoogle(Request $request)
    {
        $intent = $request->query('intent', 'login');
        abort_unless(\in_array($intent, ['login', 'signup'], true), 404);

        if (blank(config('services.google.client_id')) || blank(config('services.google.client_secret'))) {
            $destination = $intent === 'signup' ? 'register' : 'login';

            return redirect()->route($destination)
                ->with('error', 'Google sign-in is not configured. Please use your email and password or contact the administrator.');
        }

        $request->session()->put('google_auth_intent', $intent);

        return Socialite::driver('google')
            ->with(['prompt' => 'select_account'])
            ->redirect();
    }

    public function handleGoogleCallback(Request $request)
    {
        $intent = $request->session()->pull('google_auth_intent', 'login');
        $destination = $intent === 'signup' ? 'register' : 'login';

        if ($request->query('error')) {
            return redirect()->route($destination)->with('error', 'Google sign-in was cancelled or could not be completed.');
        }

        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (InvalidStateException $exception) {
            report($exception);

            return redirect()->route($destination)
                ->with('error', 'Google sign-in could not be verified. Please try again.');
        }

        if (!$googleUser instanceof GoogleSocialiteUser) {
            return redirect()->route($destination)
                ->with('error', 'Google sign-in returned an invalid response. Please try again.');
        }

        $googleData = $googleUser->getRaw();
        $email = strtolower(trim((string) $googleUser->getEmail()));
        $verified = filter_var(
            $googleData['email_verified'] ?? $googleData['verified_email'] ?? false,
            FILTER_VALIDATE_BOOLEAN
        );

        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !$verified) {
            return redirect()->route($destination)
                ->with('error', 'Google did not provide a verified email address. Please try again with a verified account.');
        }

        if ($intent === 'signup') {
            $nameParts = preg_split('/\s+/', trim((string) $googleUser->getName()), -1, PREG_SPLIT_NO_EMPTY) ?: [];
            $firstName = trim((string) ($googleData['given_name'] ?? ($nameParts[0] ?? '')));
            $lastName = trim((string) ($googleData['family_name'] ?? (\count($nameParts) > 1 ? $nameParts[\count($nameParts) - 1] : '')));
            $middleName = trim((string) ($googleData['middle_name'] ?? implode(' ', \array_slice($nameParts, 1, max(\count($nameParts) - 2, 0)))));

            return redirect()->route('register')->with('google_signup', [
                'first_name' => $firstName,
                'middle_name' => $middleName,
                'last_name' => $lastName,
                'email' => $email,
            ]);
        }

        $user = User::query()->whereRaw('LOWER(email) = ?', [$email])->first();

        if (!$user) {
            return redirect()->route('login')
                ->with('error', 'This Google email is not registered yet. Please choose Sign up to complete your account details.');
        }

        Auth::login($user);
        $request->session()->regenerate();

        return $this->authenticated($request, $user);
    }

    /**
     * Role-Based Redirection Protocol
     * Dito natin sinisiguro na ang GAD Admin ay mapupunta sa Admin Panel.
     */
    protected function authenticated(Request $request, $user)
    {
        if ($this->passwordAttemptTrackingAvailable() &&
            ($user->failed_login_attempts !== 0 || $user->password_locked_at !== null)) {
            $user->forceFill([
                'failed_login_attempts' => 0,
                'password_locked_at' => null,
            ])->saveQuietly();
        }

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
                return redirect()->route('login')->with('error', 'Your registration was rejected. '.($user->rejection_reason ?: 'Please contact the GAD office for assistance.'));
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
                ->with('status', 'Logged in successfully as '.($user->cloak_alias ?: $user->name))
                ->with('dashboard_logo_sunburst', true);
        }

        auth()->logout();

        return redirect()->route('login')
            ->with('error', 'Unsupported account role. Please contact IT.');
    }

    protected function attemptLogin(Request $request)
    {
        $user = User::whereRaw('LOWER(email) = ?', [strtolower(trim((string) $request->input($this->username())))])->first();

        if ($this->shouldTrackPasswordAttempts($user) && $user?->password_locked_at !== null) {
            return false;
        }

        return $this->guard()->attempt(
            $this->credentials($request), $request->boolean('remember')
        );
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        $user = User::whereRaw('LOWER(email) = ?', [strtolower(trim((string) $request->input($this->username())))])->first();

        if ($user && $this->shouldTrackPasswordAttempts($user)) {
            $attempts = min(self::MAX_PASSWORD_ATTEMPTS, (int) $user->failed_login_attempts + 1);
            $attributes = ['failed_login_attempts' => $attempts];

            if ($attempts >= self::MAX_PASSWORD_ATTEMPTS) {
                $attributes['password_locked_at'] = now();
            }

            $user->forceFill($attributes)->saveQuietly();

            if ($attempts >= self::MAX_PASSWORD_ATTEMPTS) {
                $message = 'Your account is locked after three failed password attempts. Please reset your password to continue.';
                $request->session()->flash('password_reset_url', route('password.code.form'));

                throw ValidationException::withMessages([
                    $this->username() => [$message],
                ]);
            }
        }

        throw ValidationException::withMessages([
            $this->username() => [$user
                ? 'The password is incorrect.'
                : 'The username or email address was not found.'],
        ]);
    }

    private function passwordAttemptTrackingAvailable(): bool
    {
        return Schema::hasColumn('users', 'failed_login_attempts')
            && Schema::hasColumn('users', 'password_locked_at');
    }

    private function shouldTrackPasswordAttempts(?User $user): bool
    {
        if (!$this->passwordAttemptTrackingAvailable() || !$user) {
            return false;
        }

        return !in_array(strtolower((string) $user->email), self::EXEMPT_PASSWORD_ATTEMPT_EMAILS, true);
    }

    public function logout(Request $request)
    {
        $keepUsername = $request->session()->get('remembered_login', false);

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if (! $keepUsername) {
            Cookie::queue(Cookie::forget('remembered_email'));
        }

        return redirect()->route('login');
    }
}
