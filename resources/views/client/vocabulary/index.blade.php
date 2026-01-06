@extends('layouts.app')

@section('title', 'Vocabulary Learning - Chinese Learning')

@section('content')
<div class="client-container vocabulary-page">
    <!-- Navigation Header -->
    @include('client.components.header')

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
    /* Vocabulary-specific header gradient */
    .vocabulary-page .page-header {
        background: linear-gradient(135deg, var(--color-secondary) 0%, var(--color-primary) 100%);
        color: white;
    }

    .vocabulary-page .page-header h1,
    .vocabulary-page .page-header p {
        color: white;
    }

    .vocabulary-page .page-header p {
        opacity: 0.95;
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
