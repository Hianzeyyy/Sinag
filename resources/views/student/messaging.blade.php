@extends('layouts.app')

@php
    use Illuminate\Support\Facades\Auth;
@endphp

@section('content')
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
    min-height: 0;
    background: var(--surface);
    padding: 1.75rem 1.5rem;
    padding-bottom: max(1.75rem, env(safe-area-inset-bottom));
    overflow-x: clip;
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

@media (min-width: 821px) and (max-width: 1199.98px) {
    .msg-page {
        padding: 1rem;
    }

    .msg-layout {
        grid-template-columns: minmax(0, 1fr) 260px;
        gap: .85rem;
    }

    .msg-bubble-wrap {
        max-width: 82%;
    }
}

@media (max-width: 820px) {
    .msg-page {
        padding: 1rem;
    }

    .msg-layout {
        grid-template-columns: 1fr;
    }

    .msg-right {
        order: -1;
    }

    .msg-input-area {
        flex-wrap: wrap;
        padding: .75rem;
    }

    .msg-textarea {
        min-width: 0;
        flex-basis: 100%;
    }

    .msg-send-btn {
        margin-left: auto;
    }

    .msg-urgent-header-btn { display: inline-flex; }
    .msg-urgent-card { display: none; }
}

/* ── CHAT CARD ────────────────────────── */
.msg-chat-card {
    background: var(--card);
    border-radius: var(--radius);
    border: 1px solid var(--border);
    box-shadow: var(--shadow);
    overflow: visible;
    display: flex;
    flex-direction: column;
    height: auto;
}

