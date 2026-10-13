<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public static function userPayload(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => strtolower(trim((string) $user->role)),
            'account_type' => $user->account_type,
            'employee_category' => $user->employee_category,
            'cloak_alias' => $user->cloak_alias,
            'age' => $user->age,
            'gender' => $user->gender,
            'department' => $user->department,
            'phone_number' => $user->phone_number,
            'availability_status' => $user->availability_status,
            'account_status' => $user->account_status,
            'profile_photo_url' => $user->profile_photo_path
                ? url('storage/' . ltrim($user->profile_photo_path, '/'))
                : null,
            'personal_data_complete' => $user->hasCompletedPersonalData(),
        ];
    }

    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:100'],
        ]);

        $user = User::where('email', strtolower(trim($data['email'])))->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages(['email' => ['These credentials do not match our records.']]);
        }

        $status = strtolower(trim((string) ($user->account_status ?? 'active')));
        if ($status === 'approved') {
            $status = 'active';
        }
        if ($status === 'rejected') {
            return response()->json([
                'message' => 'Your registration was rejected. ' . ($user->rejection_reason ?: 'Please contact the GAD office for assistance.'),
            ], 403);
        }
        if ($status !== 'active') {
            return response()->json([
                'message' => 'Your account is pending admin verification. You can sign in once approved.',
            ], 403);
        }

        $role = strtolower(trim((string) $user->role));
        if ($role === 'admin') {
            return response()->json([
                'message' => 'GAD administrator accounts use the web dashboard. Please sign in on the website.',
            ], 403);
        }
        if (!in_array($role, ['student', 'employee'], true)) {
            return response()->json(['message' => 'Unsupported account role. Please contact IT.'], 403);
        }

        $token = $user->createToken($data['device_name'] ?? 'sinag-mobile')->plainTextToken;

        return response()->json(['token' => $token, 'user' => self::userPayload($user)]);
    }

    public function register(Request $request): JsonResponse
    {
        $data = $request->all();

        $validator = Validator::make($data + $request->allFiles(), [
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
            $prefix = (string) str($data['email'] ?? '')->before('@');
            $type = $data['account_type'] ?? null;
            if ($type === 'student' && !preg_match('/^\d/', $prefix)) {
                $validator->errors()->add('email', 'Student email must start with a number.');
            }
            if ($type === 'employee' && !preg_match('/^[A-Za-z]/', $prefix)) {
                $validator->errors()->add('email', 'Employee email must start with a letter.');
            }
        });
        $validator->validate();

        $accountType = $data['account_type'];
        $cloakAlias = ucfirst($accountType) . strtoupper(Str::random(4));
        while (User::where('cloak_alias', $cloakAlias)->exists()) {
            $cloakAlias = 'Student' . strtoupper(Str::random(4));
        }

        $stored = [];
        try {
            $selfie = $request->file('selfie_image')->store('verification/selfie', 'local');
            $stored[] = ['local', $selfie];
            $photo = $request->file('profile_photo')->store('profile-photos', 'public');
            $stored[] = ['public', $photo];

            DB::transaction(function () use ($data, $accountType, $cloakAlias, $photo, $selfie) {
                DB::table('users')->insert([
                    'id' => ((int) DB::table('users')->max('id')) + 1,
                    'name' => trim($data['name']),
                    'email' => strtolower(trim($data['email'])),
                    'age' => $data['age'],
                    'gender' => $data['gender'],
                    'department' => $data['department'],
                    'phone_number' => null,
                    'password' => Hash::make($data['password']),
                    'role' => $accountType,
                    'account_type' => $accountType,
                    'employee_category' => $accountType === 'employee' ? ($data['employee_category'] ?? null) : null,
                    'cloak_alias' => $cloakAlias,
                    'profile_photo_path' => $photo,
                    'selfie_image_path' => $selfie,
                    'account_status' => 'pending',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });
        } catch (\Throwable $e) {
            foreach ($stored as [$disk, $path]) {
                Storage::disk($disk)->delete($path);
            }
            report($e);
            throw ValidationException::withMessages([
                'profile_photo' => ['The photos could not be uploaded. Please try smaller JPG, PNG, or WEBP images.'],
            ]);
        }

        return response()->json([
            'message' => 'Registration submitted. Please wait for admin verification before you can sign in.',
        ], 201);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['ok' => true]);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json(['user' => self::userPayload($request->user())]);
    }

    public function heartbeat(Request $request): JsonResponse
    {
        $request->user()->forceFill(['last_seen_at' => now()])->saveQuietly();

        return response()->json(['ok' => true]);
    }

    public function updatePhoto(Request $request): JsonResponse
    {
        $request->validate([
            'profile_photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $user = $request->user();
        if ($user->profile_photo_path && Storage::disk('public')->exists($user->profile_photo_path)) {
            Storage::disk('public')->delete($user->profile_photo_path);
        }
        $user->update(['profile_photo_path' => $request->file('profile_photo')->store('profile-photos', 'public')]);

        return response()->json(['user' => self::userPayload($user->fresh())]);
    }

    public function updateAvailability(Request $request): JsonResponse
    {
        $data = $request->validate([
            'availability_status' => ['required', Rule::in(['active', 'busy', 'do_not_disturb'])],
        ]);
        $request->user()->update($data);

        return response()->json(['user' => self::userPayload($request->user()->fresh())]);
    }
}
