<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Suggestion;
use App\Support\MediaResolver;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
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
            \App\Support\CompatibleInsert::insert('suggestion_user_upvotes', [
                'suggestion_id' => $suggestion->id,
                'user_id' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
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
        if (!$request->user()->hasCompletedPersonalData()) {
            return redirect()->route('personal-data.edit', ['return_to_suggestions' => 1])
                ->with('personal_data_required', 'Complete your personal data to access Community Suggestions.');
        }

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
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $message = trim((string) $request->message);

        if (ContentModeration::containsHarshLanguage($message)) {
            return redirect()->back()->withInput()->with('error', 'Please submit positive and constructive suggestions only. Negative or offensive language is not allowed.');
        }

        // 2. Save to Database
        $hasImageColumn = Schema::hasColumn('suggestions', 'image_path');
        if ($request->hasFile('image') && !$hasImageColumn) {
            return redirect()->back()->withInput()->with('error', 'Image attachments are temporarily unavailable. Please submit your suggestion without an image.');
        }

        $imagePath = $request->hasFile('image')
            ? MediaResolver::storeUploadedFile($request->file('image'), 'suggestions', false)
            : null;

        $suggestionData = [
            'user_id' => Auth::id(),
            'category' => $request->category,
            'message' => $message,
            'is_anonymous' => $request->boolean('is_anonymous', true),
            'status' => 'Pending',
            'created_at' => now(),
            'updated_at' => now(),
        ];

        if ($hasImageColumn) {
            $suggestionData['image_path'] = $imagePath;
        }

        \App\Support\CompatibleInsert::insert('suggestions', $suggestionData);

        // 3. Redirect back with Success Message
        return redirect()->back()
            ->with('success', 'Salamat sa iyong mungkahi! Ito ay ipapasa muna sa GAD admin para sa approval bago ma-post.');
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
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $message = trim((string) $validated['message']);

        if (ContentModeration::containsHarshLanguage($message)) {
            return redirect()->back()->with('error', 'Please submit positive and constructive suggestions only. Negative or offensive language is not allowed.');
        }

        $attributes = [
            'category' => $validated['category'],
            'message' => $message,
        ];

        $previousImagePath = $suggestion->image_path;
        if ($request->hasFile('image')) {
            $imagePath = MediaResolver::storeUploadedFile($request->file('image'), 'suggestions', false);
            $attributes['image_path'] = $imagePath;
        }

        $suggestion->update($attributes);
        if ($request->hasFile('image') && $previousImagePath) {
            MediaResolver::delete($previousImagePath, ['public', 'local', config('filesystems.user_public_disk')]);
        }

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
        if ($suggestion->image_path) {
            MediaResolver::delete($suggestion->image_path, ['public', 'local', config('filesystems.user_public_disk')]);
        }

        return redirect()->back()->with('success', 'Ang iyong suggestion ay na-delete na.');


    }

    public function show(Suggestion $suggestion)
    {
        if (strtolower(trim((string) optional(Auth::user())->role)) !== 'admin') {
            return redirect()->route('home')->with('error', 'Unauthorized access.');
        }

        $suggestion->load(['user', 'comments.user']);

        return view('admin.suggestion_show', compact('suggestion'));
    }
}