/* Chat header */
.msg-chat-header {
    background: linear-gradient(135deg, #5B21B6 0%, #7C3AED 50%, #A78BFA 100%);
    padding: 1.1rem 1.4rem;
    display: flex;
    align-items: center;
    gap: 0.85rem;
    flex-shrink: 0;
    min-width: 0;
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
    min-width: 0;
}

.msg-chat-header-name {
    font-family: 'Sora', sans-serif;
    font-size: 0.95rem;
    font-weight: 700;
    color: #fff;
    line-height: 1.2;
    overflow-wrap: anywhere;
}

.msg-chat-header-status {
    display: flex;
    align-items: center;
    gap: 0.35rem;
    font-size: 0.72rem;
    color: rgba(255,255,255,0.8);
    margin-top: 0.15rem;
}

.msg-urgent-header-btn {
    display: inline-flex;
    width: 2.65rem;
    height: 2.65rem;
    align-items: center;
    justify-content: center;
    flex: 0 0 auto;
    border: 1px solid rgba(255,255,255,0.35);
    border-radius: 50%;
    background: rgba(255,255,255,0.16);
    color: #fff;
    font-size: 1.1rem;
}

.msg-urgent-header-btn:hover,
.msg-urgent-header-btn:focus-visible {
    background: #fff;
    color: #6d55d9;
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
    flex: 0 0 auto;
    overflow: visible;
    padding: 1.25rem 1.4rem;
    display: flex;
    flex-direction: column;
    gap: 1.1rem;
    background: #FAFAFA;
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
.msg-bubble-wrap { position: relative; }
.msg-group.user .msg-bubble-wrap { align-items: flex-end; }
.msg-edited-control { position: absolute; top: -.8rem; right: .15rem; margin: 0; }
.msg-group.admin .msg-edited-control { right: auto; left: .15rem; }

.msg-bubble {
    padding: 0.75rem 1.05rem;
    border-radius: 16px;
    font-size: 0.9rem;
    line-height: 1.55;
    word-break: break-word;
}
.msg-bubble.msg-deleted {
    color: #5b4b9b !important;
    background: #f3efff !important;
    border: 1px dashed #c4b5fd;
    box-shadow: none !important;
    font-style: italic;
}
.msg-original {
    display: none;
    margin-top: .35rem;
    padding: .45rem .6rem;
    border-left: 2px solid var(--violet-light);
    border-radius: .35rem;
    color: #5b4b9b;
    background: var(--violet-pale);
    font-size: .76rem;
    white-space: pre-wrap;
}
.msg-original.is-visible { display: block; }

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
.msg-action-btn.delete { color: #e85d9e; }
.msg-action-btn:hover { opacity: 0.75; }
.msg-message-meta { display: flex; align-items: center; gap: .55rem; flex-wrap: wrap; }
.msg-action-btn { margin-inline: .08rem; }

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

.msg-info-link {
    color: var(--text);
    text-decoration: none;
    font-weight: 500;
    transition: color 0.15s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
}

.msg-info-link:hover {
    color: var(--violet);
    text-decoration: underline;
}

.msg-call-badge {
    display: inline-flex;
    align-items: center;
    font-size: 0.68rem;
    font-weight: 700;
    background: #EDE9FE;
    color: var(--violet);
    padding: 0.12rem 0.45rem;
    border-radius: 999px;
    letter-spacing: 0.02em;
    transition: background 0.15s ease, color 0.15s ease;
}

.msg-info-link:hover .msg-call-badge {
    background: var(--violet);
    color: #fff;
}


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

        {{-- ── LEFT: CHAT ─────────────────────────── --}}
        <div class="msg-chat-card">

            {{-- Chat header --}}
            <div class="msg-chat-header">
                <img src="{{ asset('images/gadlogo.png') }}" alt="GAD" class="msg-chat-header-avatar">
                <div class="msg-chat-header-info">
                    <div class="msg-chat-header-name">GAD Office</div>
                    <div class="msg-chat-header-status">
                        <span class="msg-online-dot"></span>
                        Gender and Development Office
                    </div>
                </div>
                <button class="msg-urgent-header-btn" type="button" data-urgent-call aria-label="Start urgent audio or video call" title="Call GAD Office">
                    <i class="bi bi-telephone-fill"></i>
                </button>
            </div>

            {{-- Messages thread --}}
            <div class="msg-thread" id="messagingThread">
                @if($messages->isEmpty())
                    <div class="msg-empty">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                        </svg>
                        <p>No messages yet. Start a conversation with the GAD office!</p>
                    </div>
                @else
                    @foreach($messages as $message)
                        <div class="msg-group {{ $message->sender_id === Auth::id() ? 'user' : 'admin' }}"
                             data-message-id="{{ $message->id }}">

                            @if($message->sender_id !== Auth::id())
                                <img src="{{ asset('images/gadlogo.png') }}" alt="GAD" class="msg-admin-avatar">
                            @endif

                            <div class="msg-bubble-wrap">
                                @if($message->sender_id !== Auth::id())
                                    <div class="msg-sender-name">GAD Office</div>
                                @endif

                                <div class="msg-bubble {{ $message->deleted_at ? 'msg-deleted' : '' }}" id="bubble-{{ $message->id }}">
                                    <span class="message-text" id="message-text-{{ $message->id }}">{{ $message->deleted_at ? 'This message was deleted.' : $message->message }}</span>
                                </div>

                                @if($message->original_message && !$message->deleted_at)
                                    <button class="msg-action-btn edit msg-edited-control" type="button" onclick="toggleStudentOriginal({{ $message->id }})">Edited</button>
                                @endif
                                <div class="msg-message-meta" style="display:flex; align-items:center; gap:0.5rem; flex-wrap:wrap;">
                                    <div class="msg-time">{{ $message->created_at->format('M d, g:i A') }}</div>
                                    @if($message->sender_id === Auth::id() && !$message->deleted_at)
                                        <button class="msg-action-btn edit" onclick="startEdit({{ $message->id }})">Edit</button>
                                        <button class="msg-action-btn delete" onclick="confirmDelete({{ $message->id }})">Delete</button>
                                    @endif
                                </div>
                                @if($message->original_message && !$message->deleted_at)
                                    <div class="msg-original" id="original-{{ $message->id }}"><strong>Original message:</strong> {{ $message->original_message }}</div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

            {{-- Input area --}}
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

        {{-- ── RIGHT COLUMN ────────────────────────── --}}
        <div class="msg-right">

            {{-- Office Info --}}
            <div class="msg-info-card">
                <div class="msg-info-title">Office Info</div>

                <div class="msg-info-row">
                    <div class="msg-info-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <polyline points="12 6 12 12 16 14"/>
                        </svg>
                    </div>
                    {{ $officeInfo['office_hours'] ?? 'Mon – Fri, 8:00 AM – 5:00 PM' }}
                </div>

                <div class="msg-info-row">
                    <div class="msg-info-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                            <circle cx="12" cy="10" r="3"/>
                        </svg>
                    </div>
                    {{ $officeInfo['office_location'] ?? 'Admin Building, Room 105' }}
                </div>

                <div class="msg-info-row">
                    <div class="msg-info-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                            <polyline points="22,6 12,13 2,6"/>
                        </svg>
                    </div>
                    <a href="mailto:{{ $officeInfo['office_email'] ?? 'gad@psu.edu.ph' }}" class="msg-info-link">{{ $officeInfo['office_email'] ?? 'gad@psu.edu.ph' }}</a>
                </div>

                @php
                    $gadPhone = $officeInfo['phone_number'] ?? ($admin->phone_number ?? '0919-777-7377');
                @endphp
                <div class="msg-info-row">
                    <div class="msg-info-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                        </svg>
                    </div>
                    <a href="tel:{{ $gadPhone }}" class="msg-info-link" title="Click to call GAD Office">
                        <span>{{ $gadPhone }}</span>
                        <span class="msg-call-badge"><i class="bi bi-telephone-outbound me-1"></i>Call</span>
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
const CSRF = '{{ csrf_token() }}';
const currentUserId = {{ Auth::id() }};

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

    fetch("{{ route('student.messages.store') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': CSRF
        },
        body: JSON.stringify({ message })
    })
    .then(async r => {
        if (!r.ok) {
            if ([401, 403, 419].includes(r.status)) {
                throw new Error('Your session has expired. Please sign in again.');
            }
            let message = 'Failed to send message. Please try again.';
            try {
                const payload = await r.json();
                message = payload.error || payload.message || message;
            } catch (error) {}
            throw new Error(message);
        }
        return r.json();
    })
    .then(data => {
        if (data.success) {
            const thread = document.getElementById('messagingThread');
            const empty = thread.querySelector('.msg-empty');
            if (empty) empty.remove();

            const el = document.createElement('div');
            el.className = 'msg-group user';
            el.dataset.messageId = data.message.id;
            el.innerHTML = `
                <div class="msg-bubble-wrap">
                    <div class="msg-bubble" id="bubble-${data.message.id}"><span class="message-text" id="message-text-${data.message.id}">${escapeHtml(message)}</span></div>
                    <div class="msg-message-meta"><div class="msg-time">${escapeHtml(data.message.created_at || 'Just now')}</div>
                    <button class="msg-action-btn edit" onclick="startEdit(${data.message.id})">Edit</button>
                    <button class="msg-action-btn delete" onclick="confirmDelete(${data.message.id})">Delete</button></div>
                </div>`;
            thread.appendChild(el);
            input.value = '';
        }

    })
    .catch(error => alert(error.message || 'Failed to send message. Please try again.'))
    .finally(() => { btn.disabled = false; input.disabled = false; input.focus(); });
}

