<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use App\Models\Announcement;
use App\Models\Report;
use App\Models\Suggestion;
use App\Models\Message;
use App\Models\User;
use App\Models\GadSchedule;
use App\Models\AppointmentRequest;

class StudentController extends Controller
{
    /**
     * Ginagamit ang 'auth' at 'can:student-access' middleware
     * para sa security at role-based access.
     */
    public function __construct()
    {
        $this->middleware(['auth', 'can:student-access']);
    }

    /**
     * Main Dashboard ng Student
     */
    public function index()
    {
        $announcements = Announcement::with('user')
            ->latest()
            ->take(10)
            ->get();

        $featuredAnnouncement = $announcements->first();

        $quotes = [
            'Your voice is valid, and your safety matters.',
            'Small steps toward safety are still brave steps.',
            'Speak up when something feels wrong. Courage protects community.',
            'Progress begins when someone chooses to be heard.',
            'Healing is not linear, but every step still counts.',
            'You deserve support that listens and responds with care.',
            'Safe spaces are built one honest conversation at a time.',
            'Choosing to ask for help is a strong decision.',
        ];

        $motivationQuote = $quotes[now()->dayOfYear % count($quotes)];

        return view('student.dashboard', compact(
            'announcements',
            'featuredAnnouncement',
            'motivationQuote'
        ));
    }

    /**
     * Open a single announcement for students
     */
    public function announcement(string $id)
    {
        $announcement = Announcement::with('user')->findOrFail($id);

        return view('student.announcement', compact('announcement'));
    }

    /**
     * GAD office schedule page for students.
     */
    public function gadSchedule()
    {
        $schedules = GadSchedule::with('creator')
            ->whereIn('status', ['active', 'unavailable', 'inactive'])
            ->orderBy('available_from')
            ->get();

        return view('student.gad_schedule', compact('schedules'));
    }

    /**
     * Safe Spaces Act Reporting Module (File a Report)
     */
    public function report()
    {
        return view('student.report');
    }

    /**
     * The Evidence Vault (Digital Locker)
     * Dito makikita ang status ng sariling reports at suggestions ng user.
     */
    public function vault()
    {
        $user_id = Auth::id();

        // Kunin ang sariling data ng user
        $myReports = Report::where('user_id', $user_id)->latest()->get();
        $mySuggestions = Suggestion::where('user_id', $user_id)->latest()->get();

        return view('student.vault', compact('myReports', 'mySuggestions'));
    }

    /**
     * Participatory Suggestions Hub
     * Shared wall: lahat ng suggestions ay makikita ng users para makapag-react at comment.
     */
    public function boses(Request $request)
    {
        if (!$request->user()->hasCompletedPersonalData()) {
            return redirect()->route('personal-data.edit', ['return_to_suggestions' => 1])
                ->with('personal_data_required', 'Complete your personal data to access Community Suggestions.');
        }

        $query = Suggestion::with(['user', 'comments.user'])
            ->withCount('upvotes')
            ->where(function ($query) use ($request) {
                $query->where('status', 'Approved')
                    ->orWhere('user_id', $request->user()->id);
            });

        if ($request->filled('category')) {
            $query->where('category', $request->string('category'));
        }

        $allSuggestions = $query->latest()->get();

        $categories = [
            'Gender-Based Violence Prevention',
            'Gender Equality and Inclusion',
            'GAD Programs and Services',
            'Safe Spaces and Student Welfare',
            'GAD Policy and Training',
            'Other GAD Concern',
        ];

        $existingAliases = User::query()
            ->whereNotNull('cloak_alias')
            ->where('cloak_alias', '!=', '')
            ->where('id', '!=', Auth::id())
            ->pluck('cloak_alias')
            ->map(fn ($alias) => trim((string) $alias))
            ->filter()
            ->values()
            ->all();

        return view('student.boses', compact('allSuggestions', 'categories', 'existingAliases'));
    }

    /**
     * RA 11313 Library (Wellness)
     */
    public function wellness()
    {
        return view('student.wellness');
    }

