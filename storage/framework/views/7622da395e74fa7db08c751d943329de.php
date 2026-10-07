

<?php
    use Illuminate\Support\Facades\Auth;
?>

<?php $__env->startSection('content'); ?>
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@600;700;800&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">

<style>
:root {
    --violet: #7C3AED;
    --violet-light: #A78BFA;
    --violet-pale: #EDE9FE;
    --gold: #F59E0B;
    --surface: #F5F3FF;
    --card: #FFFFFF;
    --text: #1E1B2E;
    --muted: #6B7280;
    --border: rgba(124, 58, 237, 0.1);
    --radius: 20px;
    --shadow: 0 4px 24px rgba(124, 58, 237, 0.10);
}

* { box-sizing: border-box; }

body {
    background: var(--surface);
    font-family: 'DM Sans', sans-serif;
}

.msg-page {
    min-height: calc(100vh - 60px);
    background: var(--surface);
    padding: 1.75rem 1.5rem;
}

/* ── TWO-COLUMN LAYOUT ────────────────── */
.msg-layout {
    max-width: 1140px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: 1fr 340px;
    gap: 1.25rem;
    align-items: start;
}

@media (max-width: 820px) {
    .msg-page {
        padding: 1rem;
    }

    .msg-layout {
        grid-template-columns: 1fr;
    }

    .msg-chat-card {
        height: min(680px, calc(100dvh - 9rem));
        min-height: 500px;
    }

    .msg-right {
        order: -1;
    }
}

/* ── CHAT CARD ────────────────────────── */
.msg-chat-card {
    background: var(--card);
    border-radius: var(--radius);
    border: 1px solid var(--border);
    box-shadow: var(--shadow);
    overflow: hidden;
    display: flex;
    flex-direction: column;
    height: 680px;
}

/* Chat header */
.msg-chat-header {
    background: linear-gradient(135deg, #5B21B6 0%, #7C3AED 50%, #A78BFA 100%);
    padding: 1.1rem 1.4rem;
    display: flex;
    align-items: center;
    gap: 0.85rem;
    flex-shrink: 0;
}

.msg-chat-header-avatar {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid rgba(255,255,255,0.6);
}

.msg-chat-header-info {
    flex: 1;
}

.msg-chat-header-name {
    font-family: 'Sora', sans-serif;
    font-size: 0.95rem;
    font-weight: 700;
    color: #fff;
    line-height: 1.2;
}

.msg-chat-header-status {
    display: flex;
    align-items: center;
    gap: 0.35rem;
    font-size: 0.72rem;
    color: rgba(255,255,255,0.8);
    margin-top: 0.15rem;
}

.msg-online-dot {
    width: 7px;
    height: 7px;
    background: #4ADE80;
    border-radius: 50%;
    box-shadow: 0 0 0 2px rgba(74,222,128,0.3);
}

/* Thread */
.msg-thread {
    flex: 1;
    overflow-y: auto;
    padding: 1.25rem 1.4rem;
    display: flex;
    flex-direction: column;
    gap: 1.1rem;
    background: #FAFAFA;
}

.msg-thread::-webkit-scrollbar { width: 5px; }
.msg-thread::-webkit-scrollbar-thumb {
    background: #D8B4FE;
    border-radius: 10px;
}

/* Empty state */
.msg-empty {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 0.65rem;
    color: var(--muted);
    text-align: center;
    padding: 2rem;
}

.msg-empty svg {
    width: 48px;
    height: 48px;
    opacity: 0.25;
}

.msg-empty p { font-size: 0.875rem; }

/* Message groups */
.msg-group {
    display: flex;
    gap: 0.65rem;
    animation: msgIn 0.25s ease-out;
}

@keyframes msgIn {
    from { opacity: 0; transform: translateY(8px); }
    to   { opacity: 1; transform: translateY(0); }
}

.msg-group.user { justify-content: flex-end; }
.msg-group.admin { align-items: flex-end; }

.msg-admin-avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    object-fit: cover;
    flex-shrink: 0;
    border: 2px solid #E9D5FF;
}

.msg-bubble-wrap { display: flex; flex-direction: column; max-width: 72%; }
.msg-group.user .msg-bubble-wrap { align-items: flex-end; }

.msg-bubble {
    padding: 0.75rem 1.05rem;
    border-radius: 16px;
    font-size: 0.9rem;
    line-height: 1.55;
    word-break: break-word;
}

.msg-group.user .msg-bubble {
    background: linear-gradient(135deg, var(--violet) 0%, var(--violet-light) 100%);
    color: #fff;
    border-radius: 16px 4px 16px 16px;
    box-shadow: 0 4px 14px rgba(124,58,237,0.22);
}

