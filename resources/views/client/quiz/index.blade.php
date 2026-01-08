@extends('layouts.app')

@section('title', 'Quiz & Exams - Chinese Learning')

@section('content')
<div class="client-container">
    <!-- Navigation Header -->
    @include('client.components.header')

    <!-- Main Content -->
    <div class="client-main">
        <!-- Page Header -->
        <section class="page-header">
            <div class="header-content">
                <h1>❓ Quiz & Exams</h1>
                <p>Test your knowledge and prepare for HSK exams</p>
            </div>
        </section>

        <!-- Tabs -->
        <section class="tabs-section">
            <div class="tabs">
                <button class="tab-btn active" data-tab="hsk">🎯 HSK Exams</button>
                <button class="tab-btn" data-tab="reading">📖 Reading</button>
                <button class="tab-btn" data-tab="listening">🎧 Listening</button>
            </div>
        </section>

        <!-- HSK Exams Tab -->
        <section class="tab-content active" id="hsk-tab">
            <h2>HSK Exam Levels</h2>
            <div class="exams-grid" id="hskGrid">
                <!-- Loaded dynamically -->
                <div class="spinner" style="grid-column: 1/-1; justify-self: center;"></div>
            </div>
        </section>

        <!-- Reading Tab -->
        <section class="tab-content" id="reading-tab">
            <h2>Reading Practice</h2>
            <div class="reading-list" id="readingList">
                <!-- Loaded dynamically -->
            </div>
        </section>

        <!-- Listening Tab -->
        <section class="tab-content" id="listening-tab">
            <h2>Listening Practice</h2>
            <div class="listening-list" id="listeningList">
                <!-- Loaded dynamically -->
            </div>
        </section>
    </div>