function studentMessageMarkup(message) {
    const id = Number(message.id);
    const own = Number(message.sender_id) === Number(currentUserId);
    const deleted = Boolean(message.deleted_at);
    const content = deleted ? 'This message was deleted.' : message.message;
    const actions = deleted ? '' : `${own ? `<button class="msg-action-btn edit" type="button" onclick="startEdit(${id})">Edit</button><button class="msg-action-btn delete" type="button" onclick="confirmDelete(${id})">Delete</button>` : ''}`;
    const editedControl = message.original_message && !deleted
        ? `<button class="msg-action-btn edit msg-edited-control" type="button" onclick="toggleStudentOriginal(${id})">Edited</button>`
        : '';
    const original = message.original_message && !deleted
        ? `<div class="msg-original" id="original-${id}"><strong>Original message:</strong> ${escapeHtml(message.original_message)}</div>`
        : '';
    const state = JSON.stringify([message.message, message.original_message, message.deleted_at]);
    return `<div class="msg-group ${own ? 'user' : 'admin'}" data-message-id="${id}" data-message-state="${escapeHtml(state)}">
        ${own ? '' : '<img src="{{ asset('images/gadlogo.png') }}" alt="GAD" class="msg-admin-avatar">'}
        <div class="msg-bubble-wrap">${own ? '' : '<div class="msg-sender-name">GAD Office</div>'}<div class="msg-bubble${deleted ? ' msg-deleted' : ''}" id="bubble-${id}"><span class="message-text" id="message-text-${id}">${escapeHtml(content)}</span></div>${editedControl}
        <div class="msg-message-meta"><div class="msg-time">${escapeHtml(message.created_at || '')}</div>${actions}</div>${original}</div></div>`;
}

