<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
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
            'evidence.*' => 'nullable|file|mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx|max:1048576',
        ]);

        $finalNature = $request->nature === 'Other GAD-related Concern'
            ? trim((string) $request->other_nature)
            : $request->nature;

        $user = Auth::user();
        $cloakAlias = $user->cloak_alias;

        // Backward-safe: auto-generate alias for old accounts that were created without one.
        if (blank($cloakAlias)) {
            $cloakAlias = 'Student' . strtoupper(Str::random(4));

            while (User::where('cloak_alias', $cloakAlias)->exists()) {
                $cloakAlias = 'Student' . strtoupper(Str::random(4));
            }

            $user->forceFill([
                'cloak_alias' => $cloakAlias,
                'role' => $user->role ?: 'student',
            ])->save();
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
        \Log::info('DEBUG: Attempting to save report', [
            'user_id' => Auth::id(),
            'cloak_alias' => $cloakAlias,
            'nature' => $finalNature,
            'incident_date' => $request->incident_date,
            'incident_time' => $request->incident_time,
            'location' => $request->location,
            'description' => $request->description
        ]);

        $report = Report::create([
            'user_id'       => Auth::id(),
            'incident_id'   => 'SN-' . strtoupper(Str::random(6)), // Halimbawa: SN-A1B2C3
            'cloak_alias'   => $cloakAlias,
            'nature'        => $finalNature,
            'incident_date' => $request->incident_date,
            'incident_time' => $request->incident_time,
            'location'      => $request->location,
            'description'   => $request->description,
            'evidence'      => $evidencePaths,
            'priority'      => 'Medium', // Default priority
            'status'        => 'Pending', // Default status
        ]);

        \Log::info('DEBUG: Report saved', [
            'report_id' => $report->id,
            'incident_id' => $report->incident_id
        ]);

        // 4. Redirect back
        return redirect()->back()
            ->with('success', 'Report submitted successfully! Case ID: ' . $report->incident_id . '. This has been delivered to the GAD Admin inbox for review.')
            ->with('report_case_id', $report->incident_id);
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