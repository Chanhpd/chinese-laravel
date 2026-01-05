@extends('layouts.app')

@section('title', 'Chinese Learning App - Welcome')

@section('content')
<div class="container">
    <div class="auth-card">
        <!-- Left Side - Desktop Only -->
        <div class="auth-left">
            <div class="auth-left-content">
                <div class="chinese-big">学中文</div>
                <h1>Chinese Learning App</h1>
                <p>Master Mandarin Chinese with our modern, interactive platform designed for learners of all levels</p>
                
                <div class="auth-left-features">
                    <div class="auth-left-feature">
                        <div class="icon">📚</div>
                        <div class="text">
                            <h3>HSK Vocabulary</h3>
                            <p>Comprehensive word lists for all levels</p>
                        </div>
                    </div>
                    <div class="auth-left-feature">
                        <div class="icon">✍️</div>
                        <div class="text">
                            <h3>Character Writing</h3>
                            <p>Learn stroke order and practice</p>
                        </div>
                    </div>
                    <div class="auth-left-feature">
                        <div class="icon">🎯</div>
                        <div class="text">
                            <h3>Interactive Tests</h3>
                            <p>Track your progress with quizzes</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side - Form -->
        <div class="auth-right">
            <div class="auth-right-content">
                <div class="auth-header">
                    <div class="chinese-decoration">学中文</div>
                    <h1>🇨🇳 Chinese Learning App</h1>
                    <p>Master Chinese with Modern Learning Methods</p>
                </div>

                <div class="welcome-section">
                    <div class="chinese-calligraphy">欢迎</div>
                    <h2>Welcome!</h2>
                    <p class="subtitle">Start your Chinese learning journey today</p>
                    
                    <div class="action-buttons">
                        <a href="{{ route('client.login') }}" class="btn btn-primary">
                            <span class="btn-icon">🔐</span>
                            Sign In
                        </a>
                        <a href="{{ route('client.register') }}" class="btn btn-secondary">
                            <span class="btn-icon">✨</span>
                            Create Account
                        </a>
                    </div>
                    
                    <div class="features-preview">
                        <div class="feature-item">
                            <span class="feature-emoji">📚</span>
                            <span>HSK Vocabulary</span>
                        </div>
                        <div class="feature-item">
                            <span class="feature-emoji">✍️</span>
                            <span>Character Writing</span>
                        </div>
                        <div class="feature-item">
                            <span class="feature-emoji">🎯</span>
                            <span>Practice Tests</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Redirect if already logged in
    if (auth.api.isAuthenticated()) {
        window.location.href = '{{ route('client.home') }}';
    }
</script>
@endpush
