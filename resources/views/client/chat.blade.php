@extends('layouts.app')

@section('title', 'AI Chat - Chinese Learning')

@section('content')
<div class="client-container">
    <!-- Navigation Header -->
    <nav class="client-navbar">
        <div class="navbar-brand">
            <div class="brand-logo">🇨🇳</div>
            <h1>ChineseHub</h1>
        </div>
        <ul class="nav-menu">
            <li><a href="{{ route('client.home') }}" class="nav-link">Dashboard</a></li>
            <li><a href="{{ route('client.radicals.index') }}" class="nav-link">Characters</a></li>
            <li><a href="{{ route('client.vocabulary.index') }}" class="nav-link">Vocabulary</a></li>
            <li><a href="{{ route('client.chat') }}" class="nav-link active">AI Chat</a></li>
        </ul>
        <div class="nav-user">
            <div class="user-info">
                <span class="user-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                <div>
                    <p class="user-name">{{ Auth::user()->name }}</p>
                    <p class="user-level">Learner</p>
                </div>
            </div>
            <form action="{{ route('client.logout') }}" method="POST" style="margin: 0;">
                @csrf
                <button type="submit" class="btn-logout">Logout</button>
            </form>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="client-main chat-main">
        <section class="chat-container">
            <div class="chat-header">
                <div class="chat-title">
                    <h1>🤖 AI Tutor</h1>
                    <p>Practice Chinese with our AI assistant</p>
                </div>
                <button class="btn btn-sm" id="clearChatBtn">Clear Chat</button>
            </div>

            <div class="chat-messages" id="chatMessages">
                <div class="message bot-message">
                    <div class="message-content">
                        <div class="message-text">
                            Hello! 你好! I'm your Chinese learning assistant. How can I help you today?
                        </div>
                        <span class="message-time">Just now</span>
                    </div>
                </div>
            </div>

            <div class="chat-input-area">
                <div class="chat-actions">
                    <select id="languageSelect" class="btn">
                        <option value="en">English</option>
                        <option value="cn">中文</option>
                        <option value="mixed">Mixed</option>
                    </select>
                </div>
                <form class="chat-form" id="chatForm">
                    <input 
                        type="text" 
                        id="messageInput" 
                        placeholder="Type your message here..."
                        autocomplete="off"
                        required
                    />
                    <button type="submit" class="btn btn-primary">
                        <span>Send</span>
                        <span style="font-size: 1.2em;">📤</span>
                    </button>
                </form>
            </div>
        </section>

        <!-- Chat History Sidebar -->
        <section class="chat-sidebar">
            <div class="sidebar-header">
                <h3>Chat History</h3>
                <button class="btn btn-sm" id="deleteChatBtn">Delete All</button>
            </div>
            <div class="chat-history-list" id="chatHistory">
                <!-- Loaded dynamically -->
                <p class="empty-state">No chat history yet</p>
            </div>
        </section>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('client-assets/css/variables.css') }}">
