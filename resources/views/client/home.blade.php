@extends('layouts.app')

@section('title', 'Dashboard - Learn Chinese')

@push('styles')
<link rel="stylesheet" href="{{ asset('client-assets/css/variables.css') }}">
<link rel="stylesheet" href="{{ asset('client-assets/css/base.css') }}">
<link rel="stylesheet" href="{{ asset('client-assets/css/layout.css') }}">
@endpush

@section('content')
<div class="client-container">
    <!-- Navigation Header -->
    @include('client.components.header')

    <!-- Main Content -->
    <main class="client-main">
        <!-- Welcome Section -->
        <section class="welcome-section">
            <div class="welcome-content">
                <h2>Welcome back, {{ Auth::user()->name }}! 👋</h2>
                <p>Continue your journey to master Chinese language</p>
            </div>
            <div class="welcome-stats">
                <div class="stat-card animate-fadeInUp delay-100">
                    <span class="stat-icon">🔥</span>
                    <div class="stat-info">
                        <p class="stat-label">Current Streak</p>
                        <h3 id="streakCount">0 days</h3>
                    </div>
                </div>
                <div class="stat-card animate-fadeInUp delay-200">
                    <span class="stat-icon">📚</span>
                    <div class="stat-info">
                        <p class="stat-label">Words Learned</p>
                        <h3 id="wordsLearned">0</h3>
                    </div>
                </div>
                <div class="stat-card animate-fadeInUp delay-300">
                    <span class="stat-icon">⏱️</span>
                    <div class="stat-info">
                        <p class="stat-label">Study Time</p>
                        <h3 id="studyTime">0h</h3>
                    </div>
                </div>
            </div>
        </section>

        <!-- Content Grid -->
        <div class="content-grid">
            <!-- Learning Progress -->
            <section class="dashboard-card learning-card animate-fadeInUp delay-200">
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
            <section class="dashboard-card practice-zone animate-fadeInUp delay-300">
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
            <section class="dashboard-card animate-fadeInUp delay-400">
                <h3>📊 Quick Stats</h3>
                <div class="flex flex-col gap-4">
                    <div>
                        <div class="flex-between mb-2">
                            <span>Total Progress</span>
                            <span id="totalProgress" class="font-semibold text-primary">0%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar" id="totalProgressBar" style="width: 0%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex-between mb-2">
                            <span>Accuracy Rate</span>
                            <span id="accuracy" class="font-semibold text-success">0%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar success" id="accuracyBar" style="width: 0%"></div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <!-- Download App Section -->
        <section class="download-app-section animate-fadeInUp delay-500">
            <div class="app-promo-container">
                <div class="app-promo-content">
                    <div class="app-logo">
                        <img src="{{ asset('images/logo.png') }}" alt="Chinese Learner Logo">
                    </div>
                    <h2>📱 Trải nghiệm tốt hơn với Mobile App</h2>
                    <p class="app-tagline">Học mọi lúc, mọi nơi với ứng dụng di động Chinese Learner</p>
                    
                    <div class="app-features">
                        <div class="feature-item">
                            <span class="feature-icon">✅</span>
                            <span>Tham gia thi các bài kiểm tra HSK</span>
                        </div>
                        <div class="feature-item">
                            <span class="feature-icon">✅</span>
                            <span>Luyện viết chữ Hán offline</span>
                        </div>
                        <div class="feature-item">
                            <span class="feature-icon">✅</span>
                            <span>Flashcards thông minh với AI</span>
                        </div>
                        <div class="feature-item">
                            <span class="feature-icon">✅</span>
                            <span>Nhận thông báo học hàng ngày</span>
                        </div>
                    </div>

                    <div class="app-download-buttons">
                        <a href="#" class="download-btn app-store">
                            <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 120 40'%3E%3Cpath fill='%23FFF' d='M110.135 0H9.535C4.276 0 0 4.276 0 9.535v20.93C0 35.724 4.276 40 9.535 40h100.6c5.259 0 9.535-4.276 9.535-9.535V9.535C119.67 4.276 115.394 0 110.135 0z'/%3E%3Cpath d='M24.769 20.3a4.949 4.949 0 012.356-4.152 5.066 5.066 0 00-3.99-2.158c-1.68-.176-3.308 1.005-4.164 1.005-.872 0-2.19-.988-3.608-.958a5.315 5.315 0 00-4.473 2.728c-1.934 3.348-.491 8.269 1.361 10.976.927 1.325 2.01 2.805 3.428 2.753 1.387-.058 1.905-.885 3.58-.885 1.658 0 2.144.885 3.591.852 1.489-.025 2.426-1.332 3.32-2.67a10.962 10.962 0 001.52-3.092 4.782 4.782 0 01-2.921-4.4zM22.037 12.21a4.872 4.872 0 001.115-3.49 4.957 4.957 0 00-3.208 1.66 4.636 4.636 0 00-1.143 3.361 4.1 4.1 0 003.236-1.53z' fill='%23000'/%3E%3C/svg%3E" alt="App Store">
                            <span>
                                <small>Download on the</small>
                                <strong>App Store</strong>
                            </span>
                        </a>
                        <a href="#" class="download-btn google-play">
                            <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 135 40'%3E%3Cpath fill='%23FFF' d='M130 40H5c-2.8 0-5-2.2-5-5V5c0-2.8 2.2-5 5-5h125c2.8 0 5 2.2 5 5v30c0 2.8-2.2 5-5 5z'/%3E%3Cpath d='M47.4 10.2c0 .8-.2 1.5-.7 2-.6.6-1.3.9-2.2.9-.9 0-1.6-.3-2.2-.9-.6-.6-.9-1.3-.9-2.2 0-.9.3-1.6.9-2.2.6-.6 1.3-.9 2.2-.9.4 0 .8.1 1.2.3.4.2.7.4.9.7l-.5.5c-.4-.5-.9-.7-1.6-.7-.6 0-1.2.2-1.6.7-.5.4-.7 1-.7 1.7s.2 1.3.7 1.7c.5.4 1 .7 1.6.7.7 0 1.2-.2 1.7-.7.3-.3.5-.7.5-1.2h-2.2v-.8h2.9v.4z' fill='%23000'/%3E%3C/svg%3E" alt="Google Play">
                            <span>
                                <small>GET IT ON</small>
                                <strong>Google Play</strong>
                            </span>
                        </a>
                    </div>
                </div>

                <div class="app-screenshots">
                    <div class="screenshot-carousel">
                        <div class="screenshot-item">
                            <img src="{{ asset('images/screens/character.png') }}" alt="Character Learning">
                            <p class="screenshot-label">Học chữ Hán</p>
                        </div>
                        <div class="screenshot-item">
                            <img src="{{ asset('images/screens/flashcard.png') }}" alt="Flashcard Practice">
                            <p class="screenshot-label">Flashcard thông minh</p>
                        </div>
                        <div class="screenshot-item">
                            <img src="{{ asset('images/screens/exam.png') }}" alt="HSK Exam">
                            <p class="screenshot-label">Thi HSK</p>
                        </div>
                        <div class="screenshot-item">
                            <img src="{{ asset('images/screens/exam_2.png') }}" alt="Practice Exam">
                            <p class="screenshot-label">Luyện đề thi</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
</div>
@endsection

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
