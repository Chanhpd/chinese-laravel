@extends('layouts.app')

@section('title', 'Vocabulary Learning - Chinese Learning')

@section('content')
<div class="client-container vocabulary-page">
    <!-- Navigation Header -->
    @include('client.components.header')

    <!-- Main Content -->
    <div class="client-main">
        <!-- Page Header -->
        <section class="page-header">
            <div class="header-content">
                <h1 style="color: #2d3e3f;">📕 Vocabulary Learning</h1>
                <p>Expand your vocabulary with topics and HSK levels</p>
            </div>
        </section>

        <!-- Topics Content -->
        <section class="topics-content">
            <div class="level-selector-section">
                <h2>Select HSK Level</h2>
                <div class="level-buttons" id="levelButtons">
                    <button class="level-btn active" data-level="1">HSK 1</button>
                    <button class="level-btn" data-level="2">HSK 2</button>
                    <button class="level-btn" data-level="3">HSK 3</button>
                    <button class="level-btn" data-level="4">HSK 4</button>
                    <button class="level-btn" data-level="5">HSK 5</button>
                    <button class="level-btn" data-level="6">HSK 6</button>
                </div>
            </div>
            
            <div class="topics-section">
                <h2>Topics for <span id="currentLevelText">HSK 1</span></h2>
                <div class="topics-grid" id="topicsGrid">
                    <!-- Loaded dynamically -->
                    <div class="spinner" style="grid-column: 1/-1; justify-self: center;"></div>
                </div>
                <!-- Pagination for Topics -->
                <div class="pagination-container" id="topicsPaginationContainer" style="display: none;">
                    <div class="pagination-info" id="topicsPaginationInfo"></div>
                    <div class="pagination" id="topicsPagination"></div>
                </div>
            </div>
        </section>
    </div>

    @include('client.components.footer')
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('client-assets/css/variables.css') }}">
<link rel="stylesheet" href="{{ asset('client-assets/css/base.css') }}">
<link rel="stylesheet" href="{{ asset('client-assets/css/layout.css') }}">
<link rel="stylesheet" href="{{ asset('client-assets/css/vocabulary.css') }}">
@endpush

