@extends('layouts.app')

@section('title', 'Character Detail - Chinese Learning')

@section('content')
<div class="client-container">
    <!-- Navigation Header -->
    @include('client.components.header')

    <!-- Main Content -->
    <div class="client-main">
        <a href="{{ route('client.radicals.index') }}" class="btn-back">
            ← Back to Characters
        </a>

        <div id="loadingSpinner" style="text-align: center;">
            <div class="spinner"></div>
        </div>

        <div id="characterContent" style="display: none;">
            <!-- Character Display -->
            <div class="character-display">
                <div class="character-hanzi" id="characterHanzi">明</div>
                <div class="character-pinyin" id="characterPinyin">míng</div>
                <div class="character-meaning" id="characterMeaning">bright, light</div>
            </div>

            <!-- Info Grid -->
            <div class="info-grid">
                <div class="info-card">
                    <div class="info-label">HSK Level</div>
                    <div class="info-value" id="hskLevel">HSK 1</div>
                </div>
                <div class="info-card">
                    <div class="info-label">Stroke Count</div>
                    <div class="info-value" id="strokeCount">8</div>
                </div>
                <div class="info-card">
                    <div class="info-label">Frequency</div>
                    <div class="info-value" id="frequency">Common</div>
                </div>
            </div>

            <!-- Practice Section -->
            <div class="practice-section">
                <h2>✍️ Practice Writing</h2>
                <div class="practice-area">
                    <canvas id="practiceCanvas" width="400" height="400" class="practice-canvas"></canvas>
                    <p class="practice-hint">Draw the character above on the canvas</p>
                    <div class="practice-buttons">
                        <button class="btn-practice secondary" onclick="clearCanvas()">Clear</button>
                        <button class="btn-practice primary" onclick="checkWriting()">Check</button>
                    </div>
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
<style>
    body {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
        background: linear-gradient(135deg, #e0f7fa 0%, #b2ebf2 50%, #80deea 100%);
        min-height: 100vh;
    }

    .client-main {
        padding: 2rem;
        max-width: 1200px;
        margin: 0 auto;
    }

    .btn-back {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.5rem;
        background: white;
        color: #00bcd4;
        border: 2px solid #00bcd4;
        border-radius: 8px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s;
        box-shadow: 0 2px 8px rgba(0, 188, 212, 0.2);
        margin-bottom: 2rem;
    }

    .btn-back:hover {
        background: #00bcd4;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 16px rgba(0, 188, 212, 0.3);
    }

    .character-display {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.95), rgba(255, 255, 255, 0.9));
        backdrop-filter: blur(10px);
        border-radius: 20px;
        padding: 3rem;
        text-align: center;
        margin-bottom: 2rem;
        box-shadow: 0 8px 32px rgba(0, 188, 212, 0.15);
        animation: fadeInUp 0.6s ease-out;
    }

    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .character-hanzi {
        font-size: 10rem;
        font-weight: 500;
        color: #263238;
        margin-bottom: 1rem;
        line-height: 1;
        animation: scaleIn 0.8s ease-out;
    }

    @keyframes scaleIn {
        from { opacity: 0; transform: scale(0.5); }
        to { opacity: 1; transform: scale(1); }
    }

    .character-pinyin {
        font-size: 2rem;
        color: #00bcd4;
        font-weight: 600;
        margin-bottom: 1rem;
    }

    .character-meaning {
        font-size: 1.5rem;
        color: #546e7a;
        margin-bottom: 2rem;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .info-card {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 4px 16px rgba(0, 188, 212, 0.1);
        animation: fadeInUp 0.7s ease-out;
        animation-fill-mode: both;
    }

    .info-card:nth-child(1) { animation-delay: 0.1s; }
    .info-card:nth-child(2) { animation-delay: 0.2s; }
    .info-card:nth-child(3) { animation-delay: 0.3s; }

    .info-label {
        font-size: 0.875rem;
        color: #78909c;
        margin-bottom: 0.5rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .info-value {
        font-size: 1.5rem;
        color: #263238;
        font-weight: 700;
    }

    .practice-section {
        background: white;
        border-radius: 20px;
        padding: 2rem;
        box-shadow: 0 4px 16px rgba(0, 188, 212, 0.1);
        animation: fadeInUp 0.8s ease-out;
    }

    .practice-section h2 {
        color: #263238;
        margin-bottom: 1.5rem;
        font-size: 1.5rem;
    }

    .practice-area {
        background: #f5f5f5;
        border: 2px dashed #00bcd4;
        border-radius: 12px;
        padding: 3rem;
        text-align: center;
        min-height: 300px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 1rem;
    }

    .practice-canvas {
        background: white;
        border-radius: 8px;
        box-shadow: inset 0 2px 8px rgba(0, 0, 0, 0.1);
        cursor: crosshair;
    }

    .practice-hint {
        color: #78909c;
        font-size: 1rem;
    }

    .practice-buttons {
        display: flex;
        gap: 1rem;
        margin-top: 1rem;
    }

    .btn-practice {
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
    }

    .btn-practice.primary {
        background: linear-gradient(135deg, #00bcd4, #0097a7);
        color: white;
        box-shadow: 0 4px 12px rgba(0, 188, 212, 0.3);
    }

    .btn-practice.primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0, 188, 212, 0.4);
    }

    .btn-practice.secondary {
        background: white;
        color: #00bcd4;
        border: 2px solid #00bcd4;
    }

    .btn-practice.secondary:hover {
        background: #00bcd4;
        color: white;
    }

    .spinner {
        width: 60px;
        height: 60px;
        border: 4px solid rgba(0, 188, 212, 0.1);
        border-top-color: #00bcd4;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin: 2rem auto;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    /* Responsive */
    @media (max-width: 768px) {
        .client-main {
            padding: 1rem;
        }

        .character-display {
            padding: 2rem 1rem;
        }

        .character-hanzi {
            font-size: 6rem;
        }

        .character-pinyin {
            font-size: 1.5rem;
        }

        .character-meaning {
            font-size: 1.2rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    const radicalId = {{ $id }};
    let canvas, ctx;
    let isDrawing = false;
    let lastX = 0;
    let lastY = 0;

    // Load radical details
    async function loadRadicalDetail() {
        try {
            const response = await fetch(`/api/radicals/${radicalId}`);
            
            if (!response.ok) {
                throw new Error('Radical not found');
            }

            const radical = await response.json();

            // Update UI
            document.getElementById('characterHanzi').textContent = radical.hanzi || radical.character || radical.simplified || '?';
            document.getElementById('characterPinyin').textContent = radical.pinyin || 'N/A';
            document.getElementById('characterMeaning').textContent = radical.meaning || radical.english || 'No meaning available';
            document.getElementById('hskLevel').textContent = `HSK ${radical.level || '1'}`;
            document.getElementById('strokeCount').textContent = radical.stroke_count || radical.strokes || 'N/A';
            document.getElementById('frequency').textContent = radical.frequency || 'Common';

            // Show content, hide spinner
            document.getElementById('loadingSpinner').style.display = 'none';
            document.getElementById('characterContent').style.display = 'block';

        } catch (error) {
            console.error('Error loading radical:', error);
            document.getElementById('loadingSpinner').innerHTML = `
                <div style="text-align: center; padding: 3rem; color: #ef5350;">
                    <div style="font-size: 3rem; margin-bottom: 1rem;">❌</div>
                    <p>Failed to load character details</p>
                    <a href="{{ route('client.radicals.index') }}" class="btn-back" style="margin-top: 1rem; display: inline-flex;">
                        ← Back to Characters
                    </a>
                </div>
            `;
        }
    }

    // Initialize canvas for writing practice
    function initCanvas() {
        canvas = document.getElementById('practiceCanvas');
        if (!canvas) return;

        ctx = canvas.getContext('2d');
        ctx.lineWidth = 8;
        ctx.lineCap = 'round';
        ctx.strokeStyle = '#263238';

        // Mouse events
        canvas.addEventListener('mousedown', startDrawing);
        canvas.addEventListener('mousemove', draw);
        canvas.addEventListener('mouseup', stopDrawing);
        canvas.addEventListener('mouseout', stopDrawing);

        // Touch events
        canvas.addEventListener('touchstart', handleTouch);
        canvas.addEventListener('touchmove', handleTouch);
        canvas.addEventListener('touchend', stopDrawing);
    }

    function startDrawing(e) {
        isDrawing = true;
        const rect = canvas.getBoundingClientRect();
        lastX = e.clientX - rect.left;
        lastY = e.clientY - rect.top;
    }

    function draw(e) {
        if (!isDrawing) return;
        
        const rect = canvas.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;

        ctx.beginPath();
        ctx.moveTo(lastX, lastY);
        ctx.lineTo(x, y);
        ctx.stroke();

        lastX = x;
        lastY = y;
    }

    function stopDrawing() {
        isDrawing = false;
    }

    function handleTouch(e) {
        e.preventDefault();
        const touch = e.touches[0];
        const mouseEvent = new MouseEvent(e.type === 'touchstart' ? 'mousedown' : 'mousemove', {
            clientX: touch.clientX,
            clientY: touch.clientY
        });
        canvas.dispatchEvent(mouseEvent);
    }

    function clearCanvas() {
        if (!ctx) return;
        ctx.clearRect(0, 0, canvas.width, canvas.height);
    }

    function checkWriting() {
        // This would integrate with AI to check the writing
        alert('Writing check feature will be implemented with AI integration!');
    }

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        loadRadicalDetail();
        setTimeout(initCanvas, 500); // Wait for content to load
    });
</script>
@endpush

@include('client.components.footer')
