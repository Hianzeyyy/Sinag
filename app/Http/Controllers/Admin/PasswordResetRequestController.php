<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PasswordResetRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PasswordResetRequestController extends Controller
{
    public function index()
    {
        $requests = PasswordResetRequest::with('user')->latest()->paginate(20);
        return view('admin.password-reset-requests.index', compact('requests'));
    }

    public function show(PasswordResetRequest $passwordResetRequest)
    {
        $passwordResetRequest->load('user', 'admin');
        return view('admin.password-reset-requests.show', compact('passwordResetRequest'));
    }

    public function idPicture(PasswordResetRequest $passwordResetRequest)
    {
        abort_unless(Storage::disk('local')->exists($passwordResetRequest->id_picture_path), 404);
        return response()->file(Storage::disk('local')->path($passwordResetRequest->id_picture_path), ['Content-Disposition' => 'inline; filename="id-picture-' . $passwordResetRequest->id . '"']);
    }

    public function approve(PasswordResetRequest $passwordResetRequest)
    {
        $token = Str::random(64);
        DB::transaction(function () use ($passwordResetRequest, $token) {
            $lockedRequest = PasswordResetRequest::whereKey($passwordResetRequest->id)->lockForUpdate()->firstOrFail();
            abort_unless($lockedRequest->status === 'pending', 422, 'Only pending requests can be approved.');
            $lockedRequest->update(['status' => 'approved', 'admin_id' => auth()->id(), 'reset_token_hash' => Hash::make($token), 'reset_token_expires_at' => now()->addMinutes(15), 'approved_at' => now(), 'admin_remarks' => null]);
        });
        Log::info('Password reset request approved', ['request_id' => $passwordResetRequest->id, 'reference_number' => $passwordResetRequest->reference_number, 'admin_id' => auth()->id()]);
        return back()->with('success', 'Request approved. The requester can now check the status and create a new password within 15 minutes.');
    }

    public function reject(Request $request, PasswordResetRequest $passwordResetRequest)
    {
        $validated = $request->validate(['admin_remarks' => ['required', 'string', 'max:2000']]);
        abort_unless($passwordResetRequest->status === 'pending', 422, 'Only pending requests can be rejected.');
        $passwordResetRequest->update(['status' => 'rejected', 'admin_id' => auth()->id(), 'admin_remarks' => $validated['admin_remarks']]);
        Log::info('Password reset request rejected', ['request_id' => $passwordResetRequest->id, 'reference_number' => $passwordResetRequest->reference_number, 'admin_id' => auth()->id()]);
        return back()->with('success', 'Request rejected.');
    }
}