@extends('layouts.app')

@php
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Str;
@endphp

@section('content')
<style>
    .messages-page {
        background: #f4f3ff;
        min-height: 0;
        padding: 1.5rem;
        overflow-x: clip;
    }

    .messages-page .bg-success,
    .messages-page .text-success { background-color: #d8cdfc !important; color: #5b4b9b !important; }
    .messages-page .bg-danger,
    .messages-page .text-danger { background-color: #f8cb12 !important; color: #4a3500 !important; }

    /* Layout */
    .messaging-layout {
        display: grid;
        grid-template-columns: 320px 1fr;
        gap: 1rem;
        height: auto;
    }

    @media (min-width: 821px) and (max-width: 1199.98px) {
        .messages-page {
            padding: 1rem;
        }

        .messaging-layout {
            grid-template-columns: minmax(220px, 28vw) minmax(0, 1fr);
            gap: .75rem;
        }

        .messaging-header {
            padding-inline: 1rem;
        }

        .messaging-thread {
            padding: 1rem;
        }
    }

    @media (max-width: 820px) {
        .messages-page {
            padding: 1rem;
        }

        .messaging-layout {
            grid-template-columns: 1fr;
            height: auto;
            min-height: 0;
        }

        .left-panel {
            max-height: none;
        }

        .messaging-header {
            align-items: flex-start;
            gap: .75rem;
            flex-wrap: wrap;
        }

        .messaging-input-area {
            flex-wrap: wrap;
        }

        .messaging-input-area .message-input,
        .messaging-input-area .send-btn {
            min-width: 0;
        }
    }

    /* Left Panel */
    .left-panel {
        background: white;
        min-width: 0;
        border-radius: 16px;
        border: 1px solid #ede9fe;
        display: flex;
        flex-direction: column;
        overflow: visible;
        box-shadow: 0 2px 8px rgba(124, 111, 224, 0.07);
    }

    .panel-header {
        padding: 1.1rem 1.25rem;
        border-bottom: 1px solid #f3f0ff;
    }

    .panel-title {
        font-size: 15px;
        font-weight: 700;
        color: #1f2937;
        margin: 0;
    }

    .panel-sub {
        font-size: 12px;
        color: #a78bfa;
        margin-top: 2px;
        font-weight: 500;
    }

    .search-bar {
        padding: 0.75rem 1rem;
        border-bottom: 1px solid #f3f0ff;
    }

    .search-input {
        width: 100%;
        background: #f9f7ff;
        border: 1px solid #ede9fe;
        border-radius: 10px;
        padding: 0.45rem 0.85rem;
        font-size: 13px;
        color: #374151;
        outline: none;
    }

    .search-input::placeholder {
        color: #c4b5fd;
    }

    .conversation-list {
        flex: 0 0 auto;
        overflow: visible;
    }

    .conversation-item {
        padding: 0.9rem 1.25rem;
        border-bottom: 1px solid #f9f7ff;
        cursor: pointer;
        transition: background 0.15s;
        display: flex;
        gap: 10px;
        align-items: center;
        text-decoration: none;
        color: inherit;
    }

    .conversation-item:hover {
        background: #faf9ff;
        text-decoration: none;
        color: inherit;
    }

    .conversation-item.active {
        background: #f0ecff;
        border-left: 3px solid #7c6fe0;
        padding-left: calc(1.25rem - 3px);
    }

    .conv-avatar {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: #ede9fe;
        color: #6d28d9;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        font-weight: 700;
        flex-shrink: 0;
    }

    .conv-avatar img,
    .header-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: inherit;
    }

    .conv-info {
        flex: 1;
        min-width: 0;
    }

    .conv-name {
        font-size: 13.5px;
        font-weight: 600;
        color: #111827;
    }

    .conv-preview {
        font-size: 12px;
        color: #9ca3af;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        margin-top: 2px;
    }

    .conv-meta {
        text-align: right;
        flex-shrink: 0;
    }

    .conv-time {
        font-size: 11px;
        color: #c4b5fd;
        font-weight: 500;
    }

    .unread-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: #7c6fe0;
        margin: 4px auto 0;
    }

    /* Right Panel */
    .right-panel {
        background: white;
        border-radius: 16px;
        border: 1px solid #ede9fe;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(124, 111, 224, 0.07);
    }

    .messaging-header {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #f3f0ff;
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: white;
    }

    .msg-header-left {
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 0;
    }

    .header-avatar {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        background: #ede9fe;
        color: #6d28d9;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        font-weight: 700;
        flex-shrink: 0;
    }

    .msg-name {
        font-size: 15px;
        font-weight: 700;
        color: #111827;
        margin: 0;
    }

    .msg-profile-trigger {
        border: 0;
        padding: 0;
        background: transparent;
        text-align: left;
    }

    .msg-profile-trigger:hover { color: #6d55d9; }

    .msg-email {
        overflow-wrap: anywhere;
        font-size: 12px;
        color: #a78bfa;
        margin-top: 1px;
    }

    .dept-badge {
        font-size: 11px;
        font-weight: 600;
        padding: 4px 12px;
        border-radius: 20px;
        background: #f0fdf4;
        color: #166534;
        border: 1px solid #bbf7d0;
    }

    /* Thread */
    .messaging-thread {
        flex: 0 0 auto;
        overflow: visible;
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        gap: 1rem;
        background: #faf9ff;
    }

    /* Date divider */
    .date-divider {
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 0.25rem 0;
    }

    .date-divider::before,
    .date-divider::after {
        content: '';
        flex: 1;
        height: 1px;
        background: #ede9fe;
    }

    .date-divider span {
        font-size: 11px;
        color: #c4b5fd;
        font-weight: 600;
        white-space: nowrap;
    }

    /* Messages */
    .message-group {
        display: flex;
        gap: 8px;
        max-width: 72%;
    }

    .message-group.user {
        align-self: flex-end;
        flex-direction: row-reverse;
    }

    .message-group.admin {
        align-self: flex-start;
    }

    .msg-avatar {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        font-weight: 700;
        flex-shrink: 0;
        overflow: hidden;
    }

    .msg-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .msg-avatar.outgoing-av {
        background: #ede9fe;
        color: #6d28d9;
    }

    .msg-avatar.incoming-av {
        background: #ede9fe;
        color: #6d28d9;
    }

    .msg-content {
        display: flex;
        flex-direction: column;
        gap: 3px;
    }

    .message-group.user .msg-content {
        align-items: flex-end;
    }

    .message-bubble {
        padding: 0.65rem 1rem;
        border-radius: 14px;
        font-size: 13.5px;
        line-height: 1.55;
        word-wrap: break-word;
    }

    .message-group.user .message-bubble {
        background: linear-gradient(135deg, #7c6fe0, #a78bfa);
        color: white;
        border-radius: 14px 4px 14px 14px;
    }

    .message-group.admin .message-bubble {
        background: white;
        color: #1f2937;
        border: 1px solid #ede9fe;
        border-radius: 4px 14px 14px 14px;
    }

    .message-time {
        font-size: 11px;
        color: #c4b5fd;
    }

    .message-group.admin .message-time {
        color: #d1d5db;
    }

    /* Empty state */
    .empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        color: #c4b5fd;
    }

    .empty-state i {
        font-size: 2.5rem;
        margin-bottom: 0.75rem;
        opacity: 0.6;
    }

    .empty-state p {
        font-size: 14px;
        color: #9ca3af;
        margin: 0;
    }

    /* Input Area */
    .messaging-input-area {
        padding: 1rem 1.25rem;
        border-top: 1px solid #f3f0ff;
        background: white;
        display: flex;
        gap: 10px;
        align-items: flex-end;
    }

    .message-input {
        flex: 1;
        border: 1.5px solid #ede9fe;
        border-radius: 12px;
        padding: 0.6rem 0.9rem;
        font-size: 13.5px;
        font-family: inherit;
        resize: none;
        max-height: 90px;
        color: #1f2937;
        background: #faf9ff;
        outline: none;
        transition: border-color 0.2s, box-shadow 0.2s;
        line-height: 1.5;
    }

    .message-input:focus {
        border-color: #a78bfa;
        background: white;
        box-shadow: 0 0 0 3px rgba(167, 139, 250, 0.12);
    }

    .message-input::placeholder {
        color: #c4b5fd;
    }

    .send-btn {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        border: none;
        background: linear-gradient(135deg, #7c6fe0, #a78bfa);
        color: white;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        transition: transform 0.15s, box-shadow 0.15s;
        font-size: 15px;
    }

    .send-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(124, 111, 224, 0.35);
    }

    .send-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }

    /* No conversation selected */
    .no-conversation {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        color: #c4b5fd;
    }

    .no-conversation i {
        font-size: 3rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }

    .no-conversation p {
        font-size: 14px;
        color: #9ca3af;
        margin: 0;
    }
