<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Suggestion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Support\ContentModeration;

class SuggestionController extends Controller
{
    public function upvote($id)
    {
        $suggestion = Suggestion::findOrFail($id);
        $user = Auth::user();

        if ($suggestion->isUpvotedBy($user->id)) {
            // If already upvoted, remove upvote (toggle)
            $suggestion->upvotes()->detach($user->id);
            $upvoted = false;
        } else {
            $suggestion->upvotes()->attach($user->id);
            $upvoted = true;
        }

        $upvotesCount = $suggestion->upvotes()->count();

        if (request()->expectsJson()) {
            return response()->json([
                'ok' => true,
                'upvoted' => $upvoted,
                'upvotes_count' => $upvotesCount,
            ]);
        }

        return redirect()->back();
    }

    public function store(Request $request)
    {
        $allowedCategories = [
            'Gender-Based Violence Prevention',
            'Gender Equality and Inclusion',
            'GAD Programs and Services',
            'Safe Spaces and Student Welfare',
            'GAD Policy and Training',
            'Other GAD Concern',
        ];

        // 1. Validation
        $request->validate([
            'category' => ['required', Rule::in($allowedCategories)],
            'message' => 'required|string|min:10|max:2000',
        ]);

        $message = trim((string) $request->message);

        if (ContentModeration::containsHarshLanguage($message)) {
            return redirect()->back()->with('error', 'Please submit positive and constructive suggestions only. Negative or offensive language is not allowed.');
        }

        // 2. Save to Database
        Suggestion::create([
            'user_id' => Auth::id(),
            'category' => $request->category,
            'message' => $message,
            'is_anonymous' => $request->has('is_anonymous') ? true : false,
            'status' => 'Pending',
        ]);

        // 3. Redirect back with Success Message
        return redirect()->back()->with('success', 'Salamat sa iyong mungkahi! Ito ay ipapasa muna sa GAD admin para sa approval bago ma-post.');
    }

    public function update(Request $request, $id)
    {
        $suggestion = Suggestion::findOrFail($id);
        $user = Auth::user();

        if (!$user || $suggestion->user_id !== $user->id) {
            return redirect()->back()->with('error', 'Hindi mo kayang i-edit ang suggestion na ito.');
        }

        $allowedCategories = [
            'Gender-Based Violence Prevention',
            'Gender Equality and Inclusion',
            'GAD Programs and Services',
            'Safe Spaces and Student Welfare',
            'GAD Policy and Training',
            'Other GAD Concern',
        ];

        $validated = $request->validate([
            'category' => ['required', Rule::in($allowedCategories)],
            'message' => 'required|string|min:10|max:2000',
        ]);

        $message = trim((string) $validated['message']);

        if (ContentModeration::containsHarshLanguage($message)) {
            return redirect()->back()->with('error', 'Please submit positive and constructive suggestions only. Negative or offensive language is not allowed.');
        }

        $suggestion->update([
            'category' => $validated['category'],
            'message' => $message,
        ]);

        return redirect()->back()->with('success', 'Ang iyong suggestion ay na-update na.');
    }

    public function destroy($id)
    {
        $suggestion = Suggestion::findOrFail($id);
        $user = Auth::user();

        if (!$user) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        if ($suggestion->user_id !== $user->id) {
            return redirect()->back()->with('error', 'Hindi mo kayang i-delete ang suggestion na ito.');
        }
        
        // Delete associated comments first
        $suggestion->comments()->delete();
        
        // Delete the suggestion
        $suggestion->delete();
        
        return redirect()->back()->with('success', 'Ang iyong suggestion ay na-delete na.');

        
    }

    public function show(Suggestion $suggestion)
    {
        if (Auth::user()->role !== 'admin') {
            return redirect()->route('home')->with('error', 'Unauthorized access.');
        }

        $suggestion->load(['user', 'comments.user']);

        return view('admin.suggestion_show', compact('suggestion'));
    }
}