    /**
     * Reference article page
     */
    public function reference(string $slug)
    {
        $references = [
            'ra9262' => [
                'type' => 'Reference',
                'title' => 'Republic Act 9262',
                'subtitle' => 'Anti-Violence Against Women and Children (VAWC) Act of 2004',
                'image' => 'images/ra9262.jpg',
                'lead' => 'Republic Act 9262 protects women and children from abuse and violence in intimate or family relationships.',
                'body' => 'The law covers physical, sexual, psychological, and economic abuse. It also gives victims legal remedies, protection orders, and access to support services so they can seek help safely and early.',
            ],
            'ra8353' => [
                'type' => 'Reference',
                'title' => 'Republic Act 8353',
                'subtitle' => 'The Anti-Rape Law of 1997',
                'image' => 'images/ra8353.jpg',
                'lead' => 'Republic Act 8353 strengthens the legal definition of rape and the protection of survivors.',
                'body' => 'It recognizes rape as a crime against persons and explains the legal consequences, reporting process, and support available to survivors who choose to come forward.',
            ],
            'ra9208' => [
                'type' => 'Reference',
                'title' => 'Republic Act 9208',
                'subtitle' => 'Anti-Trafficking in Persons and Special Protection of Children Act',
                'image' => 'images/ra9208.jpg',
                'lead' => 'Republic Act 9208 targets trafficking in persons, especially women and children.',
                'body' => 'The law defines trafficking offenses, sets penalties, and outlines protection measures that help rescue, safeguard, and support victims of exploitation and coercion.',
            ],
            'ra9710' => [
                'type' => 'Reference',
                'title' => 'Republic Act 9710',
                'subtitle' => 'Magna Carta of Women',
                'image' => 'images/ra9710.jpg',
                'lead' => 'Republic Act 9710 affirms the rights and dignity of women and girls.',
                'body' => 'It promotes equality in education, employment, health, and safety, and requires institutions to create spaces and policies that support women’s rights in practice.',
            ],
            'hivaids' => [
                'type' => 'Reference',
                'title' => 'HIV/AIDS Awareness',
                'subtitle' => 'Prevention, care, and support information for students',
                'image' => 'images/hivaids.jpg',
                'lead' => 'HIV/AIDS awareness helps students understand prevention, care, and responsible behavior.',
                'body' => 'The goal is to give clear, respectful information about how to protect yourself, support others, and seek reliable help without stigma or fear.',
            ],
            'antisexualharassmentpolicy' => [
                'type' => 'Reference',
                'title' => 'Anti-Sexual Harassment Policy',
                'subtitle' => 'Institutional policy on respectful conduct and reporting',
                'image' => 'images/antisexualharassmentpolicy.jpg',
                'lead' => 'This policy explains how the institution defines and handles sexual harassment.',
                'body' => 'It lays out expected conduct, reporting steps, and the protection available to anyone who experiences or witnesses harassment on campus.',
            ],
        ];

        abort_unless(isset($references[$slug]), 404);

        $article = $references[$slug];

        return view('student.article', compact('article'));
    }

    /**
     * Messaging with GAD Office
     */
    public function messaging()
    {
        $userId = Auth::id();

        // Get first admin (or use a specific admin if you prefer)
        $admin = User::where('role', 'admin')->first();

        if ($admin) {
            Message::where('sender_id', $admin->id)
                ->where('receiver_id', $userId)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
        }

        // Get all messages between student and admin
        $messages = Message::where(function($query) use ($userId, $admin) {
            $query->where('sender_id', $userId)
                  ->where('receiver_id', $admin?->id);
        })->orWhere(function($query) use ($userId, $admin) {
            $query->where('sender_id', $admin?->id)
                  ->where('receiver_id', $userId);
        })->orderBy('created_at', 'asc')->get();

        $officeInfo = \Illuminate\Support\Facades\Cache::get('gad_office_info', [
            'phone_number' => $admin?->phone_number ?: '0919-777-7377',
            'office_hours' => 'Mon – Fri, 8:00 AM – 5:00 PM',
            'office_location' => 'Admin Building, Room 105',
            'office_email' => $admin?->email ?: 'gad@psu.edu.ph',
        ]);

        return view('student.messaging', compact('messages', 'admin', 'officeInfo'));
    }

    /**
     * Urgent call placeholder (Zoom-like page)
     */
    public function urgentCall()
    {
        return view('student.urgent_call');
    }

