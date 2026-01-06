@extends('layouts.app')

@section('title', 'Practice Writing - Chinese Learning')

@section('content')
<div class="client-container">
    <!-- Navigation Header -->
    @include('client.components.header')

    <!-- Main Content -->
    <div class="client-main practice-main">
        <div class="practice-container">
            <!-- Header -->
            <div class="practice-header">
                <a href="{{ route('client.radicals.index') }}" class="btn btn-back">← Back to Radicals</a>
                <h1>🖌️ Practice Writing</h1>
            </div>

            <!-- Character Selector -->
            <div class="char-selector-section">
                <h2>Select Character to Practice</h2>
                <div class="char-selector" id="charSelector">
                    <!-- Loaded dynamically -->
                    <div class="spinner"></div>
                </div>
            </div>

            <!-- Practice Area -->
            <div class="practice-grid">
                <!-- Left: Reference Character -->
                <div class="practice-card">
                    <div class="card-header">
                        <h3>📖 Reference Character</h3>
                        <button class="btn btn-sm btn-primary" id="animateBtn">▶️ Animate</button>
                    </div>
                    <div class="character-info" id="charInfo">
                        <div class="info-item">
                            <span class="label">Pinyin:</span>
                            <span class="value" id="charPinyin">-</span>
                        </div>
                        <div class="info-item">
                            <span class="label">Meaning:</span>
                            <span class="value" id="charMeaning">-</span>
                        </div>
                        <div class="info-item">
                            <span class="label">Strokes:</span>
                            <span class="value" id="charStrokes">-</span>
                        </div>
                    </div>
                    <div class="canvas-container">
                        <svg id="reference-canvas"></svg>
                    </div>
                </div>

                <!-- Right: User Drawing -->
                <div class="practice-card">
                    <div class="card-header">
                        <h3>✍️ Your Practice</h3>
                        <div class="button-group">
                            <button class="btn btn-sm btn-secondary" id="clearBtn">🗑️ Clear</button>
                            <button class="btn btn-sm btn-info" id="hintBtn">💡 Show Guide</button>
                        </div>
                    </div>
                    <div class="canvas-container" id="user-container">
                        <canvas id="user-canvas" width="400" height="400"></canvas>
                        <svg id="user-guide"></svg>
                    </div>
                    <div class="practice-actions">
                        <button class="btn btn-success btn-lg" id="submitBtn">🎯 Check My Writing</button>
                    </div>
                </div>
            </div>

            <!-- Result Card -->
            <div class="result-card" id="resultCard">
                <h2>📊 Your Score</h2>
                <div class="score-display" id="scoreDisplay">--</div>
                <div class="score-interpretation" id="scoreInterpretation">--</div>
                <div class="score-details">
                    <p>Distance: <span id="scoreDistance">--</span></p>
                </div>
                <button class="btn btn-primary" id="tryAgainBtn">Try Again</button>
            </div>

            <!-- Loading -->
            <div class="loading-overlay" id="loadingOverlay">
                <div class="spinner spinner-lg"></div>
                <p>Processing your writing...</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('client-assets/css/variables.css') }}">
<link rel="stylesheet" href="{{ asset('client-assets/css/base.css') }}">
<link rel="stylesheet" href="{{ asset('client-assets/css/layout.css') }}">
<link rel="stylesheet" href="{{ asset('client-assets/css/practice.css') }}">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/hanzi-writer@3.5.0/dist/hanzi-writer.min.js"></script>
<script src="{{ asset('client-assets/js/practice.js') }}"></script>
@endpush
