<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\PasswordResetRequest;
use App\Models\User;
use App\Support\CompatibleInsert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    public function showRequestForm()
    {
        return view('auth.forgot-password');
    }

    public function storeRequest(Request $request)
    {
        $email = $request->input('email');
        if (is_string($email)) {
            $request->merge(['email' => Str::lower(trim($email))]);
        }

        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'student_employee_id' => ['required', 'string', 'max:100'],
            'name' => ['required', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:255'],
            'id_picture' => ['required', 'file', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'reason' => ['nullable', 'string', 'max:2000'],
        ]);

        $user = User::whereRaw('LOWER(email) = ?', [$validated['email']])->first();
        if (!$user) {
            return back()->withErrors([
                'email' => 'No SINAG account was found for this email. Check the address used when registering.',
            ])->withInput();
        }

        $path = $request->file('id_picture')->store('password-reset-requests', 'local');
        if (!is_string($path) || $path === '') {
            Log::error('Password reset request ID picture could not be stored', [
                'email' => $validated['email'],
                'ip' => $request->ip(),
            ]);

            return back()->withErrors([
                'id_picture' => 'The ID picture could not be saved. Please try again later.',
            ])->withInput();
        }

        $reference = 'PRR-' . Str::upper(Str::random(10));
        $resetRequestId = DB::transaction(function () use ($user, $validated, $reference, $path) {
            return CompatibleInsert::insert('password_reset_requests', [
                'user_id' => $user->id,
                'reference_number' => $reference,
                'email' => $user->email,
                'name' => $validated['name'],
                'student_employee_id' => $validated['student_employee_id'],
                'department' => $validated['department'] ?? null,
                'id_picture_path' => $path,
                'reason' => $validated['reason'] ?? null,
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

        });
        Log::info('Password reset request submitted', ['request_id' => $resetRequestId, 'reference_number' => $reference, 'ip' => $request->ip()]);

        return redirect()->route('password.status.form', ['reference' => $reference])
            ->with('success', 'Request submitted. Keep your reference number to check its status.')
            ->with('reference', $reference);
    }

    public function showStatusForm(Request $request)
    {
        $reference = $request->query('reference') ?? session('reference');

        return view('auth.password-status', compact('reference'));
    }

    public function checkStatus(Request $request)
    {
        $validated = $request->validate([
            'reference_number' => ['required', 'string', 'max:40'],
            'email_or_id' => ['required', 'string', 'max:255'],
        ]);
        $resetRequest = PasswordResetRequest::where('reference_number', Str::upper(trim($validated['reference_number'])))
            ->where(fn ($query) => $query->whereRaw('LOWER(email) = ?', [Str::lower(trim($validated['email_or_id']))])->orWhere('student_employee_id', trim($validated['email_or_id'])))
            ->first();
        if (!$resetRequest) return back()->withErrors(['reference_number' => 'The reference number and email or ID do not match.'])->withInput();
        $token = null;
        if ($resetRequest->status === 'approved') {
            $token = Str::random(64);
            $resetRequest->update(['reset_token_hash' => Hash::make($token), 'reset_token_expires_at' => now()->addMinutes(15)]);
        }
        Log::info('Password reset request status viewed', ['request_id' => $resetRequest->id, 'reference_number' => $resetRequest->reference_number, 'ip' => $request->ip()]);
        return view('auth.password-status', compact('resetRequest', 'token'));
    }

    public function showResetForm(PasswordResetRequest $passwordResetRequest, string $token)
    {
        $this->ensureValidResetToken($passwordResetRequest, $token);
        return view('auth.reset-password', compact('passwordResetRequest', 'token'));
    }

    public function resetPassword(Request $request, PasswordResetRequest $passwordResetRequest, string $token)
    {
        $this->ensureValidResetToken($passwordResetRequest, $token);
        $validated = $request->validate(['password' => ['required', 'string', 'min:8', 'confirmed']]);
        $user = $passwordResetRequest->user ?: User::where('email', $passwordResetRequest->email)->firstOrFail();
        $user->update(['password' => Hash::make($validated['password'])]);
        $passwordResetRequest->update(['status' => 'completed', 'reset_token_hash' => null, 'reset_token_expires_at' => null, 'completed_at' => now()]);
        Log::info('Password reset request completed', ['request_id' => $passwordResetRequest->id, 'reference_number' => $passwordResetRequest->reference_number, 'user_id' => $user->id, 'ip' => $request->ip()]);
        return redirect()->route('login')->with('status', 'Password reset successfully. You may now log in.');
    }

    private function ensureValidResetToken(PasswordResetRequest $passwordResetRequest, string $token): void
    {
        abort_unless($passwordResetRequest->status === 'approved' && $passwordResetRequest->reset_token_expires_at?->isFuture() && filled($passwordResetRequest->reset_token_hash) && Hash::check($token, $passwordResetRequest->reset_token_hash), 403, 'This password reset link is invalid, expired, or already used.');
    }
}
