<?php

namespace App\Http\Controllers;

use App\Models\Suggestion;
use App\Models\SuggestionComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Support\ContentModeration;

class SuggestionCommentController extends Controller
{
    public function store(Request $request, $suggestionId)
    {
        $request->validate([
            'comment' => 'required|string|max:1000',
        ]);

        if (ContentModeration::containsHarshLanguage((string) $request->comment)) {
            return redirect()->back()->withErrors(['comment' => 'Naglalaman ng hindi angkop na salita ang iyong komento.']);
        }

        $suggestion = Suggestion::findOrFail($suggestionId);

        SuggestionComment::create([
            'suggestion_id' => $suggestion->id,
            'user_id' => Auth::id(),
            'comment' => $request->comment,
        ]);

        return redirect()->back()->with('success', 'Comment posted!');
    }

    public function update(Request $request, $id)
    {
        $comment = SuggestionComment::findOrFail($id);

        if ($comment->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Hindi mo kayang i-edit ang comment na ito.');
        }

        $validated = $request->validate([
            'comment' => 'required|string|max:1000',
        ]);

        if (ContentModeration::containsHarshLanguage($validated['comment'])) {
            return redirect()->back()->withErrors(['comment' => 'Naglalaman ng hindi angkop na salita ang iyong komento.']);
        }

        $comment->update([
            'comment' => $validated['comment'],
        ]);

        return redirect()->back()->with('success', 'Ang iyong comment ay na-update na.');
    }

    public function destroy($id)
    {
        $comment = SuggestionComment::findOrFail($id);

        if ($comment->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Hindi mo kayang i-delete ang comment na ito.');
        }

        $comment->delete();

        return redirect()->back()->with('success', 'Ang iyong comment ay na-delete na.');
    }
}