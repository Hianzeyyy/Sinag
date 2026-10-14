<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\AppointmentRequest;
use App\Models\GadSchedule;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StudentApiController extends Controller
{
    private const QUOTES = [
        'Your voice is valid, and your safety matters.',
        'Small steps toward safety are still brave steps.',
        'Speak up when something feels wrong. Courage protects community.',
        'Progress begins when someone chooses to be heard.',
        'Healing is not linear, but every step still counts.',
        'You deserve support that listens and responds with care.',
        'Safe spaces are built one honest conversation at a time.',
        'Choosing to ask for help is a strong decision.',
    ];

    // Static reference articles, same content as StudentController::reference()
    private const REFERENCES = [
        'ra9262' => ['title' => 'Republic Act 9262', 'subtitle' => 'Anti-Violence Against Women and Children (VAWC) Act of 2004', 'lead' => 'Republic Act 9262 protects women and children from abuse and violence in intimate or family relationships.', 'body' => 'The law covers physical, sexual, psychological, and economic abuse. It also gives victims legal remedies, protection orders, and access to support services so they can seek help safely and early.'],
        'ra8353' => ['title' => 'Republic Act 8353', 'subtitle' => 'The Anti-Rape Law of 1997', 'lead' => 'Republic Act 8353 strengthens the legal definition of rape and the protection of survivors.', 'body' => 'It recognizes rape as a crime against persons and explains the legal consequences, reporting process, and support available to survivors who choose to come forward.'],
        'ra9208' => ['title' => 'Republic Act 9208', 'subtitle' => 'Anti-Trafficking in Persons and Special Protection of Children Act', 'lead' => 'Republic Act 9208 targets trafficking in persons, especially women and children.', 'body' => 'The law defines trafficking offenses, sets penalties, and outlines protection measures that help rescue, safeguard, and support victims of exploitation and coercion.'],
        'ra9710' => ['title' => 'Republic Act 9710', 'subtitle' => 'Magna Carta of Women', 'lead' => 'Republic Act 9710 affirms the rights and dignity of women and girls.', 'body' => 'It promotes equality in education, employment, health, and safety, and requires institutions to create spaces and policies that support women’s rights in practice.'],
        'hivaids' => ['title' => 'HIV/AIDS Awareness', 'subtitle' => 'Prevention, care, and support information for students', 'lead' => 'HIV/AIDS awareness helps students understand prevention, care, and responsible behavior.', 'body' => 'The goal is to give clear, respectful information about how to protect yourself, support others, and seek reliable help without stigma or fear.'],
        'antisexualharassmentpolicy' => ['title' => 'Anti-Sexual Harassment Policy', 'subtitle' => 'Institutional policy on respectful conduct and reporting', 'lead' => 'This policy explains how the institution defines and handles sexual harassment.', 'body' => 'It lays out expected conduct, reporting steps, and the protection available to anyone who experiences or witnesses harassment on campus.'],
    ];

    private function announcementPayload(Announcement $a): array
    {
        return [
            'id' => $a->id,
            'title' => $a->title,
            'body' => $a->body,
            'author' => optional($a->user)->name,
            'created_at' => optional($a->created_at)->toIso8601String(),
        ];
    }

    private function appointmentPayload(AppointmentRequest $a): array
    {
        return [
            'id' => $a->id,
            'full_name' => $a->full_name,
            'student_id' => $a->student_id,
            'urgency_level' => $a->urgency_level,
            'scheduled_date' => optional($a->scheduled_date)->toDateString(),
            'scheduled_time' => optional($a->scheduled_time)->format('H:i'),
            'description' => $a->description,
            'format' => $a->format,
            'status' => $a->status,
            'admin_notes' => $a->admin_notes,
            'cancellation_reason' => $a->cancellation_reason,
        ];
    }

    private function adminUser(): ?User
    {
        return User::where('role', 'admin')->first();
    }

    public function dashboard(): JsonResponse
    {
        $announcements = Announcement::with('user')->latest()->take(10)->get();

        return response()->json([
            'quote' => self::QUOTES[now()->dayOfYear % count(self::QUOTES)],
            'announcements' => $announcements->map(fn ($a) => $this->announcementPayload($a))->values(),
        ]);
    }

    public function announcement($id): JsonResponse
    {
        return response()->json($this->announcementPayload(Announcement::with('user')->findOrFail($id)));
    }

    public function gadSchedules(): JsonResponse
    {
        $rows = GadSchedule::whereIn('status', ['active', 'unavailable', 'inactive'])
            ->orderBy('available_from')->get()->map(fn ($s) => [
                'id' => $s->id,
                'title' => $s->title,
                'location' => $s->location,
                'details' => $s->details,
                'status' => $s->status,
                'available_from' => optional($s->available_from)->toIso8601String(),
                'available_until' => optional($s->available_until)->toIso8601String(),
            ]);

        return response()->json(['data' => $rows]);
    }

    public function references(): JsonResponse
    {
        $list = [];
        foreach (self::REFERENCES as $slug => $r) {
            $list[] = ['slug' => $slug] + $r;
        }

        return response()->json(['data' => $list]);
    }

    public function reference(string $slug): JsonResponse
    {
        abort_unless(isset(self::REFERENCES[$slug]), 404);

        return response()->json(['slug' => $slug] + self::REFERENCES[$slug]);
    }

    // ---- Appointments ----

    public function appointments(Request $request): JsonResponse
    {
        $rows = AppointmentRequest::where('user_id', $request->user()->id)->latest()->get();

        return response()->json(['data' => $rows->map(fn ($a) => $this->appointmentPayload($a))->values()]);
    }

    public function storeAppointment(Request $request): JsonResponse
    {
        $v = $request->validate([
            'full_name' => 'required|string|max:255',
            'student_id' => 'required|string|max:255',
            'urgency_level' => 'required|in:Low,Medium,High',
            'scheduled_date' => 'required|date|after_or_equal:today',
            'scheduled_time' => 'required|date_format:H:i',
            'format' => 'required|in:Face-to-Face,Online',
            'description' => 'required|string|max:2000',
        ]);

        $active = AppointmentRequest::where('user_id', $request->user()->id)
            ->whereIn('status', ['pending', 'approved'])->exists();
        if ($active) {
            return response()->json([
                'message' => 'You already have an active appointment. Edit or cancel it before scheduling another.',
            ], 422);
        }

        DB::table('appointment_requests')->insert([
            'id' => ((int) DB::table('appointment_requests')->max('id')) + 1,
            'user_id' => $request->user()->id,
            'full_name' => $v['full_name'],
            'student_id' => $v['student_id'],
            'purpose_of_visit' => 'Not specified',
            'urgency_level' => $v['urgency_level'],
            'scheduled_date' => $v['scheduled_date'],
            'scheduled_time' => $v['scheduled_time'],
            'format' => $v['format'],
            'description' => $v['description'],
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['message' => 'Appointment request submitted. Waiting for GAD office approval.'], 201);
    }

    public function updateAppointment(Request $request, $id): JsonResponse
    {
        $a = AppointmentRequest::where('user_id', $request->user()->id)->findOrFail($id);
        abort_unless($a->status === 'pending', 403, 'Only pending appointments can be edited.');
        $a->update($request->validate([
            'scheduled_date' => 'required|date|after_or_equal:today',
            'scheduled_time' => 'required|date_format:H:i',
        ]));

        return response()->json(['ok' => true]);
    }

    public function cancelAppointment(Request $request, $id): JsonResponse
    {
        $a = AppointmentRequest::where('user_id', $request->user()->id)->findOrFail($id);
        abort_unless($a->status === 'pending', 403, 'Only pending appointments can be cancelled.');
        $a->update(['status' => 'cancelled']);

        return response()->json(['ok' => true]);
    }

    // ---- Messaging with GAD office ----

    public function messages(Request $request): JsonResponse
    {
        $uid = $request->user()->id;
        $admin = $this->adminUser();

        if ($admin) {
            Message::where('sender_id', $admin->id)->where('receiver_id', $uid)
                ->whereNull('read_at')->update(['read_at' => now()]);
        }

        $messages = $admin
            ? Message::where(fn ($q) => $q->where('sender_id', $uid)->where('receiver_id', $admin->id))
                ->orWhere(fn ($q) => $q->where('sender_id', $admin->id)->where('receiver_id', $uid))
                ->orderBy('created_at')->get()
            : collect();

        $office = Cache::get('gad_office_info', [
            'phone_number' => $admin?->phone_number ?: '0919-777-7377',
            'office_hours' => 'Mon – Fri, 8:00 AM – 5:00 PM',
            'office_location' => 'Admin Building, Room 105',
            'office_email' => $admin?->email ?: 'gad@psu.edu.ph',
        ]);

        return response()->json([
            'office' => $office,
            'data' => $messages->map(fn ($m) => [
                'id' => $m->id,
                'message' => $m->deleted_at ? '' : $m->message,
                'original_message' => $m->deleted_at ? null : $m->original_message,
                'deleted_at' => optional($m->deleted_at)->toIso8601String(),
                'mine' => $m->sender_id === $uid,
                'read_at' => optional($m->read_at)->toIso8601String(),
                'created_at' => optional($m->created_at)->toIso8601String(),
            ])->values(),
        ]);
    }

    public function storeMessage(Request $request): JsonResponse
    {
        $request->validate(['message' => 'required|string|max:1000']);
        $admin = $this->adminUser();
        if (!$admin) {
            return response()->json(['message' => 'No admin available'], 404);
        }

        $id = ((int) DB::table('messages')->max('id')) + 1;
        DB::table('messages')->insert([
            'id' => $id,
            'sender_id' => $request->user()->id,
            'receiver_id' => $admin->id,
            'message' => $request->message,
            'sender_type' => 'student',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['id' => $id], 201);
    }

    public function updateMessage(Request $request, $id): JsonResponse
    {
        $request->validate(['message' => 'required|string|max:1000']);
        $m = Message::findOrFail($id);
        $admin = $this->adminUser();
        abort_unless($admin && (int) $m->sender_id === (int) $request->user()->id && (int) $m->receiver_id === (int) $admin->id, 403);
        abort_if($m->deleted_at, 409, 'Deleted messages cannot be edited.');
        $m->original_message ??= $m->message;
        $m->message = $request->message;
        $m->save();

        return response()->json(['ok' => true]);
    }

    public function destroyMessage(Request $request, $id): JsonResponse
    {
        $m = Message::findOrFail($id);
        $admin = $this->adminUser();
        abort_unless($admin && (int) $m->sender_id === (int) $request->user()->id && (int) $m->receiver_id === (int) $admin->id, 403);
        if (!$m->deleted_at) {
            $m->deleted_at = now();
            $m->message = '';
            $m->original_message = null;
            $m->save();
        }

        return response()->json(['ok' => true]);
    }

    // ---- Urgent call ----

    public function urgentNotify(Request $request): JsonResponse
    {
        $request->validate(['room' => ['nullable', 'string', 'max:120']]);
        $calls = Cache::get('urgent_calls', []);
        $callId = (string) Str::uuid();
        $room = $request->input('room') ?: ('sinag-urgent-' . now()->format('YmdHis') . '-' . $request->user()->id);

        array_unshift($calls, [
            'id' => $callId,
            'room' => $room,
            'status' => 'waiting',
            'student_id' => $request->user()->id,
            'student_name' => $request->user()->name,
            'created_at' => now()->toDateTimeString(),
        ]);
        Cache::put('urgent_calls', array_slice($calls, 0, 50), now()->addHours(6));

        return response()->json(['status' => 'notified', 'call_id' => $callId, 'room' => $room]);
    }
}
