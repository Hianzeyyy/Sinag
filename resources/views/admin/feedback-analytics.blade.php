<div class="feedback-analytics">
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <section class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <p class="small text-muted mb-1">Average rating</p>
                    <div class="d-flex align-items-center gap-2">
                        <strong class="display-6 fw-bold">{{ number_format($averageRating, 1) }}</strong>
                        <span class="text-muted">/ 5</span>
                    </div>
                    <div class="text-warning" aria-label="{{ number_format($averageRating, 1) }} out of 5 stars">
                        @for($star = 1; $star <= 5; $star++)
                            <i class="bi {{ $averageRating >= $star ? 'bi-star-fill' : ($averageRating >= $star - 0.5 ? 'bi-star-half' : 'bi-star') }}"></i>
                        @endfor
                    </div>
                </div>
            </section>
        </div>
        <div class="col-md-4">
            <section class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <p class="small text-muted mb-1">Total ratings</p>
                    <strong class="display-6 fw-bold">{{ number_format($totalRatings) }}</strong>
                    <p class="small text-muted mb-0">All submitted satisfaction ratings</p>
                </div>
            </section>
        </div>
        <div class="col-md-4">
            <section class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <p class="small text-muted mb-1">Written reviews</p>
                    <strong class="display-6 fw-bold">{{ number_format($writtenReviews) }}</strong>
                    <p class="small text-muted mb-0">Ratings that include a comment</p>
                </div>
            </section>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-5">
            <section class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <h2 class="h5 fw-bold mb-1">Rating distribution</h2>
                    <p class="small text-muted mb-4">Breakdown of all submitted ratings</p>
                    @for($rating = 5; $rating >= 1; $rating--)
                        @php
                            $ratingCount = (int) $ratingDistribution->get($rating, 0);
                            $ratingWidth = $totalRatings > 0 ? ($ratingCount / $totalRatings) * 100 : 0;
                        @endphp
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <span class="feedback-rating-label">{{ $rating }} <i class="bi bi-star-fill text-warning"></i></span>
                            <div class="progress flex-grow-1 rounded-pill" role="progressbar" aria-label="{{ $rating }} star ratings" aria-valuenow="{{ round($ratingWidth) }}" aria-valuemin="0" aria-valuemax="100">
                                <div class="progress-bar rounded-pill" style="width:{{ $ratingWidth }}%;background:#8f72f5;"></div>
                            </div>
                            <span class="small text-muted feedback-rating-count">{{ $ratingCount }}</span>
                        </div>
                    @endfor
                </div>
            </section>
        </div>
        <div class="col-lg-7">
            <section class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <h2 class="h5 fw-bold mb-1">Recent reviews</h2>
                    <p class="small text-muted mb-4">Latest written feedback from users</p>
                    <div class="d-grid gap-3">
                        @forelse($recentReviews as $review)
                            <article class="feedback-review rounded-4 border p-3">
                                <div class="d-flex justify-content-between align-items-start gap-3">
                                    <div>
                                        <strong class="d-block">{{ auth()->user()->isGadCoordinator() ? ($review->user?->name ?: 'Anonymous') : 'Anonymous ' . ($recentReviews instanceof \Illuminate\Contracts\Pagination\Paginator ? $recentReviews->firstItem() + $loop->index : $loop->iteration) }}</strong>
                                        <small class="text-muted">{{ $review->subject ? ucfirst($review->subject) : 'System feedback' }} · {{ $review->created_at?->format('M d, Y') }}</small>
                                    </div>
                                    <span class="feedback-review-rating text-warning text-nowrap" aria-label="{{ $review->rating }} out of 5 stars">
                                        @for($star = 1; $star <= 5; $star++)
                                            <i class="bi {{ $star <= $review->rating ? 'bi-star-fill' : 'bi-star' }}"></i>
                                        @endfor
                                    </span>
                                </div>
                                <p class="mb-0 mt-3 feedback-review-copy">{{ $review->feedback }}</p>
                            </article>
                        @empty
                            <div class="text-center rounded-4 border p-4 text-muted">
                                <i class="bi bi-chat-square-text fs-3 d-block mb-2"></i>
                                No written reviews yet.
                            </div>
                        @endforelse
                    </div>
                    @if($recentReviews instanceof \Illuminate\Contracts\Pagination\Paginator)
                        <div class="mt-3">{{ $recentReviews->links() }}</div>
                    @endif
                </div>
            </section>
        </div>
    </div>
</div>
<style>
    .feedback-analytics .card { background:#fff; }
    .feedback-rating-label { width:2.4rem; white-space:nowrap; }
    .feedback-rating-count { min-width:1.75rem; text-align:right; }
    .feedback-review { border-color:#ebe5f5 !important; background:#fff; }
    .feedback-review-copy { white-space:pre-wrap; overflow-wrap:anywhere; }
    body[data-admin-theme="dark"] .feedback-analytics .card,
    body[data-admin-theme="dark"] .feedback-review { background:#1b1529 !important; color:#eee8ff; }
    body[data-admin-theme="dark"] .feedback-review { border-color:#3a2d50 !important; }
</style>