</style>

<div class="messages-page">
    <div class="container-fluid">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb">
                
                
            </ol>
        </nav>

        <div class="messaging-layout">

            {{-- Left: Conversations List --}}
            <div class="left-panel">
                <div class="panel-header">
                    <div class="panel-title">Conversations</div>
                    <div class="panel-sub">{{ $conversations->count() }} active conversation(s)</div>
                </div>

                <div class="search-bar">
                    <input class="search-input" type="text" id="searchConversations" placeholder="Search conversations..." />
                </div>

                <div class="conversation-list" id="conversationList">
                    @if($conversations->isEmpty())
                        <div class="text-center p-4" style="color: #9ca3af;">
                            <i class="bi bi-chat-dots d-block mb-2" style="font-size: 2rem; opacity: 0.5;"></i>
                            <small>No conversations yet</small>
                        </div>
                    @else
                        @foreach($conversations as $conversation)
                            @php
                                $otherUser = $conversation->sender_id === Auth::id()
                                    ? $conversation->receiver
                                    : $conversation->sender;
                                $initials = collect(explode(' ', $otherUser->name))
                                    ->map(fn($w) => strtoupper(substr($w, 0, 1)))
                                    ->take(2)
                                    ->join('');
                            @endphp
                            <a href="{{ route('admin.messages', ['student_id' => $otherUser->id]) }}"
                               class="conversation-item {{ $selectedStudentId == $otherUser->id ? 'active' : '' }}">
                                <div class="conv-avatar">
                                    @if($otherUser->profile_photo_path)
                                        <img src="{{ asset('storage/' . $otherUser->profile_photo_path) }}" alt="{{ $otherUser->name }} profile photo">
                                    @else
                                        {{ $initials }}
                                    @endif
                                </div>
                                <div class="conv-info">
                                    <div class="conv-name">{{ $otherUser->name }}</div>
                                    <div class="conv-preview">{{ Str::limit($conversation->message, 40) }}</div>
                                </div>
                                <div class="conv-meta">
                                    <div class="conv-time">{{ $conversation->created_at->format('M d') }}</div>
                                    @if($selectedStudentId != $otherUser->id)
                                        <div class="unread-dot"></div>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    @endif
                </div>
            </div>

            {{-- Right: Messages Panel --}}
            <div class="right-panel">
                @if($selectedStudent)
                    @php
                        $selInitials = collect(explode(' ', $selectedStudent->name))
                            ->map(fn($w) => strtoupper(substr($w, 0, 1)))
                            ->take(2)
                            ->join('');
                    @endphp

                    {{-- Header --}}
                    <div class="messaging-header">
                        <div class="msg-header-left">
                            <div class="header-avatar">
                                @if($selectedStudent->profile_photo_path)
                                    <img src="{{ asset('storage/' . $selectedStudent->profile_photo_path) }}" alt="{{ $selectedStudent->name }} profile photo">
                                @else
                                    {{ $selInitials }}
                                @endif
                            </div>
                            <div>
                                <button type="button" class="msg-name msg-profile-trigger" data-bs-toggle="modal" data-bs-target="#messageUserProfileModal">
                                    {{ $selectedStudent->name }}
                                </button>
                                <div class="msg-email">{{ $selectedStudent->email }}</div>
                            </div>
                        </div>
                        <div>
                            <span class="dept-badge">{{ $selectedStudent->department ?? 'N/A' }}</span>
                        </div>
                    </div>

                    {{-- Thread --}}
                    <div class="messaging-thread" id="messagingThread">
                        @if($messages->isEmpty())
                            <div class="empty-state">
                                <i class="bi bi-chat-dots"></i>
                                <p>No messages yet. Start the conversation!</p>
                            </div>
                        @else
                            @php $prevDate = null; @endphp
                            @foreach($messages as $message)
                                @php $msgDate = $message->created_at->format('F j, Y'); @endphp
                                @if($msgDate !== $prevDate)
                                    <div class="date-divider">
                                        <span>{{ $msgDate }}</span>
                                    </div>
                                    @php $prevDate = $msgDate; @endphp
                                @endif

                                @php
                                    $isAdmin = $message->sender_id === Auth::id();
                                    $bubbleInitials = $isAdmin ? 'G' : $selInitials;
                                @endphp

                                <div class="message-group {{ $isAdmin ? 'user' : 'admin' }}" data-message-id="{{ $message->id }}">
                                    <div class="msg-avatar {{ $isAdmin ? 'outgoing-av' : 'incoming-av' }}">
                                        @if(!$isAdmin && $selectedStudent->profile_photo_path)
                                            <img src="{{ asset('storage/' . $selectedStudent->profile_photo_path) }}" alt="{{ $selectedStudent->name }} profile photo">
                                        @else
                                            {{ $bubbleInitials }}
                                        @endif
                                    </div>
                                    <div class="msg-content">
                                        <div class="message-bubble">{{ $message->message }}</div>
                                        <div class="message-time">{{ $message->created_at->format('g:i A') }}</div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>

                    {{-- Input --}}
                    <div class="messaging-input-area">
                        <textarea
                            class="message-input"
                            id="messageInput"
                            placeholder="Type your message... (Enter to send, Shift+Enter for new line)"
                            rows="1"
                        ></textarea>
                        <button class="send-btn" type="button" onclick="sendMessage()" title="Send">
                            <i class="bi bi-send-fill"></i>
                        </button>
                    </div>

                @else
                    <div class="no-conversation">
                        <i class="bi bi-chat-dots"></i>
                        <p>Select a conversation to start messaging</p>
                    </div>
                @endif
            </div>

        </div>
    </div>