.msg-group.admin .msg-bubble {
    background: var(--card);
    color: var(--text);
    border-radius: 4px 16px 16px 16px;
    border: 1px solid #E9D5FF;
}

.msg-sender-name {
    font-size: 0.72rem;
    font-weight: 700;
    color: var(--violet);
    margin-bottom: 0.25rem;
}

.msg-time {
    font-size: 0.68rem;
    color: var(--muted);
    margin-top: 0.3rem;
}

.msg-actions {
    display: flex;
    gap: 0.5rem;
    margin-top: 0.2rem;
}

.msg-action-btn {
    background: none;
    border: none;
    font-size: 0.72rem;
    font-weight: 600;
    cursor: pointer;
    padding: 0;
    font-family: 'DM Sans', sans-serif;
}

.msg-action-btn.edit { color: var(--violet); }
.msg-action-btn.delete { color: #b0a1fa }
.msg-action-btn:hover { opacity: 0.75; }

/* Input area */
.msg-input-area {
    padding: 1rem 1.2rem;
    border-top: 1px solid #F0E7FF;
    background: #FAFAFA;
    display: flex;
    gap: 0.65rem;
    align-items: flex-end;
    flex-shrink: 0;
}

.msg-textarea {
    flex: 1;
    border: 1.5px solid #E9D5FF;
    border-radius: 14px;
    padding: 0.75rem 1rem;
    font-size: 0.88rem;
    font-family: 'DM Sans', sans-serif;
    resize: none;
    max-height: 110px;
    color: var(--text);
    background: var(--card);
    outline: none;
    transition: border-color 0.18s, box-shadow 0.18s;
}

.msg-textarea:focus {
    border-color: var(--violet-light);
    box-shadow: 0 0 0 3px rgba(124,58,237,0.08);
}

.msg-send-btn {
    background: var(--violet);
    color: #fff;
    border: none;
    border-radius: 14px;
    padding: 0.75rem 1.1rem;
    font-family: 'Sora', sans-serif;
    font-size: 0.82rem;
    font-weight: 700;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 0.4rem;
    box-shadow: 0 4px 14px rgba(124,58,237,0.28);
    transition: background 0.18s, transform 0.15s;
    white-space: nowrap;
}

.msg-send-btn:hover { background: #6D28D9; transform: translateY(-1px); }
.msg-send-btn svg { width: 15px; height: 15px; }

/* ── RIGHT COLUMN ─────────────────────── */
.msg-right {
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
}

/* Urgent call card */
.msg-urgent-card {
    background: var(--card);
    border-radius: var(--radius);
    border: 1.5px dashed rgba(239,68,68,0.35);
    box-shadow: 0 4px 20px rgba(239,68,68,0.07);
    padding: 1.5rem 1.25rem;
    text-align: center;
}

.msg-urgent-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    background: #FEF2F2;
    color: #B0a1fa;
    font-size: 0.72rem;
    font-weight: 700;
    padding: 0.3rem 0.85rem;
    border-radius: 999px;
    margin-bottom: 0.85rem;
    letter-spacing: 0.04em;
    text-transform: uppercase;
}

.msg-urgent-badge svg { width: 12px; height: 12px; }

.msg-urgent-title {
    font-family: 'Sora', sans-serif;
    font-size: 1.05rem;
    font-weight: 800;
    color: var(--text);
    margin-bottom: 0.5rem;
}

.msg-urgent-desc {
    font-size: 0.82rem;
    color: var(--muted);
    line-height: 1.55;
    margin-bottom: 1.1rem;
}

.msg-urgent-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.55rem;
    width: 100%;
    justify-content: center;
    background: linear-gradient(135deg, #b0a1fa, #b0a1fa);
    color: #fff;
    border: none;
    border-radius: 14px;
    padding: 0.85rem 1.25rem;
    font-family: 'Sora', sans-serif;
    font-size: 0.9rem;
    font-weight: 700;
    cursor: pointer;
    box-shadow: 0 6px 20px rgba(168, 152, 247, 0.983);
    transition: transform 0.15s, box-shadow 0.15s;
    letter-spacing: 0.02em;
}

.msg-urgent-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(168, 152, 247, 0.969);
}

.msg-urgent-btn svg { width: 18px; height: 18px; }

/* Info card */
.msg-info-card {
    background: var(--card);
    border-radius: var(--radius);
    border: 1px solid var(--border);
    box-shadow: var(--shadow);
    padding: 1.25rem;
}

