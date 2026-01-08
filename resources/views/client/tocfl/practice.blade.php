@extends('layouts.app')

@section('title', 'Practice TOCFL ' . $level . ' - Chinese Learner')

@push('styles')
<link rel="stylesheet" href="{{ asset('client-assets/css/variables.css') }}">
<link rel="stylesheet" href="{{ asset('client-assets/css/base.css') }}">
<link rel="stylesheet" href="{{ asset('client-assets/css/layout.css') }}">
<link rel="stylesheet" href="{{ asset('client-assets/css/tocfl-practice.css') }}">
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js"></script>
@endpush

@section('content')
<div class="practice-container">
    <!-- Progress Bar -->
    <div class="progress-header">
        <button class="close-btn" onclick="exitPractice()">✕</button>
        <div class="progress-bar-container">
            <div class="progress-bar" id="progressBar"></div>
        </div>
        <button class="menu-btn">⋮</button>
    </div>

    <!-- Quiz Content -->
    <div class="quiz-content" id="quizContent">
        <!-- Question will be dynamically inserted here -->
    </div>

    <!-- Result Screen (hidden by default) -->
    <div class="result-screen" id="resultScreen" style="display: none;">
        <div class="result-content">
            <div class="result-icon" id="resultIcon">🎉</div>
            <h2 class="result-title" id="resultTitle">Excellent!</h2>
            <div class="result-stats">
                <div class="stat-item">
                    <div class="stat-value" id="correctCount">0</div>
                    <div class="stat-label">Correct</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value" id="wrongCount">0</div>
                    <div class="stat-label">Wrong</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value" id="accuracyPercent">0%</div>
                    <div class="stat-label">Accuracy</div>
                </div>
            </div>
            
            <div class="result-actions">
                @if($hasMore)
                <button class="btn-continue" onclick="continueNext()">
                    Continue (Next 5 words)
                </button>
                @endif
                <button class="btn-back" onclick="backToList()">
                    Back to Vocabulary List
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.practice-container {
    min-height: 100vh;
    background: linear-gradient(135deg, #e0f7fa 0%, #b2ebf2 100%);
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.progress-header {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 16px 24px;
    background: white;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.close-btn, .menu-btn {
    width: 40px;
    height: 40px;
    border: none;
    background: transparent;
    font-size: 1.5rem;
    color: #666;
    cursor: pointer;
    border-radius: 50%;
    transition: all 0.2s;
}

.close-btn:hover, .menu-btn:hover {
    background: #f0f0f0;
}

.progress-bar-container {
    flex: 1;
    height: 16px;
    background: #e0e0e0;
    border-radius: 10px;
    overflow: hidden;
}

.progress-bar {
    height: 100%;
    background: linear-gradient(90deg, #66bb6a 0%, #4caf50 100%);
    border-radius: 10px;
    width: 0%;
    transition: width 0.3s ease;
}

.quiz-content {
    max-width: 800px;
    margin: 20px auto;
    padding: 0 24px;
}

.question-badge {
    display: inline-block;
    padding: 6px 12px;
    background: rgba(102, 187, 106, 0.2);
    color: #4caf50;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    margin-bottom: 12px;
}

.question-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #2d3748;
    margin-bottom: 20px;
    text-align: center;
}

.audio-button {
    width: 90px;
    height: 90px;
    border-radius: 50%;
    border: none;
    background: linear-gradient(135deg, #00acc1 0%, #0097a7 100%);
    color: white;
    font-size: 2.5rem;
    cursor: pointer;
    box-shadow: 0 8px 24px rgba(0, 172, 193, 0.3);
    transition: all 0.3s ease;
    margin: 0 auto 20px;
    display: block;
}

.audio-button:hover {
    transform: scale(1.1);
    box-shadow: 0 12px 32px rgba(0, 172, 193, 0.4);
}

.options-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
    margin-bottom: 20px;
}

.option-card {
    background: white;
    border: 3px solid #e0e0e0;
    border-radius: 16px;
    padding: 16px;
    cursor: pointer;
    transition: all 0.2s;
    min-height: 140px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
}

.option-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    border-color: #00acc1;
}

.option-card.selected {
    border-color: #00acc1;
    background: rgba(0, 172, 193, 0.05);
}

.option-card.correct {
    border-color: #4caf50;
    background: rgba(76, 175, 80, 0.1);
    animation: correctPulse 0.5s ease;
}

.option-card.wrong {
    border-color: #f44336;
    background: rgba(244, 67, 54, 0.1);
    animation: wrongShake 0.5s ease;
}

.option-card.disabled {
    pointer-events: none;
    opacity: 0.6;
}

@keyframes correctPulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}

@keyframes wrongShake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-10px); }
    75% { transform: translateX(10px); }
}

