<?php

namespace App\Http\Controllers;

use App\Models\CustomerSatisfactionSurvey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CustomerSatisfactionController extends Controller
{
    public function store(Request $request)
    {
        if (!Schema::hasTable('customer_satisfaction_surveys')) {
            return back()->with('error', 'The satisfaction survey is not available yet. Please run the latest database migrations and try again.');
        }

        if (!Schema::hasColumn('customer_satisfaction_surveys', 'subject')
            || !Schema::hasColumn('customer_satisfaction_surveys', 'subject_id')) {
            return back()->with('error', 'The item-specific satisfaction survey is not available yet. Please run the latest database migrations and try again.');
        }

        $validated = $request->validate([
            'subject' => ['required', 'in:report,suggestion,appointment'],
            'subject_id' => ['required', 'integer', 'min:1'],
            'rating' => ['required', 'integer', 'between:1,5'],
            'feedback' => ['nullable', 'string', 'max:2000'],
            'responses' => ['required', 'array', 'size:5'],
            'responses.*' => ['required', 'integer', 'between:1,4'],
        ]);

        $table = match ($validated['subject']) {
            'report' => 'reports',
            'suggestion' => 'suggestions',
            'appointment' => 'appointment_requests',
        };
        $columns = $validated['subject'] === 'appointment' ? ['status', 'format'] : ['status'];
        $item = DB::table($table)
            ->where('id', $validated['subject_id'])
            ->where('user_id', $request->user()->id)
            ->first($columns);

        abort_unless($item, 404);
        $status = strtolower(trim((string) $item->status));
        $isAvailable = match ($validated['subject']) {
            'report' => in_array($status, ['completed', 'resolved', 'closed'], true),
            'suggestion' => $status === 'approved',
            'appointment' => $status === 'completed'
                && strtolower(trim((string) $item->format)) === 'online',
        };
        abort_unless($isAvailable, 409, 'This item is not ready for a satisfaction survey.');

        CustomerSatisfactionSurvey::firstOrCreate(
            [
                'user_id' => $request->user()->id,
                'subject' => $validated['subject'],
                'subject_id' => $validated['subject_id'],
            ],
            [
                'rating' => $validated['rating'],
                'feedback' => trim((string) ($validated['feedback'] ?? '')) ?: null,
                'responses' => $validated['responses'],
            ],
        );

        return back()->with('satisfaction_submitted', true);
    }
}
