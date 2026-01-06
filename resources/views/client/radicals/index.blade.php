@extends('layouts.app')

@section('title', 'Learn Characters - Chinese Learning')

@section('content')
<div class="client-container">
    <!-- Navigation Header -->
    <nav class="client-navbar">
        <div class="navbar-brand">
            <div class="brand-logo">🇨🇳</div>
            <h1>ChineseHub</h1>
        </div>
        <ul class="nav-menu">
            <li><a href="{{ route('client.home') }}" class="nav-link">Dashboard</a></li>
            <li><a href="{{ route('client.radicals.index') }}" class="nav-link active">Characters</a></li>
            <li><a href="{{ route('client.vocabulary.index') }}" class="nav-link">Vocabulary</a></li>
            <li><a href="{{ route('client.chat') }}" class="nav-link">AI Chat</a></li>
        </ul>
        <div class="nav-user">
            <div class="user-info">
                <span class="user-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                <div>
                    <p class="user-name">{{ Auth::user()->name }}</p>
                    <p class="user-level">Learner</p>
                </div>
            </div>
            <form action="{{ route('client.logout') }}" method="POST" style="margin: 0;">
                @csrf
                <button type="submit" class="btn-logout">Logout</button>
            </form>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="client-main">
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
                <div class="spinner" style="grid-column: 1/-1; justify-self: center;"></div>
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
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('client-assets/css/variables.css') }}">
<link rel="stylesheet" href="{{ asset('client-assets/css/base.css') }}">
<link rel="stylesheet" href="{{ asset('client-assets/css/layout.css') }}">
<style>
    .page-header {
        background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-secondary) 100%);
        color: white;
        padding: var(--spacing-10);
        border-radius: var(--border-radius-xl);
        margin-bottom: var(--spacing-10);
        box-shadow: var(--shadow-lg);
    }

    .header-content h1 {
        margin: 0 0 var(--spacing-2) 0;
        color: white;
        font-size: var(--font-size-3xl);
    }

    .header-content p {
        margin: 0;
        color: rgba(255, 255, 255, 0.9);
        font-size: var(--font-size-lg);
    }

    .levels-section {
        margin-bottom: var(--spacing-10);
    }

    .levels-section h2 {
        margin-bottom: var(--spacing-6);
        color: var(--color-primary);
    }

    .levels-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
        gap: var(--spacing-4);
    }

    .level-card {
        background: var(--color-surface);
        border: 2px solid var(--color-border);
        border-radius: var(--border-radius-lg);
        padding: var(--spacing-6);
        text-align: center;
        cursor: pointer;
        transition: all var(--transition-base);
        text-decoration: none;
        color: var(--color-text-primary);
    }

    .level-card:hover {
        border-color: var(--color-primary);
        box-shadow: var(--shadow-md);
        transform: translateY(-4px);
    }

    .level-card.active {
        background: linear-gradient(135deg, var(--color-primary-light) 0%, var(--color-secondary-light) 100%);
        border-color: var(--color-primary);
    }

    .level-number {
        font-size: var(--font-size-2xl);
        font-weight: var(--font-weight-bold);
        color: var(--color-primary);
        display: block;
    }

    .level-card.active .level-number {
        color: var(--color-primary-dark);
    }

    .level-info {
        font-size: var(--font-size-sm);
        color: var(--color-text-secondary);
        margin-top: var(--spacing-2);
    }

    .filter-section {
        background: var(--color-surface);
        padding: var(--spacing-6);
        border-radius: var(--border-radius-lg);
        margin-bottom: var(--spacing-8);
        box-shadow: var(--shadow-sm);
    }

    .filter-group {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: var(--spacing-6);
    }

    .filter-item label {
        display: block;
        margin-bottom: var(--spacing-2);
        font-weight: var(--font-weight-semibold);
    }

    .filter-item select,
    .filter-item input {
        width: 100%;
    }

    .radicals-section {
        margin-bottom: var(--spacing-10);
    }

    .radicals-section h2 {
        margin-bottom: var(--spacing-6);
        color: var(--color-primary);
    }

    .radicals-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
        gap: var(--spacing-4);
    }

    .radical-card {
        background: var(--color-surface);
        border: 2px solid var(--color-border);
        border-radius: var(--border-radius-lg);
        padding: var(--spacing-4);
        text-align: center;
        cursor: pointer;
        transition: all var(--transition-base);
        text-decoration: none;
        color: var(--color-text-primary);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .radical-card:hover {
        border-color: var(--color-primary);
        box-shadow: var(--shadow-md);
        transform: translateY(-4px);
    }

    .radical-hanzi {
        font-size: 48px;
        margin-bottom: var(--spacing-2);
        font-family: var(--font-chinese);
    }

    .radical-pinyin {
        font-size: var(--font-size-sm);
        color: var(--color-text-secondary);
        font-weight: var(--font-weight-semibold);
    }

    .radical-meaning {
        font-size: var(--font-size-xs);
        color: var(--color-text-disabled);
        margin-top: var(--spacing-1);
    }

    .no-results {
        text-align: center;
        padding: var(--spacing-10);
        color: var(--color-text-secondary);
    }

    .no-results-icon {
        font-size: 48px;
        margin-bottom: var(--spacing-4);
    }
</style>
@endpush

@push('scripts')
<script>
    let currentLevel = 1;

    async function loadLevels() {
        try {
            const response = await fetch('/api/radicals/levels');
            const levels = await response.json();

            const grid = document.getElementById('levelsGrid');
            grid.innerHTML = '';

            // Tạo cards cho các level
            for (let i = 1; i <= 6; i++) {
                const levelCard = document.createElement('a');
                levelCard.href = '#';
                levelCard.className = `level-card ${i === 1 ? 'active' : ''}`;
                levelCard.dataset.level = i;
                levelCard.innerHTML = `
                    <span class="level-number">HSK ${i}</span>
                    <span class="level-info">${Math.floor(Math.random() * 100) + 50} radicals</span>
                `;
                levelCard.addEventListener('click', (e) => {
                    e.preventDefault();
                    selectLevel(i);
                });
                grid.appendChild(levelCard);
            }
        } catch (error) {
            console.error('Error loading levels:', error);
        }
    }

    async function selectLevel(level) {
        currentLevel = level;

        // Update active card
        document.querySelectorAll('.level-card').forEach(card => {
            card.classList.remove('active');
            if (card.dataset.level == level) {
                card.classList.add('active');
            }
        });

        loadRadicals(level);
    }

    async function loadRadicals(level) {
        try {
            const response = await fetch(`/api/radicals/hsk/${level}`);
            const radicals = await response.json();

            const grid = document.getElementById('radicalsGrid');
            grid.innerHTML = '';

            if (!radicals || radicals.length === 0) {
                grid.innerHTML = `<div class="no-results" style="grid-column: 1/-1;"><div class="no-results-icon">📭</div><p>No radicals found</p></div>`;
                return;
            }

            radicals.forEach(radical => {
                const card = document.createElement('a');
                card.href = `{{ route('client.radicals.detail', '') }}/${radical.id}`;
                card.className = 'radical-card';
                card.innerHTML = `
                    <div class="radical-hanzi">${radical.hanzi || radical.character}</div>
                    <div class="radical-pinyin">${radical.pinyin || 'N/A'}</div>
                    <div class="radical-meaning">${radical.meaning || radical.english}</div>
                `;
                grid.appendChild(card);
            });
        } catch (error) {
            console.error('Error loading radicals:', error);
        }
    }

    // Filter functions
    document.getElementById('strokeFilter').addEventListener('change', function() {
        // Filter implementation will be added
        console.log('Filter by strokes:', this.value);
    });

    document.getElementById('searchRadical').addEventListener('input', function() {
        // Search implementation will be added
        console.log('Search:', this.value);
    });

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        loadLevels();
        loadRadicals(1);
    });
</script>
@endpush
