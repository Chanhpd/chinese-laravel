@extends('layouts.app')

@section('title', 'TOCFL ' . $level . ' Vocabulary - Chinese Learner')

@push('styles')
<link rel="stylesheet" href="{{ asset('client-assets/css/variables.css') }}">
<link rel="stylesheet" href="{{ asset('client-assets/css/base.css') }}">
<link rel="stylesheet" href="{{ asset('client-assets/css/layout.css') }}">
<link rel="stylesheet" href="{{ asset('client-assets/css/tocfl-level.css') }}">
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js"></script>
@endpush

@section('content')
<div class="client-container tocfl-level-page">
    @include('client.components.header')

    <main class="client-main">
        <!-- Back Button -->
        <div class="mb-4">
            <a href="{{ route('client.tocfl.index') }}" class="back-button">
                <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
                </svg>
                Back to TOCFL Levels
            </a>
        </div>

        <!-- Page Header -->
        <section class="page-header mb-4">
            <div class="text-center">
                <h1 class="display-5 fw-bold mb-2">🇹🇼 Vocabulary TOCFL {{ $level }}</h1>
                <p class="text-muted mb-0">{{ $totalWords }} Words (Traditional Chinese)</p>
            </div>
        </section>

        <!-- Mode Buttons Section -->
        <div class="mode-buttons-section">
            <div class="mode-buttons-grid">
                <a href="{{ route('client.tocfl.practice', $level) }}" class="mode-button">
                    <span class="icon">📝</span>
                    <span>Practice</span>
                </a>
                <a href="{{ route('client.tocfl.writing', ['level' => $level, 'page' => $currentPage]) }}" class="mode-button">
                    <span class="icon">✏️</span>
                    <span>Writing practice</span>
                </a>
                <div class="mode-button" onclick="alert('Flashcard mode coming soon!')">
                    <span class="icon">🃏</span>
                    <span>Flashcard</span>
                </div>
                <div class="mode-button" onclick="alert('SRS mode coming soon!')">
                    <span class="icon">🧠</span>
                    <span>SRS</span>
                </div>
                <div class="mode-button" onclick="window.location.href='{{ route('client.tocfl.level', ['level' => $level]) }}?saved=1'">
                    <span class="icon">📖</span>
                    <span>Notebook</span>
                </div>
            </div>
        </div>

        <!-- Auto-play Controls -->
        <div class="autoplay-controls">
            <button class="autoplay-btn" id="autoplayBtn" onclick="toggleAutoplay()">
                <svg width="20" height="20" fill="currentColor" viewBox="0 0 16 16" id="playIcon">
                    <path d="m11.596 8.697-6.363 3.692c-.54.313-1.233-.066-1.233-.697V4.308c0-.63.692-1.01 1.233-.696l6.363 3.692a.802.802 0 0 1 0 1.393z"/>
                </svg>
                <svg width="20" height="20" fill="currentColor" viewBox="0 0 16 16" id="pauseIcon" style="display:none;">
                    <path d="M5.5 3.5A1.5 1.5 0 0 1 7 5v6a1.5 1.5 0 0 1-3 0V5a1.5 1.5 0 0 1 1.5-1.5zm5 0A1.5 1.5 0 0 1 12 5v6a1.5 1.5 0 0 1-3 0V5a1.5 1.5 0 0 1 1.5-1.5z"/>
                </svg>
                <span id="autoplayText">Auto play</span>
            </button>
            <span class="autoplay-progress" id="autoplayProgress">{{ ($currentPage - 1) * $perPage + 1 }}/{{ $totalWords }}</span>
        </div>

        <!-- Vocabulary Grid -->
        <div class="vocab-grid" id="vocabGrid">
            @foreach($words as $index => $word)
            <div class="vocab-card-item" data-word="{{ json_encode($word) }}" data-index="{{ $index }}">
                <div class="card-number">{{ ($currentPage - 1) * $perPage + $index + 1 }}</div>
                <div class="card-image">
                    <img src="https://work.lehutv04.xyz/{{ md5($word['w']) }}_h.jpg" 
                         alt="{{ $word['w'] }}"
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                    >
                    <div class="no-image" style="display:none;">📷</div>
                </div>
                <div class="card-body">
                    <div class="chinese-char">{{ $word['w'] }}</div>
                    <div class="pinyin-text">
                        {{ $word['p'] }}
                        <button class="audio-btn" onclick="playAudio('{{ $word['w'] }}')">🔊</button>
                    </div>
                    <div class="meaning-text">{{ $word['m_en'] ?? 'The most comm...' }}</div>
                    <div class="card-actions">
                        <button class="action-btn learned" onclick="markAsLearned({{ ($currentPage - 1) * $perPage + $index }})" title="Mark as learned">
                            <svg width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.267.267 0 0 1 .02-.022z"/>
                            </svg>
                            ✓
                        </button>
                        <button class="action-btn details" onclick="showDetails({{ $index }})" title="View details">
                            <svg width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                <path d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/>
                            </svg>
                            ⓘ
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination Controls -->
        <div class="pagination-controls">
            <div class="pagination-info">
                Page {{ $currentPage }} of {{ $totalPages }} ({{ ($currentPage - 1) * $perPage + 1 }}-{{ min($currentPage * $perPage, $totalWords) }} of {{ $totalWords }} words)
            </div>
            <div class="pagination-nav">
                @if($currentPage > 1)
                <a href="{{ route('client.tocfl.level', ['level' => $level, 'page' => $currentPage - 1]) }}" class="page-btn">
                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M12 8a.5.5 0 0 1-.5.5H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5a.5.5 0 0 1 .5.5z"/>
                    </svg>
                    Previous
                </a>
                @else
                <button class="page-btn" disabled>
                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M12 8a.5.5 0 0 1-.5.5H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5a.5.5 0 0 1 .5.5z"/>
                    </svg>
                    Previous
                </button>
                @endif
                
                <span class="current-page">{{ $currentPage }}/{{ $totalPages }}</span>
                
                @if($currentPage < $totalPages)
                <a href="{{ route('client.tocfl.level', ['level' => $level, 'page' => $currentPage + 1]) }}" class="page-btn">
                    Next
                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M4 8a.5.5 0 0 1 .5-.5h5.793L8.146 5.354a.5.5 0 1 1 .708-.708l3 3a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708-.708L10.293 8.5H4.5A.5.5 0 0 1 4 8z"/>
                    </svg>
                </a>
                @else
                <button class="page-btn" disabled>
                    Next
                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M4 8a.5.5 0 0 1 .5-.5h5.793L8.146 5.354a.5.5 0 1 1 .708-.708l3 3a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708-.708L10.293 8.5H4.5A.5.5 0 0 1 4 8z"/>
                    </svg>
                </button>
                @endif
            </div>
        </div>
    </main>
