<?php

namespace App\Http\Controllers;

use App\Models\CustomerSatisfactionSurvey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class CustomerSatisfactionController extends Controller
{
    public function store(Request $request)
    {
        if (!Schema::hasTable('customer_satisfaction_surveys')) {
            return back()->with('error', 'The satisfaction survey is not available yet. Please run the latest database migrations and try again.');
        }

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'feedback' => ['nullable', 'string', 'max:2000'],
            'responses' => ['required', 'array', 'size:5'],
            'responses.*' => ['required', 'integer', 'between:1,4'],
        ]);

        CustomerSatisfactionSurvey::firstOrCreate(
            ['user_id' => $request->user()->id],
            [
                'rating' => $validated['rating'],
                'feedback' => trim((string) ($validated['feedback'] ?? '')) ?: null,
                'responses' => $validated['responses'],
            ],
        );

        return back()->with('satisfaction_submitted', true);
    }
}
