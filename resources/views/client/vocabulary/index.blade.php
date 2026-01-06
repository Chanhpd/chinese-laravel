@extends('layouts.app')

@section('title', 'Vocabulary Learning - Chinese Learning')

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
            <li><a href="{{ route('client.vocabulary.index') }}" class="nav-link active">Vocabulary</a></li>
            <li><a href="{{ route('client.chat') }}" class="nav-link">AI Chat</a></li>
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
    <div class="client-main">
        <!-- Page Header -->
        <section class="page-header">
            <div class="header-content">
                <h1>📕 Vocabulary Learning</h1>
                <p>Expand your vocabulary with topics and HSK levels</p>
            </div>
        </section>

        <!-- Tabs -->
        <section class="tabs-section">
            <div class="tabs">
                <button class="tab-btn active" data-tab="topics">📚 Topics</button>
                <button class="tab-btn" data-tab="hsk">🎯 HSK Levels</button>
                <button class="tab-btn" data-tab="search">🔍 Search</button>
            </div>
        </section>

        <!-- Topics Tab -->
        <section class="tab-content active" id="topics-tab">
            <h2>Vocabulary by Topics</h2>
            <div class="topics-grid" id="topicsGrid">
                <!-- Loaded dynamically -->
                <div class="spinner" style="grid-column: 1/-1; justify-self: center;"></div>
            </div>
        </section>

        <!-- HSK Levels Tab -->
        <section class="tab-content" id="hsk-tab">
            <h2>HSK Vocabulary Levels</h2>
            <div class="hsk-grid" id="hskGrid">
                <!-- Loaded dynamically -->
            </div>
        </section>

        <!-- Search Tab -->
        <section class="tab-content" id="search-tab">
            <h2>Search Vocabulary</h2>
            <div class="search-box">
                <input 
                    type="text" 
                    id="vocabSearch" 
                    placeholder="Search by Chinese character, pinyin, or English meaning..."
                    autocomplete="off"
                />
                <button class="btn btn-primary" id="searchBtn">Search</button>
            </div>
            <div class="search-results" id="searchResults" style="margin-top: var(--spacing-8);">
                <!-- Search results here -->
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
    .page-header {
        background: linear-gradient(135deg, var(--color-secondary) 0%, var(--color-primary) 100%);
        color: white;
        padding: var(--spacing-10);
        border-radius: var(--border-radius-xl);
        margin-bottom: var(--spacing-10);
        box-shadow: var(--shadow-lg);
    }

    .header-content h1 {
        margin: 0 0 var(--spacing-2) 0;
        color: white;
        font-size: var(--font-size-3xl);
    }

    .header-content p {
        margin: 0;
        color: rgba(255, 255, 255, 0.9);
        font-size: var(--font-size-lg);
    }

    .tabs-section {
        margin-bottom: var(--spacing-8);
    }

    .tabs {
        display: flex;
        gap: var(--spacing-4);
        border-bottom: 2px solid var(--color-border);
        overflow-x: auto;
    }

    .tab-btn {
        padding: var(--spacing-4) var(--spacing-6);
        border: none;
        background: transparent;
        cursor: pointer;
        font-size: var(--font-size-md);
        font-weight: var(--font-weight-semibold);
        color: var(--color-text-secondary);
        border-bottom: 3px solid transparent;
        transition: all var(--transition-base);
        white-space: nowrap;
    }

    .tab-btn:hover {
        color: var(--color-primary);
    }

    .tab-btn.active {
        color: var(--color-primary);
        border-bottom-color: var(--color-primary);
    }

    .tab-content {
        display: none;
        animation: slideIn var(--transition-base);
    }

    .tab-content.active {
        display: block;
    }

    .topics-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: var(--spacing-6);
    }

    .topic-card {
        background: var(--color-surface);
        border-radius: var(--border-radius-lg);
        overflow: hidden;
        box-shadow: var(--shadow-md);
        transition: all var(--transition-base);
        text-decoration: none;
        color: var(--color-text-primary);
    }

    .topic-card:hover {
        transform: translateY(-8px);
        box-shadow: var(--shadow-lg);
    }

    .topic-header {
        background: linear-gradient(135deg, var(--color-primary-light) 0%, var(--color-secondary-light) 100%);
        padding: var(--spacing-6);
        text-align: center;
        font-size: var(--font-size-3xl);
    }

    .topic-body {
        padding: var(--spacing-6);
    }

    .topic-name {
        font-size: var(--font-size-lg);
        font-weight: var(--font-weight-bold);
        color: var(--color-primary);
        margin-bottom: var(--spacing-2);
    }

    .topic-meta {
        display: flex;
        justify-content: space-between;
        font-size: var(--font-size-sm);
        color: var(--color-text-secondary);
        margin-bottom: var(--spacing-4);
    }

    .topic-btn {
        background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-secondary) 100%);
        color: white;
        border: none;
        padding: var(--spacing-3) var(--spacing-6);
        border-radius: var(--border-radius-md);
        cursor: pointer;
        font-weight: var(--font-weight-semibold);
        width: 100%;
        transition: all var(--transition-base);
    }

    .topic-btn:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .hsk-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: var(--spacing-6);
    }

    .hsk-level-card {
        background: var(--color-surface);
        border: 2px solid var(--color-border);
        border-radius: var(--border-radius-lg);
        padding: var(--spacing-6);
        text-align: center;
        cursor: pointer;
        transition: all var(--transition-base);
        text-decoration: none;
        color: var(--color-text-primary);
    }

    .hsk-level-card:hover {
        border-color: var(--color-primary);
        transform: translateY(-4px);
        box-shadow: var(--shadow-md);
    }

    .hsk-level-num {
        font-size: var(--font-size-2xl);
        font-weight: var(--font-weight-bold);
        color: var(--color-primary);
        display: block;
        margin-bottom: var(--spacing-2);
    }

    .hsk-word-count {
        font-size: var(--font-size-sm);
        color: var(--color-text-secondary);
    }

    .search-box {
        display: flex;
        gap: var(--spacing-4);
        margin-bottom: var(--spacing-8);
    }

    .search-box input {
        flex: 1;
    }

    .search-box .btn {
        white-space: nowrap;
    }

    .vocab-item {
        background: var(--color-surface);
        padding: var(--spacing-6);
        border-radius: var(--border-radius-lg);
        margin-bottom: var(--spacing-4);
        border-left: 4px solid var(--color-primary);
        box-shadow: var(--shadow-sm);
        transition: all var(--transition-base);
    }

    .vocab-item:hover {
        box-shadow: var(--shadow-md);
        transform: translateX(4px);
    }

    .vocab-hanzi {
        font-size: var(--font-size-2xl);
        font-weight: var(--font-weight-bold);
        color: var(--color-primary);
        margin-bottom: var(--spacing-2);
        font-family: var(--font-chinese);
    }

    .vocab-pinyin {
        font-size: var(--font-size-md);
        color: var(--color-text-secondary);
        font-weight: var(--font-weight-semibold);
        margin-bottom: var(--spacing-2);
    }

    .vocab-meaning {
        font-size: var(--font-size-base);
        color: var(--color-text-primary);
        margin-bottom: var(--spacing-3);
    }

    .vocab-example {
        background: var(--color-background-light);
        padding: var(--spacing-3);
        border-radius: var(--border-radius-md);
        font-size: var(--font-size-sm);
        color: var(--color-text-secondary);
        font-family: var(--font-chinese);
    }

    .no-results {
        text-align: center;
        padding: var(--spacing-10);
        color: var(--color-text-secondary);
    }

    .no-results-icon {
        font-size: 48px;
        margin-bottom: var(--spacing-4);
    }
