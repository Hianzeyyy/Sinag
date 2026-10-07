

<?php
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Str;
?>

<?php $__env->startSection('content'); ?>
<style>
    .messages-page {
        background: #f4f3ff;
        min-height: calc(100vh - 60px);
        padding: 1.5rem;
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
        height: calc(100vh - 140px);
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
            max-height: 320px;
        }
    }

    /* Left Panel */
    .left-panel {
        background: white;
        border-radius: 16px;
        border: 1px solid #ede9fe;
        display: flex;
        flex-direction: column;
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
        flex: 1;
        overflow-y: auto;
    }

    .conversation-list::-webkit-scrollbar {
        width: 4px;
    }

    .conversation-list::-webkit-scrollbar-thumb {
        background: #ddd6fe;
        border-radius: 10px;
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

    .msg-email {
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
        flex: 1;
        overflow-y: auto;
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        gap: 1rem;
        background: #faf9ff;
    }

    .messaging-thread::-webkit-scrollbar {
        width: 5px;
    }

    .messaging-thread::-webkit-scrollbar-thumb {
        background: #ddd6fe;
        border-radius: 10px;
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

            
            <div class="left-panel">
                <div class="panel-header">
                    <div class="panel-title">Conversations</div>
                    <div class="panel-sub"><?php echo e($conversations->count()); ?> active conversation(s)</div>
                </div>

                <div class="search-bar">
                    <input class="search-input" type="text" id="searchConversations" placeholder="Search conversations..." />
                </div>

                <div class="conversation-list" id="conversationList">
                    <?php if($conversations->isEmpty()): ?>
                        <div class="text-center p-4" style="color: #9ca3af;">
                            <i class="bi bi-chat-dots d-block mb-2" style="font-size: 2rem; opacity: 0.5;"></i>
                            <small>No conversations yet</small>
                        </div>
                    <?php else: ?>
                        <?php $__currentLoopData = $conversations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $conversation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $otherUser = $conversation->sender_id === Auth::id()
                                    ? $conversation->receiver
                                    : $conversation->sender;
                                $initials = collect(explode(' ', $otherUser->name))
                                    ->map(fn($w) => strtoupper(substr($w, 0, 1)))
                                    ->take(2)
                                    ->join('');
                            ?>
                            <a href="<?php echo e(route('admin.messages', ['student_id' => $otherUser->id])); ?>"
                               class="conversation-item <?php echo e($selectedStudentId == $otherUser->id ? 'active' : ''); ?>">
                                <div class="conv-avatar"><?php echo e($initials); ?></div>
                                <div class="conv-info">
                                    <div class="conv-name"><?php echo e($otherUser->name); ?></div>
                                    <div class="conv-preview"><?php echo e(Str::limit($conversation->message, 40)); ?></div>
                                </div>
                                <div class="conv-meta">
                                    <div class="conv-time"><?php echo e($conversation->created_at->format('M d')); ?></div>
                                    <?php if($selectedStudentId != $otherUser->id): ?>
                                        <div class="unread-dot"></div>
                                    <?php endif; ?>
                                </div>
                            </a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                </div>
            </div>

            
            <div class="right-panel">
                <?php if($selectedStudent): ?>
                    <?php
                        $selInitials = collect(explode(' ', $selectedStudent->name))
                            ->map(fn($w) => strtoupper(substr($w, 0, 1)))
                            ->take(2)
                            ->join('');
                    ?>

                    
                    <div class="messaging-header">
                        <div class="msg-header-left">
                            <div class="header-avatar"><?php echo e($selInitials); ?></div>
                            <div>
                                <div class="msg-name"><?php echo e($selectedStudent->name); ?></div>
                                <div class="msg-email"><?php echo e($selectedStudent->email); ?></div>
                            </div>
                        </div>
                        <div>
                            <span class="dept-badge"><?php echo e($selectedStudent->department ?? 'N/A'); ?></span>
                        </div>
                    </div>

                    
                    <div class="messaging-thread" id="messagingThread">
                        <?php if($messages->isEmpty()): ?>
                            <div class="empty-state">
                                <i class="bi bi-chat-dots"></i>
                                <p>No messages yet. Start the conversation!</p>
                            </div>
                        <?php else: ?>
                            <?php $prevDate = null; ?>
                            <?php $__currentLoopData = $messages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $message): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php $msgDate = $message->created_at->format('F j, Y'); ?>
                                <?php if($msgDate !== $prevDate): ?>
                                    <div class="date-divider">
                                        <span><?php echo e($msgDate); ?></span>
                                    </div>
                                    <?php $prevDate = $msgDate; ?>
                                <?php endif; ?>

                                <?php
                                    $isAdmin = $message->sender_id === Auth::id();
                                    $bubbleInitials = $isAdmin ? 'G' : $selInitials;
                                ?>

                                <div class="message-group <?php echo e($isAdmin ? 'user' : 'admin'); ?>">
                                    <div class="msg-avatar <?php echo e($isAdmin ? 'outgoing-av' : 'incoming-av'); ?>">
                                        <?php echo e($bubbleInitials); ?>

                                    </div>
                                    <div class="msg-content">
                                        <div class="message-bubble"><?php echo e($message->message); ?></div>
                                        <div class="message-time"><?php echo e($message->created_at->format('g:i A')); ?></div>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                    </div>

                    
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

                <?php else: ?>
                    <div class="no-conversation">
                        <i class="bi bi-chat-dots"></i>
                        <p>Select a conversation to start messaging</p>
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>

<script>
    function sendMessage() {
        const input = document.getElementById('messageInput');
        const message = input.value.trim();
        const studentId = "<?php echo e($selectedStudentId); ?>";

        if (!message || !studentId) return;

        const btn = document.querySelector('.send-btn');
        btn.disabled = true;
        input.disabled = true;

        fetch("<?php echo e(route('admin.messages.store')); ?>", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
            },
            body: JSON.stringify({
                student_id: studentId,
                message: message
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const thread = document.getElementById('messagingThread');

                const emptyState = thread.querySelector('.empty-state');
                if (emptyState) emptyState.remove();

                const msgDiv = document.createElement('div');
                msgDiv.className = 'message-group user';
                msgDiv.innerHTML = `
                    <div class="msg-avatar outgoing-av">G</div>
                    <div class="msg-content" style="align-items:flex-end;">
                        <div class="message-bubble">${escapeHtml(message)}</div>
                        <div class="message-time">Just now</div>
                    </div>
                `;
                thread.appendChild(msgDiv);

                input.value = '';
                input.style.height = 'auto';
                input.focus();
                thread.scrollTop = thread.scrollHeight;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to send message. Please try again.');
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

    // Scroll to latest message on load
    window.addEventListener('load', function() {
        const thread = document.getElementById('messagingThread');
        if (thread) thread.scrollTop = thread.scrollHeight;
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

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ANGELINE AMBROSIO\sinag\resources\views/admin/messages.blade.php ENDPATH**/ ?>