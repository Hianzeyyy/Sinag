

<?php $__env->startSection('content'); ?>
<style>
    .urgent-admin-page { padding: 1.25rem; }
    .urgent-grid { display: grid; grid-template-columns: 360px 1fr; gap: 1rem; }
    .call-item { border: 1px solid rgba(165,148,249,0.2); border-radius: 0.9rem; padding: 0.8rem; background:#fff; }
    .call-item + .call-item { margin-top: 0.7rem; }
    .status-chip { font-size: 0.72rem; padding: 0.2rem 0.55rem; border-radius: 999px; font-weight: 700; }
    .status-waiting { background:#fef3c7; color:#92400e; }
    .status-joined { background:#d8cdfc; color:#5b4b9b; }
    .jitsi-frame { width: 100%; height: 72vh; border: 0; border-radius: 1rem; background:#0f172a; }
    @media (max-width: 991px) { .urgent-grid { grid-template-columns: 1fr; } .jitsi-frame { height: 55vh; } }
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
                    <iframe id="jitsiFrame" class="jitsi-frame" allow="camera; microphone; fullscreen; display-capture" src="about:blank"></iframe>
                    <div class="small text-muted mt-2">Join a call from the left panel. Camera and mic permissions should be allowed in the browser.</div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const callListEl = document.getElementById('callList');
    const callCountEl = document.getElementById('callCount');
    const jitsiFrame = document.getElementById('jitsiFrame');
    let lastIds = new Set();

    function roomToJitsiUrl(room) {
        const safeRoom = encodeURIComponent(room || 'sinag-urgent-room');
        return `https://meet.jit.si/${safeRoom}#config.prejoinPageEnabled=false`;
    }

    async function joinCall(callId) {
        const res = await fetch(`<?php echo e(url('/admin/urgent-calls')); ?>/${callId}/join`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        });
        const data = await res.json();
        if (data && data.room) {
            jitsiFrame.src = roomToJitsiUrl(data.room);
        }
    }

    function renderCalls(calls) {
        callCountEl.textContent = `${calls.length} active`;

        const currentIds = new Set(calls.map(c => c.id));
        if (lastIds.size && calls.some(c => !lastIds.has(c.id))) {
            alert('New urgent call received.');
        }
        lastIds = currentIds;

        if (!calls.length) {
            callListEl.innerHTML = '<div class="text-muted small">No urgent calls right now.</div>';
            return;
        }

        callListEl.innerHTML = calls.map(call => `
            <div class="call-item">
                <div class="d-flex justify-content-between align-items-start gap-2">
                    <div>
                        <div class="fw-bold">${call.student_name || 'Student'}</div>
                        <div class="small text-muted">Room: ${call.room || '-'}</div>
                        <div class="small text-muted">${call.created_at || ''}</div>
                    </div>
                    <span class="status-chip ${call.status === 'joined' ? 'status-joined' : 'status-waiting'}">${call.status || 'waiting'}</span>
                </div>
                <button class="btn btn-sm mt-2" style="background:#A594F9; color:#fff;" onclick="joinCall('${call.id}')">
                    <i class="bi bi-camera-video-fill me-1"></i>Join Call
                </button>
            </div>
        `).join('');
    }

    async function loadFeed() {
        try {
            const res = await fetch("<?php echo e(route('admin.urgent.calls.feed')); ?>", { headers: { 'Accept': 'application/json' } });
            const data = await res.json();
            renderCalls(data.calls || []);
        } catch (e) {
            console.error(e);
        }
    }

    loadFeed();
    setInterval(loadFeed, 5000);
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ANGELINE AMBROSIO\sinag\resources\views/admin/urgent_calls.blade.php ENDPATH**/ ?>