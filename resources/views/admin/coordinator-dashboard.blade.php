@extends('layouts.app')

@section('content')
<style>
    .coordinator-dashboard {
        max-width: 1440px;
        margin: 0 auto;
        color: #35216d;
    }

    .coordinator-hero {
        border: 0;
        border-radius: 1.5rem;
        color: #fff;
        background: linear-gradient(120deg, #8f72f5 0%, #A594F9 58%, #c4b5fd 100%);
        box-shadow: 0 22px 46px rgba(165, 148, 249, 0.2);
    }

    .coordinator-panel,
    .coordinator-metric {
        border: 1px solid rgba(165, 148, 249, 0.14);
        border-radius: 1.25rem;
        box-shadow: 0 14px 34px rgba(165, 148, 249, 0.1);
    }

    .coordinator-metric {
        background: linear-gradient(135deg, #f1eaff 0%, #d9c8ff 100%);
    }

    .coordinator-metric-value {
        color: #4c2a9d;
        font-size: 2rem;
        font-weight: 800;
    }

    .coordinator-bar {
        height: .65rem;
        overflow: hidden;
        border-radius: 999px;
        background: #f1eaff;
    }

    .coordinator-bar-fill {
        height: 100%;
        border-radius: inherit;
        background: linear-gradient(90deg, #8f72f5, #A594F9);
    }
</style>

<div class="container-fluid coordinator-dashboard">
    <section class="card coordinator-hero mb-4">
        <div class="card-body p-4 p-lg-5 d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-4">
            <div>
                <span class="badge rounded-pill bg-white text-dark px-3 py-2 mb-3">GAD Coordinator</span>
                <h1 class="h2 fw-bold mb-2">GAD Report Analytics</h1>
                <p class="mb-0 text-white-50">Read-only overview of incident report totals and trends.</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a class="btn btn-light rounded-pill fw-semibold" href="{{ route('admin.gad-report.export', ['format' => 'pdf', 'period' => '30days']) }}">
                    <i class="bi bi-file-earmark-pdf me-1"></i> Export PDF
                </a>
                <a class="btn btn-outline-light rounded-pill fw-semibold" href="{{ route('admin.gad-report.export', ['format' => 'excel', 'period' => '30days']) }}">
                    <i class="bi bi-file-earmark-spreadsheet me-1"></i> Export Excel
                </a>
            </div>
        </div>
    </section>

    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card coordinator-metric h-100">
                <div class="card-body">
                    <div class="small fw-bold text-uppercase text-muted">Total Reports</div>
                    <div class="coordinator-metric-value">{{ number_format($totalReports) }}</div>
                    <div class="small text-muted">All incident report records</div>
                </div>
            </div>
        </div>
        @foreach(['Pending', 'Under Review', 'Resolved'] as $status)
        <div class="col-sm-6 col-xl-3">
            <div class="card coordinator-metric h-100">
                <div class="card-body">
                    <div class="small fw-bold text-uppercase text-muted">{{ $status }}</div>
                    <div class="coordinator-metric-value">{{ number_format((int) $reportCountsByStatus->get($status, 0)) }}</div>
                    <div class="small text-muted">Reports by status</div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <section class="card coordinator-panel h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h2 class="h5 fw-bold mb-0">Reports by Status</h2>
                </div>
                <div class="card-body">
                    @forelse($reportStatusCounts as $row)
                        @php($percentage = $totalReports > 0 ? ($row->total / $totalReports) * 100 : 0)
                        <div class="mb-4">
                            <div class="d-flex justify-content-between small mb-2">
                                <span class="fw-semibold">{{ $row->status }}</span>
                                <span class="text-muted">{{ number_format($row->total) }} ({{ number_format($percentage, 1) }}%)</span>
                            </div>
                            <div class="coordinator-bar" role="progressbar" aria-label="{{ $row->status }} reports" aria-valuenow="{{ (int) round($percentage) }}" aria-valuemin="0" aria-valuemax="100">
                                <div class="coordinator-bar-fill" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted mb-0">No incident reports have been recorded.</p>
                    @endforelse
                </div>
            </section>
        </div>
        <div class="col-lg-6">
            <section class="card coordinator-panel h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h2 class="h5 fw-bold mb-0">Reports by Nature</h2>
                </div>
                <div class="card-body">
                    @forelse($reportNatureCounts as $row)
                        @php($percentage = $totalReports > 0 ? ($row->total / $totalReports) * 100 : 0)
                        <div class="mb-4">
                            <div class="d-flex justify-content-between small mb-2">
                                <span class="fw-semibold">{{ $row->nature }}</span>
                                <span class="text-muted">{{ number_format($row->total) }}</span>
                            </div>
                            <div class="coordinator-bar" role="progressbar" aria-label="{{ $row->nature }} reports" aria-valuenow="{{ (int) round($percentage) }}" aria-valuemin="0" aria-valuemax="100">
                                <div class="coordinator-bar-fill" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted mb-0">No incident reports have been recorded.</p>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
</div>
@endsection
