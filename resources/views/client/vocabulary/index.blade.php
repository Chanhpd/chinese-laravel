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
            <div class="level-selector-section">
                <h2>Select HSK Level</h2>
                <div class="level-buttons" id="levelButtons">
                    <button class="level-btn active" data-level="1">HSK 1</button>
                    <button class="level-btn" data-level="2">HSK 2</button>
                    <button class="level-btn" data-level="3">HSK 3</button>
                    <button class="level-btn" data-level="4">HSK 4</button>
                    <button class="level-btn" data-level="5">HSK 5</button>
                    <button class="level-btn" data-level="6">HSK 6</button>
                </div>
            </div>
            
            <div class="topics-section">
                <h2>Topics for <span id="currentLevelText">HSK 1</span></h2>
                <div class="topics-grid" id="topicsGrid">
                    <!-- Loaded dynamically -->
                    <div class="spinner" style="grid-column: 1/-1; justify-self: center;"></div>
                </div>
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
<link rel="stylesheet" href="{{ asset('client-assets/css/vocabulary.css') }}">
@endpush

@push('scripts')
<script>
    let currentLevel = 1; // Default HSK Level 1

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

    // Level selection
    document.querySelectorAll('.level-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const level = parseInt(this.dataset.level);
            
            // Update active state
            document.querySelectorAll('.level-btn').forEach(b => {
                b.classList.remove('active');
            });
            this.classList.add('active');
            
            // Update current level
            currentLevel = level;
            document.getElementById('currentLevelText').textContent = `HSK ${level}`;
            
            // Reload topics for this level
            loadTopics(level);
        });
    });

    async function loadTopics(level = 1) {
        const grid = document.getElementById('topicsGrid');
        grid.innerHTML = '<div class="spinner" style="grid-column: 1/-1; justify-self: center;"></div>';
        
        try {
            // Load topics with vocabulary count for specific level
            // Convert level number to HSK format (1 -> HSK1)
            const hskLevel = `HSK${level}`;
            const response = await fetch(`/api/topics?with_count=true&level=${hskLevel}`);
            
            if (!response.ok) {
                throw new Error('Failed to load topics');
            }
            
            const result = await response.json();
            const topics = result.data || result;

            grid.innerHTML = '';

            if (!topics || topics.length === 0) {
                grid.innerHTML = `
                    <div class="no-results" style="grid-column: 1/-1;">
                        <div class="no-results-icon">📭</div>
                        <p>No topics found for HSK ${level}</p>
                    </div>
                `;
                return;
            }

            topics.forEach((topic, index) => {
                const card = document.createElement('a');
                card.href = `{{ route('client.vocabulary.learn', '') }}/${topic.id}?level=HSK${level}`;
                card.className = 'topic-card';
                card.style.animationDelay = `${index * 0.05}s`;
                
                const topicName = topic.name || topic.topic_name || 'Unnamed Topic';
                const vocabCount = topic.vocabularies_level_count || topic.vocabularies_count || topic.vocabulary_count || 0;
                const imageUrl = topic.image_url || topic.image;
                const icon = topic.icon || '📚';
                
                // Use image if available, otherwise use icon
                const headerContent = imageUrl 
                    ? `<img src="${imageUrl}" alt="${topicName}" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                       <div class="topic-icon-fallback" style="display:none;">${icon}</div>`
                    : `<div class="topic-icon-fallback">${icon}</div>`;
                
                card.innerHTML = `
                    <div class="topic-header">${headerContent}</div>
                    <div class="topic-body">
                        <div class="topic-name">${topicName}</div>
                        <div class="topic-meta">
                            <span>📖 ${vocabCount} words</span>
                            <span>🎯 HSK ${level}</span>
                        </div>
                        <button class="topic-btn">Start Learning</button>
                    </div>
                `;
                grid.appendChild(card);
            });
        } catch (error) {
            console.error('Error loading topics:', error);
            grid.innerHTML = `
                <div class="no-results" style="grid-column: 1/-1;">
                    <div class="no-results-icon">❌</div>
                    <p>Failed to load topics. Please try again later.</p>
                </div>
            `;
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
