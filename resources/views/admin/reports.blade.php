@extends('layouts.app')

@section('content')
<style>
    .sinag-violet-badge { background: #d8cdfc; color: #5b4b9b; border-color: #c4b5fd !important; }
    .sinag-yellow-badge { background: #f8cb12; color: #4a3500; border-color: #e5b900 !important; }
    
</style>
<div class="container py-4">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            
            <li class="breadcrumb-item active fw-bold" aria-current="page" style="color: #A594F9;">
    Report Management
</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-lg border-0 rounded-4" style="overflow: hidden;">
                <div style="height: 5px; background: #A594F9;"></div>
                
                <div class="card-header bg-white py-4 d-flex justify-content-between align-items-center border-0">
                    <div>
                        <h4 class="fw-bold mb-0 text-dark">Confidential Abuse Inbox</h4>
                        <p class="text-muted small mb-0">Managing <span class="badge bg-primary-subtle text-primary">{{ $reports->count() }} Active Cases</span> </p>
                    </div>
                    <div class="d-flex gap-2">
                        <div class="input-group input-group-sm" style="width: 250px;">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" id="reportSearch" class="form-control bg-light border-start-0" placeholder="Search Incident ID or Alias...">
                        </div>
                    </div>
                </div>
                
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="reportsTable">
                            <thead class="bg-light text-muted small">
                                <tr>
                                    <th class="ps-4 py-3">DATE FILED</th>
                                    <th>INCIDENT ID</th>
                                    <th>CLOAK ALIAS</th>
                                    <th>NATURE OF ABUSE</th>
                                    <th>PRIORITY</th>
                                    <th>STATUS</th>
                                    <th class="text-end pe-4">ACTION</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reports as $report)
                                <tr>
                                    <td class="ps-4 small text-muted">{{ $report->created_at->format('M d, Y') }}</td>
                                    <td><span class="fw-bold text-dark">#{{ $report->incident_id }}</span></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center me-2" 
                                                 style="width: 30px; height: 30px; background: {{ $report->priority == 'High' ? '#fef3c7' : '#f3e8ff' }};">
                                                <i class="bi {{ $report->priority == 'High' ? 'bi-shield-exclamation' : 'bi-incognito text-primary' }}" style="{{ $report->priority == 'High' ? 'color:#806000;' : '' }}"></i>
                                            </div>
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="small fw-medium text-secondary">{{ $report->cloak_alias }}</span>
                                                <span id="seen-badge-{{ $report->id }}" class="badge rounded-pill {{ is_null($report->seen_at) ? 'sinag-yellow-badge' : 'sinag-violet-badge' }}">
                                                    {{ is_null($report->seen_at) ? 'UNREAD' : 'READ' }}
                                                </span>
                                                @if(is_null($report->seen_at) && $report->created_at->gt(now()->subDay()))
                                                    <span id="new-badge-{{ $report->id }}" class="badge rounded-pill sinag-yellow-badge">NEW</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="small">{{ $report->nature }}</span></td>
                                    <td>
                                        <span class="badge border {{ $report->priority == 'High' ? 'sinag-yellow-badge pulse-animation' : 'sinag-violet-badge' }}">
                                            {{ $report->priority }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge rounded-pill px-3 py-2 border
                                            {{ $report->status == 'Pending' ? 'sinag-yellow-badge' : '' }}
                                            {{ $report->status == 'Under Review' ? 'sinag-violet-badge' : '' }}
                                            {{ $report->status == 'Resolved' ? 'sinag-violet-badge' : '' }}">
                                            {{ $report->status }}
                                        </span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <button class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm" 
                                                style="background: #A594F9; border: none;"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#modal-{{ $report->id }}"
                                                data-report-id="{{ $report->id }}">
                                            Review Details
                                        </button>
                                        <form action="{{ route('admin.reports.delete', $report->id) }}" method="POST" style="display:inline-block;">
                                            @csrf
                                            @method('DELETE')
                                           <button type="submit" 
        class="btn btn-sm rounded-pill px-3 ms-2 text-white" 
        style="background-color: #ee529e !important; border-color: #ee529e !important;" 
        onclick="return confirm('Are you sure you want to delete this report?')">
    Delete
</button>
                                        </form>
                                    </td>
                                </tr>

                                <div class="modal fade" id="modal-{{ $report->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-lg">
                                        <div class="modal-content border-0 rounded-4 shadow">
                                            <div class="modal-header border-0 pb-0">
                                                <h5 class="fw-bold mt-2 ps-2">Incident Report Details</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body p-4">
                                                <div class="d-flex justify-content-end mb-3">
                                                    <a href="{{ route('admin.reports.pdf', $report->id) }}" class="btn btn-outline-primary rounded-pill" target="_blank">
                                                        <i class="bi bi-file-earmark-pdf me-1"></i> Download PDF
                                                    </a>
                                                </div>
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
                                                        <span class="fw-medium"><i class="bi bi-geo-alt text-danger me-1"></i> {{ $report->location }}</span>
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
                                                            $evidenceFiles = $report->evidence_files;
                                                        @endphp
                                                        @if(count($evidenceFiles))
                                                            <div class="row g-3">
                                                                @foreach($evidenceFiles as $idx => $evidencePath)
                                                                    @php
                                                                        $extension = pathinfo($evidencePath, PATHINFO_EXTENSION);
                                                                        $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                                                        $evidenceUrl = route('reports.evidence.view', ['report' => $report->id, 'index' => $idx]);
                                                                    @endphp
                                                                    <div class="col-12 col-md-6 col-lg-4">
                                                                        @if($isImage)
                                                                            <div class="p-2 border rounded-3 bg-light text-center h-100">
                                                                                <img src="{{ $evidenceUrl }}"
                                                                                     class="img-fluid rounded shadow-sm"
                                                                                     style="max-height: 220px; cursor: zoom-in;"
                                                                                     onclick="window.open(this.src)">
                                                                                <p class="small text-muted mt-2 mb-0"><i class="bi bi-info-circle me-1"></i>Click image to preview</p>
                                                                            </div>
                                                                        @else
                                                                            <div class="d-flex align-items-center justify-content-center p-3 border rounded-3 bg-light h-100">
                                                                                <i class="bi bi-file-earmark-text fs-1 text-primary me-3"></i>
                                                                                <div class="text-start">
                                                                                    <span class="d-block small fw-bold">Document Attachment</span>
                                                                                    <a href="{{ $evidenceUrl }}" target="_blank" class="btn btn-sm btn-outline-primary mt-1">
                                                                                        <i class="bi bi-download me-1"></i> Open File
                                                                                    </a>
                                                                                </div>
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @else
                                                            <div class="p-3 border border-dashed rounded-3 text-center bg-light text-muted">
                                                                <small><i class="bi bi-camera-video-off me-1"></i> No media or files attached to this report.</small>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>

                                                <hr class="my-4">

                                                <form action="{{ route('admin.reports.update', $report->id) }}" method="POST">
                                                    @csrf
                                                    <div class="row g-3">
                                                        <div class="col-md-6">
                                                            <label class="small fw-bold mb-1">Update Case Status</label>
                                                            <select name="status" class="form-select shadow-sm">
                                                                <option value="Pending" {{ $report->status == 'Pending' ? 'selected' : '' }}>Pending</option>
                                                                <option value="Under Review" {{ $report->status == 'Under Review' ? 'selected' : '' }}>Under Review</option>
                                                                <option value="Resolved" {{ $report->status == 'Resolved' ? 'selected' : '' }}>Resolved</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="small fw-bold mb-1">Priority Level</label>
                                                            <select name="priority" class="form-select shadow-sm">
                                                                <option value="Low" {{ $report->priority == 'Low' ? 'selected' : '' }}>Low Priority</option>
                                                                <option value="Medium" {{ $report->priority == 'Medium' ? 'selected' : '' }}>Medium Priority</option>
                                                                <option value="High" {{ $report->priority == 'High' ? 'selected' : '' }}>High Priority / Urgent</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-12 mt-4">
                                                            <button type="submit" class="btn btn-primary w-100 py-2 rounded-pill shadow" style="background: #A594F9; border:none;">
                                                                <i class="bi bi-check-circle me-1"></i> Confirm Update
                                                            </button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                            <p class="mb-0">No confidential reports found.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
</div>

<script>
    // Live Search Functionality
    document.getElementById('reportSearch').addEventListener('keyup', function() {
        let filter = this.value.toUpperCase();
        let rows = document.querySelector("#reportsTable tbody").rows;
        
        for (let i = 0; i < rows.length; i++) {
            let idCol = rows[i].cells[1].textContent.toUpperCase();
            let aliasCol = rows[i].cells[2].textContent.toUpperCase();
            if (idCol.indexOf(filter) > -1 || aliasCol.indexOf(filter) > -1) {
                rows[i].style.display = "";
            } else {
                rows[i].style.display = "none";
            }      
        }
    });

    // Mark report as seen when admin opens the details modal.
    document.querySelectorAll('[id^="modal-"]').forEach((modalEl) => {
        modalEl.addEventListener('shown.bs.modal', function () {
            const reportId = this.id.replace('modal-', '');
            fetch(`{{ url('/admin/reports') }}/${reportId}/seen`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
            }).then(() => {
                const seenBadge = document.getElementById(`seen-badge-${reportId}`);
                const newBadge = document.getElementById(`new-badge-${reportId}`);

                if (seenBadge) {
                    seenBadge.className = 'badge rounded-pill sinag-violet-badge';
                    seenBadge.textContent = 'READ';
                }

                if (newBadge) {
                    newBadge.remove();
                }
            }).catch(() => {
                // Fail silently: UI will update on next full page load.
            });
        });
    });
</script>

<style>
    .table tbody tr:hover {
        background-color: #fbfaff !important;
        transition: 0.2s;
    }
    .badge { font-weight: 600; font-size: 0.75rem; }
    .pulse-animation { animation: pulse-red 2s infinite; }
    @keyframes pulse-red {
        0% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.7; transform: scale(0.95); }
        100% { opacity: 1; transform: scale(1); }
    }
    .border-dashed { border-style: dashed !important; border-width: 2px !important; }
</style>
@endsection