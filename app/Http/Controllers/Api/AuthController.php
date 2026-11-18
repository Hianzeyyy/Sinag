<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\PasswordResetRequest;
use App\Support\CompatibleInsert;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    private const MAX_PASSWORD_ATTEMPTS = 3;

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
                ? route('profile.photo', $user)
                : null,
            'verification_image_url' => ($user->id_image_path ?: $user->selfie_image_path)
                ? route('profile.verification-image', $user)
                : null,
            'id_picture_url' => ($user->id_image_path ?: $user->selfie_image_path)
                ? route('profile.verification-image', $user)
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

        if ($this->passwordAttemptTrackingAvailable() && $user?->password_locked_at !== null) {
            return response()->json([
                'message' => 'Your account is locked after three failed password attempts. Please reset your password.',
                'password_reset_url' => route('password.code.form'),
            ], 423);
        }

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            if ($user && $this->passwordAttemptTrackingAvailable()) {
                $attempts = min(self::MAX_PASSWORD_ATTEMPTS, (int) $user->failed_login_attempts + 1);
                $attributes = ['failed_login_attempts' => $attempts];

                if ($attempts >= self::MAX_PASSWORD_ATTEMPTS) {
                    $attributes['password_locked_at'] = now();
                }

                $user->forceFill($attributes)->saveQuietly();

                if ($attempts >= self::MAX_PASSWORD_ATTEMPTS) {
                    return response()->json([
                        'message' => 'Your account is locked after three failed password attempts. Please reset your password.',
                        'password_reset_url' => route('password.code.form'),
                    ], 423);
                }
            }

            throw ValidationException::withMessages(['email' => ['These credentials do not match our records.']]);
        }

        if ($this->passwordAttemptTrackingAvailable() &&
            ($user->failed_login_attempts !== 0 || $user->password_locked_at !== null)) {
            $user->forceFill([
                'failed_login_attempts' => 0,
                'password_locked_at' => null,
            ])->saveQuietly();
        }

        $status = strtolower(trim((string) ($user->account_status ?? 'active')));
        if ($status === 'approved') {
            $status = 'active';
        }
        if ($status === 'rejected') {
            return response()->json([
                'message' => 'Your registration was rejected. '.($user->rejection_reason ?: 'Please contact the GAD office for assistance.'),
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
        if (! in_array($role, ['student', 'employee'], true)) {
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
            if ($type === 'student' && ! preg_match('/^\d/', $prefix)) {
                $validator->errors()->add('email', 'Student email must start with a number.');
            }
            if ($type === 'employee' && ! preg_match('/^[A-Za-z]/', $prefix)) {
                $validator->errors()->add('email', 'Employee email must start with a letter.');
            }
        });
        $validator->validate();

        $accountType = $data['account_type'];
        $cloakAlias = ucfirst($accountType).strtoupper(Str::random(4));
        while (User::where('cloak_alias', $cloakAlias)->exists()) {
            $cloakAlias = 'Student'.strtoupper(Str::random(4));
        }

        $stored = [];
        try {
            $selfie = $request->file('selfie_image')->store('verification/selfie', 'local');
            $stored[] = ['local', $selfie];
            $photo = $request->file('profile_photo')->store('profile-photos', 'public');
            $stored[] = ['public', $photo];

            DB::transaction(function () use ($data, $accountType, $cloakAlias, $photo, $selfie) {
                CompatibleInsert::insert('users', [
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

    private function passwordAttemptTrackingAvailable(): bool
    {
        return Schema::hasColumn('users', 'failed_login_attempts')
            && Schema::hasColumn('users', 'password_locked_at');
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['ok' => true]);
    }

    public function forgotPasswordRequest(Request $request): JsonResponse
    {
        $email = $request->input('email');
        if (is_string($email)) {
            $request->merge(['email' => Str::lower(trim($email))]);
        }

        $data = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'student_employee_id' => ['required', 'string', 'max:100'],
            'name' => ['required', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:255'],
            'id_picture' => ['required', 'file', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'reason' => ['nullable', 'string', 'max:2000'],
        ]);

        $user = User::whereRaw('LOWER(email) = ?', [$data['email']])->first();
        if (! $user) {
            throw ValidationException::withMessages([
                'email' => ['No SINAG account was found for this email. Check the address used when registering.'],
            ]);
        }

        $path = $request->file('id_picture')->store('password-reset-requests', 'local');
        if (! is_string($path) || $path === '') {
            throw ValidationException::withMessages([
                'id_picture' => ['The ID picture could not be saved. Please try again later.'],
            ]);
        }

        $reference = 'PRR-'.Str::upper(Str::random(10));
        CompatibleInsert::insert('password_reset_requests', [
            'user_id' => $user->id,
            'reference_number' => $reference,
            'email' => $user->email,
            'name' => $data['name'],
            'student_employee_id' => $data['student_employee_id'],
            'department' => $data['department'] ?? null,
            'id_picture_path' => $path,
            'reason' => $data['reason'] ?? null,
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'message' => 'Password reset request submitted.',
            'reference_number' => $reference,
            'status' => 'pending',
        ], 201);
    }

    public function forgotPasswordStatus(Request $request): JsonResponse
    {
        $data = $request->validate([
            'reference_number' => ['required', 'string', 'max:40'],
            'email_or_id' => ['required', 'string', 'max:255'],
        ]);

        $resetRequest = PasswordResetRequest::where('reference_number', Str::upper(trim($data['reference_number'])))
            ->where(fn ($query) => $query
                ->whereRaw('LOWER(email) = ?', [Str::lower(trim($data['email_or_id']))])
                ->orWhere('student_employee_id', trim($data['email_or_id'])))
            ->first();

        if (! $resetRequest) {
            throw ValidationException::withMessages([
                'reference_number' => ['The reference number and email or ID do not match.'],
            ]);
        }

        $token = null;
        if ($resetRequest->status === 'approved') {
            $token = Str::random(64);
            $resetRequest->update([
                'reset_token_hash' => Hash::make($token),
                'reset_token_expires_at' => now()->addMinutes(15),
            ]);
        }

        $payload = [
            'reference_number' => $resetRequest->reference_number,
            'status' => $resetRequest->status,
        ];
        if ($token !== null) {
            $payload['reset_token'] = $token;
            $payload['reset_url'] = url('/api/forgot-password/reset/'.$resetRequest->id.'/'.$token);
        }

        return response()->json($payload);
    }

    public function forgotPasswordReset(Request $request, PasswordResetRequest $passwordResetRequest, string $token): JsonResponse
    {
        abort_unless(
            $passwordResetRequest->status === 'approved'
            && $passwordResetRequest->reset_token_expires_at?->isFuture()
            && filled($passwordResetRequest->reset_token_hash)
            && Hash::check($token, $passwordResetRequest->reset_token_hash),
            403,
            'This password reset link is invalid, expired, or already used.'
        );

        $data = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
        $user = $passwordResetRequest->user ?: User::where('email', $passwordResetRequest->email)->firstOrFail();
        $attributes = ['password' => Hash::make($data['password'])];
        if (Schema::hasColumn('users', 'failed_login_attempts') && Schema::hasColumn('users', 'password_locked_at')) {
            $attributes['failed_login_attempts'] = 0;
            $attributes['password_locked_at'] = null;
        }
        $user->update($attributes);
        $passwordResetRequest->update([
            'status' => 'completed',
            'reset_token_hash' => null,
            'reset_token_expires_at' => null,
            'completed_at' => now(),
        ]);

        return response()->json(['message' => 'Password reset successfully.']);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json(['user' => self::userPayload($request->user())]);
    }

    public function users(Request $request): JsonResponse
    {
        $viewer = $request->user();
        $perPage = max(1, min(100, (int) $request->integer('per_page', 30)));
        $users = User::query()
            ->orderBy('name')
            ->paginate($perPage);

        $users->getCollection()->transform(function (User $user) use ($viewer): array {
            $isAdminOrOwner = strtolower((string) $viewer->role) === 'admin' || $viewer->is($user);
            $verificationPath = $user->id_image_path ?: $user->selfie_image_path;

            return [
                'id' => $user->id,
                'name' => $user->name,
                'role' => strtolower(trim((string) $user->role)),
                'account_type' => $user->account_type,
                'employee_category' => $user->employee_category,
                'department' => $user->department,
                'cloak_alias' => $user->cloak_alias,
                'availability_status' => $user->availability_status,
                'profile_photo_url' => route('profile.photo', $user),
                'verification_image_url' => $isAdminOrOwner && $verificationPath
                    ? route('profile.verification-image', $user)
                    : null,
                'id_picture_url' => $isAdminOrOwner && $verificationPath
                    ? route('profile.verification-image', $user)
                    : null,
            ];
        });

        return response()->json($users);
    }

    public function showUser(Request $request, User $user): JsonResponse
    {
        $viewer = $request->user();
        $isAdminOrOwner = strtolower((string) $viewer->role) === 'admin' || $viewer->is($user);
        $verificationPath = $user->id_image_path ?: $user->selfie_image_path;
        $payload = [
            'id' => $user->id,
            'name' => $user->name,
            'role' => strtolower(trim((string) $user->role)),
            'account_type' => $user->account_type,
            'employee_category' => $user->employee_category,
            'department' => $user->department,
            'cloak_alias' => $user->cloak_alias,
            'availability_status' => $user->availability_status,
            'profile_photo_url' => route('profile.photo', $user),
            'verification_image_url' => $isAdminOrOwner && $verificationPath
                ? route('profile.verification-image', $user)
                : null,
            'id_picture_url' => $isAdminOrOwner && $verificationPath
                ? route('profile.verification-image', $user)
                : null,
        ];

        return response()->json(['user' => $payload]);
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
