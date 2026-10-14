@extends('layouts.app')

@section('content')
<style>
    .urgent-page {
        padding: clamp(.75rem, 3vw, 2rem);
        min-height: calc(100dvh - 60px);
        background: linear-gradient(135deg, #f8fafc 0%, #fbf7ff 100%);
    }

    .urgent-wrapper {
        max-width: 1100px;
        margin: 0 auto;
    }

    .video-stage {
        background: #000;
        border-radius: 1rem;
        height: min(72dvh, 760px);
        min-height: min(440px, 55dvh);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        position: relative;
        overflow: hidden;
    }
    #jitsiContainer { position: absolute; inset: 0; z-index: 2; margin: 0 !important; }
    #jitsiFrame { width: 100%; height: 100%; border: 0; border-radius: 1rem; background: #0f172a; }

    @media (max-width: 575.98px) {
        .urgent-page { padding: .75rem; }
        .video-stage { height: min(68dvh, 640px); min-height: 260px; border-radius: .75rem; }
        .call-controls { gap: 0.6rem; flex-wrap: wrap; }
        .call-btn { flex: 1 1 120px; padding: 0.7rem .75rem; }
    }

    @media (max-height: 500px) {
        .video-stage { height: 60dvh; min-height: 200px; }
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
            <p class="mb-0" style="color:#6b7280;">Start a live audio and video call with the GAD Office.</p>
        </div>

        <div class="video-stage">
            <div id="videoFallback" class="video-fake" style="position:absolute; inset:0; display:flex; align-items:center; justify-content:center;">
                <div>
                    <img src="{{ asset('images/gadlogo.png') }}" alt="GAD" style="width:86px;height:86px;border-radius:50%;object-fit:cover;margin-bottom:0.8rem;display:block;margin-left:auto;margin-right:auto;">
                    <div style="text-align:center;">Press Join Call to connect. Allow camera and microphone access in the call window.</div>
                </div>
            </div>
            <div id="jitsiContainer" style="display:none;">
                <iframe id="jitsiFrame" title="GAD Office audio and video call" allow="camera; microphone; fullscreen; display-capture; autoplay" referrerpolicy="strict-origin-when-cross-origin"></iframe>
            </div>
        </div>

        <div class="call-controls">
            <button class="call-btn join" id="joinCall"><i class="bi bi-camera-video-fill"></i> Join Call</button>
            <button class="call-btn leave" id="leaveCall"><i class="bi bi-telephone-fill"></i> Leave</button>
        </div>

        <div class="instructions" id="callStatus" role="status" aria-live="polite">Your browser may require HTTPS or localhost for camera and microphone access.</div>
    </div>
</div>

<script>
    let activeCallId = null;
    let callStatusTimer = null;

    function roomToJitsiUrl(room) {
        const safeRoom = encodeURIComponent(room || 'sinag-urgent-room');
        return `https://meet.jit.si/${safeRoom}#config.prejoinPageEnabled=true`;
    }

    function resetCall(message) {
        window.clearInterval(callStatusTimer);
        callStatusTimer = null;
        activeCallId = null;
        document.getElementById('jitsiFrame').src = 'about:blank';
        document.getElementById('jitsiContainer').style.display = 'none';
        document.getElementById('videoFallback').style.display = 'flex';
        const joinButton = document.getElementById('joinCall');
        joinButton.disabled = false;
        joinButton.innerHTML = '<i class="bi bi-camera-video-fill"></i> Join Call';
        const leaveButton = document.getElementById('leaveCall');
        leaveButton.disabled = false;
        document.getElementById('callStatus').textContent = message;
    }

    async function checkCallStatus() {
        if (!activeCallId) return;
        try {
            const response = await fetch(`{{ url('/student/urgent') }}/${encodeURIComponent(activeCallId)}/status`, {
                headers: { 'Accept': 'application/json' }
            });
            if (!response.ok) throw new Error(`Unable to check call status (${response.status}).`);
            const data = await response.json();
            if (data.status === 'ended') resetCall('The GAD Office ended the call. You can start another call if you still need help.');
        } catch (error) {
            console.error('Unable to check urgent call status:', error);
        }
    }

    document.getElementById('joinCall').addEventListener('click', async function() {
        const button = this;
        button.disabled = true;
        document.getElementById('callStatus').textContent = 'Notifying the GAD Office and preparing your call…';
        try {
            const response = await fetch("{{ route('student.urgent.notify') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
            });
            if (!response.ok) throw new Error(`Call setup failed (${response.status}). Please try again.`);
            const data = await response.json();
            if (!data.room) throw new Error('The GAD Office call room could not be created.');
            activeCallId = data.call_id;
            document.getElementById('videoFallback').style.display = 'none';
            document.getElementById('jitsiFrame').src = roomToJitsiUrl(data.room);
            document.getElementById('jitsiContainer').style.display = 'block';
            document.getElementById('callStatus').textContent = 'The GAD Office has been notified. Allow microphone and camera access in the call window.';
            button.textContent = 'Call started';
            callStatusTimer = window.setInterval(checkCallStatus, 5000);
        } catch (error) {
            document.getElementById('callStatus').textContent = error.message;
            button.disabled = false;
        }
    });

    document.getElementById('leaveCall').addEventListener('click', async function() {
        if (activeCallId) {
            this.disabled = true;
            try {
                const response = await fetch(`{{ url('/student/urgent') }}/${encodeURIComponent(activeCallId)}/end`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                });
                if (!response.ok) throw new Error(`Unable to end call (${response.status}).`);
            } catch (error) {
                document.getElementById('callStatus').textContent = `${error.message} Please retry leaving the call.`;
                this.disabled = false;
                return;
            }
        }
        resetCall('You left the call. Join again whenever you need GAD support.');
    });
</script>

@endsection