    /**
     * Handle urgent notify (placeholder) - notify admin to join call
     */
    public function urgentNotify(Request $request)
    {
        $request->validate([
            'room' => ['nullable', 'string', 'max:120'],
        ]);

        $calls = Cache::get('urgent_calls', []);

        $callId = (string) Str::uuid();
        $room = $request->input('room') ?: ('sinag-urgent-' . Str::uuid());

        array_unshift($calls, [
            'id' => $callId,
            'room' => $room,
            'status' => 'waiting',
            'student_id' => Auth::id(),
            'student_name' => Auth::user()->name,
            'created_at' => now()->toDateTimeString(),
        ]);

        // Keep list short and lightweight
        $calls = array_slice($calls, 0, 50);
        Cache::put('urgent_calls', $calls, now()->addHours(6));

        return response()->json([
            'status' => 'notified',
            'call_id' => $callId,
            'room' => $room,
        ]);
    }

    public function endUrgentCall(string $callId)
    {
        $calls = Cache::get('urgent_calls', []);
        $found = false;
        foreach ($calls as &$call) {
            if (($call['id'] ?? null) === $callId && (int) ($call['student_id'] ?? 0) === (int) Auth::id()) {
                $call['status'] = 'ended';
                $call['ended_at'] = now()->toDateTimeString();
                $found = true;
                break;
            }
        }
        unset($call);

        abort_unless($found, 404, 'Call not found or it is no longer active.');
        Cache::put('urgent_calls', $calls, now()->addHours(6));

        return response()->json(['status' => 'ended']);
    }

    public function urgentCallStatus(string $callId)
    {
        $calls = Cache::get('urgent_calls', []);
        $call = collect($calls)->first(fn ($entry) =>
            ($entry['id'] ?? null) === $callId
            && (int) ($entry['student_id'] ?? 0) === (int) Auth::id()
        );

        abort_unless($call, 404, 'Call not found.');

        return response()->json([
            'status' => $call['status'] ?? 'waiting',
            'joined_by' => $call['joined_by'] ?? null,
        ])->header('Cache-Control', 'no-store, no-cache, must-revalidate');
    }

    /**
     * Event detail page for carousel items (placeholder content)
     */
    public function event($id)
    {
        $events = [
            1 => [
                'type' => 'Event',
                'title' => 'GAD Graphics Launch',
                'subtitle' => 'A cleaner visual identity for student-facing GAD updates and support stories.',
                'image' => 'images/gadeventnew1.png',
                'lead' => 'The new GAD graphics set brings a more modern look to campus support communication.',
                'body' => 'This feature story introduces the visual refresh used across the dashboard, event cards, and announcement layouts. It is meant to make the GAD office easier to recognize while keeping student guidance clear and readable.',
            ],
            2 => [
                'type' => 'Event',
                'title' => 'Awareness and Support Week',
                'subtitle' => 'Campus reminders about reporting, privacy, and student care.',
                'image' => 'images/gadevent2.1.jpg',
                'lead' => 'Awareness events remind students that support is part of campus life, not an emergency-only feature.',
                'body' => 'This event focuses on how to report concerns, where to go for confidential support, and how to use the dashboard tools for quick access to help and updates.',
            ],
            3 => [
                'type' => 'Event',
                'title' => 'Support Spotlight',
                'subtitle' => 'Community visibility helps students reach assistance faster.',
                'image' => 'images/gadevent3.1.jpg',
                'lead' => 'Visible support makes reporting and follow-up feel less intimidating.',
                'body' => 'This event centers on student safety visibility, reassuring support channels, and the importance of making GAD information easy to find and understand.',
            ],
            4 => [
                'type' => 'Event',
                'title' => 'Student Safety Connect',
                'subtitle' => 'A faster way to link students with the right GAD office support.',
                'image' => 'images/gadevent4.1.jpg',
                'lead' => 'The dashboard now highlights event pages that feel more like article stories than simple links.',
                'body' => 'This event page shows how the refreshed interface organizes the most important student safety content with stronger visuals, hover states, and clearer routes to full details.',
            ],
            5 => [
                'type' => 'Event',
                'title' => 'Community Awareness',
                'subtitle' => 'Building a safer, more informed, and inclusive campus community.',
                'image' => 'images/gadgraphics5.jpg',
                'lead' => 'Awareness grows when students have clear information and visible support.',
                'body' => 'This event highlights the shared responsibility of students and the GAD office in creating a respectful, responsive, and inclusive university environment.',
            ],
        ];

        abort_unless(isset($events[$id]), 404);

        $article = $events[$id];

        return view('student.article', compact('article'));
    }

