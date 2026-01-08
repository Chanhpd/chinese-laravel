@extends('layouts.app')

@section('title', 'Writing Practice TOCFL ' . $level . ' - Chinese Learner')

@push('styles')
<link rel="stylesheet" href="{{ asset('client-assets/css/variables.css') }}">
<link rel="stylesheet" href="{{ asset('client-assets/css/base.css') }}">
<link rel="stylesheet" href="{{ asset('client-assets/css/layout.css') }}">
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js"></script>
@endpush

@section('content')
<div class="writing-practice-container">
    @include('client.components.header')

    <main class="writing-main">
        <!-- Back Button -->
        <div class="mb-3">
            <a href="{{ route('client.tocfl.level', ['level' => $level, 'page' => $currentPage]) }}" class="back-btn">
                <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
                </svg>
                Back
            </a>
        </div>

        <h1 class="page-title">Writing practice</h1>

        <!-- Current Word Display -->
        <div class="current-word-card">
            <div class="word-display">
                <span class="chinese-char" id="currentChar">别</span>
                <span class="pinyin-text" id="currentPinyin">[bié]</span>
                <button class="audio-btn-small" onclick="playCurrentAudio()">
                    <svg width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M11.536 14.01A8.473 8.473 0 0 0 14.026 8a8.473 8.473 0 0 0-2.49-6.01l-.708.707A7.476 7.476 0 0 1 13.025 8c0 2.071-.84 3.946-2.197 5.303l.708.707z"/>
                        <path d="M10.121 12.596A6.48 6.48 0 0 0 12.025 8a6.48 6.48 0 0 0-1.904-4.596l-.707.707A5.483 5.483 0 0 1 11.025 8a5.483 5.483 0 0 1-1.61 3.89l.706.706z"/>
                        <path d="M8.707 11.182A4.486 4.486 0 0 0 10.025 8a4.486 4.486 0 0 0-1.318-3.182L8 5.525A3.489 3.489 0 0 1 9.025 8 3.49 3.49 0 0 1 8 10.475l.707.707zM6.717 3.55A.5.5 0 0 1 7 4v8a.5.5 0 0 1-.812.39L3.825 10.5H1.5A.5.5 0 0 1 1 10V6a.5.5 0 0 1 .5-.5h2.325l2.363-1.89a.5.5 0 0 1 .529-.06z"/>
                    </svg>
                </button>
            </div>

            <!-- Character Tabs (for multi-character words) -->
            <div class="character-tabs" id="characterTabs" style="display:none;">
                <!-- Tabs will be inserted here -->
            </div>

            <!-- Dual Canvas Area -->
            <div class="dual-canvas-container">
                <!-- Left: Reference with Animation -->
                <div class="canvas-section">
                    <h3 class="canvas-label">📖 Chữ Mẫu</h3>
                    <div class="canvas-wrapper">
                        <svg id="referenceCanvas"></svg>
                    </div>
                </div>

                <!-- Right: User Drawing -->
                <div class="canvas-section">
                    <h3 class="canvas-label">✍️ Bạn Viết</h3>
                    <div class="canvas-wrapper user-canvas" style="position: relative;">
                        <canvas id="userCanvas" width="400" height="400"></canvas>
                        <svg id="userGuide" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; opacity: 0.3;"></svg>
                    </div>
                </div>
            </div>

            <!-- Canvas Controls -->
            <div class="canvas-controls">
                <button class="control-btn" onclick="playCurrentAudio()" title="Play audio">
                    <svg width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                        <path d="m11.596 8.697-6.363 3.692c-.54.313-1.233-.066-1.233-.697V4.308c0-.63.692-1.01 1.233-.696l6.363 3.692a.802.802 0 0 1 0 1.393z"/>
                    </svg>
                </button>
                <button class="control-btn" onclick="clearUserCanvas()" title="Clear">
                    <svg width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M8.086 2.207a2 2 0 0 1 2.828 0l3.879 3.879a2 2 0 0 1 0 2.828l-5.5 5.5A2 2 0 0 1 7.879 15H5.12a2 2 0 0 1-1.414-.586l-2.5-2.5a2 2 0 0 1 0-2.828l6.879-6.879zm2.121.707a1 1 0 0 0-1.414 0L4.16 7.547l5.293 5.293 4.633-4.633a1 1 0 0 0 0-1.414l-3.879-3.879zM8.746 13.547 3.453 8.254 1.914 9.793a1 1 0 0 0 0 1.414l2.5 2.5a1 1 0 0 0 .707.293H7.88a1 1 0 0 0 .707-.293l.16-.16z"/>
                    </svg>
                </button>
                <button class="control-btn" onclick="replayAnimation()" title="Replay animation">
                    <svg width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2v1z"/>
                        <path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466z"/>
                    </svg>
                </button>
                <button class="control-btn toggle" id="showGuideBtn" onclick="toggleGuide()" title="Toggle guide">
                    <svg width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/>
                        <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Word List -->
        <div class="word-list-section">
            <div class="word-list-grid" id="wordListGrid">
                @foreach($words as $index => $word)
                <button class="word-item {{ $index === 0 ? 'active' : '' }}" 
                        onclick="selectWord('{{ $word['w'] }}', '{{ $word['p'] }}', {{ $index }})"
                        data-index="{{ $index }}">
                    {{ $word['w'] }}
                </button>
                @endforeach
            </div>
        </div>


    </main>