@push('scripts')
<script>
    let currentLevel = 1; // Default HSK Level 1
    let allTopics = [];
    let filteredTopics = [];
    let currentTopicPage = 1;
    const topicsPerPage = 12;

    // Level selection
    document.querySelectorAll('.level-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const level = parseInt(this.dataset.level);
            
            // Update active state
            document.querySelectorAll('.level-btn').forEach(b => {
                b.classList.remove('active');
            });
            this.classList.add('active');
            
            // Update current level
            currentLevel = level;
            document.getElementById('currentLevelText').textContent = `HSK ${level}`;
            
            // Reload topics for this level
            loadTopics(level);
        });
    });

    async function loadTopics(level = 1) {
        const grid = document.getElementById('topicsGrid');
        grid.innerHTML = '<div class="spinner" style="grid-column: 1/-1; justify-self: center;"></div>';
        
        try {
            // Load topics with vocabulary count for specific level
            // Convert level number to HSK format (1 -> HSK1)
            const hskLevel = `HSK${level}`;
            const response = await fetch(`/api/topics?with_count=true&level=${hskLevel}`);
            
            if (!response.ok) {
                throw new Error('Failed to load topics');
            }
            
            const result = await response.json();
            const topics = result.data || result;
            
            allTopics = topics;
            filteredTopics = topics;
            
            displayTopics(topics, 1, level);
        } catch (error) {
            console.error('Error loading topics:', error);
            const grid = document.getElementById('topicsGrid');
            grid.innerHTML = `
                <div class="no-results" style="grid-column: 1/-1;">
                    <div class="no-results-icon">❌</div>
                    <p>Failed to load topics. Please try again later.</p>
                </div>
            `;
        }
    }
    
    function displayTopics(topics, page = 1, level = 1) {
        const grid = document.getElementById('topicsGrid');
        const paginationContainer = document.getElementById('topicsPaginationContainer');
        grid.innerHTML = '';
        
        filteredTopics = topics;
        currentTopicPage = page;

        if (!topics || topics.length === 0) {
            grid.innerHTML = `
                <div class="no-results" style="grid-column: 1/-1;">
                    <div class="no-results-icon">📭</div>
                    <p>No topics found for HSK ${level}</p>
                </div>
            `;
            paginationContainer.style.display = 'none';
            return;
        }

        // Calculate pagination
        const totalPages = Math.ceil(topics.length / topicsPerPage);
        const startIndex = (page - 1) * topicsPerPage;
        const endIndex = Math.min(startIndex + topicsPerPage, topics.length);
        const paginatedTopics = topics.slice(startIndex, endIndex);

        // Display topics for current page
        paginatedTopics.forEach((topic, index) => {
            const card = document.createElement('a');
            card.href = `{{ route('client.vocabulary.learn', '') }}/${topic.id}?level=HSK${level}`;
            card.className = 'topic-card';
            card.style.animationDelay = `${index * 0.05}s`;
            
            const topicName = topic.name || topic.topic_name || 'Unnamed Topic';
            const vocabCount = topic.vocabularies_level_count || topic.vocabularies_count || topic.vocabulary_count || 0;
            const imageUrl = topic.image_url || topic.image;
            const icon = topic.icon || '📚';
            
            // Use image if available, otherwise use icon
            const headerContent = imageUrl 
                ? `<img src="${imageUrl}" alt="${topicName}" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                   <div class="topic-icon-fallback" style="display:none;">${icon}</div>`
                : `<div class="topic-icon-fallback">${icon}</div>`;
            
            card.innerHTML = `
                <div class="topic-header">${headerContent}</div>
                <div class="topic-body">
                    <div class="topic-name">${topicName}</div>
                    <div class="topic-meta">
                        <span>📖 ${vocabCount} words</span>
                        <span>🎯 HSK ${level}</span>
                    </div>
                    <button class="topic-btn">Start Learning</button>
                </div>
            `;
            grid.appendChild(card);
        });

        // Show/hide pagination
        if (totalPages > 1) {
            paginationContainer.style.display = 'block';
            renderTopicsPagination(totalPages, page, topics.length, startIndex, endIndex, level);
        } else {
            paginationContainer.style.display = 'none';
        }

        // Scroll to top of topics section
        document.querySelector('.topics-section').scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    function renderTopicsPagination(totalPages, currentPage, totalItems, startIndex, endIndex, level) {
        const pagination = document.getElementById('topicsPagination');
        const paginationInfo = document.getElementById('topicsPaginationInfo');
        
        // Update info
        paginationInfo.innerHTML = `Showing ${startIndex + 1}-${endIndex} of ${totalItems} topics`;
        
        // Clear pagination
        pagination.innerHTML = '';
        
        // Previous button
        const prevBtn = document.createElement('button');
        prevBtn.className = `pagination-btn ${currentPage === 1 ? 'disabled' : ''}`;
        prevBtn.innerHTML = '← Previous';
        prevBtn.disabled = currentPage === 1;
        prevBtn.onclick = () => {
            if (currentPage > 1) displayTopics(filteredTopics, currentPage - 1, level);
        };
        pagination.appendChild(prevBtn);
        
        // Page numbers
        const pageNumbers = getPageNumbers(currentPage, totalPages);
        pageNumbers.forEach(pageNum => {
            if (pageNum === '...') {
                const ellipsis = document.createElement('span');
                ellipsis.className = 'pagination-ellipsis';
                ellipsis.textContent = '...';
                pagination.appendChild(ellipsis);
            } else {
                const pageBtn = document.createElement('button');
                pageBtn.className = `pagination-btn ${pageNum === currentPage ? 'active' : ''}`;
                pageBtn.textContent = pageNum;
                pageBtn.onclick = () => displayTopics(filteredTopics, pageNum, level);
                pagination.appendChild(pageBtn);
            }
        });
        
        // Next button
        const nextBtn = document.createElement('button');
        nextBtn.className = `pagination-btn ${currentPage === totalPages ? 'disabled' : ''}`;
        nextBtn.innerHTML = 'Next →';
        nextBtn.disabled = currentPage === totalPages;
        nextBtn.onclick = () => {
            if (currentPage < totalPages) displayTopics(filteredTopics, currentPage + 1, level);
        };
        pagination.appendChild(nextBtn);
    }

    function getPageNumbers(current, total) {
        const pages = [];
        
        if (total <= 7) {
            // Show all pages if 7 or less
            for (let i = 1; i <= total; i++) {
                pages.push(i);
            }
        } else {
            // Always show first page
            pages.push(1);
            
            if (current > 3) {
                pages.push('...');
            }
            
            // Show pages around current
            for (let i = Math.max(2, current - 1); i <= Math.min(total - 1, current + 1); i++) {
                pages.push(i);
            }
            
            if (current < total - 2) {
                pages.push('...');
            }
            
            // Always show last page
            pages.push(total);
        }
        
        return pages;
    }

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        loadTopics();
    });
</script>
@endpush

