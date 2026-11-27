@extends('layouts.app')

@section('content')
<div class="container-fluid py-4 appointment-page">
    <!-- Page Header -->
    <div class="appointment-page-header d-flex justify-content-between align-items-center mb-5">
        <div>
            <h1 class="h3 fw-bold mb-1" style="color: #a78bfa;">
    <i class="bi bi-calendar-check"></i> My Appointments
</h1>
            <p class="text-muted"></p>
        </div>
        <div class="appointment-page-actions d-flex gap-2">
            <a href="{{ route('student.gad-schedule') }}" class="btn btn-lg fw-semibold btn-outline-secondary" style="border-radius: 0.75rem;">
                <i class="bi bi-calendar3 me-1"></i> GAD Office Hours
            </a>
            <a href="{{ route('student.schedule-appointment') }}" class="appointment-primary-action" aria-label="Create a new appointment">
                <span class="appointment-primary-action__text">New Appointment</span>
                <span class="appointment-primary-action__icon" aria-hidden="true">
                    <svg class="appointment-primary-action__svg" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                </span>
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="sinag-persistent-notice alert alert-dismissible fade show" role="alert" style="background: #f3efff; border: 1px solid #d8cdfc; color: #5b4b9b;">
            <i class="bi bi-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($errors->has('appointment'))
        <div class="sinag-persistent-notice alert alert-warning">{{ $errors->first('appointment') }}</div>
    @endif

    @if ($appointments->isEmpty())
        <!-- Empty State -->
        <div class="text-center py-5">
            <div class="mb-4">
                <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
            </div>
            <h5 class="text-muted">No Appointments Yet</h5>
            <p class="text-muted mb-4">You haven't submitted any appointment requests yet.</p>
            <a href="{{ route('student.schedule-appointment') }}" class="appointment-primary-action appointment-primary-action--wide appointment-action-button" aria-label="Schedule your first appointment">
                <span class="appointment-primary-action__text">Schedule Your First Appointment</span>
                <span class="appointment-primary-action__icon" aria-hidden="true">
                    <svg class="appointment-primary-action__svg" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                </span>
            </a>
        </div>
    @else
        <!-- Appointments List -->
        <div class="row g-4">
            @foreach ($appointments as $appointment)
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden appointment-list-card" style="min-height: 280px;">
                        <div class="row g-0 h-100">
                            <!-- Left Side: Appointment Details -->
                            <div class="col-lg-8">
                                <div class="card-body p-4">
                                    <!-- Title & Meta Info -->
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div>
                                            <h5 class="fw-bold text-dark mb-1">Appointment Request</h5>
                                            <small class="text-muted">Request ID: {{ $appointment->id }}</small>
                                        </div>
                                        <span class="badge rounded-pill" style="background: #f3f4f6; color: #6b7280; font-size: 0.85rem;">
                                            Submitted: {{ $appointment->created_at->format('M d') }}
                                        </span>
                                    </div>

                                    <hr class="my-3" style="border-color: #e5e7eb;">

                                    <!-- Appointment Details Grid -->
                                    <div class="row g-3 mb-4">
                                        <!-- Urgency -->
                                        <div class="col-md-3">
                                            <label class="form-label fw-semibold text-dark small mb-1">Urgency</label>
                                            <div>
                                                @if ($appointment->urgency_level === 'High')
                                                    <span class="badge" style="background:#8f72f5; color:#fff;">{{ $appointment->urgency_level }}</span>
                                                @elseif ($appointment->urgency_level === 'Medium')
                                                    <span class="badge bg-warning text-dark">{{ $appointment->urgency_level }}</span>
                                                @else
                                                    <span class="badge bg-info">{{ $appointment->urgency_level }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <label class="form-label fw-semibold text-dark small mb-1">Format</label>
                                            <div class="text-muted">
                                                <i class="bi {{ $appointment->format === 'Online' ? 'bi-camera-video-fill' : 'bi-people-fill' }} me-1"></i>{{ $appointment->format }}
                                            </div>
                                        </div>

                                        <!-- Submitted -->
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold text-dark small mb-1">Submitted</label>
                                            <p class="mb-0 text-muted" style="font-size: 0.95rem;">
                                                {{ $appointment->created_at->format('M d, Y \a\t h:i A') }}
                                            </p>
                                            @if ($appointment->updated_at && $appointment->updated_at->gt($appointment->created_at))
                                                <p class="mb-0 small text-muted mt-1"><i class="bi bi-clock-history me-1"></i>Recent update: {{ $appointment->updated_at->format('M d, Y \a\t h:i A') }}</p>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Description -->
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold text-dark small">Your Concern</label>
                                        <p class="mb-0 text-muted" style="font-size: 0.9rem; line-height: 1.5;">{{ $appointment->description }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Side: Status & Admin Notes (Sidebar) -->
                            <div class="col-lg-4 border-start" style="background: linear-gradient(135deg, #f8f7ff 0%, #faf8ff 100%);">
                                <div class="card-body p-4 h-100 d-flex flex-column">
                                    <!-- Status Header -->
                                    <div class="mb-4">
                                        <label class="form-label fw-semibold text-dark small">Status</label>
                                        <div>
                                            @if ($appointment->status === 'pending')
                                                <span class="badge bg-warning text-dark rounded-pill" style="font-size: 0.95rem; padding: 0.5rem 1rem;">
                                                    <i class="bi bi-clock"></i> Pending Review
                                                </span>
                                                <p class="small text-muted mt-2">Waiting for GAD Office review</p>
                                            @elseif ($appointment->status === 'completed')
                                                <span class="badge rounded-pill" style="background:#d8cdfc; color:#5b4b9b; font-size: 0.95rem; padding: 0.5rem 1rem;">
                                                    <i class="bi bi-check-circle"></i> Completed
                                                </span>
                                                <p class="small text-muted mt-2">Your concern has been reviewed by the GAD Office.</p>
                                            @elseif ($appointment->status === 'approved')
                                                <span class="badge bg-info rounded-pill" style="font-size: 0.95rem; padding: 0.5rem 1rem;">
                                                    <i class="bi bi-info-circle"></i> Approved
                                                </span>
                                                <p class="small text-muted mt-2">Your appointment has been approved!</p>
                                            @elseif ($appointment->status === 'cancelled')
                                                <span class="badge rounded-pill bg-secondary" style="font-size: 0.95rem; padding: 0.5rem 1rem;">
                                                    <i class="bi bi-x-circle"></i> Cancelled
                                                </span>
                                                <p class="small text-muted mt-2">You cancelled this appointment.</p>
                                            @elseif ($appointment->status === 'rejected')
                                                <span class="badge rounded-pill" style="background:#ede9fe; color:#7658df; font-size: 0.95rem; padding: 0.5rem 1rem;">
                                                    <i class="bi bi-x-circle"></i> Rejected
                                                </span>
                                                <p class="small text-muted mt-2">The GAD Office rejected this appointment request.</p>
                                            @else
                                                <span class="badge rounded-pill" style="background:#ede9fe; color:#7658df; font-size: 0.95rem; padding: 0.5rem 1rem;">
                                                    <i class="bi bi-x-circle"></i> Rejected
                                                </span>
                                                <p class="small text-muted mt-2">Your appointment request has been declined</p>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Admin Notes -->
                                    <div class="flex-grow-1">
                                        <label class="form-label fw-semibold text-dark small">
                                            <i class="bi bi-chat-dots"></i> GAD Office Notes
                                        </label>
                                        @if ($appointment->admin_notes)
                                            <div class="alert alert-light border rounded-3 p-3" style="border-color: #A594F9; background: white; font-size: 0.9rem; line-height: 1.5;">
                                                {{ $appointment->admin_notes }}
                                            </div>
                                        @else
                                            <p class="text-muted small" style="font-style: italic;">No notes from GAD Office yet</p>
                                        @endif
                                    </div>

                                    <div class="mt-auto pt-3">
                                    @if ($appointment->status === 'pending')
                                        <button type="button" class="btn btn-sm btn-outline-primary w-100 rounded-3 fw-semibold mb-2" data-bs-toggle="modal" data-bs-target="#editAppointment{{ $appointment->id }}">
                                            <i class="bi bi-pencil"></i> Edit Date and Time
                                        </button>
                                        <form method="POST" action="{{ route('student.appointments.cancel', $appointment->id) }}" onsubmit="return confirm('Cancel this appointment?');">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-secondary w-100 rounded-3 fw-semibold">Cancel Appointment</button>
                                        </form>
                                    @elseif (in_array(strtolower(trim((string) $appointment->status)), ['completed', 'rejected', 'cancelled']))
                                        <div class="mt-auto pt-3">
                                            @if (in_array(strtolower(trim((string) $appointment->status)), ['rejected', 'cancelled']))
                                                <a href="{{ route('student.schedule-appointment') }}" class="btn btn-sm btn-outline-secondary w-100 rounded-3 fw-semibold">
                                                    <i class="bi bi-plus-circle"></i> Schedule Another
                                                </a>
                                            @endif
                                            <form method="POST" action="{{ route('student.appointments.delete', $appointment->id) }}" class="mt-2" onsubmit="return confirm('Permanently delete this appointment record?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm w-100 rounded-3 fw-semibold appointment-delete-button">
                                                    <i class="bi bi-trash me-1"></i>Delete Appointment
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if ($appointment->status === 'pending')
                    <div class="modal fade" id="editAppointment{{ $appointment->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header"><h5 class="modal-title">Edit Appointment</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                                <form method="POST" action="{{ route('student.appointments.update', $appointment->id) }}">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-body">
                                        <label class="form-label">Date</label>
                                        <input type="date" name="scheduled_date" class="form-control mb-3" value="{{ optional($appointment->scheduled_date)->format('Y-m-d') }}" min="{{ now()->toDateString() }}" required>
                                        <label class="form-label">Time</label>
                                        <input type="time" name="scheduled_time" class="form-control" value="{{ optional($appointment->scheduled_time)->format('H:i') }}" required>
                                    </div>
                                    <div class="modal-footer"><button type="submit" class="btn btn-primary">Save Changes</button></div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
        <div class="d-flex justify-content-center mt-4">{{ $appointments->links() }}</div>
    @endif
</div>

@if($pendingSatisfactionSurvey)
    @include('components.customer-satisfaction-modal', [
        'surveySubject' => $pendingSatisfactionSurvey['subject'],
        'surveySubjectId' => $pendingSatisfactionSurvey['subject_id'],
    ])
@endif

<style>
    .appointment-page {
        padding-bottom: max(1.5rem, env(safe-area-inset-bottom));
    }

    .appointment-list-card {
        transition: all 0.3s ease;
    }

    .appointment-list-card:hover {
        box-shadow: 0 0.5rem 2rem rgba(165, 148, 249, 0.15) !important;
    }

    .appointment-delete-button {
        border: 1px solid #e879a9;
        color: #a83268;
        background: #fff1f6;
    }

    .appointment-delete-button:hover,
    .appointment-delete-button:focus-visible {
        border-color: #d94b87;
        color: #fff;
        background: #d94b87;
    }
    /* From Uiverse.io by andrew-demchenk0 - https://uiverse.io/andrew-demchenk0/mighty-moose-66
       MIT License, Copyright (c) andrew-demchenk0 (A) */
    .appointment-primary-action {
        position:relative;
        display:inline-flex;
        width:11rem;
        height:40px;
        flex:0 0 auto;
        align-items:center;
        overflow:hidden;
        border:1px solid #7658df;
        color:#fff;
        background:#8f72f5;
        text-decoration:none;
        transition:all .3s;
    }

    .appointment-primary-action--wide { width:18rem; }
    .appointment-primary-action__text { position:relative; z-index:1; margin-left:12px; margin-right:46px; color:#fff; font-size:.8rem; font-weight:700; white-space:nowrap; transition:all .3s; }
    .appointment-primary-action__icon { position:absolute; top:0; right:0; left:auto; display:flex; width:39px; height:100%; align-items:center; justify-content:center; color:#fff; background:#7658df; transition:all .3s; }
    .appointment-primary-action__svg { width:24px; height:24px; stroke:currentColor; stroke-width:2; stroke-linecap:round; stroke-linejoin:round; transition:all .3s; }
    .appointment-primary-action:hover,.appointment-primary-action:focus-visible { color:#fff; background:#7658df; }
    .appointment-primary-action:hover .appointment-primary-action__text,.appointment-primary-action:focus-visible .appointment-primary-action__text { color:transparent; }
    .appointment-primary-action:hover .appointment-primary-action__icon,.appointment-primary-action:focus-visible .appointment-primary-action__icon { width:calc(100% - 2px); }
    .appointment-primary-action:active { border-color:#5b43b2; }
    .appointment-primary-action:active .appointment-primary-action__icon { background:#5b43b2; }
    .appointment-primary-action:focus-visible { outline:3px solid #c4b5fd; outline-offset:3px; }

    @media (max-width: 991.98px) {
        .appointment-list-card {
            margin-bottom: 1rem;
        }

        .appointment-list-card .col-lg-8,
        .appointment-list-card .col-lg-4 {
            border-left: 0 !important;
            border-inline-start: 0 !important;
            border-top: 1px solid #e5e7eb !important;
        }
    }

    @media (max-width: 767.98px) {
        .appointment-page-header {
            align-items: stretch !important;
            flex-direction: column;
            gap: 1rem;
            margin-bottom: 1.5rem !important;
        }

        .appointment-page-header h1 {
            font-size: 1.4rem;
        }

        .appointment-page-actions {
            flex-direction: column;
            gap: .55rem !important;
        }

        .appointment-page-actions .btn,
        .appointment-page-actions .appointment-primary-action,
        .appointment-action-button {
            width: 100%;
            min-height: 2.75rem;
        }

        .appointment-list-card {
            min-height: 0 !important;
        }

        .appointment-list-card .card-body {
            padding: 1rem !important;
        }
    }
</style>
@endsection