</div>

<style>
.writing-practice-container {
    min-height: 100vh;
    background: #f5f5f5;
}

.writing-main {
    max-width: 900px;
    margin: 0 auto;
    padding: 16px;
}

.back-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 12px;
    background: transparent;
    color: #333;
    text-decoration: none;
    font-size: 1rem;
    font-weight: 500;
}

.page-title {
    font-size: 1.5rem;
    font-weight: 600;
    color: #333;
    margin: 16px 0 24px;
}

.current-word-card {
    background: white;
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    margin-bottom: 24px;
}

.word-display {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 16px;
}

.chinese-char {
    font-family: "Noto Sans TC", "Microsoft JhengHei", sans-serif;
    font-size: 2.5rem;
    font-weight: 700;
    color: #333;
}

.pinyin-text {
    color: #666;
    font-size: 1.1rem;
}

.audio-btn-small {
    padding: 8px;
    background: #f0f0f0;
    border: none;
    border-radius: 50%;
    cursor: pointer;
    transition: all 0.2s;
}

.audio-btn-small:hover {
    background: #00acc1;
    color: white;
}

.character-tabs {
    display: flex;
    gap: 8px;
    margin-bottom: 16px;
    justify-content: center;
}

.char-tab {
    padding: 8px 16px;
    background: #f0f0f0;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    font-family: "Noto Sans TC", "Microsoft JhengHei", sans-serif;
    font-size: 1.2rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
}

.char-tab.active {
    background: #00acc1;
    color: white;
    border-color: #00acc1;
}

.dual-canvas-container {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 16px;
    margin-bottom: 20px;
}

.canvas-section {
    display: flex;
    flex-direction: column;
}

.canvas-label {
    font-size: 1rem;
    font-weight: 600;
    color: #333;
    margin-bottom: 8px;
    text-align: center;
}

.canvas-wrapper {
    position: relative;
    width: 100%;
    aspect-ratio: 1;
    background: white;
    border: 2px solid #e0e0e0;
    border-radius: 12px;
    overflow: hidden;
}

.canvas-wrapper.user-canvas {
    border-color: #00acc1;
    border-width: 3px;
}

.canvas-wrapper canvas,
.canvas-wrapper svg {
    width: 100%;
    height: 100%;
    display: block;
}

#userCanvas {
    cursor: crosshair;
    touch-action: none;
}

#userGuide.hidden {
    opacity: 0 !important;
}

.canvas-controls {
    display: flex;
    justify-content: center;
    gap: 16px;
}