.msg-info-title {
    font-family: 'Sora', sans-serif;
    font-size: 0.75rem;
    font-weight: 700;
    color: var(--muted);
    text-transform: uppercase;
    letter-spacing: 0.08em;
    margin-bottom: 1rem;
}

.msg-info-row {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.6rem 0;
    border-bottom: 1px solid var(--border);
    font-size: 0.82rem;
    color: var(--text);
}

.msg-info-row:last-child { border-bottom: none; }

.msg-info-icon {
    width: 30px;
    height: 30px;
    border-radius: 8px;
    background: var(--violet-pale);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.msg-info-icon svg { width: 14px; height: 14px; color: var(--violet); }

/* Edit textarea inside bubble */
.msg-edit-input {
    width: 100%;
    border: 1.5px solid var(--violet-light);
    border-radius: 10px;
    padding: 0.55rem 0.75rem;
    font-size: 0.88rem;
    font-family: 'DM Sans', sans-serif;
    resize: none;
    outline: none;
    background: #FAFAFA;
    color: var(--text);
}

.msg-edit-btns {
    display: flex;
    gap: 0.4rem;
    justify-content: flex-end;
    margin-top: 0.45rem;
}

.msg-edit-save {
    background: var(--violet);
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: 0.35rem 0.75rem;
    font-size: 0.78rem;
    font-weight: 600;
    cursor: pointer;
    font-family: 'DM Sans', sans-serif;
}

.msg-edit-cancel {
    background: #F3F4F6;
    color: var(--muted);
    border: none;
    border-radius: 8px;
    padding: 0.35rem 0.75rem;
    font-size: 0.78rem;
    font-weight: 600;
    cursor: pointer;
    font-family: 'DM Sans', sans-serif;
}
</style>

<div class="msg-page">
    <div class="msg-layout">

        
        <div class="msg-chat-card">

            
            <div class="msg-chat-header">
                <img src="<?php echo e(asset('images/gadlogo.png')); ?>" alt="GAD" class="msg-chat-header-avatar">
                <div class="msg-chat-header-info">
                    <div class="msg-chat-header-name">GAD Office</div>
                    <div class="msg-chat-header-status">
                        <span class="msg-online-dot"></span>
                        Gender and Development Office
                    </div>
                </div>
            </div>

            
            <div class="msg-thread" id="messagingThread">
                <?php if($messages->isEmpty()): ?>
                    <div class="msg-empty">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                        </svg>
                        <p>No messages yet. Start a conversation with the GAD office!</p>
                    </div>
                <?php else: ?>
                    <?php $__currentLoopData = $messages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $message): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="msg-group <?php echo e($message->sender_id === Auth::id() ? 'user' : 'admin'); ?>"
                             data-message-id="<?php echo e($message->id); ?>">

                            <?php if($message->sender_id !== Auth::id()): ?>
                                <img src="<?php echo e(asset('images/gadlogo.png')); ?>" alt="GAD" class="msg-admin-avatar">
                            <?php endif; ?>

                            <div class="msg-bubble-wrap">
                                <?php if($message->sender_id !== Auth::id()): ?>
                                    <div class="msg-sender-name">GAD Office</div>
                                <?php endif; ?>

                                <div class="msg-bubble" id="bubble-<?php echo e($message->id); ?>">
                                    <span class="message-text" id="message-text-<?php echo e($message->id); ?>"><?php echo e($message->message); ?></span>
                                </div>

                                <div style="display:flex; align-items:center; gap:0.5rem; flex-wrap:wrap;">
                                    <div class="msg-time"><?php echo e($message->created_at->format('M d, g:i A')); ?></div>
                                    <?php if($message->sender_id === Auth::id()): ?>
                                        <button class="msg-action-btn edit" onclick="startEdit(<?php echo e($message->id); ?>)">Edit</button>
                                        <button class="msg-action-btn delete" onclick="confirmDelete(<?php echo e($message->id); ?>)">Delete</button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>
            </div>

            
            <div class="msg-input-area">
                <textarea class="msg-textarea" id="messageInput"
                          placeholder="Type your message…" rows="2"></textarea>
                <button class="msg-send-btn" type="button" onclick="sendMessage()">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <line x1="22" y1="2" x2="11" y2="13"/>
                        <polygon points="22 2 15 22 11 13 2 9 22 2"/>
                    </svg>
                    Send
                </button>
            </div>
        </div>

        
        <div class="msg-right">

            
            <div class="msg-urgent-card">
                <div class="msg-urgent-badge">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 13.5"/>
                        <path d="M1 1l22 22"/>
                    </svg>
                    Urgent Support
                </div>
                <div class="msg-urgent-title">Need Immediate Help?</div>
                <p class="msg-urgent-desc">
                    Connect directly with our GAD office staff via live video call for urgent concerns.
                </p>
                <button class="msg-urgent-btn" type="button" id="urgentCallBtn">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polygon points="23 7 16 12 23 17 23 7"/>
                        <rect x="1" y="5" width="15" height="14" rx="2" ry="2"/>
                    </svg>
                    Start Urgent Video Call
                </button>
            </div>

            
            <div class="msg-info-card">
                <div class="msg-info-title">Office Info</div>

                <div class="msg-info-row">
                    <div class="msg-info-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <polyline points="12 6 12 12 16 14"/>
                        </svg>
                    </div>
                    Mon – Fri, 8:00 AM – 5:00 PM
                </div>

                <div class="msg-info-row">
                    <div class="msg-info-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                            <circle cx="12" cy="10" r="3"/>
                        </svg>
                    </div>
                    Admin Building, Room 105
                </div>

                <div class="msg-info-row">
                    <div class="msg-info-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                            <polyline points="22,6 12,13 2,6"/>
                        </svg>
                    </div>
                    gad@psu.edu.ph
                </div>
            </div>

        </div>
    </div>
