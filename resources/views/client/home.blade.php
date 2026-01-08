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
                    <h2>📱 Better Experience with Mobile App</h2>
                    <p class="app-tagline">Learn anytime, anywhere with Chinese Learner mobile application</p>
                    
                    <div class="app-features">
                        <div class="feature-item">
                            <span class="feature-icon">✅</span>
                            <span>Take HSK practice exams</span>
                        </div>
                        <div class="feature-item">
                            <span class="feature-icon">✅</span>
                            <span>Practice writing Chinese offline</span>
                        </div>
                        <div class="feature-item">
                            <span class="feature-icon">✅</span>
                            <span>Smart flashcards with AI</span>
                        </div>
                        <div class="feature-item">
                            <span class="feature-icon">✅</span>
                            <span>Daily learning reminders</span>
                        </div>
                    </div>

                    <div class="app-download-buttons">
                        <a href="#" class="download-btn app-store">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="28" height="28" fill="white">
                                <path d="M318.7 268.7c-.2-36.7 16.4-64.4 50-84.8-18.8-26.9-47.2-41.7-84.7-44.6-35.5-2.8-74.3 20.7-88.5 20.7-15 0-49.4-19.7-76.4-19.7C63.3 141.2 4 184.8 4 273.5q0 39.3 14.4 81.2c12.8 36.7 59 126.7 107.2 125.2 25.2-.6 43-17.9 75.8-17.9 31.8 0 48.3 17.9 76.4 17.9 48.6-.7 90.4-82.5 102.6-119.3-65.2-30.7-61.7-90-61.7-94.9zm-56.6-164.2c27.3-32.4 24.8-61.9 24-72.5-24.1 1.4-52 16.4-67.9 34.9-17.5 19.8-27.8 44.3-25.6 71.9 26.1 2 49.9-11.4 69.5-34.3z"/>
                            </svg>
                            <span>
                                <small>Download on the</small>
                                <strong>App Store</strong>
                            </span>
                        </a>
                        <a href="#" class="download-btn google-play">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="28" height="28">
                                <path fill="#4285f4" d="M48 105.2C48 83 59.6 63.7 76.8 53.9L259 234.8 76.8 415.7C59.6 405.9 48 386.6 48 364.4V105.2z"/>
                                <path fill="#34a853" d="M345.4 241L298.6 195.5 76.8 53.9C85.4 49.7 95.3 47.4 105.6 47.4c18.7 0 37.2 6.3 52.2 18.4L345.4 241z"/>
                                <path fill="#fbbc04" d="M454.8 205.7c-10.1-5.4-21.5-8.2-33.1-8.2-12 0-23.8 3-34.3 8.7L345.4 241l42.2 42.2 67.2-35.9c10.1-5.4 16.8-15.8 16.8-28.3 0-12.5-6.7-22.9-16.8-28.3z"/>
                                <path fill="#ea4335" d="M259 320.8L76.8 502.7c8.6 4.2 18.5 6.5 28.8 6.5 18.7 0 37.2-6.3 52.2-18.4L345.4 315.6 298.6 269.5 259 320.8z"/>
                            </svg>
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
                            <p class="screenshot-label">Character Learning</p>
                        </div>
                        <div class="screenshot-item">
                            <img src="{{ asset('images/screens/flashcard.png') }}" alt="Flashcard Practice">
                            <p class="screenshot-label">Smart Flashcards</p>
                        </div>
                        <div class="screenshot-item">
                            <img src="{{ asset('images/screens/exam.png') }}" alt="HSK Exam">
                            <p class="screenshot-label">HSK Exams</p>
                        </div>
                        <div class="screenshot-item">
                            <img src="{{ asset('images/screens/exam_2.png') }}" alt="Practice Exam">
                            <p class="screenshot-label">Practice Tests</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    @include('client.components.footer')
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
