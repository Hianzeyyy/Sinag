@extends('layouts.app')

@section('content')
<style>
    .urgent-admin-page { padding: clamp(.75rem, 2.5vw, 1.5rem); }
    .urgent-grid { display: grid; grid-template-columns: minmax(250px, 360px) minmax(0, 1fr); gap: 1rem; }
    .call-item { border: 1px solid rgba(165,148,249,0.2); border-radius: 0.9rem; padding: 0.8rem; background:#fff; }
    .call-item + .call-item { margin-top: 0.7rem; }
    .status-chip { font-size: 0.72rem; padding: 0.2rem 0.55rem; border-radius: 999px; font-weight: 700; }
    .status-waiting { background:#fef3c7; color:#92400e; }
    .status-joined { background:#d8cdfc; color:#5b4b9b; }
    .jitsi-frame { display:block; width: 100%; height: min(72dvh, 760px); min-height: 300px; border: 0; border-radius: 1rem; background:#0f172a; }
    @media (max-width: 991.98px) { .urgent-grid { grid-template-columns: minmax(0, 1fr); } .jitsi-frame { height: 60dvh; min-height: 260px; } }
    @media (max-width: 575.98px) { .urgent-admin-page { padding: .5rem; } .urgent-admin-page .card-body { padding: .75rem !important; } .jitsi-frame { height: 55dvh; min-height: 220px; border-radius: .75rem; } }
</style>

<div class="urgent-admin-page">
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-3 p-lg-4">
            <div class="d-flex justify-content-between align-items-center mb-3 gap-2 flex-wrap">
                <div>
                    <h4 class="fw-bold mb-1" style="color:#8f72f5;">Urgent Call Monitor</h4>
                    <div class="text-muted small">Incoming student urgent calls appear here automatically.</div>
                </div>
                <span id="callCount" class="badge rounded-pill" style="background:#A594F9;">0 active</span>
            </div>

            <div class="urgent-grid">
                <div>
                    <div id="callList"></div>
                </div>
                <div>
                    <div id="callJoinStatus" class="small text-muted mb-2" role="status" aria-live="polite">Select an active call to connect.</div>
                    <iframe id="jitsiFrame" title="GAD urgent audio and video call" class="jitsi-frame" allow="camera; microphone; fullscreen; display-capture; autoplay" referrerpolicy="strict-origin-when-cross-origin" src="about:blank"></iframe>
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mt-2">
                        <div class="small text-muted">Join a call from the list. Allow camera and microphone in your browser.</div>
                        <button class="btn btn-sm btn-outline-danger" id="endCallButton" type="button" onclick="endCall()" hidden><i class="bi bi-telephone-x me-1"></i>End Call</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const callListEl = document.getElementById('callList');
    const callCountEl = document.getElementById('callCount');
    const jitsiFrame = document.getElementById('jitsiFrame');
    const endCallButton = document.getElementById('endCallButton');
    let activeCallId = null;
    let lastIds = new Set();
    let initialFeedLoaded = false;

    function roomToJitsiUrl(room) {
        const safeRoom = encodeURIComponent(room || 'sinag-urgent-room');
        return `https://meet.jit.si/${safeRoom}#config.prejoinPageEnabled=true`;
    }

    function escapeHtml(value) {
        return String(value ?? '').replace(/[&<>"']/g, character => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
        })[character]);
    }

    async function joinCall(callId) {
        const status = document.getElementById('callJoinStatus');
        status.textContent = 'Connecting to the call…';
        try {
            const res = await fetch(`{{ url('/admin/urgent-calls') }}/${encodeURIComponent(callId)}/join`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            });
            if (!res.ok) throw new Error(`Could not join the call (${res.status}).`);
            const data = await res.json();
            if (!data.room) throw new Error(data.message || 'This call is no longer active.');
            activeCallId = callId;
            jitsiFrame.src = roomToJitsiUrl(data.room);
            status.textContent = 'Connected to the shared room. Allow camera and microphone access in the call.';
            endCallButton.hidden = false;
        } catch (error) {
            status.textContent = error.message;
        }
    }

    async function endCall() {
        if (!activeCallId) return;
        endCallButton.disabled = true;
        try {
            const response = await fetch(`{{ url('/admin/urgent-calls') }}/${encodeURIComponent(activeCallId)}/end`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            });
            if (!response.ok) throw new Error(`Unable to end call (${response.status}).`);
            jitsiFrame.src = 'about:blank';
            activeCallId = null;
            endCallButton.hidden = true;
            document.getElementById('callJoinStatus').textContent = 'Call ended. The student has been notified.';
            loadFeed();
        } catch (error) {
            document.getElementById('callJoinStatus').textContent = error.message;
        } finally {
            endCallButton.disabled = false;
        }
    }

    function renderCalls(calls) {
        callCountEl.textContent = `${calls.length} active`;

        const currentIds = new Set(calls.map(c => c.id));
        if (initialFeedLoaded && calls.some(c => !lastIds.has(c.id))) {
            const notification = document.createElement('div');
            notification.className = 'alert alert-warning m-3 shadow';
            notification.setAttribute('role', 'status');
            notification.textContent = 'New urgent call received.';
            document.body.appendChild(notification);
            setTimeout(() => notification.remove(), 6000);
        }
        lastIds = currentIds;
        initialFeedLoaded = true;

        if (!calls.length) {
            callListEl.innerHTML = '<div class="text-muted small">No urgent calls right now.</div>';
            return;
        }

        callListEl.innerHTML = calls.map(call => `
            <div class="call-item">
                <div class="d-flex justify-content-between align-items-start gap-2">
                    <div>
                        <div class="fw-bold">${escapeHtml(call.student_name || 'Student')}</div>
                        <div class="small text-muted">Room: ${escapeHtml(call.room || '-')}</div>
                        <div class="small text-muted">${escapeHtml(call.created_at || '')}</div>
                    </div>
                    <span class="status-chip ${call.status === 'joined' ? 'status-joined' : 'status-waiting'}">${escapeHtml(call.status || 'waiting')}</span>
                </div>
                <button class="btn btn-sm mt-2" style="background:#A594F9; color:#fff;" onclick="joinCall('${escapeHtml(call.id)}')">
                    <i class="bi bi-camera-video-fill me-1"></i>Join Call
                </button>
            </div>
        `).join('');
    }

    async function loadFeed() {
        try {
            const res = await fetch("{{ route('admin.urgent.calls.feed') }}", { headers: { 'Accept': 'application/json' } });
            if (!res.ok) throw new Error(`Could not load urgent calls (${res.status}).`);
            const data = await res.json();
            renderCalls(data.calls || []);
        } catch (e) {
            console.error(e);
            callCountEl.textContent = 'Calls unavailable';
        }
    }

    loadFeed();
    setInterval(loadFeed, 5000);
</script>
@endsection