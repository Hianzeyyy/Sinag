@extends('layouts.app')

@php
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Str;
@endphp

@section('content')
<style>
    .messages-page {
        background: #f4f3ff;
        min-height: calc(100dvh - 5rem);
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
        width: min(100%, 1260px);
        min-height: 0;
        margin: 0 auto;
    }

    .messages-page[data-has-conversation="false"] .messaging-layout { grid-template-columns: minmax(0, 1fr); }
    .messages-page[data-has-conversation="false"] .right-panel { display: none; }
    .messages-page[data-has-conversation="true"] .messaging-layout { grid-template-columns: minmax(0, 1fr); }
    .messages-page[data-has-conversation="true"] .left-panel { display: none; }
    .messages-page[data-has-conversation="true"] .right-panel { width: min(100%, 1040px); height: min(72dvh, 52rem); margin-inline: auto; }
    .messages-page[data-has-conversation="false"] .left-panel { height: min(72dvh, 52rem); }
    .message-back-link { display: inline-flex; flex: 0 0 auto; align-items: center; justify-content: center; }

    .messages-page > .container-fluid { width: min(100%, 1320px); margin-inline: auto; padding-inline: clamp(.25rem, 2vw, 1.5rem); }
    .messages-page[data-has-conversation="false"] { display: flex; align-items: center; }
    .messages-page[data-has-conversation="true"] { display: flex; align-items: center; }

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

        .messages-page[data-has-conversation="true"] .right-panel { display: flex; height: min(70dvh, 42rem); min-height: min(26rem, 60dvh); }
        .messages-page[data-has-conversation="false"] .left-panel { display: flex; }
        .messages-page[data-has-conversation="false"] .left-panel { height: min(70dvh, 42rem); }
        .messages-page[data-has-conversation="false"] .right-panel { display: none; }

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
        min-height: 0;
        overflow: hidden;
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
        display: flex;
        gap: .5rem;
        padding: 0.75rem 1rem;
        border-bottom: 1px solid #f3f0ff;
    }

    .search-input {
        flex: 1 1 auto;
        min-width: 0;
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

    .message-search-button {
        display: inline-flex;
        flex: 0 0 auto;
        align-items: center;
        justify-content: center;
        gap: .35rem;
        padding: .45rem .8rem;
        border: 0;
        border-radius: 10px;
        color: #fff;
        background: #8f72f5;
        font-size: .82rem;
        font-weight: 800;
    }

    .message-user-search-results:empty { display: none; }
    .message-user-search-results { max-height: 16rem; margin-top: .4rem; overflow-y: auto; border: 1px solid #ede9fe; border-radius: .7rem; background: #fff; }
    .message-user-search-results a { display: block; padding: .65rem .75rem; border-bottom: 1px solid #f3f0ff; color: #312e51; text-decoration: none; }
    .message-user-search-results a:last-child { border-bottom: 0; }
    .message-user-search-results a:hover, .message-user-search-results a:focus-visible { background: #f5f3ff; outline: none; }
    .message-user-search-results strong, .message-user-search-results small { display: block; }
    .message-user-search-results small { margin-top: .12rem; color: #78728c; }
    .message-user-search-empty { padding: .65rem .75rem; color: #78728c; font-size: .82rem; }

    .conversation-list {
        flex: 1 1 auto;
        min-height: 0;
        overflow-y: auto;
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
        background: #f3efff;
        color: #5b4b9b;
        border: 1px solid #d8cdfc;
    }

    /* Thread */
    .messaging-thread {
        flex: 1 1 auto;
        min-height: 0;
        overflow-y: auto;
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
    .message-bubble.is-deleted { color: #6b5b91; background: #f3efff !important; border: 1px dashed #c4b5fd !important; font-style: italic; }
    .message-meta { display: flex; align-items: center; flex-wrap: wrap; gap: .4rem; }
    .message-action, .message-edited-link { padding: 0; border: 0; color: #7658df; background: transparent; font-size: .7rem; text-decoration: underline; }
    .msg-content { position: relative; }
    .message-edited-link { position: absolute; top: -.8rem; right: .15rem; }
    .message-group.admin .message-edited-link { right: auto; left: .15rem; }
    .message-action.delete { margin-left: .35rem; color: #e85d9e; }
    .message-action:hover, .message-edited-link:hover { color: #5b43b2; }
    .message-original { display: none; margin-top: .4rem; padding: .5rem .65rem; border-left: 2px solid #a594f9; border-radius: .35rem; color: #5b4b9b; background: #f3efff; font-size: .78rem; white-space: pre-wrap; }
    .message-original.is-visible { display: block; }
    .message-edit-input { width: 100%; min-width: 14rem; min-height: 5rem; border: 1px solid #c4b5fd; border-radius: .6rem; padding: .55rem; color: #312e51; background: #fff; font: inherit; resize: vertical; }
    .message-edit-controls { display: flex; justify-content: flex-end; gap: .4rem; margin-top: .4rem; }
    .message-edit-controls button { border: 0; border-radius: .45rem; padding: .3rem .65rem; color: #fff; background: #8f72f5; font-size: .75rem; }
    .message-edit-controls button:first-child { color: #514675; background: #f3efff; }

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

<div class="messages-page" data-has-conversation="{{ $selectedStudent ? 'true' : 'false' }}">
    <div class="container-fluid">
        <div class="messaging-layout">

            {{-- Left: Conversations List --}}
            <div class="left-panel">
                <div class="panel-header">
                    <div class="panel-title">Conversations</div>
                    <div class="panel-sub">{{ $conversations->count() }} active conversation(s)</div>
                </div>

                <div class="search-bar">
                    <input class="search-input" type="search" id="searchPeople" placeholder="Search student / employee name or ID..." aria-label="Search students and employees by name or ID" autocomplete="off" />
                    <button class="message-search-button" type="button" id="searchPeopleButton" aria-label="Search users"><i class="bi bi-search"></i><span>Search</span></button>
                </div>
                <div class="message-user-search-results" id="messageUserSearchResults" role="listbox" aria-live="polite"></div>

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
                               data-conversation-student-id="{{ $otherUser->id }}"
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
                                    <div class="conv-preview">{{ $conversation->deleted_at ? 'Message deleted' : Str::limit($conversation->message, 40) }}</div>
                                </div>
                                <div class="conv-meta">
                                    <div class="conv-time">{{ $conversation->created_at->format('M d') }}</div>
                                    @if($conversation->conversation_unread && (int) $selectedStudentId !== (int) $otherUser->id)
                                        <div class="unread-dot"></div>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    @endif
                </div>
            </div>

            {{-- Right: Messages Panel --}}
            @if($selectedStudent)
            <div class="right-panel">
                    @php
                        $selInitials = collect(explode(' ', $selectedStudent->name))
                            ->map(fn($w) => strtoupper(substr($w, 0, 1)))
                            ->take(2)
                            ->join('');
                    @endphp

                    {{-- Header --}}
                    <div class="messaging-header">
                        <a class="message-back-link btn btn-sm btn-outline-secondary" href="{{ route('admin.messages') }}" aria-label="Back to conversations"><i class="bi bi-arrow-left"></i></a>
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
                                        <div class="message-bubble {{ $message->deleted_at ? 'is-deleted' : '' }}" id="messageBubble{{ $message->id }}">{{ $message->deleted_at ? 'This message was deleted.' : $message->message }}</div>
                                        <div class="message-meta">
                                            <span class="message-time">{{ $message->created_at->format('g:i A') }}</span>
                                            @if(!$message->deleted_at && $isAdmin)
                                                <button class="message-action" type="button" onclick="startAdminEdit({{ $message->id }})">Edit</button>
                                                <button class="message-action" type="button" onclick="deleteAdminMessage({{ $message->id }})">Delete</button>
                                            @endif
                                        </div>
                                        @if($message->original_message && !$message->deleted_at)<button class="message-edited-link" type="button" onclick="toggleOriginal({{ $message->id }})">Edited</button>@endif
                                        @if($message->original_message && !$message->deleted_at)<div class="message-original" id="messageOriginal{{ $message->id }}"><strong>Original message:</strong> {{ $message->original_message }}</div>@endif
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

            </div>
            @endif

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
    const adminCsrf = '{{ csrf_token() }}';

    function adminMessageMarkup(message) {
        const id = Number(message.id);
        const own = Number(message.sender_id) === Number(adminId);
        const deleted = Boolean(message.deleted_at);
        const classes = `message-bubble${deleted ? ' is-deleted' : ''}`;
        const content = deleted ? 'This message was deleted.' : message.message;
        const actions = deleted ? '' : `${own ? `<button class="message-action" type="button" onclick="startAdminEdit(${id})">Edit</button><button class="message-action delete" type="button" onclick="deleteAdminMessage(${id})">Delete</button>` : ''}`;
        const original = message.original_message && !deleted
            ? `<button class="message-edited-link" type="button" onclick="toggleOriginal(${id})">Edited</button><div class="message-original" id="messageOriginal${id}"><strong>Original message:</strong> ${escapeHtml(message.original_message)}</div>`
            : '';
        const state = JSON.stringify([message.message, message.original_message, message.deleted_at]);
        return `<div class="message-group ${own ? 'user' : 'admin'}" data-message-id="${id}" data-message-state="${escapeHtml(state)}">
            <div class="msg-avatar ${own ? 'outgoing-av' : 'incoming-av'}">${own ? 'G' : @json($selInitials ?? '')}</div>
            <div class="msg-content">
                <div class="${classes}" id="messageBubble${id}">${escapeHtml(content)}</div>
                <div class="message-meta"><span class="message-time">${escapeHtml(message.created_at || '')}</span>${actions}</div>
                ${original}
            </div></div>`;
    }

    function toggleOriginal(id) {
        document.getElementById(`messageOriginal${id}`)?.classList.toggle('is-visible');
    }

    function startAdminEdit(id) {
        const bubble = document.getElementById(`messageBubble${id}`);
        if (!bubble || bubble.classList.contains('is-deleted')) return;
        const current = bubble.textContent;
        bubble.dataset.editOriginal = current;
        bubble.innerHTML = `<textarea class="message-edit-input" id="adminEditInput${id}" maxlength="1000">${escapeHtml(current)}</textarea>
            <div class="message-edit-controls"><button type="button" onclick="cancelAdminEdit(${id})">Cancel</button><button type="button" onclick="saveAdminEdit(${id})">Save</button></div>`;
        document.getElementById(`adminEditInput${id}`)?.focus();
    }

    function cancelAdminEdit(id) {
        const bubble = document.getElementById(`messageBubble${id}`);
        if (bubble) bubble.textContent = bubble.dataset.editOriginal || '';
    }

    async function checkAdminMessageResponse(response, action) {
        if (response.ok) return;

        let message = `${action} failed (${response.status}).`;
        if (response.headers.get('content-type')?.includes('application/json')) {
            const payload = await response.json();
            if (payload.message) message = payload.message;
            else if (payload.errors) message = Object.values(payload.errors).flat().join('\n');
        }

        throw new Error(message);
    }

    async function saveAdminEdit(id) {
        const input = document.getElementById(`adminEditInput${id}`);
        const message = input?.value.trim();
        if (!message) return alert('Message cannot be empty.');
        try {
            const response = await fetch(`/admin/messages/${id}/update`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': adminCsrf },
                body: JSON.stringify({ message })
            });
            await checkAdminMessageResponse(response, 'Editing message');
            await refreshAdminMessages();
        } catch (error) {
            alert(error.message || 'Failed to edit message.');
        }
    }

    async function deleteAdminMessage(id) {
        if (!confirm('Delete this message?')) return;
        try {
            const response = await fetch(`/admin/messages/${id}`, {
                method: 'DELETE',
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': adminCsrf }
            });
            await checkAdminMessageResponse(response, 'Deleting message');
            await refreshAdminMessages();
        } catch (error) {
            alert(error.message || 'Failed to delete message.');
        }
    }

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

                thread.insertAdjacentHTML('beforeend', adminMessageMarkup({ ...data.message, sender_id: adminId }));

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
        try {
            const params = new URLSearchParams({ after_id: 0 });
            if (selectedStudentId) params.set('student_id', selectedStudentId);
            const response = await fetch(`{{ route('admin.messages.feed') }}?${params}`, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (!response.ok) throw new Error(`Message refresh failed (${response.status}).`);
            const data = await response.json();
            const unreadStudentIds = new Set((data.unread_student_ids || []).map(Number));
            document.querySelectorAll('[data-conversation-student-id]').forEach(item => {
                const studentId = Number(item.dataset.conversationStudentId);
                const meta = item.querySelector('.conv-meta');
                if (!meta) return;

                let dot = meta.querySelector('.unread-dot');
                if (unreadStudentIds.has(studentId)) {
                    if (!dot) {
                        dot = document.createElement('div');
                        dot.className = 'unread-dot';
                        meta.appendChild(dot);
                    }
                } else {
                    dot?.remove();
                }
            });

            if (thread) {
                for (const message of data.messages) {
                    const current = thread.querySelector(`[data-message-id="${message.id}"]`);
                    const state = JSON.stringify([message.message, message.original_message, message.deleted_at]);
                    if (current?.dataset.messageState === state) continue;
                    if (current) current.outerHTML = adminMessageMarkup(message);
                    else {
                        thread.querySelector('.empty-state')?.remove();
                        thread.insertAdjacentHTML('beforeend', adminMessageMarkup(message));
                    }
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

    (function () {
        const search = document.getElementById('searchPeople');
        const results = document.getElementById('messageUserSearchResults');
        if (!search || !results) return;

        let activeController;
        async function runUserSearch() {
            activeController?.abort();
            const query = search.value.trim();
            results.replaceChildren();
            if (query.length < 2) {
                results.innerHTML = '<div class="message-user-search-empty">Enter at least 2 characters to search.</div>';
                return;
            }
            activeController = new AbortController();
            results.innerHTML = '<div class="message-user-search-empty">Searching students and employees…</div>';
            try {
                const url = new URL(@json(route('admin.messages.search-users')), window.location.origin);
                url.searchParams.set('q', query);
                const response = await fetch(url, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    signal: activeController.signal
                });
                if (!response.ok) throw new Error(`User search failed (${response.status}).`);
                const payload = await response.json();
                results.replaceChildren();
                if (!payload.users.length) {
                    results.innerHTML = '<div class="message-user-search-empty">No matching student or employee found.</div>';
                    return;
                }
                payload.users.forEach(function (user) {
                    const link = document.createElement('a');
                    link.href = user.url;
                    link.setAttribute('role', 'option');
                    const name = document.createElement('strong');
                    name.textContent = user.name;
                    const details = document.createElement('small');
                    details.textContent = [
                        user.role,
                        user.student_employee_id ? 'ID: ' + user.student_employee_id : 'Account ID: ' + user.id,
                        user.department
                    ].filter(Boolean).join(' · ');
                    link.append(name, details);
                    results.appendChild(link);
                });
            } catch (error) {
                if (error.name === 'AbortError') return;
                console.error('Unable to search student and employee accounts:', error);
                results.innerHTML = '<div class="message-user-search-empty">Search is unavailable right now. Please try again.</div>';
            }
        }
        document.getElementById('searchPeopleButton')?.addEventListener('click', runUserSearch);
        search.addEventListener('keydown', event => {
            if (event.key === 'Enter') {
                event.preventDefault();
                runUserSearch();
            }
        });
    })();
</script>

@endsection