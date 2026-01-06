@extends('layouts.app')

@section('title', 'Learn Vocabulary - Chinese Learning')

@section('content')
<div class="client-container vocabulary-learn-page">
    <!-- Navigation Header -->
    @include('client.components.header')

    <!-- Main Content -->
    <div class="client-main">
        <!-- Header with topic info and progress -->
        <div class="learn-header">
            <div class="header-left">
                <a href="{{ route('client.vocabulary.index') }}" class="back-btn">
                    ← Back
                </a>
                <div class="topic-info">
                    <h1 class="topic-name" id="topicName">Loading...</h1>
                    <div class="topic-meta">
                        <span id="vocabCount">0 words</span>
                        <span id="levelBadge">HSK 1</span>
                    </div>
                </div>
            </div>
            <div class="header-right">
                <div class="progress-bar">
                    <div class="progress-fill" id="progressFill"></div>
                </div>
                <span class="progress-text" id="progressText">0/0</span>
            </div>
        </div>

        <!-- Mode Navigation -->
        <div class="mode-nav">
            <button class="mode-btn active" data-mode="review">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z" fill="currentColor"/>
                </svg>
                <span>Review</span>
            </button>
            <button class="mode-btn" data-mode="flashcards">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <rect x="3" y="5" width="18" height="14" rx="2" stroke="currentColor" stroke-width="2" fill="none"/>
                    <line x1="3" y1="10" x2="21" y2="10" stroke="currentColor" stroke-width="2"/>
                </svg>
                <span>Flashcards</span>
            </button>
            <button class="mode-btn" data-mode="spelling">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z" fill="currentColor"/>
                </svg>
                <span>Spelling</span>
            </button>
            <button class="mode-btn" data-mode="quiz">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" fill="currentColor"/>
                </svg>
                <span>Quiz</span>
            </button>
        </div>

        <!-- Learning Content Area -->
        <div class="learn-content">
            <!-- Review Mode -->
            <div class="mode-content active" id="reviewMode">
                <div class="vocab-card">
                    <div class="card-number" id="cardNumber">1 / 10</div>
                    <div class="vocab-image" id="reviewImageContainer" style="display: none;">
                        <img id="reviewImage" src="" alt="Vocabulary illustration">
                    </div>
                    <div class="vocab-hanzi" id="reviewHanzi">你好</div>
                    <div class="vocab-pinyin" id="reviewPinyin">nǐ hǎo</div>
                    <div class="vocab-meaning" id="reviewMeaning">Hello</div>
                    <div class="vocab-example" id="reviewExample">
                        <div class="example-zh">你好！很高兴见到你。</div>
                        <div class="example-en">Hello! Nice to meet you.</div>
                    </div>
                </div>
                <div class="card-actions">
                    <button class="action-btn btn-prev" id="prevBtn">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2"/>
                        </svg>
                        Previous
                    </button>
                    <button class="action-btn btn-next" id="nextBtn">
                        Next
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path d="M9 18l6-6-6-6" stroke="currentColor" stroke-width="2"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Flashcards Mode -->
            <div class="mode-content" id="flashcardsMode">
                <div class="flashcard" id="flashcard">
                    <div class="flashcard-inner">
                        <div class="flashcard-front">
                            <div class="card-hint">Click to reveal</div>
                            <div class="flashcard-hanzi" id="flashcardHanzi">你好</div>
                            <div class="flashcard-pinyin" id="flashcardPinyin">nǐ hǎo</div>
                        </div>
                        <div class="flashcard-back">
                            <div class="flashcard-image" id="flashcardImageContainer" style="display: none;">
                                <img id="flashcardImage" src="" alt="Vocabulary illustration">
                            </div>
                            <div class="flashcard-meaning" id="flashcardMeaning">Hello</div>
                            <div class="flashcard-example" id="flashcardExample">你好！很高兴见到你。</div>
                        </div>
                    </div>
                </div>
                <div class="flashcard-actions">
                    <button class="flashcard-btn btn-dont-know" id="dontKnowBtn">
                        ❌ Don't Know
                    </button>
                    <button class="flashcard-btn btn-know" id="knowBtn">
                        ✅ Know It
                    </button>
                </div>
            </div>

            <!-- Spelling Mode -->
            <div class="mode-content" id="spellingMode">
                <div class="spelling-card">
                    <div class="spelling-audio">
                        <button class="audio-btn" id="playAudioBtn">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none">
                                <path d="M3 9v6h4l5 5V4L7 9H3zm13.5 3c0-1.77-1.02-3.29-2.5-4.03v8.05c1.48-.73 2.5-2.25 2.5-4.02z" fill="currentColor"/>
                            </svg>
                        </button>
                        <p class="audio-instruction">Listen and type what you hear</p>
                    </div>
                    <div class="spelling-hint" id="spellingHint">
                        <span class="hint-label">Meaning:</span>
                        <span id="spellingMeaning">Hello</span>
                    </div>
                    <input type="text" class="spelling-input" id="spellingInput" placeholder="Type the pinyin here..." autocomplete="off">
                    <div class="spelling-feedback" id="spellingFeedback"></div>
                    <button class="check-btn" id="checkSpellingBtn">Check Answer</button>
                </div>
            </div>

            <!-- Quiz Mode -->
            <div class="mode-content" id="quizMode">
                <div class="quiz-card">
                    <div class="quiz-question">
                        <div class="question-text" id="quizQuestion">What does "你好" mean?</div>
                        <div class="quiz-hanzi" id="quizHanzi">你好</div>
                    </div>
                    <div class="quiz-options" id="quizOptions">
                        <button class="quiz-option">Hello</button>
                        <button class="quiz-option">Goodbye</button>
                        <button class="quiz-option">Thank you</button>
                        <button class="quiz-option">Sorry</button>
                    </div>
                    <div class="quiz-feedback" id="quizFeedback"></div>
                    <button class="next-question-btn hidden" id="nextQuestionBtn">Next Question</button>
                </div>
                <div class="quiz-score" id="quizScore">
                    <span>Score: <strong id="scoreValue">0/0</strong></span>
                </div>
            </div>
        </div>

        <!-- Completion Modal -->
        <div class="completion-modal hidden" id="completionModal">
            <div class="modal-content">
                <div class="modal-icon">🎉</div>
                <h2>Congratulations!</h2>
                <p id="completionMessage">You've completed this lesson!</p>
                <div class="modal-stats" id="modalStats"></div>
                <div class="modal-actions">
                    <button class="modal-btn btn-restart" id="restartBtn">Restart Lesson</button>
                    <a href="{{ route('client.vocabulary.index') }}" class="modal-btn btn-home">Back to Topics</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('client-assets/css/variables.css') }}">
<link rel="stylesheet" href="{{ asset('client-assets/css/base.css') }}">
<link rel="stylesheet" href="{{ asset('client-assets/css/layout.css') }}">
<link rel="stylesheet" href="{{ asset('client-assets/css/vocabulary-learn.css') }}">
@endpush

@push('scripts')
<script>
    const topicId = {{ $topicId ?? 'null' }};
    const level = "{{ $level ?? 'HSK1' }}";
</script>
<script src="{{ asset('client-assets/js/vocabulary-learn.js') }}"></script>
@endpush