</div>
 @include('client.components.footer')
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('client-assets/css/variables.css') }}">
<link rel="stylesheet" href="{{ asset('client-assets/css/base.css') }}">
<link rel="stylesheet" href="{{ asset('client-assets/css/layout.css') }}">
<style>
    body {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
        background: linear-gradient(135deg, #e0f7fa 0%, #b2ebf2 50%, #80deea 100%);
        min-height: 100vh;
    }

    .page-header {
        background: linear-gradient(135deg, var(--color-accent) 0%, var(--color-accent-dark) 100%);
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

    .exams-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: var(--spacing-6);
    }

    .exam-card {
        background: var(--color-surface);
        border-radius: var(--border-radius-lg);
        overflow: hidden;
        box-shadow: var(--shadow-md);
        transition: all var(--transition-base);
        text-decoration: none;
        color: var(--color-text-primary);
    }

    .exam-card:hover {
        transform: translateY(-8px);
        box-shadow: var(--shadow-lg);
    }

    .exam-header {
        background: linear-gradient(135deg, var(--color-accent) 0%, var(--color-accent-dark) 100%);
        padding: var(--spacing-6);
        text-align: center;
        color: white;
    }

    .exam-level {
        font-size: var(--font-size-2xl);
        font-weight: var(--font-weight-bold);
        display: block;
    }

    .exam-body {
        padding: var(--spacing-6);
    }

    .exam-meta {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: var(--spacing-4);
        margin-bottom: var(--spacing-4);
        font-size: var(--font-size-sm);
    }

    .exam-meta-item {
        text-align: center;
    }

    .exam-meta-label {
        color: var(--color-text-secondary);
        display: block;
        margin-bottom: var(--spacing-1);
    }

    .exam-meta-value {
        font-weight: var(--font-weight-bold);
        color: var(--color-primary);
    }

    .exam-btn {
        background: linear-gradient(135deg, var(--color-accent) 0%, var(--color-accent-dark) 100%);
        color: white;
        border: none;
        padding: var(--spacing-3) var(--spacing-6);
        border-radius: var(--border-radius-md);
        cursor: pointer;
        font-weight: var(--font-weight-semibold);
        width: 100%;
        transition: all var(--transition-base);
    }

    .exam-btn:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .reading-list,
    .listening-list {
        display: flex;
        flex-direction: column;
        gap: var(--spacing-4);
    }

    .practice-item {
        background: var(--color-surface);
        padding: var(--spacing-6);
        border-radius: var(--border-radius-lg);
        border-left: 4px solid var(--color-primary);
        box-shadow: var(--shadow-sm);
        transition: all var(--transition-base);
    }

    .practice-item:hover {
        box-shadow: var(--shadow-md);
        transform: translateX(4px);
    }

    .practice-title {
        font-size: var(--font-size-lg);
        font-weight: var(--font-weight-bold);
        color: var(--color-primary);
        margin-bottom: var(--spacing-2);
    }

    .practice-meta {
        display: flex;
        gap: var(--spacing-6);
        margin-bottom: var(--spacing-4);
        font-size: var(--font-size-sm);
        color: var(--color-text-secondary);
    }

    .practice-btn {
        display: inline-block;
        background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-secondary) 100%);
        color: white;
        padding: var(--spacing-2) var(--spacing-6);
        border-radius: var(--border-radius-md);
        text-decoration: none;
        font-weight: var(--font-weight-semibold);
        transition: all var(--transition-base);
        border: none;
        cursor: pointer;
    }

    .practice-btn:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
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
            
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            
            document.querySelectorAll('.tab-btn').forEach(b => {
                b.classList.remove('active');
            });
            
            document.getElementById(`${tabName}-tab`).classList.add('active');
            this.classList.add('active');
        });
    });

    async function loadHSKExams() {
        const grid = document.getElementById('hskGrid');
        grid.innerHTML = '';

        try {
            for (let i = 1; i <= 6; i++) {
                const card = document.createElement('a');
                card.href = '#';
                card.className = 'exam-card';
                card.innerHTML = `
                    <div class="exam-header">
                        <span class="exam-level">HSK ${i}</span>
                    </div>
                    <div class="exam-body">
                        <div class="exam-meta">
                            <div class="exam-meta-item">
                                <span class="exam-meta-label">Questions</span>
                                <span class="exam-meta-value">${40 + i * 10}</span>
                            </div>
                            <div class="exam-meta-item">
                                <span class="exam-meta-label">Time</span>
                                <span class="exam-meta-value">${90 + i * 10}min</span>
                            </div>
                        </div>
                        <button class="exam-btn">Start Exam</button>
                    </div>
                `;
                grid.appendChild(card);
            }
        } catch (error) {
            console.error('Error loading HSK exams:', error);
        }
    }

    async function loadReadingExams() {
        const list = document.getElementById('readingList');
        list.innerHTML = '';

        try {
            const mockData = [
                { id: 1, title: 'Basic Dialogues', difficulty: 'Beginner', questions: 5 },
                { id: 2, title: 'Short Stories', difficulty: 'Intermediate', questions: 10 },
                { id: 3, title: 'News Articles', difficulty: 'Advanced', questions: 15 },
            ];

            mockData.forEach(item => {
                const element = document.createElement('div');
                element.className = 'practice-item';
                element.innerHTML = `
                    <div class="practice-title">${item.title}</div>
                    <div class="practice-meta">
                        <span>📊 ${item.difficulty}</span>
                        <span>❓ ${item.questions} questions</span>
                    </div>
                    <button class="practice-btn" onclick="startPractice(${item.id})">Start</button>
                `;
                list.appendChild(element);
            });
        } catch (error) {
            console.error('Error loading reading exams:', error);
        }
    }

    async function loadListeningExams() {
        const list = document.getElementById('listeningList');
        list.innerHTML = '';

        try {
            const mockData = [
                { id: 1, title: 'Common Phrases', difficulty: 'Beginner', duration: '5 min' },
                { id: 2, title: 'Conversations', difficulty: 'Intermediate', duration: '10 min' },
                { id: 3, title: 'Lectures', difficulty: 'Advanced', duration: '15 min' },
            ];

            mockData.forEach(item => {
                const element = document.createElement('div');
                element.className = 'practice-item';
                element.innerHTML = `
                    <div class="practice-title">${item.title}</div>
                    <div class="practice-meta">
                        <span>📊 ${item.difficulty}</span>
                        <span>⏱️ ${item.duration}</span>
                    </div>
                    <button class="practice-btn" onclick="startPractice(${item.id})">Start</button>
                `;
                list.appendChild(element);
            });
        } catch (error) {
            console.error('Error loading listening exams:', error);
        }
    }

    function startPractice(id) {
        console.log('Starting practice:', id);
        alert('Practice feature will be available soon!');
    }

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        loadHSKExams();
        loadReadingExams();
        loadListeningExams();
    });
</script>
@endpush