</div>

@if($selectedStudent)
    @php
        $messageUserAccountType = $selectedStudent->account_type ?? $selectedStudent->role;
        $messageUserPhone = preg_replace('/[^0-9+]/', '', (string) $selectedStudent->phone_number);
    @endphp
    <div class="modal fade" id="messageUserProfileModal" tabindex="-1" aria-labelledby="messageUserProfileModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold" id="messageUserProfileModalTitle">User profile</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-0">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        @if($selectedStudent->profile_photo_path)
                            <img src="{{ asset('storage/' . $selectedStudent->profile_photo_path) }}" alt="{{ $selectedStudent->name }} profile photo" class="rounded-circle" style="width:4.5rem;height:4.5rem;object-fit:cover;">
                        @else
                            <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white" style="width:4.5rem;height:4.5rem;background:#8f72f5;">{{ $selInitials }}</div>
                        @endif
                        <div>
                            <h6 class="fw-bold mb-1">{{ $selectedStudent->name }}</h6>
                            <div class="small text-muted">{{ ucfirst($messageUserAccountType) }}{{ $selectedStudent->employee_category ? ' · ' . ucfirst(str_replace('_', ' ', $selectedStudent->employee_category)) : '' }}</div>
                            <div class="small text-muted">{{ $selectedStudent->cloak_alias ?: 'No alias set' }}</div>
                        </div>
                    </div>
                    <dl class="row mb-0 small">
                        <dt class="col-5 text-muted">Email</dt><dd class="col-7">{{ $selectedStudent->email ?: 'Not provided' }}</dd>
                        <dt class="col-5 text-muted">Department / Course</dt><dd class="col-7">{{ $selectedStudent->department ?: 'Not provided' }}</dd>
                        <dt class="col-5 text-muted">Age / Gender</dt><dd class="col-7">{{ $selectedStudent->age ?: 'N/A' }} / {{ $selectedStudent->gender ?: 'N/A' }}</dd>
                        <dt class="col-5 text-muted">Contact number</dt>
                        <dd class="col-7 mb-0">
                            @if($messageUserPhone)
                                <a href="tel:{{ $messageUserPhone }}" class="fw-semibold text-decoration-none"><i class="bi bi-telephone-outbound me-1"></i>{{ $selectedStudent->phone_number }}</a>
                            @else
                                Not provided
                            @endif
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
@endif

