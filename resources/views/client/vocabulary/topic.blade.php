@extends('layouts.app')

@section('title', 'Topic Vocabulary - Chinese Learning')

@section('content')
<div class="client-container vocabulary-page">
    <!-- Navigation Header -->
    @include('client.components.header')

    <!-- Main Content -->
    <div class="client-main">
        <!-- Topic Header -->
        <section class="topic-detail-header">
            <a href="{{ route('client.vocabulary.index') }}" class="btn-back">← Back to Topics</a>
            <div class="topic-info">
                <div class="topic-icon" id="topicIcon">📚</div>
                <div class="topic-details">
                    <h1 id="topicName">Loading...</h1>
                    <p id="topicDescription">Loading topic details...</p>
                    <div class="topic-stats">
                        <span class="stat-item">
                            <span class="stat-icon">📖</span>
                            <span id="vocabCount">0</span> Words
                        </span>
                        <span class="stat-item">
                            <span class="stat-icon">🎯</span>
                            HSK <span id="topicLevel">1-6</span>
                        </span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Vocabulary List -->
        <section class="vocabulary-list-section">
            <div class="vocabulary-controls">
                <h2>Vocabulary List</h2>
                <div class="controls-group">
                    <select id="sortSelect" class="form-control">
                        <option value="default">Default Order</option>
                        <option value="alphabetical">Alphabetical</option>
                        <option value="level">By HSK Level</option>
                    </select>
                    <button class="btn btn-primary" id="practiceBtn">
                        🎯 Practice All
                    </button>
                </div>
            </div>

            <div class="vocabulary-grid" id="vocabularyGrid">
                <div class="spinner"></div>
            </div>
        </section>
    </div>

    @include('client.components.footer')
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('client-assets/css/variables.css') }}">
<link rel="stylesheet" href="{{ asset('client-assets/css/base.css') }}">
<link rel="stylesheet" href="{{ asset('client-assets/css/layout.css') }}">
<link rel="stylesheet" href="{{ asset('client-assets/css/vocabulary.css') }}">
<style>
    .topic-detail-header {
        background: white;
        border-radius: var(--border-radius-xl);
        padding: var(--spacing-8);
        margin-bottom: var(--spacing-8);
        box-shadow: var(--shadow-lg);
    }

    .btn-back {
        display: inline-block;
        padding: var(--spacing-3) var(--spacing-5);
        background: white;
        color: var(--color-primary);
        border: 2px solid var(--color-primary);
        border-radius: var(--border-radius-lg);
        text-decoration: none;
        font-weight: var(--font-weight-semibold);
        margin-bottom: var(--spacing-6);
        transition: all var(--transition-base);
    }

    .btn-back:hover {
        background: var(--color-primary);
        color: white;
    }

    .topic-info {
        display: flex;
        gap: var(--spacing-6);
        align-items: center;
    }

    .topic-icon {
        font-size: 80px;
        width: 120px;
        height: 120px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, var(--color-primary-50) 0%, var(--color-secondary-50) 100%);
        border-radius: var(--border-radius-xl);
    }

    .topic-details {
        flex: 1;
    }

    .topic-details h1 {
        margin: 0 0 var(--spacing-2) 0;
        color: var(--color-text-primary);
        font-size: var(--font-size-2xl);
    }

    .topic-details p {
        margin: 0 0 var(--spacing-4) 0;
        color: var(--color-text-secondary);
        font-size: var(--font-size-base);
    }

    .topic-stats {
        display: flex;
        gap: var(--spacing-6);
    }

    .stat-item {
        display: flex;
        align-items: center;
        gap: var(--spacing-2);
        padding: var(--spacing-3) var(--spacing-5);
        background: var(--color-background-light);
        border-radius: var(--border-radius-lg);
        font-weight: var(--font-weight-semibold);
        color: var(--color-text-primary);
    }

    .stat-icon {
        font-size: var(--font-size-lg);
    }

    .vocabulary-list-section {
        background: white;
        border-radius: var(--border-radius-xl);
        padding: var(--spacing-8);
        box-shadow: var(--shadow-lg);
    }

    .vocabulary-controls {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: var(--spacing-6);
        padding-bottom: var(--spacing-4);
        border-bottom: 2px solid var(--color-border-light);
    }

    .vocabulary-controls h2 {
        margin: 0;
        color: var(--color-text-primary);
        font-size: var(--font-size-xl);
    }

    .controls-group {
        display: flex;
        gap: var(--spacing-4);
        align-items: center;
    }

    .vocabulary-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: var(--spacing-5);
    }

    .vocab-card {
        background: white;
        border: 2px solid var(--color-border-light);
        border-radius: var(--border-radius-lg);
        padding: var(--spacing-5);
        transition: all var(--transition-base);
        cursor: pointer;
    }

    .vocab-card:hover {
        border-color: var(--color-primary);
        box-shadow: var(--shadow-md);
        transform: translateY(-2px);
    }

    .vocab-card-header {
        display: flex;
        justify-content: space-between;
        align-items: start;
        margin-bottom: var(--spacing-3);
    }

    .vocab-character {
        font-size: var(--font-size-2xl);
        font-weight: var(--font-weight-bold);
        color: var(--color-primary);
    }

    .vocab-level {
        padding: var(--spacing-1) var(--spacing-3);
        background: var(--color-background-light);
        border-radius: var(--border-radius-md);
        font-size: var(--font-size-xs);
        font-weight: var(--font-weight-semibold);
        color: var(--color-text-secondary);
    }

    .vocab-card-body {
        display: flex;
        flex-direction: column;
        gap: var(--spacing-2);
    }

    .vocab-pinyin {
        font-size: var(--font-size-base);
        color: var(--color-text-secondary);
        font-style: italic;
    }

    .vocab-meaning {
        font-size: var(--font-size-base);
        color: var(--color-text-primary);
    }

    @media (max-width: 768px) {
        .topic-info {
            flex-direction: column;
            text-align: center;
        }

        .vocabulary-controls {
            flex-direction: column;
            gap: var(--spacing-4);
            align-items: stretch;
        }

        .controls-group {
            flex-direction: column;
        }

        .vocabulary-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    const topicId = {{ $id }};
    const urlParams = new URLSearchParams(window.location.search);
    const selectedLevel = urlParams.get('level') || '1';
    let vocabularies = [];

    async function loadTopicData() {
        try {
            // Load topic details with vocabularies for specific level
            const response = await fetch(`/api/topics/${topicId}?with_count=true&level=${selectedLevel}`);
            
            if (!response.ok) {
                throw new Error('Failed to load topic');
            }
            
            const result = await response.json();
            const topic = result.data || result;

            // Update topic header
            const topicIcon = document.getElementById('topicIcon');
            if (topic.image_url || topic.image) {
                topicIcon.innerHTML = `<img src="${topic.image_url || topic.image}" alt="${topic.name}" style="width: 100%; height: 100%; object-fit: cover; border-radius: var(--border-radius-lg);">`;
            } else {
                topicIcon.textContent = topic.icon || '📚';
            }
            
            document.getElementById('topicName').textContent = topic.name || 'Unnamed Topic';
            document.getElementById('topicDescription').textContent = topic.description || '';
            document.getElementById('vocabCount').textContent = topic.vocabularies_level_count || topic.vocabularies_count || 0;
            document.getElementById('topicLevel').textContent = selectedLevel;
            
            // Load vocabularies
            loadVocabularies(topicId, selectedLevel);

        } catch (error) {
            console.error('Error loading topic:', error);
            document.getElementById('topicName').textContent = 'Error Loading Topic';
            document.getElementById('topicDescription').textContent = 'Failed to load topic details.';
        }
    }

    async function loadVocabularies(topicId, level) {
        const grid = document.getElementById('vocabularyGrid');
        grid.innerHTML = '<div class="spinner" style="grid-column: 1/-1; justify-self: center;"></div>';

        try {
            const response = await fetch(`/api/topics/${topicId}/vocabularies?level=${level}`);
            
            if (!response.ok) {
                throw new Error('Failed to load vocabularies');
            }
            
            const result = await response.json();
            vocabularies = result.data || result;

            displayVocabularies(vocabularies);

        } catch (error) {
            console.error('Error loading vocabularies:', error);
            grid.innerHTML = `
                <div class="no-results" style="grid-column: 1/-1;">
                    <div class="no-results-icon">❌</div>
                    <p>Failed to load vocabularies.</p>
                </div>
            `;
        }
    }

    function displayVocabularies(vocabs) {
        const grid = document.getElementById('vocabularyGrid');
        grid.innerHTML = '';

        if (!vocabs || vocabs.length === 0) {
            grid.innerHTML = `
                <div class="no-results" style="grid-column: 1/-1;">
                    <div class="no-results-icon">📭</div>
                    <p>No vocabularies found for this topic.</p>
                </div>
            `;
            return;
        }

        vocabs.forEach((vocab, index) => {
            const card = document.createElement('div');
            card.className = 'vocab-card';
            card.style.animationDelay = `${index * 0.03}s`;
            
            const character = vocab.hanzi || vocab.chinese || vocab.simplified || '?';
            const pinyin = vocab.pinyin || '';
            const meaning = vocab.meaning || vocab.english || '';
            const level = vocab.level || 'HSK 1';

            card.innerHTML = `
                <div class="vocab-card-header">
                    <span class="vocab-character">${character}</span>
                    <span class="vocab-level">${level}</span>
                </div>
                <div class="vocab-card-body">
                    <div class="vocab-pinyin">${pinyin}</div>
                    <div class="vocab-meaning">${meaning}</div>
                </div>
            `;
            
            grid.appendChild(card);
        });
    }

    // Sort functionality
    document.getElementById('sortSelect').addEventListener('change', function() {
        const sortBy = this.value;
        let sorted = [...vocabularies];

        switch(sortBy) {
            case 'alphabetical':
                sorted.sort((a, b) => {
                    const aChar = a.hanzi || a.chinese || '';
                    const bChar = b.hanzi || b.chinese || '';
                    return aChar.localeCompare(bChar);
                });
                break;
            case 'level':
                sorted.sort((a, b) => {
                    const aLevel = parseInt((a.level || 'HSK 1').replace(/\D/g, '')) || 1;
                    const bLevel = parseInt((b.level || 'HSK 1').replace(/\D/g, '')) || 1;
                    return aLevel - bLevel;
                });
                break;
            default:
                // Keep original order
                break;
        }

        displayVocabularies(sorted);
    });

    // Practice button (placeholder)
    document.getElementById('practiceBtn').addEventListener('click', function() {
        alert('Practice feature coming soon!');
    });

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        loadTopicData();
    });
</script>
@endpush

