<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ReportApiController extends Controller
{
    public const NATURES = [
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

    public function index(Request $request): JsonResponse
    {
        $reports = Report::where('user_id', $request->user()->id)->latest()->get()->map(fn (Report $r) => [
            'id' => $r->id,
            'incident_id' => $r->incident_id,
            'nature' => $r->nature,
            'incident_date' => optional($r->incident_date)->toDateString(),
            'incident_time' => $r->incident_time,
            'location' => $r->location,
            'description' => $r->description,
            'status' => $r->status,
            'priority' => $r->priority,
            'evidence_count' => count($r->evidence_files),
            'created_at' => optional($r->created_at)->toIso8601String(),
        ]);

        return response()->json(['data' => $reports, 'natures' => self::NATURES]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'nature' => ['required', 'string', Rule::in(self::NATURES)],
            'other_nature' => 'required_if:nature,Other GAD-related Concern|nullable|string|min:3|max:150',
            'incident_date' => 'required|date|before_or_equal:today',
            'incident_time' => 'required|date_format:H:i',
            'description' => 'required|string|min:5',
            'location' => 'required|string|max:255',
            'course' => 'nullable|string|max:120',
            'gender' => 'required|string|in:Female,Male',
            'evidence' => 'nullable|array',
            'evidence.*' => 'nullable|file|mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx|max:10240',
        ]);

        $user = $request->user();
        $nature = $request->nature === 'Other GAD-related Concern'
            ? trim((string) $request->other_nature)
            : $request->nature;

        $alias = $user->cloak_alias;
        if (blank($alias)) {
            do {
                $alias = 'Student' . strtoupper(Str::random(4));
            } while (User::where('cloak_alias', $alias)->exists());
        }

        $paths = [];
        foreach ((array) $request->file('evidence', []) as $file) {
            $path = $file->store('evidence', 'local');
            if (!is_string($path)) {
                throw new \RuntimeException('Incident evidence could not be stored.');
            }

            $paths[] = $path;
        }

        do {
            $incidentId = 'SN-' . strtoupper(Str::random(6));
        } while (DB::table('reports')->where('incident_id', $incidentId)->exists());

        $row = [
            'user_id' => $user->id,
            'incident_id' => $incidentId,
            'cloak_alias' => $alias,
            'nature' => $nature,
            'incident_date' => $request->incident_date,
            'incident_time' => $request->input('incident_time'),
            'course' => $request->input('course'),
            'gender' => $request->input('gender'),
            'location' => $request->location,
            'description' => $request->description,
            'evidence' => json_encode($paths),
            'priority' => 'Medium',
            'status' => 'Pending',
            'created_at' => now(),
            'updated_at' => now(),
        ];

        DB::table('reports')->insert(array_intersect_key($row, array_flip(Schema::getColumnListing('reports'))));

        return response()->json([
            'message' => 'Report submitted. It has been delivered to the GAD Admin inbox for review.',
            'incident_id' => $incidentId,
        ], 201);
    }

    public function evidence(Request $request, Report $report, int $index)
    {
        abort_unless($report->user_id === $request->user()->id, 403);

        $files = $report->evidence_files;
        abort_unless(isset($files[$index]) && Storage::disk('local')->exists($files[$index]), 404);

        return Storage::disk('local')->response($files[$index]);
    }
}