</div>

<!-- Word Detail Modal -->
<div class="modal fade word-modal" id="wordDetailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">📖 Word Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="wordDetailContent">
                <!-- Content will be populated by JavaScript -->
            </div>
        </div>
    </div>
</div>

<style>
.chinese-char {
    font-family: "Noto Sans TC", "Microsoft JhengHei", sans-serif;
}

.sticky-top {
    position: sticky;
    top: 0;
    z-index: 10;
    background: #f8f9fa;
}

.vocab-card-item.playing {
    border: 4px solid #66bb6a !important;
    box-shadow: 0 0 0 4px rgba(102, 187, 106, 0.3), 0 12px 30px rgba(0, 0, 0, 0.15) !important;
    transform: translateY(-8px) scale(1.05) !important;
    animation: pulse-border 1.5s ease-in-out infinite;
}

@keyframes pulse-border {
    0%, 100% {
        box-shadow: 0 0 0 4px rgba(102, 187, 106, 0.3), 0 12px 30px rgba(0, 0, 0, 0.15);
    }
    50% {
        box-shadow: 0 0 0 8px rgba(102, 187, 106, 0.4), 0 16px 40px rgba(0, 0, 0, 0.2);
    }
}
</style>

<script>
const words = @json($words);
const currentPage = {{ $currentPage }};
const perPage = {{ $perPage }};
const level = '{{ $level }}';
let learnedWords = JSON.parse(localStorage.getItem('tocfl_' + level + '_learned') || '[]');
let autoplayActive = false;
let autoplayInterval = null;
let currentAutoplayIndex = 0;

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    // Mark previously learned words
    learnedWords.forEach(globalIndex => {
        const pageStartIndex = (currentPage - 1) * perPage;
        const localIndex = globalIndex - pageStartIndex;
        if (localIndex >= 0 && localIndex < words.length) {
            const card = document.querySelectorAll('.vocab-card-item')[localIndex];
            if (card) {
                card.querySelector('.action-btn.learned').classList.add('active');
            }
        }
    });
});

function markAsLearned(globalIndex) {
    const pageStartIndex = (currentPage - 1) * perPage;
    const localIndex = globalIndex - pageStartIndex;
    const card = document.querySelectorAll('.vocab-card-item')[localIndex];
    const btn = card.querySelector('.action-btn.learned');
    
    if (learnedWords.includes(globalIndex)) {
        learnedWords = learnedWords.filter(i => i !== globalIndex);
        btn.classList.remove('active');
    } else {
        learnedWords.push(globalIndex);
        btn.classList.add('active');
    }
    
    localStorage.setItem('tocfl_' + level + '_learned', JSON.stringify(learnedWords));
}

