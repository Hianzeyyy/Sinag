<?php

namespace App\Http\Controllers;

use App\Models\Suggestion;
use App\Models\SuggestionComment;
use App\Models\SuggestionReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SuggestionReportController extends Controller
{
    public function storePost(Request $request, Suggestion $suggestion)
    {
        $validated = $request->validate(['reason' => ['required', 'string', 'max:500']]);
        SuggestionReport::firstOrCreate(['reporter_id' => Auth::id(), 'suggestion_id' => $suggestion->id, 'status' => 'pending'], ['reason' => $validated['reason']]);
        Log::info('Suggestion post reported', ['suggestion_id' => $suggestion->id, 'reporter_id' => Auth::id()]);
        return back()->with('success', 'Post reported to the administrator.');
    }

    public function storeComment(Request $request, SuggestionComment $comment)
    {
        $validated = $request->validate(['reason' => ['required', 'string', 'max:500']]);
        SuggestionReport::firstOrCreate(['reporter_id' => Auth::id(), 'comment_id' => $comment->id, 'status' => 'pending'], ['reason' => $validated['reason'], 'suggestion_id' => $comment->suggestion_id]);
        Log::info('Suggestion comment reported', ['comment_id' => $comment->id, 'reporter_id' => Auth::id()]);
        return back()->with('success', 'Comment reported to the administrator.');
    }
}