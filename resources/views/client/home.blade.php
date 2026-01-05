@extends('layouts.app')

@section('title', 'Trang chủ - Chinese Learning App')

@section('content')
<div class="container">
    <div class="home-container">
        <div class="home-header">
            <h1>🇨🇳 Chinese Learning App</h1>
            
            <div class="user-info">
                <div class="user-avatar" id="userAvatar">{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}</div>
                <div class="user-details">
                    <h3 id="userName">{{ auth()->user()->name ?? 'User Name' }}</h3>
                    <p id="userEmail">{{ auth()->user()->email ?? 'user@example.com' }}</p>
                </div>
                <form action="{{ route('client.logout') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn-logout">Sign Out</button>
                </form>
            </div>
        </div>

        <div class="home-content">
            <div class="chinese-calligraphy-large">学习中文</div>
            <h2>Welcome to Chinese Learning App! 🎉</h2>
            <p>
                You've successfully signed in. Start your journey to master Chinese today.
                Explore our comprehensive learning features below.
            </p>

            <div class="features-grid">
                <div class="feature-card">
                    <h3>📚 Vocabulary Learning</h3>
                    <p>Rich vocabulary system organized by topics with pinyin and example sentences.</p>
                </div>

                <div class="feature-card">
                    <h3>✍️ Character Writing</h3>
                    <p>Learn to write radicals and Chinese characters with stroke-by-stroke guidance.</p>
                </div>

                <div class="feature-card">
                    <h3>🎯 Level Assessment</h3>
                    <p>Take tests to evaluate and track your learning progress systematically.</p>
                </div>

                <div class="feature-card">
                    <h3>💬 AI Chatbot</h3>
                    <p>Practice Chinese conversation with our intelligent AI chatbot assistant.</p>
                </div>

                <div class="feature-card">
                    <h3>📊 Progress Tracking</h3>
                    <p>View detailed learning statistics and monitor your personal achievements.</p>
                </div>

                <div class="feature-card">
                    <h3>⭐ Streak & Rewards</h3>
                    <p>Maintain daily learning streaks and earn rewards for your dedication.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Initialize home page
    auth.initHomePage();
</script>
@endpush
