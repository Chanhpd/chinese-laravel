@extends('layouts.app')

@section('title', 'Learn Characters - Chinese Learning')

@push('styles')
<link rel="stylesheet" href="{{ asset('client-assets/css/variables.css') }}">
<link rel="stylesheet" href="{{ asset('client-assets/css/base.css') }}">
<link rel="stylesheet" href="{{ asset('client-assets/css/layout.css') }}">
@endpush

@section('content')
<div class="client-container">
    <!-- Navigation Header -->
    @include('client.components.header')

    <!-- Main Content -->
    <main class="client-main">
        <!-- Page Header -->
        <section class="page-header">
            <div class="header-content">
                <h1>✍️ HSK Radicals Learning</h1>
                <p>Master Chinese characters and radicals to build your writing foundation</p>
            </div>
        </section>

        <!-- HSK Levels Selection -->
        <section class="levels-section">
            <h2>Select HSK Level</h2>
            <div class="levels-grid" id="levelsGrid">
                <!-- Loaded dynamically -->
                <div class="spinner spinner-lg" style="grid-column: 1/-1; justify-self: center;"></div>
            </div>
        </section>

        <!-- Filter Section -->
        <section class="filter-section">
            <div class="filter-group">
                <div class="filter-item">
                    <label for="strokeFilter">Filter by Stroke Count:</label>
                    <select id="strokeFilter">
                        <option value="">All Strokes</option>
                        <option value="1">1 stroke</option>
                        <option value="2">2 strokes</option>
                        <option value="3">3 strokes</option>
                        <option value="4">4 strokes</option>
                        <option value="5">5+ strokes</option>
                    </select>
                </div>
                <div class="filter-item">
                    <label for="searchRadical">Search:</label>
                    <input type="text" id="searchRadical" placeholder="Search by character or pinyin..." />
                </div>
            </div>
        </section>

        <!-- Radicals List -->
        <section class="radicals-section">
            <h2>Radicals for HSK 1</h2>
            <div class="radicals-grid" id="radicalsGrid">
                <!-- Loaded dynamically -->
            </div>
        </section>
    </main>
</div>
@endsection

@push('scripts')
<script>
    let currentLevel = 1;
    let allRadicals = [];

    async function loadLevels() {
        const grid = document.getElementById('levelsGrid');
        grid.innerHTML = '';

        // Create cards for levels 1-6
        for (let i = 1; i <= 6; i++) {
            const levelCard = document.createElement('div');
            levelCard.className = `level-card animate-scaleIn delay-${i}00 ${i === 1 ? 'active' : ''}`;
            levelCard.dataset.level = i;
            levelCard.innerHTML = `
                <span class="level-number">HSK ${i}</span>
                <span class="level-info">Click to load</span>
            `;
            levelCard.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                selectLevel(i);
            });
            grid.appendChild(levelCard);
        }
    }

    function selectLevel(level) {
        currentLevel = level;

        // Update active state
        document.querySelectorAll('.level-card').forEach(card => {
            card.classList.remove('active');
            if (parseInt(card.dataset.level) === level) {
                card.classList.add('active');
            }
        });

        // Update section title
        document.querySelector('.radicals-section h2').textContent = `Radicals for HSK ${level}`;

        loadRadicals(level);
    }

    async function loadRadicals(level) {
        const grid = document.getElementById('radicalsGrid');
        grid.innerHTML = '<div class="spinner spinner-lg" style="grid-column: 1/-1; justify-self: center;"></div>';

        try {
            const response = await fetch(`/api/radicals/hsk/${level}`);
            
            if (!response.ok) {
                throw new Error('Failed to load radicals');
            }

            const radicals = await response.json();
            allRadicals = radicals;

            displayRadicals(radicals);
        } catch (error) {
            console.error('Error loading radicals:', error);
            grid.innerHTML = `
                <div class="no-results" style="grid-column: 1/-1; text-align: center; padding: 3rem;">
                    <div style="font-size: 4rem; margin-bottom: 1rem;">❌</div>
                    <p>Failed to load radicals. Please try again.</p>
                </div>
            `;
        }
    }

    function displayRadicals(radicals) {
        const grid = document.getElementById('radicalsGrid');
        grid.innerHTML = '';

        if (!radicals || radicals.length === 0) {
            grid.innerHTML = `
                <div class="no-results" style="grid-column: 1/-1; text-align: center; padding: 3rem;">
                    <div style="font-size: 4rem; margin-bottom: 1rem;">📭</div>
                    <p class="text-muted">No radicals found for this level</p>
                </div>
            `;
            return;
        }

        radicals.forEach((radical, index) => {
            const card = document.createElement('a');
            card.href = `{{ route('client.radicals.practice') }}`;
            card.className = 'radical-card';
            card.style.animationDelay = `${index * 0.02}s`;
            
            const hanzi = radical.hanzi || radical.character || radical.simplified || '?';
            const pinyin = radical.pinyin || '';
            const meaning = radical.meaning || radical.english || '';
            const strokes = radical.stroke_count || radical.strokes || '';

            card.innerHTML = `
                <div class="radical-character">${hanzi}</div>
                <div class="radical-pinyin">${pinyin}</div>
                <div class="radical-meaning">${meaning}</div>
                ${strokes ? `<div class="radical-strokes">${strokes} strokes</div>` : ''}
            `;
            
            grid.appendChild(card);
        });
    }

    // Filter by stroke count
    document.getElementById('strokeFilter')?.addEventListener('change', function() {
        const strokeCount = this.value;
        let filtered = allRadicals;

        if (strokeCount) {
            filtered = allRadicals.filter(r => {
                const strokes = parseInt(r.stroke_count || r.strokes || 0);
                if (strokeCount === '5') {
                    return strokes >= 5;
                }
                return strokes === parseInt(strokeCount);
            });
        }

        displayRadicals(filtered);
    });

    // Search functionality
    let searchTimeout;
    document.getElementById('searchRadical')?.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const searchTerm = this.value.toLowerCase().trim();

        searchTimeout = setTimeout(() => {
            if (!searchTerm) {
                displayRadicals(allRadicals);
                return;
            }

            const filtered = allRadicals.filter(r => {
                const hanzi = (r.hanzi || r.character || r.simplified || '').toLowerCase();
                const pinyin = (r.pinyin || '').toLowerCase();
                const meaning = (r.meaning || r.english || '').toLowerCase();
                
                return hanzi.includes(searchTerm) || 
                       pinyin.includes(searchTerm) || 
                       meaning.includes(searchTerm);
            });

            displayRadicals(filtered);
        }, 300);
    });

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        loadLevels();
        loadRadicals(1);
    });
</script>
@endpush