<link rel="stylesheet" href="{{ asset('client-assets/css/base.css') }}">
<link rel="stylesheet" href="{{ asset('client-assets/css/layout.css') }}">
<style>
    .chat-main {
        display: grid;
        grid-template-columns: 1fr 300px;
        gap: var(--spacing-6);
        height: calc(100vh - 100px);
        padding: var(--spacing-6) !important;
    }

    @media (max-width: 1024px) {
        .chat-main {
            grid-template-columns: 1fr;
            height: auto;
        }

        .chat-sidebar {
            display: none;
        }
    }

    .chat-container {
        background: var(--color-surface);
        border-radius: var(--border-radius-lg);
        box-shadow: var(--shadow-lg);
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .chat-header {
        padding: var(--spacing-6);
        border-bottom: 2px solid var(--color-border);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .chat-title h1 {
        margin: 0 0 var(--spacing-1) 0;
        font-size: var(--font-size-xl);
        color: var(--color-primary);
    }

    .chat-title p {
        margin: 0;
        font-size: var(--font-size-sm);
        color: var(--color-text-secondary);
    }

    .chat-messages {
        flex: 1;
        overflow-y: auto;
        padding: var(--spacing-6);
        display: flex;
        flex-direction: column;
        gap: var(--spacing-4);
        background: linear-gradient(135deg, var(--color-background-light) 0%, var(--color-surface) 100%);
    }

    .message {
        display: flex;
        animation: slideIn var(--transition-base);
    }

    .message.bot-message {
        justify-content: flex-start;
    }

    .message.user-message {
        justify-content: flex-end;
    }

    .message-content {
        max-width: 70%;
        display: flex;
        flex-direction: column;
        gap: var(--spacing-2);
    }

    .message-text {
        padding: var(--spacing-4) var(--spacing-6);
        border-radius: var(--border-radius-lg);
        line-height: var(--line-height-relaxed);
    }

    .bot-message .message-text {
        background: var(--color-primary-light);
        color: var(--color-text-primary);
        border-bottom-left-radius: var(--border-radius-md);
    }

    .user-message .message-text {
        background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-secondary) 100%);
        color: white;
        border-bottom-right-radius: var(--border-radius-md);
    }

    .message-time {
        font-size: var(--font-size-xs);
        color: var(--color-text-secondary);
        padding: 0 var(--spacing-4);
    }

    .chat-input-area {
        padding: var(--spacing-6);
        border-top: 2px solid var(--color-border);
        background: var(--color-surface);
    }

    .chat-actions {
        margin-bottom: var(--spacing-4);
    }

    .chat-form {
        display: flex;
        gap: var(--spacing-4);
    }

    .chat-form input {
        flex: 1;
    }

    .chat-form .btn {
        white-space: nowrap;
    }

    .chat-sidebar {
        background: var(--color-surface);
        border-radius: var(--border-radius-lg);
        padding: var(--spacing-6);
        box-shadow: var(--shadow-md);
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .sidebar-header {
        margin-bottom: var(--spacing-6);
        padding-bottom: var(--spacing-4);
        border-bottom: 2px solid var(--color-border);
    }

    .sidebar-header h3 {
        margin: 0 0 var(--spacing-2) 0;
        color: var(--color-primary);
        font-size: var(--font-size-lg);
    }

    .chat-history-list {
        flex: 1;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: var(--spacing-3);
    }

    .empty-state {
        text-align: center;
        color: var(--color-text-secondary);
        padding: var(--spacing-4);
        font-size: var(--font-size-sm);
        margin: 0;
    }

    .history-item {
        padding: var(--spacing-3);
        background: var(--color-background-light);
        border-radius: var(--border-radius-md);
        cursor: pointer;
        transition: all var(--transition-base);
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: var(--font-size-sm);
        color: var(--color-text-secondary);
    }

    .history-item:hover {
        background: var(--color-border);
        transform: translateX(-4px);
    }

    .history-item.active {
        background: linear-gradient(135deg, var(--color-primary-light) 0%, var(--color-secondary-light) 100%);
        color: var(--color-primary);
    }

    .history-text {
        flex: 1;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .history-delete {
        background: none;
        border: none;
        color: var(--color-error);
        cursor: pointer;
        padding: 0;
        font-size: var(--font-size-base);
        transition: all var(--transition-base);
    }

    .history-delete:hover {
        transform: scale(1.2);
    }

    .loading-indicator {
        display: inline-block;
        width: 8px;
        height: 8px;
        background: var(--color-primary);
        border-radius: 50%;
        animation: pulse 1.5s infinite;
        margin: 0 2px;
    }

    .loading-indicator:nth-child(2) {
        animation-delay: 0.2s;
    }

    .loading-indicator:nth-child(3) {
        animation-delay: 0.4s;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes pulse {
        0%, 100% {
            opacity: 0.6;
        }
        50% {
            opacity: 1;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    const chatForm = document.getElementById('chatForm');
    const messageInput = document.getElementById('messageInput');
    const chatMessages = document.getElementById('chatMessages');
    const clearChatBtn = document.getElementById('clearChatBtn');
    const deleteChatBtn = document.getElementById('deleteChatBtn');

    // Send message
    chatForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const message = messageInput.value.trim();

        if (!message) return;

        // Add user message
        addMessage(message, 'user');
        messageInput.value = '';

        // Show loading
        showLoadingIndicator();

        try {
            const response = await fetch('/api/chat', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Authorization': 'Bearer ' + localStorage.getItem('api_token')
                },
                body: JSON.stringify({
                    message: message,
                    language: document.getElementById('languageSelect').value
                })
            });

            const data = await response.json();

            if (data.success) {
                // Remove loading indicator
                removeLoadingIndicator();
                // Add bot response
                addMessage(data.response, 'bot');
                // Load chat history
                loadChatHistory();
            }
        } catch (error) {
            console.error('Error sending message:', error);
            removeLoadingIndicator();
            addMessage('Sorry, there was an error. Please try again.', 'bot');
        }
    });

    function addMessage(text, sender) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${sender}-message`;

        const time = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

        messageDiv.innerHTML = `
            <div class="message-content">
                <div class="message-text">${escapeHtml(text)}</div>
                <span class="message-time">${time}</span>
            </div>
        `;

        chatMessages.appendChild(messageDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    function showLoadingIndicator() {
        const messageDiv = document.createElement('div');
        messageDiv.className = 'message bot-message';
        messageDiv.id = 'loading-message';
        messageDiv.innerHTML = `
            <div class="message-content">
                <div class="message-text">
                    <span class="loading-indicator"></span>
                    <span class="loading-indicator"></span>
                    <span class="loading-indicator"></span>
                </div>
            </div>
        `;
        chatMessages.appendChild(messageDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    function removeLoadingIndicator() {
        const loadingMsg = document.getElementById('loading-message');
        if (loadingMsg) {
            loadingMsg.remove();
        }
    }

    async function loadChatHistory() {
        try {
            const response = await fetch('/api/chat/history', {
                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem('api_token'),
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) return;

            const history = await response.json();
            const historyList = document.getElementById('chatHistory');

            if (!history || history.length === 0) {
                historyList.innerHTML = '<p class="empty-state">No chat history yet</p>';
                return;
            }

            historyList.innerHTML = '';
            history.forEach(item => {
                const historyItem = document.createElement('div');
                historyItem.className = 'history-item';
                historyItem.innerHTML = `
                    <span class="history-text">${escapeHtml(item.message.substring(0, 30))}...</span>
                    <button class="history-delete" onclick="deleteHistory(${item.id})" type="button">✕</button>
                `;
                historyList.appendChild(historyItem);
            });
        } catch (error) {
            console.error('Error loading chat history:', error);
        }
    }

    async function deleteHistory(id) {
        if (!confirm('Delete this chat?')) return;

        try {
            const response = await fetch(`/api/chat/history/${id}`, {
                method: 'DELETE',
                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem('api_token'),
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            if (response.ok) {
                loadChatHistory();
            }
        } catch (error) {
            console.error('Error deleting chat:', error);
        }
    }

    clearChatBtn.addEventListener('click', async () => {
        if (!confirm('Clear all messages?')) return;

        chatMessages.innerHTML = `
            <div class="message bot-message">
                <div class="message-content">
                    <div class="message-text">
                        Hello! 你好! I'm your Chinese learning assistant. How can I help you today?
                    </div>
                    <span class="message-time">Just now</span>
                </div>
            </div>
        `;
    });

    deleteChatBtn.addEventListener('click', async () => {
        if (!confirm('Delete all chat history?')) return;

        try {
            const response = await fetch('/api/chat/history', {
                method: 'DELETE',
                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem('api_token'),
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            if (response.ok) {
                loadChatHistory();
            }
        } catch (error) {
            console.error('Error deleting history:', error);
        }
    });

    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, m => map[m]);
    }

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        loadChatHistory();
        messageInput.focus();
    });
</script>
@endpush
