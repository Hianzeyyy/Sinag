<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Suggestion;
use App\Models\SuggestionComment;
use App\Support\ContentModeration;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class SuggestionApiController extends Controller
{
    public const CATEGORIES = [
        'Gender-Based Violence Prevention',
        'Gender Equality and Inclusion',
        'GAD Programs and Services',
        'Safe Spaces and Student Welfare',
        'GAD Policy and Training',
        'Other GAD Concern',
    ];

    // Only the cloak alias is ever exposed, never the real name.
    private function suggestionPayload(Suggestion $s, int $viewerId): array
    {
        return [
            'id' => $s->id,
            'alias' => optional($s->user)->cloak_alias ?? 'Anonymous',
            'category' => $s->category,
            'message' => $s->message,
            'status' => $s->status,
            'is_mine' => $s->user_id === $viewerId,
            'upvotes_count' => $s->upvotes_count ?? $s->upvotes()->count(),
            'upvoted' => $s->isUpvotedBy($viewerId),
            'created_at' => optional($s->created_at)->toIso8601String(),
            'comments' => $s->relationLoaded('comments')
                ? $s->comments->map(fn ($c) => [
                    'id' => $c->id,
                    'alias' => optional($c->user)->cloak_alias ?? 'Anonymous',
                    'comment' => $c->comment,
                    'is_mine' => $c->user_id === $viewerId,
                    'created_at' => optional($c->created_at)->toIso8601String(),
                ])->values()
                : [],
        ];
    }

    public function index(Request $request): JsonResponse
    {
        $uid = $request->user()->id;
        $query = Suggestion::with(['user', 'comments.user'])
            ->withCount('upvotes')
            ->where(function ($query) use ($uid) {
                $query->where('status', 'Approved')
                    ->orWhere('user_id', $uid);
            });
        if ($request->filled('category')) {
            $query->where('category', $request->string('category'));
        }

        return response()->json([
            'data' => $query->latest()->get()->map(fn ($s) => $this->suggestionPayload($s, $uid))->values(),
            'categories' => self::CATEGORIES,
        ]);
    }

    public function mine(Request $request): JsonResponse
    {
        $uid = $request->user()->id;
        $rows = Suggestion::with(['user', 'comments.user'])->withCount('upvotes')
            ->where('user_id', $uid)->latest()->get();

        return response()->json(['data' => $rows->map(fn ($s) => $this->suggestionPayload($s, $uid))->values()]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'category' => ['required', Rule::in(self::CATEGORIES)],
            'message' => 'required|string|min:10|max:2000',
            'is_anonymous' => 'nullable|boolean',
        ]);
        $message = trim($data['message']);
        $this->assertClean($message, 'message', 'Please submit positive and constructive suggestions only. Negative or offensive language is not allowed.');

        DB::table('suggestions')->insert([
            'user_id' => $request->user()->id,
            'category' => $data['category'],
            'message' => $message,
            'is_anonymous' => (bool) ($data['is_anonymous'] ?? false),
            'status' => 'Pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['message' => 'Salamat sa iyong mungkahi! Ipapasa muna ito sa GAD admin para sa approval bago ma-post.'], 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $s = Suggestion::findOrFail($id);
        abort_unless($s->user_id === $request->user()->id, 403, 'Hindi mo kayang i-edit ang suggestion na ito.');

        $data = $request->validate([
            'category' => ['required', Rule::in(self::CATEGORIES)],
            'message' => 'required|string|min:10|max:2000',
        ]);
        $message = trim($data['message']);
        $this->assertClean($message, 'message', 'Please submit positive and constructive suggestions only.');

        $s->update(['category' => $data['category'], 'message' => $message]);

        return response()->json(['ok' => true]);
    }

    public function destroy(Request $request, $id): JsonResponse
    {
        $s = Suggestion::findOrFail($id);
        abort_unless($s->user_id === $request->user()->id, 403, 'Hindi mo kayang i-delete ang suggestion na ito.');
        $s->comments()->delete();
        $s->delete();

        return response()->json(['ok' => true]);
    }

    public function upvote(Request $request, $id): JsonResponse
    {
        $s = Suggestion::findOrFail($id);
        $uid = $request->user()->id;

        if ($s->isUpvotedBy($uid)) {
            $s->upvotes()->detach($uid);
            $upvoted = false;
        } else {
            DB::table('suggestion_user_upvotes')->insert([
                'suggestion_id' => $s->id,
                'user_id' => $uid,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $upvoted = true;
        }

        return response()->json(['ok' => true, 'upvoted' => $upvoted, 'upvotes_count' => $s->upvotes()->count()]);
    }

    public function comment(Request $request, $id): JsonResponse
    {
        $data = $request->validate(['comment' => 'required|string|max:1000']);
        $this->assertClean($data['comment'], 'comment', 'Naglalaman ng hindi angkop na salita ang iyong komento.');
        $s = Suggestion::findOrFail($id);
        DB::table('suggestion_comments')->insert([
            'suggestion_id' => $s->id,
            'user_id' => $request->user()->id,
            'comment' => $data['comment'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['message' => 'Comment posted!'], 201);
    }

    public function updateComment(Request $request, $id): JsonResponse
    {
        $c = SuggestionComment::findOrFail($id);
        abort_unless($c->user_id === $request->user()->id, 403, 'Hindi mo kayang i-edit ang comment na ito.');
        $data = $request->validate(['comment' => 'required|string|max:1000']);
        $this->assertClean($data['comment'], 'comment', 'Naglalaman ng hindi angkop na salita ang iyong komento.');
        $c->update($data);

        return response()->json(['ok' => true]);
    }

    public function destroyComment(Request $request, $id): JsonResponse
    {
        $c = SuggestionComment::findOrFail($id);
        abort_unless($c->user_id === $request->user()->id, 403, 'Hindi mo kayang i-delete ang comment na ito.');
        $c->delete();

        return response()->json(['ok' => true]);
    }

    public function reportSuggestion(Request $request, $id): JsonResponse
    {
        $s = Suggestion::findOrFail($id);

        return $this->storeReport($request, ['suggestion_id' => $s->id], $s->id);
    }

    public function reportComment(Request $request, $id): JsonResponse
    {
        $c = SuggestionComment::findOrFail($id);

        return $this->storeReport($request, ['comment_id' => $c->id], $c->suggestion_id);
    }

    private function storeReport(Request $request, array $target, int $suggestionId): JsonResponse
    {
        $data = $request->validate(['reason' => ['required', 'string', 'max:500']]);
        $key = ['reporter_id' => $request->user()->id, 'status' => 'pending'] + $target;

        if (!DB::table('suggestion_reports')->where($key)->exists()) {
            DB::table('suggestion_reports')->insert($key + [
                'suggestion_id' => $suggestionId,
                'reason' => $data['reason'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return response()->json(['message' => 'Reported to the administrator.']);
    }

    private function assertClean(string $text, string $field, string $message): void
    {
        if (ContentModeration::containsHarshLanguage($text)) {
            throw ValidationException::withMessages([$field => [$message]]);
        }
    }
}
