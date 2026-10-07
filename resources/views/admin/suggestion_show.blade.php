@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <div>
            <p class="text-uppercase text-muted small mb-1">Suggestion Details</p>
            <h3 class="fw-bold mb-0" style="color:#7c3aed;">Freedom Wall Entry #{{ $suggestion->id }}</h3>
        </div>
        <a href="{{ route('admin.suggestions') }}" class="btn btn-outline-secondary rounded-pill px-4">
            <i class="bi bi-arrow-left me-1"></i> Back to Suggestions
        </a>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center">
                    <div>
                        <span class="badge rounded-pill px-3 py-2" style="background:#f5f3ff; color:#7c3aed; border:1px solid #ddd6fe;">{{ $suggestion->category }}</span>
                    </div>
                    <span class="text-muted small">Posted {{ $suggestion->created_at->diffForHumans() }}</span>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:#f3e8ff;color:#7c3aed;">
                            <i class="bi {{ $suggestion->is_anonymous ? 'bi-incognito' : 'bi-person-fill' }} fs-5"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-dark">
                                {{ $suggestion->is_anonymous ? 'Anonymous' : optional($suggestion->user)->name ?? 'Unknown User' }}
                            </div>
                            <div class="text-muted small">{{ $suggestion->status }}</div>
                        </div>
                    </div>

                    <div class="p-4 rounded-4 border bg-light" style="white-space: pre-wrap; line-height:1.8; color:#1f2937;">
                        {{ $suggestion->message }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Details</h5>
                    <div class="mb-3">
                        <div class="text-muted small">Submitted By</div>
                        <div class="fw-semibold">{{ $suggestion->is_anonymous ? 'Anonymous' : optional($suggestion->user)->name ?? 'Unknown User' }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small">Date Submitted</div>
                        <div class="fw-semibold">{{ $suggestion->created_at->format('M d, Y h:i A') }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small">Status</div>
                        <span class="badge rounded-pill px-3 py-2 bg-warning-subtle text-warning border border-warning-subtle">{{ $suggestion->status }}</span>
                    </div>
                    <div>
                        <div class="text-muted small">Comments</div>
                        <div class="fw-semibold">{{ $suggestion->comments->count() }}</div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Moderation</h5>
                    <form action="{{ route('admin.suggestions.review', $suggestion->id) }}" method="POST" class="d-grid gap-2">
                        @csrf
                        <input type="hidden" name="action" value="approve">
                        <button type="submit" class="btn btn-success rounded-pill">
                            Approve Suggestion
                        </button>
                    </form>
                    <form action="{{ route('admin.suggestions.review', $suggestion->id) }}" method="POST" class="d-grid gap-2 mt-2" onsubmit="return confirm('Reject this suggestion?');">
                        @csrf
                        <input type="hidden" name="action" value="reject">
                        <button type="submit" class="btn btn-outline-danger rounded-pill">
                            Reject Suggestion
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mt-4">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-3">Comments</h5>
            @forelse($suggestion->comments as $comment)
                <div class="d-flex gap-3 mb-3 pb-3 border-bottom">
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:40px;height:40px;background:#ede9fe;color:#6d28d9;">
                        <i class="bi bi-chat-dots"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between flex-wrap gap-2">
                            <div class="fw-semibold">{{ optional($comment->user)->name ?? 'Unknown User' }}</div>
                            <div class="text-muted small">{{ $comment->created_at->diffForHumans() }}</div>
                        </div>
                        <div class="text-dark mt-1" style="white-space: pre-wrap;">{{ $comment->comment }}</div>
                    </div>
                </div>
            @empty
                <div class="text-muted">No comments yet.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
