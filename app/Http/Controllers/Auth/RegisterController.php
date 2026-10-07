<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'account_type' => ['required', Rule::in(['student', 'employee'])],
            'employee_category' => ['nullable', Rule::requiredIf(fn () => ($data['account_type'] ?? null) === 'employee'), Rule::in(['teaching', 'non_teaching'])],
            'age' => ['required', 'integer', 'min:1', 'max:120'],
            'gender' => ['required', 'in:Male,Female,Prefer not to say'],
            'department' => ['required', 'string', 'max:255'],
            'profile_photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'selfie_image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ])->after(function ($validator) use ($data) {
            $emailPrefix = (string) str($data['email'] ?? '')->before('@');
            $accountType = $data['account_type'] ?? null;

            if ($accountType === 'student' && !preg_match('/^\d/', $emailPrefix)) {
                $validator->errors()->add('email', 'Student email must start with a number.');
            }

            if ($accountType === 'employee' && !preg_match('/^[A-Za-z]/', $emailPrefix)) {
                $validator->errors()->add('email', 'Employee email must start with a letter.');
            }
        });
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @return User
     */
    protected function create(array $data)
    {
        $accountType = $data['account_type'];
        $baseAlias = ucfirst($accountType) . strtoupper(Str::random(4));
        $cloakAlias = $baseAlias;

        while (User::where('cloak_alias', $cloakAlias)->exists()) {
            $cloakAlias = 'Student' . strtoupper(Str::random(4));
        }

        $selfieImagePath = request()->file('selfie_image')
            ? request()->file('selfie_image')->store('verification/selfie', 'public')
            : null;

        $profilePhotoPath = request()->file('profile_photo')
            ? request()->file('profile_photo')->store('profile-photos', 'public')
            : null;

        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'age' => $data['age'],
            'gender' => $data['gender'],
            'department' => $data['department'],
            'phone_number' => null,
            'password' => Hash::make($data['password']),
            'role' => $accountType,
            'account_type' => $accountType,
            'employee_category' => $accountType === 'employee' ? ($data['employee_category'] ?? null) : null,
            'cloak_alias' => $cloakAlias,
            'profile_photo_path' => $profilePhotoPath,
            'selfie_image_path' => $selfieImagePath,
            'account_status' => 'pending',
        ]);
    }

    protected function registered(Request $request, $user)
    {
        Auth::logout();

        return redirect()->route('login')->with('status', 'Registration submitted. Please wait for admin verification before you can sign in.');
    }
}
