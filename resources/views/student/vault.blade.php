@extends('layouts.app')

@section('content')
<style>
    body {
        background: linear-gradient(135deg, #f3e8ff 0%, #f8fafc 100%) !important;
    }
    .vault-glass {
        background: rgba(255,255,255,0.8);
        box-shadow: 0 8px 40px #a594f922, 0 1.5px 8px #a594f911;
        backdrop-filter: blur(8px);
        border: 1.5px solid #ede9fe;
    }
    .vault-title {
        font-size: 2.1rem;
        font-weight: 900;
        letter-spacing: 1.2px;
        background: linear-gradient(90deg, #A594F9 60%, #c4b5fd 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        position: relative;
        z-index: 2;
    }
    .vault-action-btn {
        background: linear-gradient(90deg, #A594F9 60%, #facc15 100%);
        color: #fff;
        font-weight: 700;
        font-size: 1.05rem;
        box-shadow: 0 2px 12px #a594f933;
        border: none;
        transition: transform 0.12s, box-shadow 0.12s;
    }
    .vault-action-btn:hover {
        background: #A594F9;
        color: #fff;
        transform: scale(1.04);
        box-shadow: 0 6px 24px #a594f944;
    }
    .vault-badge {
        background: linear-gradient(90deg, #ede9fe 60%, #facc15 100%);
        color: #A594F9;
        font-weight: 600;
    }
    .vault-status {
        font-weight: 600;
        letter-spacing: 0.5px;
    }
    .vault-status-resolved {
        background: #d8cdfc;
        color: #5b4b9b;
    }
    .nav-pills .nav-link {
        color: #A594F9;
        background: #ede9fe;
        font-weight: 700;
        position: relative;
        transition: background 0.18s, color 0.18s;
    }
    .nav-pills .nav-link.active {
        background: linear-gradient(90deg, #A594F9 60%, #facc15 100%) !important;
        color: #fff !important;
        box-shadow: 0 2px 12px #a594f933;
    }
    .nav-pills .nav-link.active::after {
        content: '';
        display: block;
        position: absolute;
        left: 50%;
        bottom: -8px;
        transform: translateX(-50%);
        width: 60%;
        height: 4px;
        border-radius: 2px;
        background: linear-gradient(90deg, #facc15 0%, #A594F9 100%);
        animation: vaultTabAccent 0.5s cubic-bezier(.4,2,.6,1) 1;
    }
    @keyframes vaultTabAccent {
        0% { width: 0; opacity: 0; }
        100% { width: 60%; opacity: 1; }
    }
</style>
<div class="container-fluid py-4">
    {{-- Page Header --}}
    <div class="text-center mb-5">
        <h2 class="fw-bold vault-title mb-1">Report Status</h2>
        
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4" style="border-radius: 12px;">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        </div>
    @endif

    {{-- Tabs Navigation --}}
    <ul class="nav nav-pills mb-4 gap-2" id="vaultTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active fw-bold px-4 rounded-pill" id="reports-tab" data-bs-toggle="pill" data-bs-target="#reports" type="button" role="tab">
                My Reports ({{ $myReports->count() }})
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-bold px-4 rounded-pill" id="suggestions-tab" data-bs-toggle="pill" data-bs-target="#suggestions" type="button" role="tab">
                My Suggestions ({{ $mySuggestions->count() }})
            </button>
        </li>
    </ul>

    <div class="tab-content" id="vaultTabsContent">
        {{-- REPORTS TAB --}}
        <div class="tab-pane fade show active" id="reports" role="tabpanel">
            @if($myReports->isEmpty())
                <div class="text-center py-5 vault-glass rounded-4 shadow-sm">
                    <i class="bi bi-clipboard-x" style="color:#A594F9; font-size:4rem;"></i>
                    <p class="mt-3 text-muted">You haven't filed any reports yet.</p>
                    <a href="{{ route('student.report') }}" class="btn vault-action-btn rounded-pill px-4 mt-2">File a Report Now</a>
                </div>
            @else
                <div class="row g-3">
                    @foreach($myReports as $report)
                        <div class="col-12">
                            <div class="card border-0 shadow-sm rounded-4 overflow-hidden vault-glass">
                                <div class="card-body p-4">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div>
                                            <span class="badge vault-badge mb-2">{{ $report->category }}</span>
                                            <h5 class="fw-bold mb-1">Case #{{ str_pad($report->id, 5, '0', STR_PAD_LEFT) }}</h5>
                                            <small class="text-muted"><i class="bi bi-calendar3 me-1"></i> Filed on {{ $report->created_at->format('M d, Y') }}</small>
                                        </div>
                                        <div class="text-end">
                                            @php
                                                $statusClass = [
                                                    'Pending' => 'bg-warning text-dark',
                                                    'Under Review' => 'bg-info text-white',
                                                    'Resolved' => 'vault-status-resolved',
                                                    'Dismissed' => 'bg-secondary text-white'
                                                ][$report->status] ?? 'bg-dark';
                                            @endphp
                                            <span class="badge {{ $statusClass }} py-2 px-3 rounded-pill vault-status">{{ $report->status }}</span>
                                            <div class="mt-2">
                                                @if($report->is_anonymous)
                                                    <span class="badge bg-dark rounded-pill" title="Filed Anonymously">
                                                        <i class="bi bi-incognito"></i> Anonymous
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <p class="text-muted text-truncate mb-3" style="max-width: 80%;">{{ $report->description }}</p>
                                    @php
                                        $modalId = 'reportModal-' . $report->id . '-' . uniqid();
                                    @endphp
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-light btn-sm rounded-pill px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#{{ $modalId }}">View Details</button>
                                        @if($report->status == 'Pending')
                                            <button class="btn btn-sm rounded-pill px-3 fw-bold" style="color:#7658df; border:1px solid #a594f9; background:#f3efff;">Cancel Report</button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <!-- Report Details Modal - Outside Card -->
                            <div class="modal fade" id="{{ $modalId }}" tabindex="-1" aria-labelledby="reportModalLabel-{{ $modalId }}" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
                                    <div class="modal-content border-0 rounded-4 shadow">
                                        <div class="modal-header border-0 pb-0">
                                            <h5 class="fw-bold mt-2 ps-2">Incident Report Details</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body p-4">
                                            <div class="row g-4">
                                                <div class="col-md-6">
                                                    <label class="small text-muted d-block">Incident ID</label>
                                                    <span class="fw-bold">#{{ $report->incident_id }}</span>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="small text-muted d-block">Reporter Alias</label>
                                                    <span class="fw-bold text-primary">{{ $report->cloak_alias }}</span>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="small text-muted d-block">Location of Incident</label>
                                                    <span class="fw-medium"><i class="bi bi-geo-alt me-1" style="color:#7658df;"></i> {{ $report->location }}</span>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="small text-muted d-block">Nature of Abuse</label>
                                                    <span class="badge bg-light text-dark border">{{ $report->nature }}</span>
                                                </div>
                                                <div class="col-12">
                                                    <label class="small text-muted d-block">Statement of Facts</label>
                                                    <div class="p-3 bg-light rounded-3 small text-dark border shadow-sm mt-1" style="line-height: 1.6;">
                                                        {{ $report->description }}
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <label class="small text-muted d-block mb-2">Attached Evidence</label>
                                                    @php
                                                        $evidenceArr = $report->evidence_files;
                                                    @endphp
                                                    @if($evidenceArr && count($evidenceArr))
                                                        <div class="row g-3">
                                                            @foreach($evidenceArr as $idx => $evidencePath)
                                                                @php 
                                                                    $extension = pathinfo($evidencePath, PATHINFO_EXTENSION);
                                                                    $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                                                    $evidenceUrl = route('reports.evidence.view', ['report' => $report->id, 'index' => $idx]);
                                                                @endphp
                                                                <div class="col-12 col-md-6 col-lg-4">
                                                                    @if($isImage)
                                                                        <div class="evidence-img-wrapper bg-white rounded-3 p-2 shadow-sm text-center" style="height:220px; display:flex; flex-direction:column; justify-content:center; align-items:center;">
                                                                            <img src="{{ $evidenceUrl }}" class="img-fluid rounded mb-2" style="max-height: 160px; max-width:100%; object-fit:contain; cursor: zoom-in;" onclick="window.open(this.src)">
                                                                            <p class="small text-muted mb-0"><i class="bi bi-info-circle me-1"></i>Click image to preview</p>
                                                                        </div>
                                                                    @else
                                                                        <div class="evidence-doc-wrapper bg-white rounded-3 p-3 shadow-sm d-flex flex-column align-items-center justify-content-center" style="height: 120px;">
                                                                            <i class="bi bi-file-earmark-text fs-2 text-primary mb-2"></i>
                                                                            <span class="d-block small fw-bold mb-1">Document Attachment</span>
                                                                            <a href="{{ $evidenceUrl }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                                                <i class="bi bi-download me-1"></i> Open File
                                                                            </a>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @else
                                                        <div class="p-3 border border-dashed rounded-3 text-center bg-light text-muted">
                                                            No evidence attached.
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- SUGGESTIONS TAB --}}
        <div class="tab-pane fade" id="suggestions" role="tabpanel">
            @if($mySuggestions->isEmpty())
                <div class="text-center py-5 vault-glass rounded-4 shadow-sm">
                    <i class="bi bi-chat-left-dots" style="color:#A594F9; font-size:4rem;"></i>
                    <p class="mt-3 text-muted">No suggestions found.</p>
                </div>
            @else
                <div class="table-responsive bg-white p-3 rounded-4 shadow-sm">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Suggestion</th>
                                <th>Status</th>
                                <th>Upvotes</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($mySuggestions as $sugg)
                                <tr>
                                    <td><button type="button" class="btn btn-link p-0 text-start text-decoration-none" data-bs-toggle="modal" data-bs-target="#mySuggestionModal{{ $sugg->id }}"><span class="text-truncate d-inline-block" style="max-width: 250px;">{{ $sugg->message }}</span></button></td>
                                    <td><button type="button" class="btn btn-sm border-0 rounded-pill" style="background:#f3efff;color:#5b4b9b;" data-bs-toggle="modal" data-bs-target="#mySuggestionModal{{ $sugg->id }}">{{ $sugg->status ?? 'Shared' }}</button></td>
                                    <td><i class="bi bi-arrow-up-circle-fill text-primary"></i> {{ $sugg->upvotes_count ?? 0 }}</td>
                                    <td>{{ $sugg->created_at->diffForHumans() }}</td>
                                </tr>
                                <div class="modal fade" id="mySuggestionModal{{ $sugg->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header"><h5 class="modal-title">Suggestion Details</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                                            <div class="modal-body"><div class="small text-muted text-uppercase">{{ $sugg->category }}</div><p class="mt-2 mb-0" style="white-space:pre-wrap;">{{ $sugg->message }}</p></div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>



<script>
    // Ensure Bootstrap modal is initialized only once
    document.addEventListener('DOMContentLoaded', function() {
        // No manual initialization needed if using data-bs-toggle and data-bs-target
        // This script is intentionally left blank to avoid double initialization
    });
</script>
@endsection