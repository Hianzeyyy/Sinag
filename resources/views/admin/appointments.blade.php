@extends('layouts.app')

@section('content')
<div class="container-fluid py-4 appointments-page">
    <!-- Page Header -->
    <div class="text-center mb-4">
        <div>
           <h1 class="h3 fw-bold mb-1" style="color: #A594F9;">
           <i class="bi bi-calendar-check"></i> Appointment Requests
          </h1>
            <p class="text-muted mb-0">Review concerns, leave notes, and mark requests as completed</p>
        </div>
        <span class="badge bg-primary rounded-pill fs-6 mt-3">{{ $appointments->count() }} Requests</span>
    </div>

    @if (session('success'))
        <div class="alert alert-dismissible fade show" role="alert" style="background: #f3efff; border: 1px solid #d8cdfc; color: #5b4b9b;">
            <i class="bi bi-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($appointments->isEmpty())
        <!-- Empty State -->
        <div class="text-center py-5">
            <div class="mb-4">
                <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
            </div>
            <h5 class="text-muted">No Appointment Requests Yet</h5>
            <p class="text-muted">There are currently no appointment requests to review.</p>
        </div>
    @else
        <div class="appointment-filters d-flex justify-content-center flex-wrap gap-2 mb-4" role="group" aria-label="Filter appointments by priority">
            <button type="button" class="appointment-filter active" data-urgency="all">All <span>{{ $appointments->total() }}</span></button>
            @foreach(['High', 'Medium', 'Low'] as $urgency)
                <button type="button" class="appointment-filter" data-urgency="{{ strtolower($urgency) }}">{{ $urgency }} <span>{{ $appointmentsByUrgency->get($urgency, collect())->count() }}</span></button>
            @endforeach
        </div>

        <div class="table-responsive bg-white rounded-4 shadow-sm border">
            <table class="table table-hover align-middle mb-0" id="appointmentGrid">
                <thead class="table-light">
                    <tr class="small text-muted text-uppercase">
                        <th class="ps-4">Priority</th>
                        <th>Requested by</th>
                        <th>Email</th>
                        <th>Scheduled</th>
                        <th>Submitted</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
            @foreach ($appointments as $appointment)
                @php($urgency = strtolower($appointment->urgency_level ?? 'low'))
                <tr class="appointment-grid-item" data-urgency="{{ $urgency }}">
                    <td class="ps-4"><span class="appointment-priority priority-{{ $urgency }}">{{ ucfirst($urgency) }}</span></td>
                    <td><strong>{{ $appointment->full_name }}</strong><small class="d-block text-muted">ID: {{ $appointment->student_id }}</small></td>
                    <td class="small">{{ optional($appointment->user)->email ?: 'N/A' }}</td>
                    <td class="small">{{ optional($appointment->scheduled_date)->format('M d, Y') }}<span class="d-block text-muted">{{ optional($appointment->scheduled_time)->format('h:i A') }}</span></td>
                    <td class="small">{{ $appointment->created_at->format('M d, Y h:i A') }}</td>
                    <td>
                        @if ($appointment->status === 'pending') <span class="badge bg-warning text-dark rounded-pill">Pending</span>
                        @elseif ($appointment->status === 'completed') <span class="badge sinag-violet-badge rounded-pill">Completed</span>
                        @elseif ($appointment->status === 'approved') <span class="badge sinag-violet-badge rounded-pill">Approved</span>
                        @elseif ($appointment->status === 'rejected') <span class="badge sinag-alert-badge rounded-pill">Rejected</span>
                        @else <span class="badge bg-secondary rounded-pill">{{ ucfirst($appointment->status) }}</span> @endif
                    </td>
                    <td class="text-end pe-4"><button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#appointmentModal{{ $appointment->id }}"><i class="bi bi-eye me-1"></i>View</button></td>
                </tr>
                <tr><td colspan="7" class="p-0 border-0"><div class="modal fade" id="appointmentModal{{ $appointment->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-lg">
                                        <div class="modal-content appointment-resume-modal">
                                            <div class="modal-header border-0"><div><div class="small text-uppercase text-muted">Appointment Details</div><h5 class="modal-title fw-bold">{{ $appointment->full_name }}</h5></div><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                                            <div class="modal-body">
                                                <div class="appointment-detail-grid">
                                                    <div><span>Student/Employee ID</span><strong>{{ $appointment->student_id }}</strong></div>
                                                    <div><span>Email</span><strong>{{ optional($appointment->user)->email ?: 'N/A' }}</strong></div>
                                                    <div><span>Priority</span><strong>{{ $appointment->urgency_level }}</strong></div>
                                                    <div><span>Scheduled</span><strong>{{ optional($appointment->scheduled_date)->format('M d, Y') }} at {{ optional($appointment->scheduled_time)->format('h:i A') }}</strong></div>
                                                    <div><span>Status</span><strong>{{ ucfirst($appointment->status) }}</strong></div>
                                                    <div><span>Submitted</span><strong>{{ $appointment->created_at->format('M d, Y h:i A') }}</strong></div>
                                                </div>
                                                <div class="mt-3"><div class="small text-uppercase text-muted fw-bold">Concern</div><p class="mt-2 mb-0" style="white-space:pre-wrap;">{{ $appointment->description }}</p></div>
                                                <form method="POST" action="{{ route('admin.appointments.add-details', $appointment->id) }}" class="mt-3">@csrf<label class="small text-uppercase text-muted fw-bold">Thoughts / Comments</label><textarea class="form-control mt-2" name="admin_notes" rows="3" required>{{ old('admin_notes', $appointment->admin_notes) }}</textarea><button class="btn btn-sm mt-2" style="background:#A594F9;color:#fff;" type="submit"><i class="bi bi-save me-1"></i>Save Notes</button></form>
                                                @if($appointment->admin_notes)<div class="mt-3"><div class="small text-uppercase text-muted fw-bold">Admin Notes</div><p class="mt-2 mb-0">{{ $appointment->admin_notes }}</p></div>@endif
                                                @if($appointment->cancellation_reason)<div class="mt-3"><div class="small text-uppercase text-muted fw-bold">Cancellation Reason</div><p class="mt-2 mb-0">{{ $appointment->cancellation_reason }}</p></div>@endif
                                            </div>
                                            <div class="modal-footer border-0">
                                                @if($appointment->status !== 'completed' && $appointment->status !== 'cancelled')
                                                    <form method="POST" action="{{ route('admin.appointments.complete', $appointment->id) }}" class="me-auto">@csrf<button type="submit" class="btn text-white" style="background:#b0a1fa;" onclick="return confirm('Mark this appointment as completed?')"><i class="bi bi-check2-circle me-1"></i>Mark as Completed</button></form>
                                                @endif
                                                @if($appointment->status !== 'completed' && $appointment->status !== 'cancelled')
                                                    <form method="POST" action="{{ route('admin.appointments.cancel', $appointment->id) }}" class="d-flex gap-2 w-100">@csrf<input class="form-control" name="cancellation_reason" placeholder="Why is this appointment cancelled?" required><button class="btn btn-outline-secondary" type="submit">Cancel</button></form>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                </div></td></tr>
            @endforeach
                </tbody>
            </table>
        </div>
        <div id="appointmentEmpty" class="alert alert-light border rounded-3 text-muted text-center d-none mt-3">No appointments match this priority.</div>
        <div class="d-flex justify-content-center mt-4">
            {{ $appointments->onEachSide(1)->links() }}
        </div>
    @endif
