@extends('layouts.app')

@section('title', 'HSK Learning - Chinese Learner')

@push('styles')
<link rel="stylesheet" href="{{ asset('client-assets/css/variables.css') }}">
<link rel="stylesheet" href="{{ asset('client-assets/css/base.css') }}">
<link rel="stylesheet" href="{{ asset('client-assets/css/layout.css') }}">
<link rel="stylesheet" href="{{ asset('client-assets/css/hsk.css') }}">
@endpush

@section('content')
<div class="client-container hsk-page">
    @include('client.components.header')

    <main class="client-main" style="background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); min-height: 100vh;">
        <!-- Page Header -->
        <section class="page-header mb-5 text-center">
            <div class="header-icon mb-3">
                <span style="font-size: 4rem;">📚</span>
            </div>
            <h1 class="display-4 fw-bold mb-3" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">HSK Learning</h1>
            <p class="lead text-secondary mb-0">Choose your HSK level to start learning Chinese vocabulary</p>
        </section>

        <!-- HSK Levels Horizontal Scroll -->
        <div class="hsk-levels-scroll mb-5">
            <div class="levels-container">
                @foreach($hskLevels as $index => $hsk)
                <a href="{{ route('client.hsk.level', $hsk['level']) }}" class="text-decoration-none">
                    <div class="hsk-level-card" data-level="{{ $hsk['level'] }}">
                        <div class="card-gradient-bg"></div>
                        <div class="card-content">
                            <div class="level-number">{{ $hsk['level'] }}</div>
                            <div class="card-info">
                                <h3 class="level-title">{{ $hsk['name'] }}</h3>
                                <p class="level-desc">{{ $hsk['description'] }}</p>
                            </div>
                            <div class="card-footer-custom">
                                <span class="badge-custom">
                                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                        <path d="M1 2.828c.885-.37 2.154-.769 3.388-.893 1.33-.134 2.458.063 3.112.752v9.746c-.935-.53-2.12-.603-3.213-.493-1.18.12-2.37.461-3.287.811V2.828zm7.5-.141c.654-.689 1.782-.886 3.112-.752 1.234.124 2.503.523 3.388.893v9.923c-.918-.35-2.107-.692-3.287-.81-1.094-.111-2.278-.039-3.213.492V2.687zM8 1.783C7.015.936 5.587.81 4.287.94c-1.514.153-3.042.672-3.994 1.105A.5.5 0 0 0 0 2.5v11a.5.5 0 0 0 .707.455c.882-.4 2.303-.881 3.68-1.02 1.409-.142 2.59.087 3.223.877a.5.5 0 0 0 .78 0c.633-.79 1.814-1.019 3.222-.877 1.378.139 2.8.62 3.681 1.02A.5.5 0 0 0 16 13.5v-11a.5.5 0 0 0-.293-.455c-.952-.433-2.48-.952-3.994-1.105C10.413.809 8.985.936 8 1.783z"/>
                                    </svg>
                                    Vocabulary
                                </span>
                                <span class="arrow-icon">→</span>
                            </div>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
        </div>

        <!-- Info Section -->
        <div class="info-card">
            <div class="info-header">
                <span class="info-icon">ℹ️</span>
                <h4>About HSK</h4>
            </div>
            <div class="info-content">
                <p class="info-intro">
                    <strong>HSK (Hanyu Shuiping Kaoshi)</strong> is the standardized test of Chinese language proficiency for non-native speakers.
                </p>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-badge hsk-beginner">1-2</div>
                        <div class="info-text">
                            <strong>Beginner</strong>
                            <p>Basic daily conversations</p>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-badge hsk-intermediate">3-4</div>
                        <div class="info-text">
                            <strong>Intermediate</strong>
                            <p>More complex topics</p>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-badge hsk-advanced">5-6</div>
                        <div class="info-text">
                            <strong>Advanced</strong>
                            <p>Professional & academic</p>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-badge hsk-mastery">7-9</div>
                        <div class="info-text">
                            <strong>Mastery</strong>
                            <p>Near-native proficiency</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    @include('client.components.footer')
</div>
@endsection