async function refreshMessages() {
    const thread = document.getElementById('messagingThread');
    try {
        const response = await fetch(`{{ route('student.messages.feed') }}?after_id=0`, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        });
        if (!response.ok) throw new Error(`Message refresh failed (${response.status}).`);
        const data = await response.json();
        for (const message of data.messages) {
            const existing = thread.querySelector(`[data-message-id="${message.id}"]`);
            if (existing?.querySelector('textarea')) continue;
            const state = JSON.stringify([message.message, message.original_message, message.deleted_at]);
            if (existing?.dataset.messageState === state) continue;
            thread.querySelector('.msg-empty')?.remove();
            if (existing) existing.outerHTML = studentMessageMarkup(message);
            else thread.insertAdjacentHTML('beforeend', studentMessageMarkup(message));
        }
    } catch (error) {
        console.error('Unable to refresh GAD messages:', error);
    }
}
setInterval(refreshMessages, 3000);

document.getElementById('messageInput').addEventListener('keydown', function(e) {
    if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendMessage(); }
});

document.querySelectorAll('[data-urgent-call]').forEach(function(button) {
    button.addEventListener('click', function() {
        window.location.href = "{{ route('student.urgent') }}";
    });
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

function toggleStudentOriginal(id) {
    document.getElementById('original-' + id)?.classList.toggle('is-visible');
}

function saveEdit(id) {
    const input = document.getElementById('edit-input-' + id);
    if (!input) return;
    const newText = input.value.trim();
    if (!newText) return alert('Message cannot be empty.');

    fetch(`/student/messages/${id}/update`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': CSRF
        },
        body: JSON.stringify({ message: newText })
    })
    .then(async r => {
        if (!r.ok) {
            if ([401, 403, 419].includes(r.status)) {
                throw new Error('Your session has expired. Please sign in again.');
            }
            let errorMessage = 'Failed to update message.';
            try {
                const payload = await r.json();
                errorMessage = payload.message || payload.error || errorMessage;
            } catch (error) {}
            throw new Error(errorMessage);
        }
        return r.json();
    })
    .then(data => {
        if (data.success) {
            const bubble = document.getElementById('bubble-' + id);
            bubble.classList.remove('msg-deleted');
            bubble.innerHTML = `<span class="message-text" id="message-text-${id}">${escapeHtml(newText)}</span>`;
            const row = bubble.closest('.msg-bubble-wrap');
            let original = document.getElementById('original-' + id);
            if (!original) {
                original = document.createElement('div');
                original.className = 'msg-original';
                original.id = 'original-' + id;
                row.appendChild(original);
            }
            original.innerHTML = `<strong>Original message:</strong> ${escapeHtml(data.message.original_message)}`;
            const wrap = bubble.closest('.msg-bubble-wrap');
            if (!wrap.querySelector('.msg-edited-control')) {
                wrap.insertAdjacentHTML('beforeend', `<button class="msg-action-btn edit msg-edited-control" type="button" onclick="toggleStudentOriginal(${id})">Edited</button>`);
            }
        } else alert('Failed to update message');
    })
    .catch(error => alert(error.message || 'Failed to update message'));
}

function confirmDelete(id) {
    if (!confirm('Delete this message? This cannot be undone.')) return;
    fetch(`/student/messages/${id}`, {
        method: 'DELETE',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': CSRF
        }
    })
    .then(async r => {
        if (!r.ok) {
            if ([401, 403, 419].includes(r.status)) {
                throw new Error('Your session has expired. Please sign in again.');
            }
            let errorMessage = 'Failed to delete message.';
            try {
                const payload = await r.json();
                errorMessage = payload.message || payload.error || errorMessage;
            } catch (error) {}
            throw new Error(errorMessage);
        }
        return r.json();
    })
    .then(data => {
        if (data.success) {
            const row = document.querySelector(`[data-message-id="${id}"]`);
            const bubble = document.getElementById('bubble-' + id);
            if (bubble) {
                bubble.classList.add('msg-deleted');
                bubble.textContent = 'This message was deleted.';
                row?.querySelector('.msg-message-meta')?.querySelectorAll('.msg-action-btn').forEach(button => button.remove());
                document.getElementById('original-' + id)?.remove();
            }
        } else alert('Failed to delete message');
    })
    .catch(error => alert(error.message || 'Failed to delete message'));
}
</script>
@endsection