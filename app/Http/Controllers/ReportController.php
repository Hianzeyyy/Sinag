<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ReportController extends Controller
{
    /**
     * I-save ang isinumiteng report sa database.
     */
    public function store(Request $request)
    {
        $failureReference = (string) Str::uuid();

        try {
            return $this->storeReport($request);
        } catch (\Illuminate\Validation\ValidationException $exception) {
            throw $exception;
        } catch (\Throwable $exception) {
            $this->logReportFailure($exception, [
                'user_id' => Auth::id(),
                'stage' => 'controller',
                'reference' => $failureReference,
            ]);

            return redirect()->route('student.report')->withInput()->withErrors([
                'report' => 'The report could not be submitted right now. Please try again. If the problem continues, contact the GAD office. Reference: ' . $failureReference,
            ]);
        }
    }

    private function storeReport(Request $request)
    {
        $allowedIncidentTypes = [
            'Catcalling',
            'Stalking',
            'Groping',
            'Cyber-harassment',
            'Gender-based Discrimination',
            'Unequal Opportunities',
            'Sexist Remarks or Jokes',
            'Domestic Concern Affecting School/Work',
            'Retaliation or Intimidation',
            'Other GAD-related Concern',
        ];



        // 1. Validation - Siguraduhing tumutugma sa migration columns
        $request->validate([
            'nature' => ['required', 'string', Rule::in($allowedIncidentTypes)],
            'other_nature' => 'required_if:nature,Other GAD-related Concern|nullable|string|min:3|max:150',
            'incident_date' => 'required|date|before_or_equal:today',
            'incident_time' => 'required|date_format:H:i',
            'description' => 'required|string|min:5',
            'location' => 'required|string|max:255',
            'course' => 'nullable|string|max:120',
            'gender' => 'required|string|in:Female,Male',
            'evidence.*' => 'nullable|file|mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx|max:10240',
        ]);

        $finalNature = $request->nature === 'Other GAD-related Concern'
            ? trim((string) $request->other_nature)
            : $request->nature;

        $user = $request->user();
        if (!$user) {
            return redirect()->route('login');
        }

        $cloakAlias = $user->cloak_alias;

        // Backward-safe: auto-generate alias for old accounts that were created without one.
        if (blank($cloakAlias)) {
            $cloakAlias = 'Student' . strtoupper(Str::random(4));

            while (User::where('cloak_alias', $cloakAlias)->exists()) {
                $cloakAlias = 'Student' . strtoupper(Str::random(4));
            }

        }

        // 2. Handle File Uploads (Optional)
        $evidencePaths = [];
        if ($request->hasFile('evidence')) {
            foreach ($request->file('evidence') as $file) {
                // I-save sa public storage para madaling ma-view ng Admin (or 'private' if preferred)
                $path = $file->store('evidence', 'public');
                $evidencePaths[] = $path;
            }
        }

        // 3. Create the Report Record gamit ang bagong Schema
        $incidentId = 'SN-' . strtoupper(Str::random(6));
        while (\DB::table('reports')->where('incident_id', $incidentId)->exists()) {
            $incidentId = 'SN-' . strtoupper(Str::random(6));
        }
        $reportData = [
            'user_id'       => $user->id,
            'incident_id'   => $incidentId,
            'cloak_alias'   => $cloakAlias,
            'nature'        => $finalNature,
            'incident_date' => $request->incident_date,
            'location'      => $request->location,
            'description'   => $request->description,
            'evidence'      => json_encode($evidencePaths, JSON_THROW_ON_ERROR),
            'priority'      => 'Medium', // Default priority
            'status'        => 'Pending', // Default status
            'created_at'    => now(),
            'updated_at'    => now(),
        ];

        $optionalData = [
            'course' => $request->input('course'),
            'gender' => $request->input('gender'),
            'incident_time' => $request->input('incident_time'),
        ];

        try {
            \DB::table('reports')->insertGetId($reportData);
        } catch (\Throwable $exception) {
            $this->logReportFailure($exception, [
                'user_id' => Auth::id(),
                'stage' => 'insert-report',
            ]);

            throw $exception;
        }

        try {
            $columns = array_flip(Schema::getColumnListing('reports'));
            $metadata = array_intersect_key($optionalData, $columns);

            if ($metadata !== []) {
                \DB::table('reports')->where('incident_id', $incidentId)->update($metadata);
            }
        } catch (\Throwable $exception) {
            \Log::warning('Report saved without optional metadata', [
                'user_id' => $user->id,
                'incident_id' => $incidentId,
                'exception' => $exception::class,
                'message' => $exception->getMessage(),
            ]);
        }

        // 4. Redirect back
        return redirect()->route('student.report')
            ->with('success', 'Report submitted successfully! Case ID: ' . $incidentId . '. This has been delivered to the GAD Admin inbox for review.')
            ->with('report_case_id', $incidentId);
    }

    private function logReportFailure(\Throwable $exception, array $context): void
    {
        try {
            \Log::error('Report submission failed', $context + [
                'exception' => $exception::class,
                'message' => $exception->getMessage(),
            ]);
        } catch (\Throwable $loggingException) {
            // Logging must never replace the user-facing report error response.
        }
    }

    private function safeFailureMessage(\Throwable $exception): string
    {
        $message = trim(preg_replace('/\s+/', ' ', $exception->getMessage()));
        return $message !== '' ? 'Server details: ' . \Illuminate\Support\Str::limit($message, 240) : 'Please try again or contact the GAD office.';
    }

    /**
     * Securely render evidence files for admins and report owners.
     */
    public function viewEvidence(Report $report, int $index)
    {
        $user = Auth::user();

        if (!$user || ($user->role !== 'admin' && $report->user_id !== $user->id)) {
            abort(403);
        }

        $files = $report->evidence_files;

        if (!isset($files[$index])) {
            abort(404);
        }

        $path = $files[$index];

        if (!Storage::disk('public')->exists($path)) {
            abort(404);
        }

        return response()->file(Storage::disk('public')->path($path));
    }
}