.control-btn {
    width: 56px;
    height: 56px;
    border-radius: 50%;
    border: none;
    background: white;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
}

.control-btn:hover {
    background: #f0f0f0;
    transform: scale(1.05);
}

.control-btn.toggle {
    background: #4caf50;
    color: white;
}

.control-btn.toggle svg {
    fill: white;
}

.word-list-section {
    margin-bottom: 24px;
}

.word-list-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.word-item {
    padding: 12px 20px;
    background: white;
    border: 2px solid #e0e0e0;
    border-radius: 12px;
    font-family: "Noto Sans TC", "Microsoft JhengHei", sans-serif;
    font-size: 1.25rem;
    font-weight: 600;
    color: #333;
    cursor: pointer;
    transition: all 0.2s;
}

.word-item:hover {
    background: #f0f0f0;
}

.word-item.active {
    background: #4caf50;
    color: white;
    border-color: #4caf50;
}

@media (max-width: 768px) {
    .dual-canvas-container {
        grid-template-columns: 1fr;
    }
}
</style>

<script src="https://cdn.jsdelivr.net/npm/hanzi-writer@3.5/dist/hanzi-writer.min.js"></script>
<script>
const words = @json($words);
let currentWordIndex = 0;
let currentCharIndex = 0;
let currentWord = null;
let userCanvas, userCtx;
let isDrawing = false;
let lastX = 0;
let lastY = 0;
let showGuide = true;
let referenceWriter = null;
let guideWriter = null;

// Initialize canvases
function initCanvases() {
    // User canvas
    userCanvas = document.getElementById('userCanvas');
    if (!userCanvas) return;

    userCtx = userCanvas.getContext('2d');
    userCtx.lineWidth = 8;
    userCtx.lineCap = 'round';
    userCtx.lineJoin = 'round';
    userCtx.strokeStyle = '#333';

    // Mouse events
    userCanvas.addEventListener('mousedown', startDrawing);
    userCanvas.addEventListener('mousemove', draw);
    userCanvas.addEventListener('mouseup', stopDrawing);
    userCanvas.addEventListener('mouseout', stopDrawing);

    // Touch events
    userCanvas.addEventListener('touchstart', handleTouchStart);
    userCanvas.addEventListener('touchmove', handleTouchMove);
    userCanvas.addEventListener('touchend', stopDrawing);
}

function startDrawing(e) {
    isDrawing = true;
    const rect = userCanvas.getBoundingClientRect();
    lastX = (e.clientX - rect.left) * (userCanvas.width / rect.width);
    lastY = (e.clientY - rect.top) * (userCanvas.height / rect.height);
}

function draw(e) {
    if (!isDrawing) return;
    
    const rect = userCanvas.getBoundingClientRect();
    const x = (e.clientX - rect.left) * (userCanvas.width / rect.width);
    const y = (e.clientY - rect.top) * (userCanvas.height / rect.height);

    userCtx.beginPath();
    userCtx.moveTo(lastX, lastY);
    userCtx.lineTo(x, y);
    userCtx.stroke();

    lastX = x;
    lastY = y;
}

function stopDrawing() {
    isDrawing = false;
}

function handleTouchStart(e) {
    e.preventDefault();
    const touch = e.touches[0];
    const rect = userCanvas.getBoundingClientRect();
    isDrawing = true;
    lastX = (touch.clientX - rect.left) * (userCanvas.width / rect.width);
    lastY = (touch.clientY - rect.top) * (userCanvas.height / rect.height);
}

function handleTouchMove(e) {
    if (!isDrawing) return;
    e.preventDefault();
    
    const touch = e.touches[0];
    const rect = userCanvas.getBoundingClientRect();
    const x = (touch.clientX - rect.left) * (userCanvas.width / rect.width);
    const y = (touch.clientY - rect.top) * (userCanvas.height / rect.height);

    userCtx.beginPath();
    userCtx.moveTo(lastX, lastY);
    userCtx.lineTo(x, y);
    userCtx.stroke();

    lastX = x;
    lastY = y;
}