</div>

<style>
    .appointments-page {
        max-width: 1480px;
        margin: 0 auto;
    }

    .appointment-grid-item { width: 100%; }

    .appointment-priority {
        align-self: flex-start;
        padding: 0.3rem 0.6rem;
        border: 1px solid currentColor;
        border-radius: 999px;
        font-size: 0.68rem;
        font-weight: 800;
        white-space: nowrap;
    }

    .priority-high { color: #6d55d9; background: #f3efff; }
    .priority-medium { color: #9a7200; background: #fff8d6; }
    .priority-low { color: #2563eb; background: #eff6ff; }

    .appointment-filters .appointment-filter {
        border: 1px solid #d8cdfc;
        border-radius: 999px;
        padding: 0.45rem 0.9rem;
        background: #f5f3ff;
        color: #5b4b9b;
        font-weight: 800;
        font-size: 0.82rem;
    }

    .appointment-filters .appointment-filter span {
        display: inline-flex;
        min-width: 1.35rem;
        justify-content: center;
        margin-left: 0.25rem;
        border-radius: 999px;
        background: #fff;
        color: #5b4b9b;
    }

    .appointment-filters .appointment-filter.active,
    .appointment-filters .appointment-filter:hover {
        background: #8f72f5;
        color: #fff;
    }

    .appointment-card .card-header {
        min-height: 72px;
    }

    .appointment-card .card-body {
        font-size: 0.86rem;
    }

    .appointment-card textarea {
        min-height: 74px;
        font-size: 0.82rem;
    }

    .appointment-card .alert {
        margin-bottom: 0.9rem !important;
    }

    .appointment-card {
        transition: all 0.3s ease;
    }

    .appointment-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 2rem rgba(165, 148, 249, 0.15) !important;
    }

    .form-control:focus {
        border-color: #A594F9;
        box-shadow: 0 0 0 0.2rem rgba(165, 148, 249, 0.25);
    }

    .form-label {
        margin-bottom: 0.5rem;
    }

    .sinag-violet-badge { background: #d8cdfc; color: #5b4b9b; }
    .sinag-alert-badge { background: #f8cb12; color: #4a3500; }
    .sinag-violet-alert { background: #f3efff; color: #5b4b9b; border-color: #d8cdfc !important; }
    .appointment-detail-trigger { cursor: pointer; }
    .appointment-resume-modal { border: 0; border-radius: 1rem; }
    .appointment-detail-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: .75rem; }
    .appointment-detail-grid > div { padding: .7rem; border: 1px solid #ede9fe; border-radius: .6rem; background: #faf9ff; }
    .appointment-detail-grid span { display: block; color: #6b7280; font-size: .7rem; text-transform: uppercase; letter-spacing: .06em; }
    .appointment-detail-grid strong { display: block; margin-top: .25rem; overflow-wrap: anywhere; }

    @media (max-width: 575.98px) {
        .container-fluid.py-4 { padding: 1rem !important; }
        .appointment-card .card-header { padding: 0.75rem !important; }
        .appointment-detail-grid { grid-template-columns: 1fr; }
    }
</style>
<script>
    document.querySelectorAll('.appointment-filter').forEach(function (button) {
        button.addEventListener('click', function () {
            const selected = button.dataset.urgency;
            let visible = 0;
            document.querySelectorAll('.appointment-filter').forEach(item => item.classList.remove('active'));
            button.classList.add('active');
            document.querySelectorAll('.appointment-grid-item').forEach(function (item) {
                const matches = selected === 'all' || item.dataset.urgency === selected;
                item.classList.toggle('d-none', !matches);
                if (matches) visible++;
            });
            document.getElementById('appointmentEmpty').classList.toggle('d-none', visible !== 0);
        });
    });
</script>
@endsection