    /**
     * Store a message from student to admin
     */
    public function storeMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $admin = User::where('role', 'admin')->first();

        if (!$admin) {
            return response()->json(['error' => 'No admin available'], 404);
        }

        $messageId = ((int) \DB::table('messages')->max('id')) + 1;
        \DB::table('messages')->insert([
            'id' => $messageId,
            'sender_id' => Auth::id(),
            'receiver_id' => $admin->id,
            'message' => $request->message,
            'sender_type' => 'student',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $message = Message::findOrFail($messageId);

        return response()->json([
            'success' => true,
            'message' => $message,
        ]);
    }

    public function messageFeed(Request $request)
    {
        $admin = User::where('role', 'admin')->first();
        abort_unless($admin, 404, 'GAD Office is unavailable.');
        $messages = Message::where(function ($query) use ($admin) {
                $query->where(function ($query) use ($admin) {
                    $query->where('sender_id', Auth::id())->where('receiver_id', $admin->id);
                })->orWhere(function ($query) use ($admin) {
                    $query->where('sender_id', $admin->id)->where('receiver_id', Auth::id());
                });
            })
            ->orderBy('id')
            ->get()
            ->values();

        Message::where('sender_id', $admin->id)
            ->where('receiver_id', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json([
            'messages' => $messages->map(fn (Message $message) => [
                'id' => $message->id,
                'message' => $message->message,
                'original_message' => $message->original_message,
                'deleted_at' => $message->deleted_at?->toIso8601String(),
                'sender_id' => $message->sender_id,
                'created_at' => $message->created_at?->format('M d, g:i A'),
            ]),
        ])->header('Cache-Control', 'no-store, no-cache, must-revalidate');
    }

    public function notificationFeed()
    {
        $admin = User::where('role', 'admin')->first();
        $unreadQuery = Message::query()
            ->where('receiver_id', Auth::id())
            ->whereNull('read_at')
            ->whereNull('deleted_at');

        if ($admin) {
            $unreadQuery->where('sender_id', $admin->id);
        } else {
            $unreadQuery->whereRaw('1 = 0');
        }

        $latestUnread = (clone $unreadQuery)->latest('id')->first();
        $latestCall = collect(Cache::get('urgent_calls', []))->first(
            fn ($call) => (int) ($call['student_id'] ?? 0) === (int) Auth::id()
        );

        return response()->json([
            'unread_count' => $unreadQuery->count(),
            'latest_unread_id' => $latestUnread?->id,
            'latest_call' => $latestCall ? [
                'id' => $latestCall['id'] ?? null,
                'status' => $latestCall['status'] ?? 'waiting',
            ] : null,
        ])->header('Cache-Control', 'no-store, no-cache, must-revalidate');
    }

    /**
     * Update an existing message (student can only edit their own messages)
     */
    public function updateMessage(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $admin = User::where('role', 'admin')->first();
        abort_unless($admin, 404, 'GAD Office is unavailable.');
        $message = Message::findOrFail($id);

        if ((int) $message->sender_id !== (int) Auth::id() || (int) $message->receiver_id !== (int) $admin->id) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        abort_if($message->deleted_at, 409, 'Deleted messages cannot be edited.');
        $message->original_message ??= $message->message;
        $message->message = $request->message;
        $message->save();

        return response()->json(['success' => true, 'message' => $message]);
    }

    /**
     * Delete a message (student can only delete their own messages)
     */
    public function destroyMessage(Request $request, $id)
    {
        $admin = User::where('role', 'admin')->first();
        abort_unless($admin, 404, 'GAD Office is unavailable.');
        $message = Message::findOrFail($id);

        if ((int) $message->sender_id !== (int) Auth::id() || (int) $message->receiver_id !== (int) $admin->id) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        if (!$message->deleted_at) {
            $message->deleted_at = now();
            $message->message = '';
            $message->original_message = null;
            $message->save();
        }

        return response()->json(['success' => true]);
    }

    /**
     * Show the appointment request form
     */
    public function appointmentForm()
    {
        return view('student.schedule_appointment');
    }

    /**
     * Store a new appointment request
     */
    public function storeAppointment(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'student_id' => 'required|string|max:255',
            'urgency_level' => 'required|in:Low,Medium,High',
            'scheduled_date' => 'required|date|after_or_equal:today',
            'scheduled_time' => 'required|date_format:H:i',
            'format' => 'required|in:Face-to-Face,Online',
            'description' => 'required|string|max:2000',
        ]);

        if ($this->isGadOfficeUnavailable($validated['scheduled_date'])) {
            return back()->withInput()->withErrors([
                'scheduled_date' => 'The GAD Office is unavailable on this date. Please choose another date.',
            ]);
        }

        $hasActiveRequest = AppointmentRequest::where('user_id', Auth::id())
            ->whereIn('status', ['pending', 'approved'])
            ->exists();

        if ($hasActiveRequest) {
            return redirect()->route('student.appointments')
                ->withErrors(['appointment' => 'You already have an active appointment. Edit or cancel it before scheduling another.']);
        }

        $appointmentId = ((int) \DB::table('appointment_requests')->max('id')) + 1;
        \DB::table('appointment_requests')->insert([
            'id' => $appointmentId,
            'user_id' => Auth::id(),
            'full_name' => $validated['full_name'],
            'student_id' => $validated['student_id'],
            'purpose_of_visit' => 'Not specified',
            'urgency_level' => $validated['urgency_level'],
            'scheduled_date' => $validated['scheduled_date'],
            'scheduled_time' => $validated['scheduled_time'],
            'format' => $validated['format'],
            'description' => $validated['description'],
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('student.schedule-appointment')
            ->with('success', 'Ang iyong appointment request ay naisumite na. Hinihintay ang aprubahan ng GAD office.');
    }

    public function updateAppointment(Request $request, $id)
    {
        $appointment = AppointmentRequest::where('user_id', Auth::id())->findOrFail($id);

        abort_unless($appointment->status === 'pending', 403, 'Only pending appointments can be edited.');

        $validated = $request->validate([
            'scheduled_date' => 'required|date|after_or_equal:today',
            'scheduled_time' => 'required|date_format:H:i',
        ]);

        if ($this->isGadOfficeUnavailable($validated['scheduled_date'])) {
            return back()->withInput()->withErrors([
                'scheduled_date' => 'The GAD Office is unavailable on this date. Please choose another date.',
            ]);
        }

        $appointment->update($validated);

        return redirect()->route('student.appointments')->with('success', 'Appointment date and time updated.');
    }

    private function isGadOfficeUnavailable(string $date): bool
    {
        return GadSchedule::query()
            ->where(function ($query) {
                $query->where('status', 'unavailable')->orWhere('details', 'unavailable');
            })
            ->whereDate('available_from', '<=', $date)
            ->where(function ($query) use ($date) {
                $query->whereNull('available_until')->orWhereDate('available_until', '>=', $date);
            })
            ->exists();
    }

    public function cancelAppointment($id)
    {
        $appointment = AppointmentRequest::where('user_id', Auth::id())->findOrFail($id);

        abort_unless($appointment->status === 'pending', 403, 'Only pending appointments can be cancelled.');

        $appointment->update(['status' => 'cancelled']);

        return redirect()->route('student.appointments')->with('success', 'Appointment cancelled.');
    }

    public function deleteAppointment($id)
    {
        $appointment = AppointmentRequest::where('user_id', Auth::id())->findOrFail($id);

        abort_unless(in_array($appointment->status, ['cancelled', 'rejected'], true), 403, 'Only cancelled or rejected appointments can be deleted.');

        $appointment->delete();

        return redirect()->route('student.appointments')->with('success', 'Appointment record deleted.');
    }

    /**
     * Show all appointments of the current user
     */
    public function myAppointments()
    {
        $appointments = AppointmentRequest::where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('student.appointments', compact('appointments'));
    }

}
