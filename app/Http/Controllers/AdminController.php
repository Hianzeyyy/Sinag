<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Report;
use App\Models\Suggestion;
use App\Models\User;
use App\Models\Message;
use App\Models\GadSchedule;
use App\Models\AppointmentRequest;
use App\Models\SuggestionReport;

class AdminController extends Controller
{
    /**
     * Auth protection para sa GAD Personnel.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Dashboard Analytics: Live counts para sa Dashboard cards.
     */
    public function index()
    {
        // Security Check
        if (Auth::user()->role !== 'admin') {
            return redirect()->route('home')->with('error', 'Unauthorized access.');
        }

        // 1. Dashboard Summary Data
        $totalReports     = Report::count();
        $pendingReports   = Report::where('status', 'Pending')->count();
        $resolvedReports  = Report::where('status', 'Resolved')->count();
        $totalSuggestions = Suggestion::count();
        
        // 2. Active Emergencies Logic
        $activeEmergencies = Report::where(function($query) {
                                    $query->where('status', 'Emergency')
                                          ->orWhere('priority', 'High');
                                })
                                ->where('status', '!=', 'Resolved')
                                ->count();
        
        // 3. Recent Reports Table
        $recentReports = Report::with('user')
                                ->orderByRaw("FIELD(status, 'Emergency') DESC")
                                ->latest()
                                ->take(5)
                                ->get();
        
        $officeInfo = \Illuminate\Support\Facades\Cache::get('gad_office_info', [
            'phone_number' => Auth::user()->phone_number ?: '0919-777-7377',
            'office_hours' => 'Mon – Fri, 8:00 AM – 5:00 PM',
            'office_location' => 'Admin Building, Room 105',
            'office_email' => Auth::user()->email ?: 'gad@psu.edu.ph',
        ]);
        
        return view('admin.dashboard', compact(
            'totalReports', 
            'pendingReports', 
            'resolvedReports', 
            'totalSuggestions',
            'activeEmergencies',
            'recentReports',
            'officeInfo'
        )); 
    }

    /**
     * Confidential Inbox: Listahan ng lahat ng reports.
     */
    public function reports()
    {
        if (Auth::user()->role !== 'admin') return redirect()->route('home');
        
        $reports = Report::with('user')
            ->orderByRaw('CASE WHEN seen_at IS NULL THEN 0 ELSE 1 END')
            ->orderByDesc('created_at')
            ->get();
        return view('admin.reports', compact('reports'));
    }

    /**
     * Mark a report as seen when admin opens its details.
     */
    public function markSeen($id)
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $report = Report::findOrFail($id);

        if (is_null($report->seen_at)) {
            $report->update(['seen_at' => now()]);
        }