function showDetails(index) {
    const word = words[index];
    const hash = CryptoJS.MD5(word.w).toString();
    const imageUrl = `https://work.lehutv04.xyz/${hash}_h.jpg`;
    
    const content = `
        <div class="row">
            <div class="col-md-6 text-center mb-4">
                <h2 class="display-3 fw-bold" style="color: #00acc1;">${word.w}</h2>
                <h4 class="text-primary mb-3">${word.p}</h4>
                <button class="btn btn-info" onclick="playAudio('${word.w}')">
                    🔊 Play Audio
                </button>
            </div>
            <div class="col-md-6 mb-4">
                <img src="${imageUrl}" 
                     alt="${word.w}" 
                     class="img-fluid rounded shadow"
                     style="max-height: 300px; width: 100%; object-fit: cover;"
                     onerror="this.style.display='none'; this.nextElementSibling.style.display='block';"
                >
                <div style="display: none; padding: 60px; text-align: center; background: #f0f0f0; border-radius: 8px;">
                    <p class="text-muted">📷 Image not available</p>
                </div>
            </div>
            <div class="col-12">
                <h5 class="fw-bold mb-3">Meaning:</h5>
                <div class="alert alert-info mb-0">
                    <p class="mb-0 fs-5">${word.m_en || 'No meaning available'}</p>
                </div>
            </div>
        </div>
    `;
    
    document.getElementById('wordDetailContent').innerHTML = content;
    const modal = new bootstrap.Modal(document.getElementById('wordDetailModal'));
    modal.show();
}

function playAudio(text) {
    // Use Web Speech API for Traditional Chinese
    if ('speechSynthesis' in window) {
        const utterance = new SpeechSynthesisUtterance(text);
        utterance.lang = 'zh-TW'; // Traditional Chinese
        utterance.rate = 0.8;
        speechSynthesis.speak(utterance);
    } else {
        alert('Text-to-speech is not supported in your browser');
    }
}

function toggleAutoplay() {
    autoplayActive = !autoplayActive;
    const btn = document.getElementById('autoplayBtn');
    const playIcon = document.getElementById('playIcon');
    const pauseIcon = document.getElementById('pauseIcon');
    const text = document.getElementById('autoplayText');
    
    if (autoplayActive) {
        btn.classList.add('playing');
        playIcon.style.display = 'none';
        pauseIcon.style.display = 'block';
        text.textContent = 'Stop';
        startAutoplay();
    } else {
        btn.classList.remove('playing');
        playIcon.style.display = 'block';
        pauseIcon.style.display = 'none';
        text.textContent = 'Auto play';
        stopAutoplay();
    }
}

function startAutoplay() {
    currentAutoplayIndex = 0;
    playCurrentWord();
    
    autoplayInterval = setInterval(() => {
        currentAutoplayIndex++;
        if (currentAutoplayIndex >= words.length) {
            // Go to next page if available
            const nextPageLink = document.querySelector('.pagination-nav a[href*="page=' + (currentPage + 1) + '"]');
            if (nextPageLink) {
                window.location.href = nextPageLink.href;
            } else {
                stopAutoplay();
                toggleAutoplay();
            }
        } else {
            playCurrentWord();
        }
    }, 3000); // 3 seconds per word
}

function stopAutoplay() {
    if (autoplayInterval) {
        clearInterval(autoplayInterval);
        autoplayInterval = null;
    }
}

function playCurrentWord() {
    if (currentAutoplayIndex < words.length) {
        const word = words[currentAutoplayIndex];
        const globalIndex = (currentPage - 1) * perPage + currentAutoplayIndex + 1;
        document.getElementById('autoplayProgress').textContent = globalIndex + '/{{ $totalWords }}';
        
        // Scroll to current card and highlight it
        const cards = document.querySelectorAll('.vocab-card-item');
        if (cards[currentAutoplayIndex]) {
            cards[currentAutoplayIndex].scrollIntoView({ behavior: 'smooth', block: 'center' });
            // Remove previous highlights
            cards.forEach(c => {
                c.classList.remove('playing');
                c.style.transform = '';
            });
            // Add highlight to current card
            cards[currentAutoplayIndex].classList.add('playing');
        }
        
        playAudio(word.w);
    }
}
</script>

    @include('client.components.footer')
</div>
@endsection