.option-image {
    width: 100%;
    height: 100px;
    object-fit: cover;
    border-radius: 12px;
    margin-bottom: 8px;
}

.option-image-placeholder {
    width: 100%;
    height: 100px;
    background: linear-gradient(135deg, #e0f7fa 0%, #b2ebf2 100%);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.5rem;
    margin-bottom: 8px;
}

.option-text {
    font-family: "Noto Sans TC", "Microsoft JhengHei", sans-serif;
    font-size: 1.5rem;
    font-weight: 700;
    color: #2d3748;
}

.check-button {
    width: 100%;
    max-width: 400px;
    margin: 0 auto;
    display: block;
    padding: 14px 28px;
    background: #4caf50;
    color: white;
    border: none;
    border-radius: 12px;
    font-size: 1rem;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s;
    opacity: 0.5;
    pointer-events: none;
}

.check-button.enabled {
    opacity: 1;
    pointer-events: auto;
}

.check-button.enabled:hover {
    background: #45a049;
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(76, 175, 80, 0.4);
}

.result-screen {
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: calc(100vh - 80px);
    padding: 24px;
}

.result-content {
    background: white;
    border-radius: 24px;
    padding: 48px;
    max-width: 500px;
    width: 100%;
    text-align: center;
    box-shadow: 0 10px 40px rgba(0,0,0,0.1);
}

.result-icon {
    font-size: 5rem;
    margin-bottom: 24px;
    animation: bounceIn 0.6s ease;
}

@keyframes bounceIn {
    0% { transform: scale(0); }
    50% { transform: scale(1.2); }
    100% { transform: scale(1); }
}

.result-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: #2d3748;
    margin-bottom: 32px;
}

.result-stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 24px;
    margin-bottom: 40px;
    padding: 24px;
    background: #f7fafc;
    border-radius: 16px;
}

.stat-item {
    text-align: center;
}

.stat-value {
    font-size: 2.5rem;
    font-weight: 900;
    color: #00acc1;
    margin-bottom: 8px;
}

.stat-label {
    font-size: 0.875rem;
    color: #718096;
    font-weight: 600;
    text-transform: uppercase;
}

