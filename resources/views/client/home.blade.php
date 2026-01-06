@extends('layouts.app')

@section('title', 'Dashboard - Learn Chinese')

@section('content')
<div class="client-container">
    <!-- Navigation Header -->
    <nav class="client-navbar">
        <div class="navbar-brand">
            <div class="brand-logo">🇨🇳</div>
            <h1>ChineseHub</h1>
        </div>
        <ul class="nav-menu">
            <li><a href="{{ route('client.home') }}" class="nav-link active">Dashboard</a></li>
            <li><a href="{{ route('client.radicals.index') }}" class="nav-link">Characters</a></li>
            <li><a href="{{ route('client.vocabulary.index') }}" class="nav-link">Vocabulary</a></li>
            <li><a href="{{ route('client.chat') }}" class="nav-link">AI Chat</a></li>
        </ul>
        <div class="nav-user">
            <div class="user-info">
                <span class="user-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                <div>
                    <p class="user-name">{{ Auth::user()->name }}</p>
                    <p class="user-level" id="userLevel">Beginner</p>
                </div>
            </div>
            <form action="{{ route('client.logout') }}" method="POST" style="margin: 0;">
                @csrf
                <button type="submit" class="btn-logout">Logout</button>
            </form>
        </div>
    </nav>

    <!-- Main Content -->
        <!-- Welcome Section -->
        <section class="welcome-section">
            <div class="welcome-content">
                <h2>Welcome back, {{ Auth::user()->name }}! 👋</h2>
                <p>Continue your journey to master Chinese language</p>
            </div>
            <div class="welcome-stats">
                <div class="stat-card">
                    <span class="stat-icon">🔥</span>
                    <div class="stat-info">
                        <p class="stat-label">Current Streak</p>
                        <p class="stat-value" id="streakCount">0 days</p>
                    </div>
                </div>
                <div class="stat-card">
                    <span class="stat-icon">📚</span>
                    <div class="stat-info">
                        <p class="stat-label">Words Learned</p>
                        <p class="stat-value" id="wordsLearned">0</p>
                    </div>
                </div>
                <div class="stat-card">
                    <span class="stat-icon">⏱️</span>
                    <div class="stat-info">
                        <p class="stat-label">Study Time</p>
                        <p class="stat-value" id="studyTime">0h</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Content Grid -->
        <div class="content-grid">
            <!-- Learning Progress -->
            <section class="dashboard-card learning-card">
                <h3>📖 Learning Progress</h3>
                <div class="progress-levels" id="progressLevels">
                    <div class="level-item">
                        <div class="level-badge hsk1">
                            <span class="level-name">HSK 1</span>
                            <span class="level-progress">0%</span>
                        </div>
                        <div class="level-bar">
                            <div class="level-fill" style="width: 0%"></div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Practice Zone -->
            <section class="dashboard-card practice-zone">
                <h3>🎮 Practice Zone</h3>
                <div class="practice-items">
                    <a href="{{ route('client.radicals.index') }}" class="practice-item">
                        <span class="practice-icon">✍️</span>
                        <div class="practice-info">
                            <h4>Characters</h4>
                            <p>Learn HSK radicals</p>
                        </div>
                    </a>
                    <a href="{{ route('client.vocabulary.index') }}" class="practice-item">
                        <span class="practice-icon">📕</span>
                        <div class="practice-info">
                            <h4>Vocabulary</h4>
                            <p>Master new words</p>
                        </div>
                    </a>
                    <a href="{{ route('client.quiz.index') }}" class="practice-item">
                        <span class="practice-icon">❓</span>
                        <div class="practice-info">
                            <h4>Quiz</h4>
                            <p>Test your knowledge</p>
                        </div>
                    </a>
                    <a href="{{ route('client.chat') }}" class="practice-item">
                        <span class="practice-icon">🤖</span>
                        <div class="practice-info">
                            <h4>AI Chat</h4>
                            <p>Practice conversation</p>
                        </div>
                    </a>
                </div>
            </section>

            <!-- Quick Stats -->
            <section class="dashboard-card">
                <h3>📊 Quick Stats</h3>
                <div style="display: flex; flex-direction: column; gap: var(--spacing-4);">
                    <div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: var(--spacing-2);">
                            <span>Total Progress</span>
                            <span id="totalProgress" style="font-weight: var(--font-weight-semibold); color: var(--color-primary);">0%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar" id="totalProgressBar" style="width: 0%"></div>
                        </div>
                    </div>
                    <div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: var(--spacing-2);">
                            <span>Accuracy Rate</span>
                            <span id="accuracy" style="font-weight: var(--font-weight-semibold); color: var(--color-success);">0%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar success" id="accuracyBar" style="width: 0%"></div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('client-assets/css/variables.css') }}">
<link rel="stylesheet" href="{{ asset('client-assets/css/base.css') }}">
<link rel="stylesheet" href="{{ asset('client-assets/css/layout.css') }}">
@endpush

@push('scripts')
<script>
    // Load user statistics
    async function loadUserStats() {
        try {
            const response = await fetch('/api/user', {
                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem('api_token'),
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) return;

            const data = await response.json();
            
            // Update user level based on progress
            const userLevel = document.getElementById('userLevel');
            if (data.level) userLevel.textContent = data.level;

            // Mock stats for now - will be replaced with actual API calls
            document.getElementById('streakCount').textContent = '5 days';
            document.getElementById('wordsLearned').textContent = '245';
            document.getElementById('studyTime').textContent = '12h';
            document.getElementById('totalProgress').textContent = '65%';
            document.getElementById('totalProgressBar').style.width = '65%';
            document.getElementById('accuracy').textContent = '82%';
            document.getElementById('accuracyBar').style.width = '82%';

        } catch (error) {
            console.error('Error loading stats:', error);
        }
    }

    // Load HSK levels for progress display
    async function loadProgressLevels() {
        try {
            const response = await fetch('/api/radicals/hsk');
            const data = await response.json();

            // Group by level
            const levelMap = {};
            data.forEach(radical => {
                const level = radical.level || 'HSK1';
                if (!levelMap[level]) levelMap[level] = 0;
                levelMap[level]++;
            });

            const progressContainer = document.getElementById('progressLevels');
            progressContainer.innerHTML = '';

            const levels = ['HSK1', 'HSK2', 'HSK3', 'HSK4', 'HSK5', 'HSK6'];
            levels.forEach((level, index) => {
                const count = levelMap[level] || 0;
                const percentage = Math.floor((Math.random() * 100)); // Mock progress
                const levelNum = index + 1;
                const badgeClass = `hsk${levelNum}`;

                progressContainer.innerHTML += `
                    <div class="level-item">
                        <div class="level-badge ${badgeClass}">
                            <span class="level-name">${level}</span>
                            <span class="level-progress">${percentage}%</span>
                        </div>
                        <div class="level-bar">
                            <div class="level-fill" style="width: ${percentage}%"></div>
                        </div>
                    </div>
                `;
            });
        } catch (error) {
            console.error('Error loading progress levels:', error);
        }
    }

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        loadUserStats();
        loadProgressLevels();
    });
</script>
@endpush