</style>
@endpush

@push('scripts')
<script>
    // Tab switching
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const tabName = this.dataset.tab;
            
            // Hide all tabs
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Remove active from all buttons
            document.querySelectorAll('.tab-btn').forEach(b => {
                b.classList.remove('active');
            });
            
            // Show selected tab
            document.getElementById(`${tabName}-tab`).classList.add('active');
            this.classList.add('active');
        });
    });

    async function loadTopics() {
        try {
            const response = await fetch('/api/topics');
            const topics = await response.json();

            const grid = document.getElementById('topicsGrid');
            grid.innerHTML = '';

            if (!topics || topics.length === 0) {
                grid.innerHTML = `<div class="no-results" style="grid-column: 1/-1;"><div class="no-results-icon">📭</div><p>No topics found</p></div>`;
                return;
            }

            topics.forEach(topic => {
                const card = document.createElement('a');
                card.href = `{{ route('client.vocabulary.topic', '') }}/${topic.id}`;
                card.className = 'topic-card';
                card.innerHTML = `
                    <div class="topic-header">${topic.icon || '📚'}</div>
                    <div class="topic-body">
                        <div class="topic-name">${topic.name}</div>
                        <div class="topic-meta">
                            <span>📖 ${topic.vocabulary_count || 0} words</span>
                        </div>
                        <button class="topic-btn">Learn</button>
                    </div>
                `;
                grid.appendChild(card);
            });
        } catch (error) {
            console.error('Error loading topics:', error);
        }
    }

    async function loadHSKLevels() {
        const grid = document.getElementById('hskGrid');
        grid.innerHTML = '';

        for (let i = 1; i <= 6; i++) {
            const card = document.createElement('a');
            card.href = '#';
            card.className = 'hsk-level-card';
            card.innerHTML = `
                <span class="hsk-level-num">HSK ${i}</span>
                <span class="hsk-word-count">${i * 150} words</span>
            `;
            grid.appendChild(card);
        }
    }

    // Search functionality
    document.getElementById('searchBtn').addEventListener('click', searchVocabulary);
    document.getElementById('vocabSearch').addEventListener('keyup', function(e) {
        if (e.key === 'Enter') searchVocabulary();
    });

    async function searchVocabulary() {
        const query = document.getElementById('vocabSearch').value.trim();
        if (!query) {
            document.getElementById('searchResults').innerHTML = '';
            return;
        }

        try {
            const response = await fetch(`/api/vocabularies/search?q=${encodeURIComponent(query)}`);
            const results = await response.json();

            const resultsDiv = document.getElementById('searchResults');
            resultsDiv.innerHTML = '';

            if (!results || results.length === 0) {
                resultsDiv.innerHTML = `<div class="no-results"><div class="no-results-icon">🔍</div><p>No vocabulary found</p></div>`;
                return;
            }

            results.forEach(vocab => {
                const item = document.createElement('div');
                item.className = 'vocab-item';
                item.innerHTML = `
                    <div class="vocab-hanzi">${vocab.hanzi || vocab.chinese}</div>
                    <div class="vocab-pinyin">${vocab.pinyin}</div>
                    <div class="vocab-meaning">${vocab.meaning || vocab.english}</div>
                    ${vocab.example ? `<div class="vocab-example">"${vocab.example}"</div>` : ''}
                `;
                resultsDiv.appendChild(item);
            });
        } catch (error) {
            console.error('Error searching vocabulary:', error);
        }
    }

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        loadTopics();
        loadHSKLevels();
    });
</script>
@endpush