.result-actions {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.btn-continue, .btn-back {
    padding: 16px 32px;
    border: none;
    border-radius: 12px;
    font-size: 1.1rem;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-continue {
    background: linear-gradient(135deg, #66bb6a 0%, #4caf50 100%);
    color: white;
}

.btn-continue:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(102, 187, 106, 0.4);
}

.btn-back {
    background: white;
    color: #00acc1;
    border: 2px solid #00acc1;
}

.btn-back:hover {
    background: #00acc1;
    color: white;
}

@media (max-width: 768px) {
    .options-grid {
        grid-template-columns: 1fr;
    }
    
    .question-title {
        font-size: 1.5rem;
    }
    
    .result-content {
        padding: 32px 24px;
    }
}
</style>

<script>
const words = @json($words);
const level = '{{ $level }}';
const hasMore = {{ $hasMore ? 'true' : 'false' }};
const nextStart = {{ $nextStart }};

let questions = [];
let currentQuestionIndex = 0;
let correctAnswers = 0;
let wrongAnswers = 0;
let selectedOption = null;

// Generate questions: 2 per word (meaning + audio)
function generateQuestions() {
    questions = [];
    
    words.forEach(word => {
        // Question 1: Which word means "..."
        questions.push({
            type: 'meaning',
            word: word,
            question: `Which word means: "${word.m_en}"`,
        });
        
        // Question 2: Choose what you hear
        questions.push({
            type: 'audio',
            word: word,
            question: 'Choose what you hear',
        });
    });
    
    // Shuffle questions
    questions = questions.sort(() => Math.random() - 0.5);
}

// Generate image URL
function generateWordImageUrl(word) {
    const hash = CryptoJS.MD5(word).toString();
    return `https://work.lehutv04.xyz/${hash}_h.jpg`;
}

// Get random wrong options
function getWrongOptions(correctWord) {
    const options = [];
    const availableWords = words.filter(w => w.w !== correctWord.w);
    
    while (options.length < 3 && availableWords.length > 0) {
        const randomIndex = Math.floor(Math.random() * availableWords.length);
        options.push(availableWords.splice(randomIndex, 1)[0]);
    }
    
    return options;
}

// Render current question
function renderQuestion() {
    if (currentQuestionIndex >= questions.length) {
        showResults();
        return;
    }
    
    const question = questions[currentQuestionIndex];
    const correctWord = question.word;
    const wrongOptions = getWrongOptions(correctWord);
    const allOptions = [correctWord, ...wrongOptions].sort(() => Math.random() - 0.5);
    
    selectedOption = null;
    
    let html = `
        <div class="question-badge">New</div>
        <h2 class="question-title">${question.question}</h2>
    `;
    
    if (question.type === 'audio') {
        html += `
            <button class="audio-button" onclick="playAudio('${correctWord.w}')">
                🔊
            </button>
        `;
    }
    
    html += '<div class="options-grid">';
    
    allOptions.forEach((option, index) => {
        const imageUrl = generateWordImageUrl(option.w);
        html += `
            <div class="option-card" onclick="selectOption(${index}, '${option.w}', '${correctWord.w}')">
                <img src="${imageUrl}" 
                     alt="${option.w}" 
                     class="option-image"
                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                >
                <div class="option-image-placeholder" style="display:none;">📷</div>
                <div class="option-text">${option.w}</div>
            </div>
        `;
    });
    
    html += '</div>';
    html += '<button class="check-button" id="checkButton" onclick="checkAnswer()">CHECK</button>';
    
    document.getElementById('quizContent').innerHTML = html;
    
    // Update progress
    updateProgress();
}

function selectOption(index, selected, correct) {
    if (selectedOption !== null) return; // Already selected
    
    const cards = document.querySelectorAll('.option-card');
    cards.forEach(card => card.classList.remove('selected'));
    cards[index].classList.add('selected');
    
    selectedOption = { index, selected, correct };
    
    document.getElementById('checkButton').classList.add('enabled');
}

function checkAnswer() {
    if (!selectedOption) return;
    
    const cards = document.querySelectorAll('.option-card');
    cards.forEach(card => card.classList.add('disabled'));
    
    const isCorrect = selectedOption.selected === selectedOption.correct;
    
    if (isCorrect) {
        cards[selectedOption.index].classList.add('correct');
        correctAnswers++;
    } else {
        cards[selectedOption.index].classList.add('wrong');
        wrongAnswers++;
        
        // Highlight correct answer
        cards.forEach((card, idx) => {
            if (card.querySelector('.option-text').textContent === selectedOption.correct) {
                card.classList.add('correct');
            }
        });
    }
    
    // Wait then go to next question
    setTimeout(() => {
        currentQuestionIndex++;
        renderQuestion();
    }, 1500);
}

function updateProgress() {
    const progress = ((currentQuestionIndex + 1) / questions.length) * 100;
    document.getElementById('progressBar').style.width = progress + '%';
}

function showResults() {
    const totalQuestions = questions.length;
    const accuracy = Math.round((correctAnswers / totalQuestions) * 100);
    
    document.getElementById('quizContent').style.display = 'none';
    document.getElementById('resultScreen').style.display = 'flex';
    
    document.getElementById('correctCount').textContent = correctAnswers;
    document.getElementById('wrongCount').textContent = wrongAnswers;
    document.getElementById('accuracyPercent').textContent = accuracy + '%';
    
    if (accuracy >= 80) {
        document.getElementById('resultIcon').textContent = '🎉';
        document.getElementById('resultTitle').textContent = 'Excellent!';
    } else if (accuracy >= 60) {
        document.getElementById('resultIcon').textContent = '👍';
        document.getElementById('resultTitle').textContent = 'Good Job!';
    } else {
        document.getElementById('resultIcon').textContent = '💪';
        document.getElementById('resultTitle').textContent = 'Keep Practicing!';
    }
}

function playAudio(text) {
    if ('speechSynthesis' in window) {
        const utterance = new SpeechSynthesisUtterance(text);
        utterance.lang = 'zh-TW';
        utterance.rate = 0.8;
        speechSynthesis.speak(utterance);
    }
}

function continueNext() {
    window.location.href = '{{ route("client.tocfl.practice", $level) }}?start=' + nextStart;
}

function backToList() {
    window.location.href = '{{ route("client.tocfl.level", $level) }}';
}

function exitPractice() {
    if (confirm('Are you sure you want to exit? Your progress will be lost.')) {
        backToList();
    }
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    generateQuestions();
    renderQuestion();
});
</script>

@endsection

@include('client.components.footer')
