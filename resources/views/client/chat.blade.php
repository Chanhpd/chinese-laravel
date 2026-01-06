@extends('layouts.app')

@section('title', 'AI Chat - Chinese Learning')

@section('content')
<div class="client-container">
    <!-- Navigation Header -->
    @include('client.components.header')

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
                    <button type="submit" class="btn-send">
                        <span>Send</span>
                        <span>📤</span>
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
<link rel="stylesheet" href="{{ asset('client-assets/css/chat.css') }}">
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
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    message: message,
                    language: document.getElementById('languageSelect').value
                })
            });

            const data = await response.json();

            // Remove loading indicator
            removeLoadingIndicator();

            if (data.success || data.status === 'success') {
                // Add bot response
                const botMessage = data.response || data.bot_reply || 'No response from AI';
                addMessage(botMessage, 'bot');
                // Load chat history
                loadChatHistory();
            } else {
                // Show error from API
                const errorMsg = data.message || 'Sorry, there was an error. Please try again.';
                addMessage(errorMsg, 'bot');
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
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            if (!response.ok) return;

            const result = await response.json();
            const historyList = document.getElementById('chatHistory');

            // Check if we have data
            const history = result.data || result;
            
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
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
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
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
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
