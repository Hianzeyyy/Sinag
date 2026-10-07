

<?php $__env->startSection('content'); ?>
<style>
    .urgent-page {
        padding: 2rem;
        min-height: calc(100vh - 60px);
        background: linear-gradient(135deg, #f8fafc 0%, #fbf7ff 100%);
    }

    .urgent-wrapper {
        max-width: 1100px;
        margin: 0 auto;
    }

    .video-stage {
        background: #000;
        border-radius: 1rem;
        height: 640px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        position: relative;
        overflow: hidden;
    }

    @media (max-width: 575.98px) {
        .urgent-page { padding: 1rem; }
        .video-stage { height: min(640px, 125vw); min-height: 320px; }
        .call-controls { gap: 0.6rem; flex-wrap: wrap; }
        .call-btn { flex: 1 1 140px; padding: 0.75rem 1rem; }
    }

    .video-fake {
        width: 100%;
        height: 100%;
        background-image: linear-gradient(135deg, rgba(165,148,249,0.12), rgba(250,204,21,0.08));
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        font-weight: 700;
        color: rgba(255,255,255,0.9);
    }

    .call-controls {
        display: flex;
        gap: 1rem;
        margin-top: 1rem;
        justify-content: center;
    }

    .call-btn {
        padding: 0.9rem 1.4rem;
        border-radius: 0.85rem;
        font-weight: 800;
        border: none;
        cursor: pointer;
        box-shadow: 0 8px 26px rgba(0,0,0,0.2);
    }

    .call-btn.join { background: linear-gradient(135deg, #10b981 0%, #06b6d4 100%); color: #fff; }
    .call-btn.leave { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: #fff; }
    .call-btn.mute { background: #f3f4f6; color: #111827; }

    .instructions {
        margin-top: 1.25rem;
        color: #6b7280;
        text-align: center;
    }
</style>

<div class="urgent-page">
    <div class="urgent-wrapper">
        <div class="text-center mb-3">
            <h2 class="fw-bold" style="color:#5b21b6;">Urgent Video Call</h2>
            <p class="mb-0" style="color:#6b7280;">This page will connect you to the GAD office for urgent assistance (placeholder Zoom-like experience).</p>
        </div>

        <div class="video-stage">
            <video id="localVideo" autoplay playsinline style="width:100%; height:100%; object-fit:cover; background:#000; border-radius:1rem;" muted></video>
            <div id="videoFallback" class="video-fake" style="position:absolute; inset:0; display:flex; align-items:center; justify-content:center;">
                <div>
                    <img src="<?php echo e(asset('images/gadlogo.png')); ?>" alt="GAD" style="width:86px;height:86px;border-radius:50%;object-fit:cover;margin-bottom:0.8rem;display:block;margin-left:auto;margin-right:auto;">
                    <div style="text-align:center;">Connecting to GAD Office…</div>
                </div>
            </div>
        </div>

        <div class="call-controls">
            <button class="call-btn join" id="joinCall"><i class="bi bi-camera-video-fill"></i> Join Call</button>
            <button class="call-btn mute" id="toggleMute"><i class="bi bi-mic-mute-fill"></i> Mute</button>
            <button class="call-btn leave" id="leaveCall"><i class="bi bi-telephone-fill"></i> Leave</button>
        </div>

        <div id="jitsiContainer" class="mt-3" style="display:none;">
            <iframe id="jitsiFrame" style="width:100%; height:72vh; border:0; border-radius:1rem; background:#0f172a;" allow="camera; microphone; fullscreen; display-capture"></iframe>
        </div>

        <div class="instructions">Tip: This is a placeholder UI. Integrate with a real WebRTC or Zoom SDK for live calls.</div>
    </div>
</div>

<script>
    let localStream = null;
    let activeRoom = null;

    function roomToJitsiUrl(room) {
        const safeRoom = encodeURIComponent(room || 'sinag-urgent-room');
        return `https://meet.jit.si/${safeRoom}#config.prejoinPageEnabled=false`;
    }

    function setFallbackMessage(message) {
        const fallback = document.getElementById('videoFallback');
        fallback.style.display = 'flex';
        fallback.innerHTML = `
            <div>
                <img src="<?php echo e(asset('images/gadlogo.png')); ?>" alt="GAD" style="width:86px;height:86px;border-radius:50%;object-fit:cover;margin-bottom:0.8rem;display:block;margin-left:auto;margin-right:auto;">
                <div style="text-align:center; max-width:460px; padding:0 1rem;">${message}</div>
            </div>
        `;
    }

    async function startLocalCamera() {
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            setFallbackMessage('Camera API is not available in this browser. Please use latest Chrome/Edge and open via localhost/https.');
            return false;
        }

        try {
            localStream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: 'user' },
                audio: true,
            });
            const localVideo = document.getElementById('localVideo');
            localVideo.srcObject = localStream;
            // hide fallback
            document.getElementById('videoFallback').style.display = 'none';
            return true;
        } catch (err) {
            console.error('Camera access denied or error', err);
            setFallbackMessage('Unable to access camera/mic. Please allow permissions in browser site settings and reload.');
            return false;
        }
    }

    document.getElementById('joinCall').addEventListener('click', async function() {
        const started = await startLocalCamera();
        if (!started) {
            return;
        }

        if (!activeRoom) {
            activeRoom = `sinag-urgent-${Date.now()}-<?php echo e(Auth::id()); ?>`;
        }

        // Show shared room so both student and admin can join same call
        const jitsiFrame = document.getElementById('jitsiFrame');
        jitsiFrame.src = roomToJitsiUrl(activeRoom);
        document.getElementById('jitsiContainer').style.display = 'block';

        // Notify admin (placeholder)
        fetch("<?php echo e(route('student.urgent.notify')); ?>", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ action: 'join', timestamp: Date.now(), room: activeRoom })
        }).then(res => res.json()).then(data => {
            if (data.status === 'notified') {
                alert('Admin has been notified. Waiting for them to join...');
            }
        }).catch(err => {
            console.error(err);
        });
    });

    document.getElementById('leaveCall').addEventListener('click', function() {
        if (localStream) {
            localStream.getTracks().forEach(t => t.stop());
            localStream = null;
        }
        document.getElementById('jitsiFrame').src = 'about:blank';
        window.location.href = '<?php echo e(route('student.messaging')); ?>';
    });

    document.getElementById('toggleMute').addEventListener('click', function() {
        if (!localStream) {
            alert('You are not in a call yet. Press Join Call first.');
            return;
        }
        const audioTracks = localStream.getAudioTracks();
        if (audioTracks.length) {
            const muted = !audioTracks[0].enabled;
            audioTracks.forEach(t => t.enabled = muted);
            alert(muted ? 'Unmuted' : 'Muted');
        }
    });
</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ANGELINE AMBROSIO\sinag\resources\views\student\urgent_call.blade.php ENDPATH**/ ?>