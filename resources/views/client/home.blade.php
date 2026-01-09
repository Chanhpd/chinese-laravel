@extends('layouts.app')

@section('title', 'Dashboard - Learn Chinese')

@push('styles')
<link rel="stylesheet" href="{{ asset('client-assets/css/variables.css') }}">
<link rel="stylesheet" href="{{ asset('client-assets/css/base.css') }}">
<link rel="stylesheet" href="{{ asset('client-assets/css/layout.css') }}">
<link rel="stylesheet" href="{{ asset('client-assets/css/home.css') }}">
@endpush

@section('content')
<div class="client-container">
    <!-- Navigation Header -->
    @include('client.components.header')

    <!-- Main Content -->
    <main class="client-main home-main">
        <!-- Hero Section -->
        <section class="hero-section">
            <div class="hero-content">
                <h1 class="hero-title">Master Chinese Language</h1>
                <p class="hero-subtitle">Learn characters, vocabulary, and practice with AI - Your complete Chinese learning platform</p>
            </div>
        </section>

        <!-- Learning Modes Section -->
        <section class="learning-modes-section">
            <div class="section-header">
                <h2 class="section-title">Choose Your Learning Path</h2>
                <p class="section-description">Multiple ways to practice and improve your Chinese skills</p>
            </div>

            <div class="learning-modes-grid">
                <!-- Characters Mode -->
                <a href="{{ route('client.radicals.index') }}" class="mode-card card-characters">
                    <div class="mode-icon">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M4 7h16M4 12h16M4 17h10"/>
                        </svg>
                    </div>
                    <h3 class="mode-title">Characters</h3>
                    <p class="mode-description">Learn HSK radicals and Chinese characters step by step</p>
                    <div class="mode-badge">150+ Radicals</div>
                </a>

                <!-- Vocabulary Mode -->
                <a href="{{ route('client.vocabulary.index') }}" class="mode-card card-vocabulary">
                    <div class="mode-icon">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                        </svg>
                    </div>
                    <h3 class="mode-title">Vocabulary</h3>
                    <p class="mode-description">Build your word bank with context and examples</p>
                    <div class="mode-badge">1000+ Words</div>
                </a>

                <!-- HSK Mode -->
                <a href="#" class="mode-card card-hsk">
                    <div class="mode-icon">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <polyline points="14 2 14 8 20 8"/>
                            <line x1="16" y1="13" x2="8" y2="13"/>
                            <line x1="16" y1="17" x2="8" y2="17"/>
                            <polyline points="10 9 9 9 8 9"/>
                        </svg>
                    </div>
                    <h3 class="mode-title">HSK</h3>
                    <p class="mode-description">Prepare for HSK exams with practice tests</p>
                    <div class="mode-badge">HSK 1-6</div>
                </a>

                <!-- TOCFL Mode -->
                <a href="#" class="mode-card card-tocfl">
                    <div class="mode-icon">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 2L2 7l10 5 10-5-10-5z"/>
                            <path d="M2 17l10 5 10-5M2 12l10 5 10-5"/>
                        </svg>
                    </div>
                    <h3 class="mode-title">TOCFL</h3>
                    <p class="mode-description">Practice for TOCFL certification tests</p>
                    <div class="mode-badge">All Levels</div>
                </a>

                <!-- AI Chat Mode -->
                <a href="{{ route('client.chat') }}" class="mode-card card-chat">
                    <div class="mode-icon">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                            <path d="M8 10h.01M12 10h.01M16 10h.01"/>
                        </svg>
                    </div>
                    <h3 class="mode-title">AI Chat</h3>
                    <p class="mode-description">Practice conversation with AI language assistant</p>
                    <div class="mode-badge">24/7 Available</div>
                </a>
            </div>
        </section>

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
    // Add smooth scroll behavior for better UX
    document.addEventListener('DOMContentLoaded', function() {
        // Add animation on scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        document.querySelectorAll('.mode-card').forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'all 0.5s ease-out';
            observer.observe(card);
        });
    });
</script>
@endpush