        return response()->json([
            'ok' => true,
            'seen_at' => optional($report->seen_at)->toDateTimeString(),
        ]);
    }

    public function downloadReportPdf($id)
    {
        if (strtolower(trim((string) Auth::user()->role)) !== 'admin') {
            abort(403);
        }

        $report = Report::with('user')->findOrFail($id);
        $pdf = Pdf::loadView('admin.report-pdf', [
            'report' => $report,
            'evidenceFiles' => $report->evidence_files,
        ])->setPaper('a4', 'portrait');

        return $pdf->download('SINAG-Report-' . $report->incident_id . '.pdf');
    }

    /**
     * Update Report Status & Priority mula sa Admin view.
     */
    public function updateStatus(Request $request, $id)
    {
        if (Auth::user()->role !== 'admin') return redirect()->route('home');

        $report = Report::findOrFail($id);
        
        $report->update([
            'status'   => $request->status,
            'priority' => $request->priority ?? $report->priority
        ]);

        return back()->with('success', "Report #{$report->incident_id} updated successfully.");
    }

    /**
     * Participatory Suggestions (Suggestions Management)
     */
    public function suggestions()
    {
        if (Auth::user()->role !== 'admin') return redirect()->route('home');

        $suggestions = Suggestion::with('user')->latest()->get();
        $pendingSuggestionReports = SuggestionReport::where('status', 'pending')->count();
        return view('admin.suggestions', compact('suggestions', 'pendingSuggestionReports'));
    }

    public function suggestionReports()
    {
        if (strtolower(trim((string) Auth::user()->role)) !== 'admin') return redirect()->route('home');

        $reports = SuggestionReport::with(['reporter', 'suggestion.user', 'comment.user'])->latest()->paginate(20);
        return view('admin.suggestion_reports', compact('reports'));
    }

    public function deleteReportedSuggestion(SuggestionReport $suggestionReport)
    {
        if (strtolower(trim((string) Auth::user()->role)) !== 'admin') return redirect()->route('home');

        $suggestionReport->update(['status' => 'resolved', 'reviewed_by' => Auth::id(), 'reviewed_at' => now()]);

        if ($suggestionReport->comment_id && $suggestionReport->comment) {
            $suggestionReport->comment->delete();
        } elseif ($suggestionReport->suggestion) {
            $suggestionReport->suggestion->comments()->delete();
            $suggestionReport->suggestion->delete();
        }
        return back()->with('success', 'Reported content deleted.');
    }

    public function dismissSuggestionReport(SuggestionReport $suggestionReport)
    {
        if (strtolower(trim((string) Auth::user()->role)) !== 'admin') return redirect()->route('home');
        $suggestionReport->update(['status' => 'dismissed', 'reviewed_by' => Auth::id(), 'reviewed_at' => now()]);
        return back()->with('success', 'Report dismissed.');
    }

    /**
     * Moderate suggestion before publishing to student feed.
     */
    public function reviewSuggestion(Request $request, $id)
    {
        if (Auth::user()->role !== 'admin') return redirect()->route('home');

        $request->validate([
            'action' => 'required|in:approve,reject',
        ]);

        $suggestion = Suggestion::findOrFail($id);

        $suggestion->status = $request->action === 'approve' ? 'Approved' : 'Rejected';
        $suggestion->save();

        return back()->with('success', 'Suggestion status updated successfully.');
    }

    /**
     * User Management: Listahan ng mga rehistradong account.
     */
    public function users(Request $request)
    {
        if (Auth::user()->role !== 'admin') return redirect()->route('home');

        $baseQuery = User::where('id', '!=', Auth::id());
        $usersQuery = (clone $baseQuery)
            ->when($request->filled('q'), function ($query) use ($request) {
                $terms = preg_split('/\s+/', strtolower(trim($request->input('q'))), -1, PREG_SPLIT_NO_EMPTY);
                $query->where(function ($search) use ($terms) {
                    foreach ($terms as $term) {
                        $like = '%' . $term . '%';
                        $search->where(function ($termQuery) use ($like) {
                            $termQuery->whereRaw('LOWER(name) LIKE ?', [$like])
                                ->orWhereRaw('LOWER(email) LIKE ?', [$like])
                                ->orWhereRaw('LOWER(phone_number) LIKE ?', [$like])
                                ->orWhereRaw('LOWER(department) LIKE ?', [$like])
                                ->orWhereRaw('LOWER(employee_category) LIKE ?', [$like])
                                ->orWhereRaw('LOWER(account_type) LIKE ?', [$like])
                                ->orWhereRaw('LOWER(gender) LIKE ?', [$like])
                                ->orWhere('age', 'like', $like);
                        });
                    }
                });
            })
            ->when($request->filled('account_type'), function ($query) use ($request) {
                $type = $request->input('account_type');
                if ($type === 'student') {
                    $query->where(function ($student) {
                        $student->where('account_type', 'student')->orWhere(function ($legacy) {
                            $legacy->where('role', 'student')->whereNull('account_type');
                        });
                    });
                } elseif (in_array($type, ['teaching', 'non_teaching'], true)) {
                    $query->where('account_type', 'employee')
                        ->where('employee_category', $type);
                } else {
                    $query->where('employee_category', $type);
                }
            })
            ->when($request->filled('gender'), fn ($query) => $query->where('gender', $request->input('gender')))
            ->when($request->filled('department'), function ($query) use ($request) {
                $department = $request->input('department');
                $aliases = [
                    'Bachelor of Science in Accountancy' => ['Bachelor of Science in Accountancy', 'Bsa', 'BSA'],
                    'BS Information Technology (BSIT)' => ['BS Information Technology (BSIT)', 'BS Information Technology', 'BSIT', 'Bsit'],
                    'BS Business Administration (BSBA)' => ['BS Business Administration (BSBA)', 'BS Business Administration', 'BSBA'],
                    'Bachelor of Elementary Education (BEEd)' => ['Bachelor of Elementary Education (BEEd)', 'Bachelor of Elementary Education', 'BEEd'],
                    'Bachelor of Technology and Livelihood Education (BTLEd)' => ['Bachelor of Technology and Livelihood Education (BTLEd)', 'Bachelor of Technology and Livelihood Education', 'BTLEd'],
                ];
                $values = $aliases[$department] ?? [$department];
                $query->whereIn('department', $values);
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $status = $request->input('status');
                if ($status === 'active') {
                    $query->whereIn('account_status', ['active', 'approved']);
                } elseif (in_array($status, ['pending', 'rejected'], true)) {
                    $query->where('account_status', $status);
                }
            });

        $users = $usersQuery
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(30)
            ->withQueryString();
        $userCounts = $baseQuery
            ->selectRaw("SUM(account_status = 'pending') as pending, SUM(account_status IN ('active', 'approved')) as active, SUM(account_status = 'rejected') as rejected, COUNT(*) as total")
            ->first();
        $departmentType = $request->input('account_type');
        $studentDepartments = [
            'Bachelor of Science in Accountancy' => 'Bachelor of Science in Accountancy',
            'Bachelor Industrial Technology - Major in Electrical Technology' => 'Bachelor in Industrial Technology - Major in Electrical Technology',
            'Bachelor Industrial Technology - Major in Food Management Service' => 'Bachelor in Industrial Technology - Major in Food Management Service',
            'Bachelor Industrial Technology - Major in Mechanical Technology' => 'Bachelor in Industrial Technology - Major in Mechanical Technology',
            'Bachelor of Elementary Education (BEEd)' => 'Bachelor of Elementary Education',
            'Bachelor of Technology and Livelihood Education (BTLEd)' => 'Bachelor of Technology and Livelihood Education',
            'Bachelor Secondary Education (BSEd) - Major in English' => 'Bachelor of Secondary Education - Major in English',
            'Bachelor Secondary Education (BSEd) - Major in Math' => 'Bachelor of Secondary Education - Major in Math',
            'Bachelor Secondary Education (BSEd) - Major in Science' => 'Bachelor of Secondary Education - Major in Science',
            'BS Business Administration (BSBA)' => 'Bachelor of Science in Business Administration',
            'BS Information Technology (BSIT)' => 'Bachelor of Science in Information Technology',
        ];
        $employeeDepartments = [
            'College of Technology and Business' => 'College of Technology and Business',
            'College of Education' => 'College of Education',
        ];
        $departments = match ($departmentType) {
            'student' => $studentDepartments,
            'teaching', 'non_teaching' => $employeeDepartments,
            default => $studentDepartments + $employeeDepartments,
        };

        return view('admin.users-list', compact('users', 'userCounts', 'departments'));
    }

    /**
     * Show a specific user's profile for admin review.
     */
    public function showUser($id)
    {
        if (Auth::user()->role !== 'admin') return redirect()->route('home');

        $user = User::findOrFail($id);

        return view('profile.show', compact('user'));
    }

    public function deleteUser($id)
    {
        if (strtolower(trim((string) Auth::user()->role)) !== 'admin') {
            return redirect()->route('home')->with('error', 'Unauthorized access.');
        }

        if ((int) $id === (int) Auth::id()) {
            return back()->with('error', 'You cannot delete your own administrator account.');
        }

        $user = User::findOrFail($id);
        $deletedUserId = $user->id;
        $deletedUserEmail = $user->email;

        foreach (array_filter([$user->id_image_path, $user->selfie_image_path, $user->profile_photo_path]) as $path) {
            Storage::disk('public')->delete($path);
            Storage::disk('local')->delete($path);
        }

        $user->delete();

        Log::info('User account deleted by admin', [
            'deleted_user_id' => $deletedUserId,
            'deleted_user_email' => $deletedUserEmail,
            'admin_id' => Auth::id(),
            'ip' => request()->ip(),
        ]);

        return redirect()->route('admin.users')->with('success', 'User account deleted successfully.');
    }

    /**
     * Admin Messaging Page - List conversations and messages
     */
    public function messages(Request $request)
    {
        if (Auth::user()->role !== 'admin') return redirect()->route('home');
        
        $adminId = Auth::id();
        $selectedStudentId = $request->query('student_id');
        
        $conversations = Message::where('receiver_id', $adminId)
            ->orWhere('sender_id', $adminId)
            ->with(['sender', 'receiver'])
            ->latest()
            ->get()
            ->groupBy(function($message) use ($adminId) {
                return $message->sender_id == $adminId ? $message->receiver_id : $message->sender_id;
            })
            ->map(function($messages) {
                return $messages->first();
            })
            ->sortByDesc('created_at');
        
        $messages = collect();
        $selectedStudent = null;
        
        if ($selectedStudentId) {
            $selectedStudent = User::find($selectedStudentId);
            Message::where('sender_id', $selectedStudentId)
                ->where('receiver_id', $adminId)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);

            $messages = Message::where(function($query) use ($adminId, $selectedStudentId) {
                $query->where('sender_id', $selectedStudentId)
                      ->where('receiver_id', $adminId);
            })->orWhere(function($query) use ($adminId, $selectedStudentId) {
                $query->where('sender_id', $adminId)
                      ->where('receiver_id', $selectedStudentId);
            })->orderBy('created_at', 'asc')->get();
        }
        
        return view('admin.messages', compact('conversations', 'messages', 'selectedStudent', 'selectedStudentId'));
    }

    /**
     * GAD office meeting schedule management.
     */
    public function gadSchedules()
    {
        if (Auth::user()->role !== 'admin') return redirect()->route('home');

        $schedules = GadSchedule::with('creator')
            ->orderByDesc('available_from')
            ->latest()
            ->get();

        return view('admin.gad_schedules', compact('schedules'));
    }

    /**
     * Store a new schedule entry for students.
     */
    public function storeGadSchedule(Request $request)
    {
        if (Auth::user()->role !== 'admin') return redirect()->route('home');

        $validated = $request->validate([
            'title'           => ['required', 'string', 'max:255'],
            'location'        => ['required', 'string', 'max:255'],
            'available_from'  => ['required', 'date'],
            'available_until' => ['nullable', 'date', 'after_or_equal:available_from'],
            'details'         => ['nullable', 'string', 'max:1000'],
            'status'          => ['required', 'in:active,inactive'],
        ]);

        GadSchedule::create([
            ...$validated,
            'created_by' => Auth::id(),
        ]);

        return back()->with('success', 'GAD schedule saved successfully.');
    }

    /**
     * Update an existing schedule entry for students.
     */
    public function updateGadSchedule(Request $request, $id)
    {
        if (Auth::user()->role !== 'admin') return redirect()->route('home');

        $validated = $request->validate([
            'title'           => ['required', 'string', 'max:255'],
            'location'        => ['required', 'string', 'max:255'],
            'available_from'  => ['required', 'date'],
            'available_until' => ['nullable', 'date', 'after_or_equal:available_from'],
            'details'         => ['nullable', 'string', 'max:1000'],
            'status'          => ['required', 'in:active,inactive'],
        ]);

        $schedule = GadSchedule::findOrFail($id);
        $schedule->update($validated);

        return back()->with('success', 'GAD schedule updated successfully.');
    }

    /**
     * Toggle schedule visibility for students.
     */
    public function updateGadScheduleStatus(Request $request, $id)
    {
        if (Auth::user()->role !== 'admin') return redirect()->route('home');

        $validated = $request->validate([
            'status' => ['required', 'in:active,inactive'],
        ]);

        $schedule = GadSchedule::findOrFail($id);
        $schedule->update(['status' => $validated['status']]);

        return back()->with('success', 'Schedule status updated.');
    }

    /**
     * Sync calendar day toggles from the admin calendar UI.
     * Tinatanggap nito ang array ng { date, available } objects
     * at ino-update ang GadSchedule records accordingly.
     */
    public function syncGadSchedule(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'updates'              => ['required', 'array'],
            'updates.*.date'       => ['required', 'date_format:Y-m-d'],
            'updates.*.start_time' => ['nullable', 'date_format:H:i'],
            'updates.*.end_time'   => ['nullable', 'date_format:H:i'],
            'updates.*.remove'     => ['nullable', 'boolean'],
        ]);

        foreach ($request->updates as $update) {
            $date      = $update['date'];
            $remove    = (bool) ($update['remove'] ?? false);
            $startTime = $update['start_time'] ?? null;
            $endTime   = $update['end_time'] ?? null;

            $existing = GadSchedule::whereDate('available_from', $date)->first();

            if ($remove || (!$startTime && !$endTime)) {
                if ($existing) {
                    $existing->delete();
                }
                continue;
            }

            if ($startTime > $endTime) {
                return response()->json(['message' => 'Start time must be earlier than end time.'], 422);
            }

            $payload = [
                'title'           => 'Office Hours',
                'location'        => 'GAD Office',
                'available_from'  => $date . ' ' . $startTime . ':00',
                'available_until' => $date . ' ' . $endTime . ':00',
                'status'          => 'active',
            ];

            if ($existing) {
                $existing->update($payload);
            } else {
                GadSchedule::create($payload + ['created_by' => Auth::id()]);
            }
        }

        return response()->json(['success' => true]);
    }

    /**
     * Admin urgent calls interface
     */
    public function urgentCalls()
    {
        if (Auth::user()->role !== 'admin') return redirect()->route('home');
        return view('admin.urgent_calls');
    }

    /**
     * JSON feed for pending/joined urgent calls
     */
    public function urgentCallsFeed()
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $calls = Cache::get('urgent_calls', []);
        return response()->json(['calls' => $calls]);
    }

    /**
     * Mark call as joined by admin
     */
    public function joinUrgentCall($callId)
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $calls = Cache::get('urgent_calls', []);
        $room = null;

        foreach ($calls as &$call) {
            if (($call['id'] ?? null) === $callId) {
                $call['status']    = 'joined';
                $call['joined_at'] = now()->toDateTimeString();
                $call['joined_by'] = Auth::user()->name;
                $room = $call['room'] ?? null;
                break;
            }
        }
        unset($call);

        Cache::put('urgent_calls', $calls, now()->addHours(6));

        return response()->json([
            'ok'   => true,
            'room' => $room,
        ]);
    }

    /**
     * Approve newly registered user account.
     */
    public function approveUser($id)
    {
        if (Auth::user()->role !== 'admin') return redirect()->route('home');

        $user = User::findOrFail($id);

        $user->update([
            'account_status'   => 'active',
            'approved_at'      => now(),
            'approved_by'      => Auth::id(),
            'rejection_reason' => null,
        ]);

        try {
            Mail::raw(
                "Hello {$user->name},\n\nYour SINAG account has been approved by the GAD admin. You can now sign in and use the system.\n\nThank you.",
                function ($message) use ($user) {
                    $message->to($user->email)
                        ->subject('SINAG Account Approved');
                }
            );
        } catch (\Throwable $e) {
            \Log::warning('Approval email failed for user ' . $user->id . ': ' . $e->getMessage());
        }

        return back()->with('success', "User {$user->name} has been approved.");
    }

    /**
     * Reject newly registered user account.
     */
    public function rejectUser(Request $request, $id)
    {
        if (Auth::user()->role !== 'admin') return redirect()->route('home');

        $user = User::findOrFail($id);

        $reason = $request->input('rejection_reason');

        $user->update([
            'account_status'   => 'rejected',
            'approved_at'      => null,
            'approved_by'      => Auth::id(),
            'rejection_reason' => $reason,
        ]);

        $reasonText = $reason ? "Reason: {$reason}\n\n" : '';

        try {
            Mail::raw(
                "Hello {$user->name},\n\nYour SINAG registration was not approved at this time.\n{$reasonText}Please coordinate with the GAD office for the next steps.\n\nThank you.",
                function ($message) use ($user) {
                    $message->to($user->email)
                        ->subject('SINAG Account Verification Update');
                }
            );
        } catch (\Throwable $e) {
            \Log::warning('Rejection email failed for user ' . $user->id . ': ' . $e->getMessage());
        }

        return back()->with('success', "User {$user->name} has been rejected.");
    }

    /**
     * Delete a report (admin only)
     */
    public function deleteReport($id)
    {
        if (Auth::user()->role !== 'admin') return redirect()->route('home');
        $report = \App\Models\Report::findOrFail($id);
        $report->delete();
        return redirect()->back()->with('success', 'Report deleted successfully.');
    }

    /**
     * Store a message from admin to student
     */
    public function storeMessage(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'student_id' => 'required|exists:users,id',
            'message'    => 'required|string|max:1000',
        ]);

        $message = Message::create([
            'sender_id'   => Auth::id(),
            'receiver_id' => $request->student_id,
            'message'     => $request->message,
            'sender_type' => 'admin',
        ]);

        return response()->json([
            'success' => true,
            'message' => $message,
        ]);
    }

    /**
     * Show appointment requests management page
     */
    public function appointments()
    {
        if (Auth::user()->role !== 'admin') {
            return redirect()->route('home');
        }

        $appointments = AppointmentRequest::with('user')
            ->orderByRaw("FIELD(urgency_level, 'High', 'Medium', 'Low')")
            ->latest()
            ->paginate(30)
            ->withQueryString();

        $appointmentsByUrgency = $appointments->getCollection()->groupBy('urgency_level');

        return view('admin.appointments', compact('appointments', 'appointmentsByUrgency'));
    }

    /**
     * Mark an appointment request as completed.
     */
    public function completeAppointment($id)
    {
        if (Auth::user()->role !== 'admin') {
            return redirect()->route('home');
        }

        $appointment = AppointmentRequest::findOrFail($id);
        $appointment->update(['status' => 'completed']);

        return back()->with('success', 'Appointment marked as completed.');
    }

    public function cancelAppointment(Request $request, $id)
    {
        if (Auth::user()->role !== 'admin') {
            return redirect()->route('home');
        }

        $validated = $request->validate([
            'cancellation_reason' => ['required', 'string', 'max:1000'],
        ]);

        $appointment = AppointmentRequest::findOrFail($id);
        $appointment->update([
            'status' => 'cancelled',
            'cancellation_reason' => $validated['cancellation_reason'],
        ]);

        return back()->with('success', 'Appointment cancelled and the reason was recorded.');
    }

    /**
     * Add administrative details/notes to appointment
     */
    public function addAppointmentDetails(Request $request, $id)
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'admin_notes' => 'required|string|max:1000',
        ]);

        $appointment = AppointmentRequest::findOrFail($id);
        $appointment->update(['admin_notes' => $validated['admin_notes']]);

        return back()->with('success', 'Administrative details added successfully.');
    }

    /**
     * Update GAD Office information and hotline phone number.
     */
    public function updateOfficeInfo(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $validated = $request->validate([
            'phone_number' => ['required', 'string', 'max:50'],
            'office_hours' => ['nullable', 'string', 'max:100'],
            'office_location' => ['nullable', 'string', 'max:150'],
            'office_email' => ['nullable', 'email', 'max:150'],
        ]);

        $user = Auth::user();
        $user->phone_number = $validated['phone_number'];
        if (!empty($validated['office_email'])) {
            $user->email = $validated['office_email'];
        }
        $user->save();

        \Illuminate\Support\Facades\Cache::forever('gad_office_info', [
            'phone_number' => $validated['phone_number'],
            'office_hours' => $validated['office_hours'] ?? 'Mon – Fri, 8:00 AM – 5:00 PM',
            'office_location' => $validated['office_location'] ?? 'Admin Building, Room 105',
            'office_email' => $validated['office_email'] ?? $user->email ?? 'gad@psu.edu.ph',
        ]);

        return back()->with('success', 'GAD Office information and contact number updated successfully.');
    }

}