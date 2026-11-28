<?php

namespace App\Http\Controllers;
use App\Mail\AccountStatusMail;
use App\Mail\ReportResolvedMail;
use App\Notifications\AccountStatusNotification;
use App\Notifications\ReportResolvedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Report;
use App\Models\Suggestion;
use App\Models\User;
use App\Models\Message;
use App\Models\GadSchedule;
use App\Models\AppointmentRequest;
use App\Models\CustomerSatisfactionSurvey;
use App\Models\ReportStatusUpdate;
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
        $user = Auth::user();
        if (strtolower(trim((string) optional($user)->role)) !== 'admin') {
            return redirect()->route('home')->with('error', 'Unauthorized access.');
        }

        if ($user->isGadCoordinator()) {
            $reportStatusCounts = Report::notArchived()
                ->selectRaw("COALESCE(status, 'Unspecified') as status, COUNT(*) as total")
                ->groupBy('status')
                ->orderBy('status')
                ->get();
            $reportNatureCounts = Report::notArchived()
                ->selectRaw("COALESCE(nature, 'Unspecified') as nature, COUNT(*) as total")
                ->groupBy('nature')
                ->orderByDesc('total')
                ->orderBy('nature')
                ->get();
            $reportCountsByStatus = $reportStatusCounts->pluck('total', 'status');
            $totalReports = (int) $reportStatusCounts->sum('total');
            $reportCountsByPriority = Report::notArchived()
                ->selectRaw("COALESCE(priority, 'Unspecified') as priority, COUNT(*) as total")
                ->groupBy('priority')
                ->orderBy('priority')
                ->get();
            $feedbackCount = 0;
            $averageRating = 0.0;
            $recentFeedback = collect();
            if (Schema::hasTable('customer_satisfaction_surveys')) {
                $feedbackQuery = CustomerSatisfactionSurvey::query();
                $feedbackCount = (clone $feedbackQuery)->count();
                $averageRating = (float) ((clone $feedbackQuery)->avg('rating') ?? 0);
                $recentFeedback = $feedbackQuery->with('user')->latest()->take(5)->get();
            }

            return view('admin.coordinator-dashboard', compact(
                'reportStatusCounts',
                'reportNatureCounts',
                'reportCountsByStatus',
                'reportCountsByPriority',
                'totalReports',
                'feedbackCount',
                'averageRating',
                'recentFeedback'
            ));
        }

        // 1. Dashboard Summary Data
        $totalReports     = Report::notArchived()->count();
        $pendingReports   = Report::notArchived()->where('status', 'Pending')->count();
        $resolvedReports  = Report::notArchived()->where('status', 'Resolved')->count();
        $totalSuggestions = Suggestion::notArchived()->count();
        $resolvedPercentage = $totalReports > 0 ? (int) round(($resolvedReports / $totalReports) * 100) : 0;
        $pendingPercentage = $totalReports > 0 ? (int) round(($pendingReports / $totalReports) * 100) : 0;

        // 2. Active Emergencies Logic
        $activeEmergencies = Report::notArchived()->where(function($query) {
                                    $query->where('status', 'Emergency')
                                          ->orWhere('priority', 'High');
                                })
                                ->where('status', '!=', 'Resolved')
                                ->count();

        // 3. Recent Reports Table
        $recentReports = Report::notArchived()->with('user')
                                ->orderByRaw("CASE WHEN status = 'Emergency' THEN 0 ELSE 1 END")
                                ->latest()
                                ->take(5)
                                ->get();

        $recentUsers = User::where('role', '!=', 'admin')
            ->latest('created_at')
            ->take(5)
            ->get();
        $accountQuery = User::where('role', '!=', 'admin');
        if (Schema::hasColumn('users', 'account_status')) {
            $approvedAccountCount = (clone $accountQuery)
                ->where(function ($query) {
                    $query->whereIn('account_status', ['active', 'approved'])
                        ->orWhereNull('account_status');
                })
                ->count();
            $pendingAccountCount = (clone $accountQuery)
                ->where('account_status', 'pending')
                ->count();
        } else {
            $approvedAccountCount = (clone $accountQuery)->count();
            $pendingAccountCount = 0;
        }
        $upcomingAppointments = AppointmentRequest::with('user')
            ->whereDate('scheduled_date', '>=', today())
            ->whereIn('status', ['pending', 'approved'])
            ->orderBy('scheduled_date')
            ->orderBy('scheduled_time')
            ->take(5)
            ->get();
        $reportCategories = Report::notArchived()
            ->selectRaw("COALESCE(nature, 'Unspecified') as label, COUNT(*) as total")
            ->groupBy('nature')
            ->orderByDesc('total')
            ->limit(6)
            ->get();
        $suggestionCategories = Suggestion::notArchived()
            ->selectRaw("COALESCE(category, 'Unspecified') as label, COUNT(*) as total")
            ->groupBy('category')
            ->orderByDesc('total')
            ->limit(6)
            ->get();
        $recentSuggestions = Suggestion::notArchived()
            ->with('user')
            ->withCount(['comments', 'upvotes'])
            ->latest()
            ->take(2)
            ->get();
        $feedbackCount = 0;
        $averageRating = 0.0;
        $recentFeedback = collect();
        if (Schema::hasTable('customer_satisfaction_surveys')) {
            $feedbackQuery = CustomerSatisfactionSurvey::query();
            $feedbackCount = (clone $feedbackQuery)->count();
            $averageRating = (float) (clone $feedbackQuery)->avg('rating');
            $recentFeedback = $feedbackQuery->with('user')->latest()->take(5)->get();
        }
        $dashboardNow = now('Asia/Manila');
        $greeting = $dashboardNow->hour < 12 ? 'Good morning' : ($dashboardNow->hour < 18 ? 'Good afternoon' : 'Good evening');
        $dashboardDate = $dashboardNow->format('l, F j, Y');

        $officeInfo = Cache::get('gad_office_info', [
            'phone_number' => Auth::user()->phone_number ?: '0919-777-7377',
            'office_hours' => 'Mon – Fri, 8:00 AM – 5:00 PM',
            'office_location' => 'Admin Building, Room 105',
            'office_email' => Auth::user()->email ?: 'gad@psu.edu.ph',
        ]);

        return view('admin.dashboard', compact(
            'totalReports',
            'pendingReports',
            'resolvedReports',
            'resolvedPercentage',
            'pendingPercentage',
            'totalSuggestions',
            'activeEmergencies',
            'recentReports',
            'recentUsers',
            'approvedAccountCount',
            'pendingAccountCount',
            'upcomingAppointments',
            'reportCategories',
            'suggestionCategories',
            'recentSuggestions',
            'feedbackCount',
            'averageRating',
            'recentFeedback',
            'greeting',
            'dashboardDate',
            'officeInfo'
        ));
    }

    /**
     * Confidential Inbox: Listahan ng lahat ng reports.
     */
    public function reports(Request $request)
    {
        if (strtolower(trim((string) optional(Auth::user())->role)) !== 'admin') return redirect()->route('home');

        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'in:Pending,Under Review,Resolved,Emergency'],
            'report_id' => ['nullable', 'integer', 'min:1'],
        ]);
        $query = trim($validated['q'] ?? '');
        $escapedQuery = '%' . addcslashes($query, '\\%_') . '%';

        $relations = Schema::hasTable('report_status_updates')
            ? ['user', 'statusUpdates']
            : ['user'];
        $reports = Report::query()->with($relations)
            ->when(!isset($validated['report_id']), fn ($reports) => $reports->notArchived())
            ->when(isset($validated['report_id']), fn ($reports) => $reports->whereKey($validated['report_id']))
            ->when($query !== '', fn ($reports) => $reports->where(function ($search) use ($escapedQuery) {
                $search->where('incident_id', 'like', $escapedQuery)
                    ->orWhere('cloak_alias', 'like', $escapedQuery);
            }))
            ->when($validated['status'] ?? null, fn ($reports, $status) => $reports->where('status', $status))
            ->orderByRaw('CASE WHEN seen_at IS NULL THEN 0 ELSE 1 END')
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();
        return view('admin.reports', compact('reports'));
    }

    /**
     * Mark a report as seen when admin opens its details.
     */
    public function markSeen($id)
    {
        if (strtolower(trim((string) optional(Auth::user())->role)) !== 'admin') {
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
        try {
            $pdf = Pdf::loadView('admin.report-pdf', [
                'report' => $report,
                'evidenceFiles' => $report->evidence_files,
            ])->setPaper('a4', 'portrait');

            return $pdf->download('SINAG-Report-' . $report->incident_id . '.pdf');
        } catch (\Throwable $exception) {
            report($exception);
            \Log::error('Individual report PDF export failed; displaying report details.', [
                'report_id' => $report->id,
                'exception' => $exception::class,
                'message' => $exception->getMessage(),
            ]);

            return response()->view('admin.report-pdf', [
                'report' => $report,
                'evidenceFiles' => $report->evidence_files,
                'pdfFallback' => true,
            ]);
        }
    }

    /**
     * Update Report Status & Priority mula sa Admin view.
     */
    public function updateStatus(Request $request, $id)
    {
        if (strtolower(trim((string) optional(Auth::user())->role)) !== 'admin') return redirect()->route('home');
        $reportUpdateRedirect = fn () => Auth::user()?->isGadCoordinator()
            ? redirect()->route('admin.report-tracking', ['report_id' => $id])
            : back();

        $validated = $request->validate([
            'status' => ['required', 'string', 'in:Pending,Under Review,Resolved'],
            'priority' => ['nullable', 'string', 'in:Low,Medium,High'],
            'admin_comment' => ['required', 'string', 'min:5', 'max:4000'],
            'send_to_coordinator' => ['sometimes', 'boolean'],
        ]);

        $sendToCoordinator = (bool) ($validated['send_to_coordinator'] ?? false);
        $coordinator = null;
        if ($sendToCoordinator) {
            $coordinator = User::query()
                ->where('role', 'admin')
                ->where(function ($query): void {
                    $query->whereRaw('LOWER(account_type) = ?', ['coordinator'])
                        ->orWhereRaw('LOWER(email) = ?', ['gadcoordinator@psu.edu.ph']);
                })
                ->first();

            if (!$coordinator || !$this->isMessageRecipient($coordinator)) {
                return back()->withInput()->withErrors([
                    'send_to_coordinator' => 'No GAD Coordinator account is available to receive this report.',
                ]);
            }
        }

        $shouldSendResolutionNotice = false;
        \DB::transaction(function () use (
            $id,
            $validated,
            $sendToCoordinator,
            $coordinator,
            &$shouldSendResolutionNotice
        ): void {
            $report = Report::notArchived()->whereKey($id)->lockForUpdate()->firstOrFail();
            $previousStatus = $report->status;
            $details = trim((string) ($validated['admin_comment'] ?? ''));
            $report->update([
                'status' => $validated['status'],
                'priority' => $validated['priority'] ?? $report->priority,
                'admin_comment' => isset($validated['admin_comment'])
                    ? trim($validated['admin_comment'])
                    : $report->admin_comment,
            ]);

            if ($sendToCoordinator) {
                $details .= "\n\nThis report was sent to the GAD Coordinator for follow-up.";
            }

            if ($previousStatus !== $report->status || $details !== '') {
                ReportStatusUpdate::create([
                    'report_id' => $report->id,
                    'admin_id' => Auth::id(),
                    'from_status' => $previousStatus,
                    'to_status' => $report->status,
                    'details' => $details !== ''
                        ? $details
                        : "Status changed from {$previousStatus} to {$report->status}.",
                ]);
            }

            if ($sendToCoordinator && $coordinator) {
                \App\Support\CompatibleInsert::insert('messages', [
                    'sender_id' => Auth::id(),
                    'receiver_id' => $coordinator->id,
                    'message' => sprintf(
                        "Report #%s has been forwarded to you for review.\nPriority: %s\nStatus: %s\n\nOpen Report Details & Analytics: %s",
                        $report->incident_id,
                        $report->priority ?: 'Unspecified',
                        $report->status ?: 'Pending',
                        route('admin.report-tracking', ['report_id' => $report->getKey()])
                    ),
                    'sender_type' => 'admin',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            if (strtolower($report->status) === 'resolved' && ! $report->resolution_notice_sent_at) {
                $report->user->notify(new ReportResolvedNotification(
                    (int) $report->getKey(),
                    $report->incident_id,
                ));
                $report->update(['resolution_notice_sent_at' => now()]);
                $shouldSendResolutionNotice = true;
            }
        });

        if ($shouldSendResolutionNotice) {
            $report = Report::with('user')->findOrFail($id);

            $coordinatorNotice = $sendToCoordinator
                ? ' It was also forwarded to the GAD Coordinator.'
                : '';

            try {
                Mail::to($report->user->email)->send(new ReportResolvedMail($report->incident_id));
            } catch (\Throwable $exception) {
                Log::error('SINAG report resolution email failed', [
                    'report_id' => $report->getKey(),
                    'user_id' => $report->user_id,
                    'admin_id' => Auth::id(),
                    'exception' => $exception::class,
                    'message' => $exception->getMessage(),
                ]);

                return $reportUpdateRedirect()->with('warning', "Report #{$report->incident_id} was resolved and the in-app notice was sent, but the email could not be sent. The survey is now available in the user's Evidence Vault.{$coordinatorNotice}");
            }

            return $reportUpdateRedirect()->with('success', "Report #{$report->incident_id} was resolved and the user was notified by email and in-app.{$coordinatorNotice}");
        }

        $report = Report::findOrFail($id);

        $coordinatorNotice = $sendToCoordinator
            ? ' and forwarded to the GAD Coordinator'
            : '';

        return $reportUpdateRedirect()->with('success', "Report #{$report->incident_id} updated successfully{$coordinatorNotice}.");
    }

    /**
     * Participatory Suggestions (Suggestions Management)
     */
    public function suggestions()
    {
        if (strtolower(trim((string) optional(Auth::user())->role)) !== 'admin') return redirect()->route('home');

        $suggestions = Suggestion::notArchived()->with('user')->latest()->paginate(15);
        $pendingSuggestionReports = SuggestionReport::where('status', 'pending')->count();
        return view('admin.suggestions', compact('suggestions', 'pendingSuggestionReports'));
    }

    public function coordinatorReports(Request $request)
    {
        abort_unless(Auth::user()?->isGadCoordinator(), 403);

        $validated = $request->validate([
            'report_id' => ['nullable', 'integer', 'min:1'],
        ]);
        $relations = Schema::hasTable('report_status_updates')
            ? ['user', 'statusUpdates.admin']
            : ['user'];
        $reports = Report::query()
            ->with($relations)
            ->when(!isset($validated['report_id']), fn ($query) => $query->notArchived())
            ->when(isset($validated['report_id']), fn ($query) => $query->whereKey($validated['report_id']))
            ->orderByRaw("CASE WHEN priority = 'High' THEN 0 ELSE 1 END")
            ->orderByRaw("CASE WHEN status = 'Emergency' THEN 0 ELSE 1 END")
            ->latest()
            ->paginate(15)
            ->withQueryString();
        $reportCountsByStatus = Report::notArchived()
            ->selectRaw("COALESCE(status, 'Unspecified') as status, COUNT(*) as total")
            ->groupBy('status')
            ->orderBy('status')
            ->pluck('total', 'status');
        $reportCategories = Report::notArchived()
            ->selectRaw("COALESCE(nature, 'Unspecified') as label, COUNT(*) as total")
            ->groupBy('nature')
            ->orderByDesc('total')
            ->limit(8)
            ->get();
        $reportCountsByPriority = Report::notArchived()
            ->selectRaw("COALESCE(priority, 'Unspecified') as priority, COUNT(*) as total")
            ->groupBy('priority')
            ->orderByDesc('total')
            ->get();
        $totalReports = (int) $reportCountsByStatus->sum();

        return view('admin.coordinator-reports', compact(
            'reports',
            'reportCountsByStatus',
            'reportCategories',
            'reportCountsByPriority',
            'totalReports'
        ));
    }

    public function archive()
    {
        return redirect()->route('admin.settings', ['section' => 'archive']);
    }

    public function restoreReport($id)
    {
        $report = Report::archived()->findOrFail($id);
        $report->update(['archived_at' => null, 'archived_by' => null]);

        return back()->with('success', "Report #{$report->incident_id} restored from archive.");
    }

    public function restoreSuggestion($id)
    {
        $suggestion = Suggestion::archived()->findOrFail($id);
        $suggestion->update(['archived_at' => null, 'archived_by' => null]);

        return back()->with('success', 'Suggestion restored from archive.');
    }

    public function permanentlyDeleteReport($id)
    {
        if (strtolower(trim((string) Auth::user()?->role)) !== 'admin') {
            return redirect()->route('home');
        }

        $report = Report::archived()->findOrFail($id);
        $incidentId = $report->incident_id;

        foreach ($report->evidence_files as $path) {
            \App\Support\MediaResolver::delete($path, $this->mediaStorageDisks());
        }

        \DB::transaction(function () use ($report): void {
            $report->statusUpdates()->delete();
            $report->delete();
        });

        return back()->with('success', "Report #{$incidentId} permanently deleted.");
    }

    public function permanentlyDeleteSuggestion($id)
    {
        if (strtolower(trim((string) Auth::user()?->role)) !== 'admin') {
            return redirect()->route('home');
        }

        $suggestion = Suggestion::archived()->findOrFail($id);
        $imagePath = $suggestion->image_path;

        if ($imagePath) {
            \App\Support\MediaResolver::delete($imagePath, $this->mediaStorageDisks());
        }

        \DB::transaction(function () use ($suggestion): void {
            $suggestion->comments()->delete();
            $suggestion->upvotes()->detach();
            $suggestion->delete();
        });

        return back()->with('success', 'Suggestion permanently deleted.');
    }

    private function mediaStorageDisks(): array
    {
        return array_values(array_unique(array_filter([
            'public',
            'public_s3',
            'local',
            'private_s3',
            config('filesystems.user_public_disk'),
            config('filesystems.user_private_disk'),
        ], static fn ($disk): bool => is_string($disk) && $disk !== '')));
    }

    public function activityLog()
    {
        return redirect()->route('admin.settings', ['section' => 'activity']);
    }

    public function settings(Request $request)
    {
        $section = $request->query('section', 'appearance');
        abort_unless(in_array($section, ['appearance', 'archive', 'activity', 'feedback'], true), 404);
        abort_if(Auth::user()?->isGadCoordinator() && ! in_array($section, ['appearance', 'feedback'], true), 403);

        $data = ['section' => $section];
        if ($section === 'archive') {
            $data['archivedReports'] = Report::archived()
                ->with('user')
                ->latest('archived_at')
                ->paginate(10, ['*'], 'reports_page')
                ->withQueryString();
            $data['archivedSuggestions'] = Suggestion::archived()
                ->with('user')
                ->latest('archived_at')
                ->paginate(10, ['*'], 'suggestions_page')
                ->withQueryString();
        } elseif ($section === 'activity') {
            $activityQuery = Schema::hasColumn('users', 'last_seen_at')
                ? User::whereNotNull('last_seen_at')->orderByDesc('last_seen_at')
                : User::query()->whereRaw('1 = 0');
            $data['recentUsers'] = $activityQuery
                ->paginate(25)
                ->withQueryString();
        } elseif ($section === 'feedback') {
            $data['totalRatings'] = 0;
            $data['averageRating'] = 0.0;
            $data['writtenReviews'] = 0;
            $data['ratingDistribution'] = collect();
            $data['recentReviews'] = collect();

            if (Schema::hasTable('customer_satisfaction_surveys')) {
                $surveyQuery = CustomerSatisfactionSurvey::query();
                $data['totalRatings'] = (clone $surveyQuery)->count();
                $data['averageRating'] = (float) ((clone $surveyQuery)->avg('rating') ?? 0);
                $data['writtenReviews'] = (clone $surveyQuery)
                    ->whereNotNull('feedback')
                    ->where('feedback', '<>', '')
                    ->count();
                $data['ratingDistribution'] = (clone $surveyQuery)
                    ->selectRaw('rating, COUNT(*) as total')
                    ->groupBy('rating')
                    ->pluck('total', 'rating');
                $data['recentReviews'] = $surveyQuery
                    ->with('user')
                    ->whereNotNull('feedback')
                    ->where('feedback', '<>', '')
                    ->latest()
                    ->paginate(10)
                    ->withQueryString();
            }
        }

        return view('admin.settings', $data);
    }

    public function updateSettings(Request $request)
    {
        abort_unless(
            Auth::user()?->isGadCoordinator() || strtolower(trim((string) Auth::user()?->role)) === 'admin',
            403
        );

        $validated = $request->validate([
            'theme' => ['required', 'in:light,dark'],
        ]);

        Auth::user()->update(['admin_theme' => $validated['theme']]);

        return back()->with('success', 'Dashboard appearance saved.');
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
            $suggestionReport->suggestion->update([
                'archived_at' => now(),
                'archived_by' => Auth::id(),
            ]);
        }
        return back()->with('success', 'Reported content was archived.');
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
        if (strtolower(trim((string) optional(Auth::user())->role)) !== 'admin') return redirect()->route('home');

        $request->validate([
            'action' => 'required|in:approve,reject',
            'admin_remarks' => ['nullable', 'string', 'max:1000'],
        ]);

        $suggestion = Suggestion::notArchived()->findOrFail($id);

        $suggestion->status = $request->action === 'approve' ? 'Approved' : 'Rejected';
        $suggestion->admin_remarks = trim((string) $request->input('admin_remarks')) ?: null;
        $suggestion->save();

        return back()->with('success', 'Suggestion status updated successfully.');
    }

    public function deleteSuggestion($id)
    {
        if (strtolower(trim((string) optional(Auth::user())->role)) !== 'admin') {
            return redirect()->route('home');
        }

        $suggestion = Suggestion::notArchived()->findOrFail($id);
        $suggestion->update([
            'archived_at' => now(),
            'archived_by' => Auth::id(),
        ]);

        return back()->with('success', 'Suggestion moved to the archive.');
    }

    /**
     * User Management: Listahan ng mga rehistradong account.
     */
    public function users(Request $request)
    {
        if (strtolower(trim((string) optional(Auth::user())->role)) !== 'admin') return redirect()->route('home');

        $baseQuery = User::query();
        $usersQuery = (clone $baseQuery)
            ->when($request->filled('q'), function ($query) use ($request) {
                $terms = preg_split('/\s+/', strtolower(trim($request->input('q'))), -1, PREG_SPLIT_NO_EMPTY);
                $query->where(function ($search) use ($terms) {
                    foreach ($terms as $term) {
                        $like = '%' . $term . '%';
                        $search->where(function ($termQuery) use ($like) {
                            $termQuery->whereRaw('LOWER(name) LIKE ?', [$like])
                                ->orWhereRaw('LOWER(first_name) LIKE ?', [$like])
                                ->orWhereRaw('LOWER(middle_name) LIKE ?', [$like])
                                ->orWhereRaw('LOWER(last_name) LIKE ?', [$like])
                                ->orWhereRaw('LOWER(email) LIKE ?', [$like])
                                ->orWhereRaw('LOWER(phone_number) LIKE ?', [$like])
                                ->orWhereRaw('LOWER(cloak_alias) LIKE ?', [$like])
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
            ->paginate(20)
            ->withQueryString();
        $filteredUserCount = (clone $usersQuery)->reorder()->count();
        $userCounts = $baseQuery
            ->selectRaw("SUM(CASE WHEN account_status = 'pending' THEN 1 ELSE 0 END) as pending, SUM(CASE WHEN account_status IN ('active', 'approved') THEN 1 ELSE 0 END) as active, SUM(CASE WHEN account_status = 'rejected' THEN 1 ELSE 0 END) as rejected, COUNT(*) as total")
            ->first();
        $departmentType = $request->input('account_type');
        $studentDepartments = [
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
        $teachingDepartments = [
            'College of Technology and Business' => 'College of Technology and Business',
            'College of Education' => 'College of Education',
        ];
        $nonTeachingDepartments = [
            'Non-Teaching' => 'Non-Teaching',
            'Office of the Registrar' => 'Office of the Registrar',
            'Human Resource Office' => 'Human Resource Office',
            'Finance Office' => 'Finance Office',
            'Library' => 'Library',
            'IT Services' => 'IT Services',
            'Student Affairs Office' => 'Student Affairs Office',
        ];
        $departments = match ($departmentType) {
            'student' => $studentDepartments,
            'teaching' => $teachingDepartments,
            'non_teaching' => $nonTeachingDepartments,
            default => $studentDepartments + $teachingDepartments + $nonTeachingDepartments,
        };

        return view('admin.users-list', compact('users', 'userCounts', 'departments', 'filteredUserCount'));
    }

    /**
     * Show a specific user's profile for admin review.
     */
    public function showUser($id)
    {
        if (strtolower(trim((string) optional(Auth::user())->role)) !== 'admin') return redirect()->route('home');

        $user = User::findOrFail($id);
        $verificationImagePath = $user->id_image_path ?: $user->selfie_image_path;
        $verificationImageAvailable = false;

        $verificationImageAvailable = \App\Support\MediaResolver::exists($verificationImagePath);

        return view('profile.show', compact('user', 'verificationImageAvailable'));
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

        foreach (array_unique(array_filter([$user->id_image_path, $user->selfie_image_path, $user->profile_photo_path])) as $path) {
            \App\Support\MediaResolver::delete($path);
        }

        $this->revokeUserAccess($user);
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
        if (strtolower(trim((string) optional(Auth::user())->role)) !== 'admin') return redirect()->route('home');

        $adminIds = $this->gadOfficeAdminIds();
        $selectedStudentId = $request->query('student_id');
        $conversations = $this->buildAdminConversations($adminIds);

        $messages = collect();
        $selectedStudent = null;
        $messageWarning = null;

        if ($selectedStudentId) {
            // Only users and GAD coordinators can be opened as conversations.
            // This prevents a crafted student_id from exposing admin-to-admin messages.
            $selectedStudent = User::find($selectedStudentId);

            if (!$selectedStudent || !$this->isMessageRecipient($selectedStudent)) {
                $selectedStudent = null;
                $selectedStudentId = null;
                $messageWarning = 'This conversation is no longer available.';
            } else {
                Message::where('sender_id', $selectedStudentId)
                    ->whereIn('receiver_id', $adminIds)
                    ->whereNull('read_at')
                    ->update(['read_at' => now()]);

                $messages = $this->messagesForConversation($selectedStudent, $adminIds);
            }
        }

        return view('admin.messages', compact('conversations', 'messages', 'selectedStudent', 'selectedStudentId', 'messageWarning'));
    }

    private function buildAdminConversations(array $adminIds): \Illuminate\Support\Collection
    {
        /** @var \Illuminate\Database\Eloquent\Collection<int, Message> $messages */
        $messages = Message::query()
            ->whereIn('receiver_id', $adminIds)
            ->orWhereIn('sender_id', $adminIds)
            ->with(['sender', 'receiver'])
            ->latest()
            ->get();

        $conversations = collect();
        foreach ($messages as $message) {
            if ($message->deleted_at) {
                continue;
            }

            $isAdminSender = in_array((int) $message->sender_id, $adminIds, true);
            $otherUser = $isAdminSender ? $message->receiver : $message->sender;

            // Older versions allowed an admin to address another admin.
            if (!$otherUser || !$this->isMessageRecipient($otherUser)) {
                continue;
            }

            $conversationId = $isAdminSender ? $message->receiver_id : $message->sender_id;
            if (!$conversations->has($conversationId)) {
                $message->setAttribute('conversation_unread', false);
                $conversations->put($conversationId, $message);
            }

            if (in_array((int) $message->receiver_id, $adminIds, true) && $message->read_at === null) {
                $latestMessage = $conversations->get($conversationId);
                if ($latestMessage instanceof Message) {
                    $latestMessage->setAttribute('conversation_unread', true);
                }
            }
        }

        return $conversations->sortByDesc('created_at');
    }

    private function messagesForConversation(User $student, array $adminIds): \Illuminate\Support\Collection
    {
        /** @var \Illuminate\Database\Eloquent\Collection<int, Message> $messages */
        $messages = Message::query()
            ->where(function ($query) use ($student, $adminIds) {
                $query->where('sender_id', $student->id)
                    ->whereIn('receiver_id', $adminIds);
            })
            ->orWhere(function ($query) use ($student, $adminIds) {
                $query->whereIn('sender_id', $adminIds)
                    ->where('receiver_id', $student->id);
            })
            ->orderBy('created_at')
            ->get();

        return $messages->reject(fn (Message $message) => $message->deleted_at)->values();
    }

    public function searchMessageUsers(Request $request)
    {
        $validated = $request->validate([
            'q' => ['required', 'string', 'min:2', 'max:100'],
        ]);

        $term = trim($validated['q']);
        $like = '%' . addcslashes($term, '\\%_') . '%';
        $appointmentMatches = AppointmentRequest::query()
            ->where('student_id', 'like', $like)
            ->distinct()
            ->pluck('user_id');

        /** @var \Illuminate\Database\Eloquent\Collection<int, User> $users */
        $users = User::query()
            ->whereIn('role', ['student', 'employee'])
            ->where(function ($query) use ($like, $term, $appointmentMatches) {
                $query->where('name', 'like', $like);
                if (ctype_digit($term)) {
                    $query->orWhere('id', (int) $term);
                }
                if ($appointmentMatches->isNotEmpty()) {
                    $query->orWhereIn('id', $appointmentMatches);
                }
            })
            ->orderBy('name')
            ->limit(12)
            ->get(['id', 'name', 'role', 'account_type', 'department']);

        $appointmentIds = AppointmentRequest::query()
            ->whereIn('user_id', $users->pluck('id'))
            ->select('user_id', 'student_id')
            ->latest('id')
            ->get()
            ->unique('user_id')
            ->keyBy('user_id');

        return response()->json([
            'users' => $users->map(fn (User $user) => [
                'id' => $user->id,
                'name' => $user->name,
                'role' => $user->account_type ?? $user->role,
                'department' => $user->department,
                'student_employee_id' => optional($appointmentIds->get($user->id))->student_id,
                'url' => route('admin.messages', ['student_id' => $user->id]),
            ])->values(),
        ])->header('Cache-Control', 'no-store, no-cache, must-revalidate');
    }

    /**
     * GAD office meeting schedule management.
     */
    public function gadSchedules()
    {
        if (strtolower(trim((string) optional(Auth::user())->role)) !== 'admin') {
            return redirect()->route('home');
        }

        $schedules = GadSchedule::with('creator')
            ->orderByDesc('available_from')
            ->orderByDesc('updated_at')
            ->get();

        return view('admin.gad_schedules', compact('schedules'));
    }

    public function scheduleFeed()
    {
        if (!Auth::user()) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $schedules = \DB::table('gad_schedules')
            ->whereIn('status', ['active', 'unavailable', 'inactive'])
            ->orderBy('available_from')
            ->get()
            ->map(function ($schedule) {
                $availableFrom = \Carbon\Carbon::parse($schedule->available_from);
                $availableUntil = $schedule->available_until
                    ? \Carbon\Carbon::parse($schedule->available_until)
                    : null;
                $type = $schedule->status === 'unavailable' || $schedule->details === 'unavailable'
                    ? 'unavailable'
                    : ($schedule->details === 'all_day' ? 'available' : 'hours');

                return [
                    'date' => $availableFrom->format('Y-m-d'),
                    'start_time' => $availableFrom->format('H:i'),
                    'end_date' => $availableUntil?->format('Y-m-d'),
                    'end_time' => $availableUntil?->format('H:i'),
                    'status' => $schedule->status,
                    'availability_type' => $type,
                ];
            })
            ->values();

        return response()->json(['schedules' => $schedules])
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate');
    }

    /**
     * Store a new schedule entry for students.
     */
    public function storeGadSchedule(Request $request)
    {
        if (strtolower(trim((string) optional(Auth::user())->role)) !== 'admin') {
            return redirect()->route('home');
        }

        $validated = $request->validate([
            'title'           => ['required', 'string', 'max:255'],
            'location'        => ['required', 'string', 'max:255'],
            'available_from'  => ['required', 'date'],
            'available_until' => ['nullable', 'date', 'after_or_equal:available_from'],
            'details'         => ['nullable', 'string', 'max:1000'],
            'status'          => ['required', 'in:active,inactive,unavailable'],
        ]);

        \App\Support\CompatibleInsert::insert('gad_schedules', [
            ...$validated,
            'created_by' => Auth::id(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'GAD schedule saved successfully.');
    }

    /**
     * Update an existing schedule entry for students.
     */
    public function updateGadSchedule(Request $request, $id)
    {
        if (strtolower(trim((string) optional(Auth::user())->role)) !== 'admin') {
            return redirect()->route('home');
        }

        $validated = $request->validate([
            'title'           => ['required', 'string', 'max:255'],
            'location'        => ['required', 'string', 'max:255'],
            'available_from'  => ['required', 'date'],
            'available_until' => ['nullable', 'date', 'after_or_equal:available_from'],
            'details'         => ['nullable', 'string', 'max:1000'],
            'status'          => ['required', 'in:active,inactive,unavailable'],
        ]);

        $schedule = GadSchedule::findOrFail($id);
        \DB::table('gad_schedules')->where('id', $schedule->id)->update([
            ...$validated,
            'updated_at' => now(),
        ]);

        return back()->with('success', 'GAD schedule updated successfully.');
    }

    /**
     * Toggle schedule visibility for students.
     */
    public function updateGadScheduleStatus(Request $request, $id)
    {
        if (strtolower(trim((string) optional(Auth::user())->role)) !== 'admin') {
            return redirect()->route('home');
        }

        $validated = $request->validate([
            'status' => ['required', 'in:active,inactive,unavailable'],
        ]);

        $schedule = GadSchedule::findOrFail($id);
        \DB::table('gad_schedules')->where('id', $schedule->id)->update([
            'status' => $validated['status'],
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Schedule status updated.');
    }

    /**
     * Sync calendar day toggles from the admin calendar UI.
     * Handles available (all day), specific period hours, and unavailable statuses.
     */
    public function syncGadSchedule(Request $request)
    {
        if (strtolower(trim((string) optional(Auth::user())->role)) !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        try {
            $validated = $request->validate([
                'updates'                     => ['required', 'array'],
                'updates.*.date'              => ['required', 'date_format:Y-m-d'],
                'updates.*.availability_type' => ['nullable', 'in:available,hours,unavailable,remove'],
                'updates.*.start_time'        => ['nullable', 'date_format:H:i'],
                'updates.*.end_time'          => ['nullable', 'date_format:H:i'],
                'updates.*.remove'            => ['nullable', 'boolean'],
            ]);

            foreach ($validated['updates'] as $update) {
                if (($update['availability_type'] ?? 'hours') !== 'hours') {
                    continue;
                }

                if (($update['start_time'] ?? '09:00') > ($update['end_time'] ?? '17:00')) {
                    return response()->json(['message' => 'Start time must be earlier than end time.'], 422);
                }
            }

            \DB::transaction(function () use ($validated): void {
                foreach ($validated['updates'] as $update) {
                    $date = $update['date'];
                    $remove = (bool) ($update['remove'] ?? false);
                    $type = $update['availability_type'] ?? 'hours';
                    $existing = \DB::table('gad_schedules')
                        ->whereDate('available_from', $date)
                        ->first();

                    if ($remove || $type === 'remove') {
                        if ($existing) {
                            \DB::table('gad_schedules')->where('id', $existing->id)->delete();
                        }
                        continue;
                    }

                    if ($type === 'unavailable') {
                        $payload = [
                            'title' => 'Unavailable / Closed',
                            'location' => 'GAD Office',
                            'available_from' => $date . ' 00:00:00',
                            'available_until' => $date . ' 23:59:59',
                            'details' => 'unavailable',
                            'status' => 'unavailable',
                        ];
                    } elseif ($type === 'available') {
                        $payload = [
                            'title' => 'Available (All Day)',
                            'location' => 'GAD Office',
                            'available_from' => $date . ' 08:00:00',
                            'available_until' => $date . ' 17:00:00',
                            'details' => 'all_day',
                            'status' => 'active',
                        ];
                    } else {
                        $startTime = $update['start_time'] ?? '09:00';
                        $endTime = $update['end_time'] ?? '17:00';
                        $payload = [
                            'title' => 'Office Hours',
                            'location' => 'GAD Office',
                            'available_from' => $date . ' ' . $startTime . ':00',
                            'available_until' => $date . ' ' . $endTime . ':00',
                            'details' => 'specific_hours',
                            'status' => 'active',
                        ];
                    }

                    if ($existing) {
                        \DB::table('gad_schedules')->where('id', $existing->id)->update([
                            ...$payload,
                            'updated_at' => now(),
                        ]);
                    } else {
                        \App\Support\CompatibleInsert::insert('gad_schedules', $payload + [
                            'created_by' => Auth::id(),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            });

            $savedSchedules = \DB::table('gad_schedules')
                ->whereIn('status', ['active', 'unavailable', 'inactive'])
                ->orderBy('available_from')
                ->get()
                ->map(fn ($schedule) => [
                    'date' => \Carbon\Carbon::parse($schedule->available_from)->format('Y-m-d'),
                    'start_time' => \Carbon\Carbon::parse($schedule->available_from)->format('H:i'),
                    'end_date' => $schedule->available_until
                        ? \Carbon\Carbon::parse($schedule->available_until)->format('Y-m-d')
                        : null,
                    'end_time' => $schedule->available_until
                        ? \Carbon\Carbon::parse($schedule->available_until)->format('H:i')
                        : null,
                    'status' => $schedule->status,
                    'availability_type' => $schedule->details === 'all_day'
                        ? 'available'
                        : ($schedule->status === 'unavailable' || $schedule->details === 'unavailable'
                            ? 'unavailable'
                            : 'hours'),
                ])->values();

            return response()->json(['success' => true, 'schedules' => $savedSchedules])
                ->header('Cache-Control', 'no-store, no-cache, must-revalidate');
        } catch (\Illuminate\Validation\ValidationException $exception) {
            throw $exception;
        } catch (\Throwable $exception) {
            report($exception);
            \Log::error('GAD schedule sync failed', [
                'admin_id' => Auth::id(),
                'message' => $exception->getMessage(),
            ]);

            return response()->json([
                'message' => 'The schedule could not be saved: ' . \Illuminate\Support\Str::limit(
                    trim(preg_replace('/\s+/', ' ', $exception->getMessage())),
                    240
                ),
            ], 500);
        }
    }

    /**
     * Admin urgent calls interface
     */
    public function urgentCalls()
    {
        if (strtolower(trim((string) optional(Auth::user())->role)) !== 'admin') return redirect()->route('home');
        return view('admin.urgent_calls');
    }

    public function startUrgentCall(int $studentId)
    {
        if (strtolower(trim((string) optional(Auth::user())->role)) !== 'admin') {
            return redirect()->route('home');
        }

        $student = User::findOrFail($studentId);
        abort_unless($this->isMessageRecipient($student), 404);

        $calls = Cache::get('urgent_calls', []);
        $activeCall = collect($calls)->first(
            fn ($call) => (int) ($call['student_id'] ?? 0) === $student->id
                && ($call['status'] ?? null) !== 'ended'
        );
        if ($activeCall) {
            return redirect()->route('admin.urgent.calls', ['call_id' => $activeCall['id']])
                ->with('warning', 'There is already an active call with this user.');
        }

        $callId = (string) \Illuminate\Support\Str::uuid();
        array_unshift($calls, [
            'id' => $callId,
            'room' => 'sinag-urgent-' . \Illuminate\Support\Str::uuid(),
            'status' => 'waiting',
            'initiated_by' => 'admin',
            'admin_id' => Auth::id(),
            'admin_name' => Auth::user()->name,
            'student_id' => $student->id,
            'student_name' => $student->name,
            'created_at' => now()->toDateTimeString(),
        ]);
        Cache::put('urgent_calls', array_slice($calls, 0, 50), now()->addHours(6));

        return redirect()->route('admin.urgent.calls', ['call_id' => $callId])
            ->with('success', "Calling {$student->name}. The call room is ready.");
    }

    /**
     * JSON feed for pending/joined urgent calls
     */
    public function urgentCallsFeed()
    {
        if (strtolower(trim((string) optional(Auth::user())->role)) !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $calls = array_values(array_filter(
            Cache::get('urgent_calls', []),
            fn ($call) => ($call['status'] ?? null) !== 'ended'
        ));

        return response()->json(['calls' => $calls])
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate');
    }

    /**
     * Mark call as joined by admin
     */
    public function joinUrgentCall($callId)
    {
        if (strtolower(trim((string) optional(Auth::user())->role)) !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $calls = Cache::get('urgent_calls', []);
        $room = null;

        foreach ($calls as &$call) {
            if (($call['id'] ?? null) === $callId && ($call['status'] ?? null) !== 'ended') {
                $call['status']    = 'joined';
                $call['joined_at'] = now()->toDateTimeString();
                $call['joined_by'] = Auth::user()->name;
                $room = $call['room'] ?? null;
                break;
            }
        }
        unset($call);

        abort_unless($room, 404, 'Call not found or it has ended.');
        Cache::put('urgent_calls', $calls, now()->addHours(6));

        return response()->json([
            'ok'   => true,
            'room' => $room,
        ]);
    }

    public function endUrgentCall($callId)
    {
        if (strtolower(trim((string) optional(Auth::user())->role)) !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $calls = Cache::get('urgent_calls', []);
        $found = false;
        foreach ($calls as &$call) {
            if (($call['id'] ?? null) === $callId && ($call['status'] ?? null) !== 'ended') {
                $call['status'] = 'ended';
                $call['ended_at'] = now()->toDateTimeString();
                $call['ended_by'] = Auth::user()->name;
                $found = true;
                break;
            }
        }
        unset($call);

        abort_unless($found, 404, 'Call not found or it has already ended.');
        Cache::put('urgent_calls', $calls, now()->addHours(6));

        return response()->json(['status' => 'ended']);
    }

    /**
     * Approve newly registered user account.
     */
    public function approveUser(Request $request, $id)
    {
        if (strtolower(trim((string) optional(Auth::user())->role)) !== 'admin') return redirect()->route('home');

        $validated = $request->validate([
            'approval_note' => ['nullable', 'string', 'max:2000'],
        ]);
        $user = User::findOrFail($id);

        $statusChanged = \DB::transaction(function () use ($user, $validated): bool {
            if (! $this->updateAccountStatus($user, 'active')) {
                return false;
            }

            $user->notify(new AccountStatusNotification('approved', $validated['approval_note'] ?? null));

            return true;
        });

        if (! $statusChanged) {
            return redirect('/admin/users')->with('success', 'This account is already approved; no duplicate notification was sent.');
        }

        \Log::info('SINAG account approved', ['user_id' => $user->id, 'admin_id' => Auth::id()]);

        try {
            Mail::to($user->email)->send(new AccountStatusMail(
                $user->name,
                'approved',
                $validated['approval_note'] ?? null,
            ));
        } catch (\Throwable $exception) {
            Log::error('SINAG account approval email failed', [
                'user_id' => $user->id,
                'admin_id' => Auth::id(),
                'exception' => $exception::class,
                'message' => $exception->getMessage(),
            ]);
            return redirect('/admin/users')->with('success', 'Account approved.');
        }

        return redirect('/admin/users')->with('success', 'Account approved.');
    }

    /**
     * Reject newly registered user account.
     */
    public function rejectUser(Request $request, $id)
    {
        if (strtolower(trim((string) optional(Auth::user())->role)) !== 'admin') return redirect()->route('home');

        $user = User::findOrFail($id);

        $validated = $request->validate([
            'rejection_reason' => ['nullable', 'string', 'max:2000'],
        ]);
        $reason = $validated['rejection_reason'] ?? null;

        $statusChanged = \DB::transaction(function () use ($user, $reason): bool {
            if (! $this->updateAccountStatus($user, 'rejected', $reason)) {
                return false;
            }

            $this->revokeUserAccess($user);
            $user->notify(new AccountStatusNotification('rejected', $reason));

            return true;
        });

        if (! $statusChanged) {
            return back()->with('success', 'This account is already rejected; no duplicate notification was sent.');
        }

        try {
            Mail::to($user->email)->send(new AccountStatusMail(
                $user->name,
                'rejected',
                $reason,
            ));
        } catch (\Throwable $exception) {
            Log::error('SINAG account rejection email failed', [
                'user_id' => $user->id,
                'admin_id' => Auth::id(),
                'exception' => $exception::class,
                'message' => $exception->getMessage(),
            ]);
            return back()->with('success', 'Account rejected.');
        }

        return back()->with('success', 'Account rejected.');
    }

    private function updateAccountStatus(User $user, string $status, ?string $reason = null): bool
    {
        $updates = [
            'account_status' => $status,
            'updated_at' => now(),
        ];
        $optionalColumns = [
            'approved_at' => $status === 'active' ? now() : null,
            'approved_by' => Auth::id(),
            'rejection_reason' => $reason,
        ];

        foreach ($optionalColumns as $column => $value) {
            if (Schema::hasColumn('users', $column)) {
                $updates[$column] = $value;
            }
        }

        $query = \DB::table('users')->where('id', $user->id);
        $query->where(function ($query) use ($status): void {
            if ($status === 'active') {
                $query->whereNull('account_status')
                    ->orWhereNotIn('account_status', ['active', 'approved']);

                return;
            }

            $query->whereNull('account_status')
                ->orWhere('account_status', '!=', $status);
        });

        return $query->update($updates) > 0;
    }

    /**
     * Delete a report (admin only)
     */
    public function deleteReport($id)
    {
        if (strtolower(trim((string) optional(Auth::user())->role)) !== 'admin') return redirect()->route('home');
        $report = Report::notArchived()->findOrFail($id);
        $report->update([
            'archived_at' => now(),
            'archived_by' => Auth::id(),
        ]);
        return redirect()->back()->with('success', 'Report moved to the archive.');
    }

    /**
     * Store a message from an admin to a user.
     */
    public function storeMessage(Request $request)
    {
        if (strtolower(trim((string) optional(Auth::user())->role)) !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'student_id' => 'required|exists:users,id',
            'message'    => 'required|string|max:1000',
        ]);

        $student = User::find($request->student_id);

        if (!$student || !$this->isMessageRecipient($student)) {
            return response()->json(['error' => 'Invalid message recipient'], 422);
        }

        $messageId = \App\Support\CompatibleInsert::insert('messages', [
            'sender_id'   => Auth::id(),
            'receiver_id' => $student->id,
            'message'     => $request->message,
            'sender_type' => 'admin',
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
        $message = Message::findOrFail($messageId);

        return response()->json([
            'success' => true,
            'message' => $message,
        ]);
    }

    public function updateMessage(Request $request, $id)
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:1000'],
        ]);

        $message = Message::findOrFail($id);
        abort_unless((int) $message->sender_id === (int) Auth::id(), 403, 'You can only edit messages you sent.');
        $recipient = User::find($message->receiver_id);
        abort_unless($recipient && $this->isMessageRecipient($recipient), 403, 'This is not a user conversation.');
        if (Schema::hasColumn('messages', 'deleted_at')) {
            abort_if($message->deleted_at, 409, 'Deleted messages cannot be edited.');
        }

        if (Schema::hasColumn('messages', 'original_message')) {
            $message->original_message ??= $message->message;
        }
        $message->message = $validated['message'];
        $message->save();

        return response()->json(['success' => true, 'message' => $message]);
    }

    public function destroyMessage($id)
    {
        $message = Message::findOrFail($id);
        abort_unless((int) $message->sender_id === (int) Auth::id(), 403, 'You can only delete messages you sent.');
        $recipient = User::find($message->receiver_id);
        abort_unless($recipient && $this->isMessageRecipient($recipient), 403, 'This is not a user conversation.');

        if (!Schema::hasColumn('messages', 'deleted_at')) {
            $message->delete();

            return response()->json(['success' => true]);
        }

        if (!$message->deleted_at) {
            $message->deleted_at = now();
            $message->message = '';
            if (Schema::hasColumn('messages', 'original_message')) {
                $message->original_message = null;
            }
            $message->save();
        }

        return response()->json(['success' => true]);
    }

    public function messageFeed(Request $request)
    {
        $validated = $request->validate([
            'student_id' => ['nullable', 'integer', 'exists:users,id'],
            'after_id' => ['nullable', 'integer', 'min:0'],
        ]);

        $adminIds = $this->gadOfficeAdminIds();
        $unreadQuery = Message::whereIn('receiver_id', $adminIds)
            ->whereNotIn('sender_id', $adminIds)
            ->whereNull('read_at');
        if (empty($validated['student_id'])) {
            $latestUnread = (clone $unreadQuery)->latest('id')->first();

            return response()->json([
                'messages' => [],
                'unread_count' => $unreadQuery->count(),
                'latest_unread_student_id' => $latestUnread?->sender_id,
                'unread_student_ids' => (clone $unreadQuery)->distinct()->pluck('sender_id')->values(),
            ])->header('Cache-Control', 'no-store, no-cache, must-revalidate');
        }

        $student = User::find($validated['student_id']);
        abort_unless($student && $this->isMessageRecipient($student), 404);
        /** @var \Illuminate\Database\Eloquent\Collection<int, Message> $messages */
        $messages = Message::where(function ($query) use ($student, $adminIds) {
                $query->whereIn('sender_id', $adminIds)->where('receiver_id', $student->id);
            })->orWhere(function ($query) use ($student, $adminIds) {
                $query->where('sender_id', $student->id)->whereIn('receiver_id', $adminIds);
            })
            ->orderBy('id')
            ->get();

        Message::where('sender_id', $student->id)
            ->whereIn('receiver_id', $adminIds)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json([
            'messages' => $messages->map(fn (Message $message) => [
                'id' => $message->id,
                'message' => $message->message,
                'original_message' => $message->original_message,
                'deleted_at' => $message->deleted_at?->toIso8601String(),
                'sender_id' => $message->sender_id,
                'is_anonymous' => !$student->isGadCoordinator() && (bool) $message->is_anonymous,
                'created_at' => $message->created_at?->toIso8601String(),
            ]),
            'unread_count' => $unreadQuery->count(),
            'latest_unread_student_id' => (clone $unreadQuery)->latest('id')->value('sender_id'),
            'unread_student_ids' => (clone $unreadQuery)->distinct()->pluck('sender_id')->values(),
        ])->header('Cache-Control', 'no-store, no-cache, must-revalidate');
    }

    private function isMessageRecipient(User $user): bool
    {
        return strtolower(trim((string) $user->role)) !== 'admin' || $user->isGadCoordinator();
    }

    private function gadOfficeAdminIds(): array
    {
        $adminIds = User::query()
            ->where('role', 'admin')
            ->orderBy('id')
            ->get()
            ->reject(fn (User $user) => $user->isGadCoordinator())
            ->modelKeys();

        return array_map('intval', $adminIds);
    }

    /**
     * Show appointment requests management page
     */
    public function appointments(Request $request)
    {
        if (strtolower(trim((string) optional(Auth::user())->role)) !== 'admin') {
            return redirect()->route('home');
        }

        $validated = $request->validate([
            'open' => ['nullable', 'integer', 'min:1'],
        ]);
        $appointmentQuery = AppointmentRequest::with('user');
        if (isset($validated['open'])) {
            $appointmentQuery->whereKey($validated['open']);
        } else {
            $appointmentQuery
                ->orderByRaw("CASE urgency_level WHEN 'High' THEN 0 WHEN 'Medium' THEN 1 WHEN 'Low' THEN 2 ELSE 3 END")
                ->latest();
        }
        $appointments = $appointmentQuery
            ->paginate(isset($validated['open']) ? 1 : 30)
            ->withQueryString();

        $appointmentsByUrgency = $appointments->getCollection()->groupBy('urgency_level');

        return view('admin.appointments', compact('appointments', 'appointmentsByUrgency'));
    }

    private function revokeUserAccess(User $user): void
    {
        $user->tokens()->delete();

        $sessionTable = config('session.table', 'sessions');
        if (Schema::hasTable($sessionTable) && Schema::hasColumn($sessionTable, 'user_id')) {
            \DB::table($sessionTable)->where('user_id', $user->id)->delete();
        }
    }

    /**
     * Mark an appointment request as completed.
     */
    public function completeAppointment($id)
    {
        if (strtolower(trim((string) optional(Auth::user())->role)) !== 'admin') {
            return redirect()->route('home');
        }

        $appointment = AppointmentRequest::findOrFail($id);
        $appointment->update(['status' => 'completed']);

        return back()->with('success', 'Appointment marked as completed.');
    }

    public function cancelAppointment(Request $request, $id)
    {
        if (strtolower(trim((string) optional(Auth::user())->role)) !== 'admin') {
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
        if (strtolower(trim((string) optional(Auth::user())->role)) !== 'admin') {
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
        if (strtolower(trim((string) optional(Auth::user())->role)) !== 'admin') {
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

        Cache::forever('gad_office_info', [
            'phone_number' => $validated['phone_number'],
            'office_hours' => $validated['office_hours'] ?? 'Mon – Fri, 8:00 AM – 5:00 PM',
            'office_location' => $validated['office_location'] ?? 'Admin Building, Room 105',
            'office_email' => $validated['office_email'] ?? $user->email ?? 'gad@psu.edu.ph',
        ]);

        return back()->with('success', 'GAD Office information and contact number updated successfully.');
    }

}