</div>

<script>
const CSRF = '<?php echo e(csrf_token()); ?>';

function escapeHtml(text) {
    const map = { '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;' };
    return text.replace(/[&<>"']/g, m => map[m]);
}

function sendMessage() {
    const input = document.getElementById('messageInput');
    const message = input.value.trim();
    if (!message) return;

    const btn = document.querySelector('.msg-send-btn');
    btn.disabled = true;
    input.disabled = true;

    fetch("<?php echo e(route('student.messages.store')); ?>", {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({ message })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const thread = document.getElementById('messagingThread');
            const empty = thread.querySelector('.msg-empty');
            if (empty) empty.remove();

            const el = document.createElement('div');
            el.className = 'msg-group user';
            el.innerHTML = `
                <div class="msg-bubble-wrap">
                    <div class="msg-bubble">${escapeHtml(message)}</div>
                    <div class="msg-time">Just now</div>
                </div>`;
            thread.appendChild(el);
            input.value = '';
            thread.scrollTop = thread.scrollHeight;
        }
    })
    .catch(() => alert('Failed to send message. Please try again.'))
    .finally(() => { btn.disabled = false; input.disabled = false; input.focus(); });
}

document.getElementById('messageInput').addEventListener('keydown', function(e) {
    if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendMessage(); }
});

document.getElementById('urgentCallBtn').addEventListener('click', function() {
    window.location.href = "<?php echo e(route('student.urgent')); ?>";
});

window.addEventListener('load', function() {
    const t = document.getElementById('messagingThread');
    t.scrollTop = t.scrollHeight;
});

function startEdit(id) {
    const textEl = document.getElementById('message-text-' + id);
    const bubble = document.getElementById('bubble-' + id);
    if (!textEl || !bubble) return;
    const current = textEl.textContent.trim();
    bubble.dataset.original = current;
    bubble.innerHTML = `
        <textarea class="msg-edit-input" id="edit-input-${id}" rows="3">${escapeHtml(current)}</textarea>
        <div class="msg-edit-btns">
            <button class="msg-edit-cancel" onclick="cancelEdit(${id})">Cancel</button>
            <button class="msg-edit-save" onclick="saveEdit(${id})">Save</button>
        </div>`;
    document.getElementById('edit-input-' + id).focus();
}

function cancelEdit(id) {
    const bubble = document.getElementById('bubble-' + id);
    const original = bubble?.dataset?.original || '';
    bubble.innerHTML = `<span class="message-text" id="message-text-${id}">${escapeHtml(original)}</span>`;
}

function saveEdit(id) {
    const input = document.getElementById('edit-input-' + id);
    if (!input) return;
    const newText = input.value.trim();
    if (!newText) return alert('Message cannot be empty.');

    fetch(`/student/messages/${id}/update`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({ message: newText })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const bubble = document.getElementById('bubble-' + id);
            bubble.innerHTML = `<span class="message-text" id="message-text-${id}">${escapeHtml(newText)}</span>`;
        } else alert('Failed to update message');
    })
    .catch(() => alert('Failed to update message'));
}

function confirmDelete(id) {
    if (!confirm('Delete this message? This cannot be undone.')) return;
    fetch(`/student/messages/${id}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': CSRF }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const el = document.querySelector(`[data-message-id="${id}"]`);
            if (el) el.remove();
        } else alert('Failed to delete message');
    })
    .catch(() => alert('Failed to delete message'));
}
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ANGELINE AMBROSIO\sinag\resources\views/student/messaging.blade.php ENDPATH**/ ?>