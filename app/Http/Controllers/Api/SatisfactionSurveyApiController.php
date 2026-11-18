<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SatisfactionSurveyApiController extends Controller
{
    private const SUBJECTS = ['report', 'suggestion'];

    public function show(Request $request, string $subject, int $id): JsonResponse
    {
        $this->assertSubjectBelongsToUser($request, $subject, $id);
        $survey = $this->survey($request->user()->id, $subject, $id);

        return response()->json([
            'available' => $this->isAvailable($subject, $id),
            'submitted' => $survey !== null,
            'survey' => $survey,
        ]);
    }

    public function store(Request $request, string $subject, int $id): JsonResponse
    {
        $this->assertSubjectBelongsToUser($request, $subject, $id);
        abort_unless($this->isAvailable($subject, $id), 409, 'This item is not ready for a satisfaction survey.');

        $data = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'feedback' => ['nullable', 'string', 'max:2000'],
            'responses' => ['required', 'array', 'size:5'],
            'responses.*' => ['required', 'integer', 'min:1', 'max:4'],
        ]);
        $userId = $request->user()->id;

        if ($this->survey($userId, $subject, $id) !== null) {
            return response()->json(['message' => 'You already submitted this survey.'], 409);
        }

        DB::table('customer_satisfaction_surveys')->insert([
            'user_id' => $userId,
            'subject' => $subject,
            'subject_id' => $id,
            'rating' => $data['rating'],
            'feedback' => $data['feedback'] ?? null,
            'responses' => json_encode($data['responses']),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['message' => 'Thank you for sharing your feedback.'], 201);
    }

    private function assertSubjectBelongsToUser(Request $request, string $subject, int $id): void
    {
        abort_unless(in_array($subject, self::SUBJECTS, true), 404);
        $table = $subject === 'report' ? 'reports' : 'suggestions';
        abort_unless(
            DB::table($table)->where('id', $id)->where('user_id', $request->user()->id)->exists(),
            404
        );
    }

    private function isAvailable(string $subject, int $id): bool
    {
        $table = $subject === 'report' ? 'reports' : 'suggestions';
        $status = strtolower(trim((string) DB::table($table)->where('id', $id)->value('status')));
        $terminal = $subject === 'report'
            ? ['completed', 'resolved', 'closed']
            : ['approved', 'disapproved', 'rejected'];

        return in_array($status, $terminal, true);
    }

    private function survey(int $userId, string $subject, int $subjectId): ?array
    {
        $row = DB::table('customer_satisfaction_surveys')
            ->where('user_id', $userId)
            ->where('subject', $subject)
            ->where('subject_id', $subjectId)
            ->first();

        return $row ? [
            'rating' => (int) $row->rating,
            'feedback' => $row->feedback,
            'responses' => json_decode((string) $row->responses, true) ?: [],
            'created_at' => $row->created_at,
        ] : null;
    }
}