function clearUserCanvas() {
    if (!userCtx) return;
    userCtx.fillStyle = 'white';
    userCtx.fillRect(0, 0, userCanvas.width, userCanvas.height);
}

function toggleGuide() {
    showGuide = !showGuide;
    const guide = document.getElementById('userGuide');
    const btn = document.getElementById('showGuideBtn');
    
    if (showGuide) {
        guide.classList.remove('hidden');
        btn.classList.add('toggle');
    } else {
        guide.classList.add('hidden');
        btn.classList.remove('toggle');
    }
}

function initHanziWriter(char) {
    // Clear previous writers
    document.getElementById('referenceCanvas').innerHTML = '';
    document.getElementById('userGuide').innerHTML = '';
    
    // Create reference writer with animation
    referenceWriter = HanziWriter.create('referenceCanvas', char, {
        width: 400,
        height: 400,
        padding: 20,
        showOutline: true,
        strokeAnimationSpeed: 1,
        delayBetweenStrokes: 200,
        strokeColor: '#00acc1',
        radicalColor: '#0097a7'
    });
    
    // Create guide writer (outline only)
    guideWriter = HanziWriter.create('userGuide', char, {
        width: 400,
        height: 400,
        padding: 20,
        showOutline: true,
        showCharacter: false
    });
    
    // Auto play animation
    setTimeout(() => {
        referenceWriter.animateCharacter();
    }, 300);
}

function replayAnimation() {
    if (referenceWriter) {
        referenceWriter.animateCharacter();
    }
}

function selectWord(char, pinyin, index) {
    currentWordIndex = index;
    currentWord = words[index];
    
    // Split characters
    const chars = char.split('');
    
    // Update display
    document.getElementById('currentChar').textContent = char;
    document.getElementById('currentPinyin').textContent = `[${pinyin}]`;
    
    // Show/hide tabs for multi-character words
    const tabsContainer = document.getElementById('characterTabs');
    if (chars.length > 1) {
        tabsContainer.style.display = 'flex';
        tabsContainer.innerHTML = '';
        
        chars.forEach((c, i) => {
            const tab = document.createElement('button');
            tab.className = 'char-tab' + (i === 0 ? ' active' : '');
            tab.textContent = c;
            tab.onclick = () => selectCharacter(i);
            tabsContainer.appendChild(tab);
        });
        
        currentCharIndex = 0;
        selectCharacter(0);
    } else {
        tabsContainer.style.display = 'none';
        currentCharIndex = 0;
        updateCharacterDisplay(char);
    }
    
    // Update active state in word list
    document.querySelectorAll('.word-item').forEach(item => {
        item.classList.remove('active');
    });
    document.querySelector(`[data-index="${index}"]`).classList.add('active');
    
    // Clear user canvas
    clearUserCanvas();
    
    // Show guide by default
    if (!showGuide) {
        toggleGuide();
    }
}

function selectCharacter(index) {
    currentCharIndex = index;
    const chars = currentWord.w.split('');
    
    // Update tab active state
    document.querySelectorAll('.char-tab').forEach((tab, i) => {
        tab.classList.toggle('active', i === index);
    });
    
    updateCharacterDisplay(chars[index]);
    clearUserCanvas();
}

function updateCharacterDisplay(char) {
    // Initialize HanziWriter for both reference and guide
    initHanziWriter(char);
}

function playCurrentAudio() {
    const word = words[currentWordIndex];
    if ('speechSynthesis' in window) {
        const utterance = new SpeechSynthesisUtterance(word.w);
        utterance.lang = 'zh-TW';
        utterance.rate = 0.8;
        speechSynthesis.speak(utterance);
    }
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    initCanvases();
    
    // Set first word
    if (words.length > 0) {
        selectWord(words[0].w, words[0].p, 0);
    }
});
</script>

    @include('client.components.footer')
</div>
@endsection
