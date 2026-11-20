@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="mb-4">
        <h1 class="h3 fw-bold mb-1" style="color:#8f72f5;">Community Directory</h1>
        <p class="text-muted mb-0">Meet the active members of the SINAG community.</p>
    </div>

    <div class="row g-3">
        @forelse($users as $user)
            <div class="col-6 col-md-4 col-lg-3">
                <article class="card h-100 border-0 shadow-sm rounded-4 text-center p-3">
                    <img
                        src="{{ route('profile.photo', $user) }}"
                        alt="{{ $user->cloak_alias ?: $user->name }} profile photo"
                        class="rounded-circle mx-auto mb-3"
                        width="112"
                        height="112"
                        loading="lazy"
                        style="object-fit:cover;"
                    >
                    <h2 class="h6 fw-bold mb-1">{{ $user->cloak_alias ?: $user->name }}</h2>
                    @if($user->department)
                        <p class="small text-muted mb-0">{{ $user->department }}</p>
                    @endif
                </article>
            </div>
        @empty
            <div class="col-12">
                <p class="text-center text-muted py-5 mb-0">No active community members found.</p>
            </div>
        @endforelse
    </div>

    @if($users->hasPages())
        <div class="mt-4">{{ $users->links() }}</div>
    @endif
</div>
@endsection
