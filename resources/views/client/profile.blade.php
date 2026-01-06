@extends('layouts.app')

@section('title', 'My Profile - Chinese Learning')

@section('content')
<div class="client-container">
    <!-- Navigation Header -->
    @include('client.components.header')

    <!-- Main Content -->
    <div class="client-main">
        <!-- Page Header -->
        <section class="page-header">
            <div class="header-content">
                <h1>👤 My Profile</h1>
                <p>Manage your learning account and settings</p>
            </div>
        </section>

        <!-- Profile Grid -->
        <div class="profile-grid">
            <!-- Profile Card -->
            <section class="profile-card">
                <h2>User Information</h2>
                
                @if ($errors->any())
                    <div class="alert alert-error">
                        <span class="alert-icon">⚠️</span>
                        <div>
                            @foreach ($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if (session('success'))
                    <div class="alert alert-success">
                        <span class="alert-icon">✅</span>
                        <p>{{ session('success') }}</p>
                    </div>
                @endif

                <form action="{{ route('client.profile.update') }}" method="POST">
                    @csrf

                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <input 
                            type="text" 
                            id="name" 
                            name="name" 
                            value="{{ Auth::user()->name }}"
                            required
                        />
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            value="{{ Auth::user()->email }}"
                            required
                        />
                    </div>

                    <div class="form-group">
                        <label for="created">Member Since</label>
                        <input 
                            type="text" 
                            id="created" 
                            value="{{ Auth::user()->created_at->format('F d, Y') }}"
                            disabled
                        />
                    </div>

                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </form>
            </section>

            <!-- Statistics Card -->
            <section class="profile-card">
                <h2>Learning Statistics</h2>
                
                <div class="stats-list">
                    <div class="stat-item">
                        <div class="stat-icon">📚</div>
                        <div class="stat-details">
                            <span class="stat-name">Words Learned</span>
                            <span class="stat-number" id="wordsLearned">0</span>
                        </div>
                    </div>

                    <div class="stat-item">
                        <div class="stat-icon">📖</div>
                        <div class="stat-details">
                            <span class="stat-name">Lessons Completed</span>
                            <span class="stat-number" id="lessonsCompleted">0</span>
                        </div>
                    </div>

                    <div class="stat-item">
                        <div class="stat-icon">🎯</div>
                        <div class="stat-details">
                            <span class="stat-name">Quiz Score</span>
                            <span class="stat-number" id="quizScore">0%</span>
                        </div>
                    </div>

                    <div class="stat-item">
                        <div class="stat-icon">🔥</div>
                        <div class="stat-details">
                            <span class="stat-name">Current Streak</span>
                            <span class="stat-number" id="streak">0 days</span>
                        </div>
                    </div>

                    <div class="stat-item">
                        <div class="stat-icon">⏱️</div>
                        <div class="stat-details">
                            <span class="stat-name">Total Study Time</span>
                            <span class="stat-number" id="studyTime">0h</span>
                        </div>
                    </div>

                    <div class="stat-item">
                        <div class="stat-icon">🏆</div>
                        <div class="stat-details">
                            <span class="stat-name">Achievements</span>
                            <span class="stat-number" id="achievements">0</span>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Learning Progress Card -->
            <section class="profile-card">
                <h2>HSK Level Progress</h2>
                
                <div class="progress-list">
                    <div class="progress-item">
                        <div class="progress-header">
                            <span class="progress-name">HSK 1</span>
                            <span class="progress-percent" id="hsk1-percent">0%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar" id="hsk1-bar" style="width: 0%"></div>
                        </div>
                    </div>

                    <div class="progress-item">
                        <div class="progress-header">
                            <span class="progress-name">HSK 2</span>
                            <span class="progress-percent" id="hsk2-percent">0%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar" id="hsk2-bar" style="width: 0%"></div>
                        </div>
                    </div>

                    <div class="progress-item">
                        <div class="progress-header">
                            <span class="progress-name">HSK 3</span>
                            <span class="progress-percent" id="hsk3-percent">0%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar" id="hsk3-bar" style="width: 0%"></div>
                        </div>
                    </div>

                    <div class="progress-item">
                        <div class="progress-header">
                            <span class="progress-name">HSK 4</span>
                            <span class="progress-percent" id="hsk4-percent">0%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar" id="hsk4-bar" style="width: 0%"></div>
                        </div>
                    </div>

                    <div class="progress-item">
                        <div class="progress-header">
                            <span class="progress-name">HSK 5</span>
                            <span class="progress-percent" id="hsk5-percent">0%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar" id="hsk5-bar" style="width: 0%"></div>
                        </div>
                    </div>

                    <div class="progress-item">
                        <div class="progress-header">
                            <span class="progress-name">HSK 6</span>
                            <span class="progress-percent" id="hsk6-percent">0%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar" id="hsk6-bar" style="width: 0%"></div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Preferences Card -->
            <section class="profile-card">
                <h2>Preferences</h2>

                <div class="form-group">
                    <label for="language">Preferred Language</label>
                    <select id="language">
                        <option value="en">English</option>
                        <option value="cn">中文</option>
                        <option value="mixed">Mixed (中文 + English)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="difficulty">Learning Difficulty</label>
                    <select id="difficulty">
                        <option value="beginner">Beginner</option>
                        <option value="intermediate" selected>Intermediate</option>
                        <option value="advanced">Advanced</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="notifications">
                        <input type="checkbox" id="notifications" checked />
                        Enable Notifications
                    </label>
                </div>

                <div class="form-group">
                    <label for="darkMode">
                        <input type="checkbox" id="darkMode" />
                        Dark Mode
                    </label>
                </div>

                <button class="btn btn-secondary" onclick="savePreferences()">Save Preferences</button>
            </section>
        </div>
    </div>
</div>
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
        background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-secondary) 100%);
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

    .profile-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
        gap: var(--spacing-8);
    }

    @media (max-width: 768px) {
        .profile-grid {
            grid-template-columns: 1fr;
        }
    }

    .profile-card {
        background: var(--color-surface);
        border-radius: var(--border-radius-lg);
        padding: var(--spacing-8);
        box-shadow: var(--shadow-md);
        transition: all var(--transition-base);
    }

    .profile-card:hover {
        box-shadow: var(--shadow-lg);
    }

    .profile-card h2 {
        margin-top: 0;
        color: var(--color-primary);
        margin-bottom: var(--spacing-6);
    }

    .stats-list {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: var(--spacing-4);
    }

    .stat-item {
        display: flex;
        align-items: center;
        gap: var(--spacing-4);
        padding: var(--spacing-4);
        background: var(--color-background-light);
        border-radius: var(--border-radius-lg);
    }

    .stat-icon {
        font-size: 32px;
        flex-shrink: 0;
    }

    .stat-details {
        flex: 1;
    }

    .stat-name {
        display: block;
        font-size: var(--font-size-sm);
        color: var(--color-text-secondary);
        margin-bottom: var(--spacing-1);
    }

    .stat-number {
        display: block;
        font-size: var(--font-size-xl);
        font-weight: var(--font-weight-bold);
        color: var(--color-primary);
    }

    .progress-list {
        display: flex;
        flex-direction: column;
        gap: var(--spacing-6);
    }

    .progress-item {
        display: flex;
        flex-direction: column;
        gap: var(--spacing-2);
    }

    .progress-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: var(--spacing-2);
    }

    .progress-name {
        font-weight: var(--font-weight-semibold);
        color: var(--color-text-primary);
    }

    .progress-percent {
        font-weight: var(--font-weight-bold);
        color: var(--color-primary);
    }
</style>
@endpush

@push('scripts')
<script>
    // Load statistics
    async function loadStatistics() {
        // Mock data - replace with actual API calls
        document.getElementById('wordsLearned').textContent = '245';
        document.getElementById('lessonsCompleted').textContent = '12';
        document.getElementById('quizScore').textContent = '82%';
        document.getElementById('streak').textContent = '5 days';
        document.getElementById('studyTime').textContent = '25h';
        document.getElementById('achievements').textContent = '8';

        // HSK Progress
        const hskProgress = [45, 30, 15, 5, 0, 0];
        hskProgress.forEach((progress, index) => {
            const levelNum = index + 1;
            document.getElementById(`hsk${levelNum}-percent`).textContent = `${progress}%`;
            document.getElementById(`hsk${levelNum}-bar`).style.width = `${progress}%`;
        });
    }

    function savePreferences() {
        const prefs = {
            language: document.getElementById('language').value,
            difficulty: document.getElementById('difficulty').value,
            notifications: document.getElementById('notifications').checked,
            darkMode: document.getElementById('darkMode').checked
        };
        localStorage.setItem('userPreferences', JSON.stringify(prefs));
        alert('Preferences saved successfully!');
    }

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        loadStatistics();
    });
</script>
@endpush