<script>
    const adminId = {{ Auth::id() }};
    const selectedStudentId = {{ (int) ($selectedStudentId ?? 0) }};
    function sendMessage() {
        const input = document.getElementById('messageInput');
        const message = input.value.trim();
        const studentId = "{{ $selectedStudentId }}";

        if (!message || !studentId) return;

        const btn = document.querySelector('.send-btn');
        btn.disabled = true;
        input.disabled = true;

        fetch("{{ route('admin.messages.store') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                student_id: studentId,
                message: message
            })
        })
        .then(async response => {
            if (!response.ok) {
                if ([401, 403, 419].includes(response.status)) {
                    throw new Error('Your session has expired. Please sign in again.');
                }

                let message = 'Failed to send message. Please try again.';
                try {
                    const payload = await response.json();
                    message = payload.error || payload.message || message;
                } catch (error) {
                    // Keep the generic message for non-JSON server responses.
                }
                throw new Error(message);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                const thread = document.getElementById('messagingThread');

                const emptyState = thread.querySelector('.empty-state');
                if (emptyState) emptyState.remove();

                const msgDiv = document.createElement('div');
                msgDiv.className = 'message-group user';
                msgDiv.dataset.messageId = data.message.id;
                msgDiv.innerHTML = `
                    <div class="msg-avatar outgoing-av">G</div>
                    <div class="msg-content" style="align-items:flex-end;">
                        <div class="message-bubble">${escapeHtml(data.message.message)}</div>
                        <div class="message-time">${escapeHtml(data.message.created_at || 'Just now')}</div>
                    </div>
                `;
                thread.appendChild(msgDiv);

                input.value = '';
                input.style.height = 'auto';
                input.focus();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert(error.message || 'Failed to send message. Please try again.');
        })
        .finally(() => {
            btn.disabled = false;
            input.disabled = false;
            input.focus();
        });
    }

    function escapeHtml(text) {
        const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
        return text.replace(/[&<>"']/g, m => map[m]);
    }

    async function refreshAdminMessages() {
        const thread = selectedStudentId ? document.getElementById('messagingThread') : null;
        const afterId = thread ? Math.max(0, ...[...thread.querySelectorAll('[data-message-id]')].map(el => Number(el.dataset.messageId) || 0)) : 0;
        try {
            const params = new URLSearchParams({ after_id: afterId });
            if (selectedStudentId) params.set('student_id', selectedStudentId);
            const response = await fetch(`{{ route('admin.messages.feed') }}?${params}`, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (!response.ok) throw new Error(`Message refresh failed (${response.status}).`);
            const data = await response.json();
            if (thread) {
                for (const message of data.messages) {
                    if (message.sender_id === adminId || thread.querySelector(`[data-message-id="${message.id}"]`)) continue;
                    thread.querySelector('.empty-state')?.remove();
                    const row = document.createElement('div');
                    row.className = 'message-group admin';
                    row.dataset.messageId = message.id;
                    const avatar = document.createElement('div');
                    avatar.className = 'msg-avatar incoming-av';
                    @if($selectedStudent && $selectedStudent->profile_photo_path)
                        avatar.innerHTML = `<img src="{{ asset('storage/' . $selectedStudent->profile_photo_path) }}" alt="">`;
                    @else
                        avatar.textContent = @json($selectedStudent ? $selInitials : '');
                    @endif
                    const content = document.createElement('div');
                    content.className = 'msg-content';
                    const bubble = document.createElement('div');
                    bubble.className = 'message-bubble';
                    bubble.textContent = message.message;
                    const time = document.createElement('div');
                    time.className = 'message-time';
                    time.textContent = message.created_at || '';
                    content.append(bubble, time);
                    row.append(avatar, content);
                    thread.appendChild(row);
                }
            }
        } catch (error) {
            console.error('Unable to refresh admin messages:', error);
        }
    }
    setInterval(refreshAdminMessages, 3000);

    // Enter to send, Shift+Enter for newline
    document.getElementById('messageInput')?.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });

    // Auto-resize textarea
    document.getElementById('messageInput')?.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 90) + 'px';
    });

    // Client-side search filter
    document.getElementById('searchConversations')?.addEventListener('input', function() {
        const query = this.value.toLowerCase();
        document.querySelectorAll('.conversation-item').forEach(item => {
            const name = item.querySelector('.conv-name')?.textContent.toLowerCase() || '';
            const preview = item.querySelector('.conv-preview')?.textContent.toLowerCase() || '';
            item.style.display = (name.includes(query) || preview.includes(query)) ? '' : 'none';
        });
    });
</script>

@endsection