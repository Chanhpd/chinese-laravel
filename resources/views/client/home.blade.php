@extends('layouts.app')

@section('title', 'Dashboard - Chinese Learning')

@section('content')
<div class="client-container">
    <!-- Navigation Header -->
    <nav class="client-navbar">
        <div class="navbar-brand">
            <div class="brand-logo">🇨🇳</div>
            <h1>CN Chinese Learning</h1>
        </div>
        <ul class="nav-menu">
            <li><a href="#" class="nav-link active">Dashboard</a></li>
            <li><a href="#" class="nav-link">Lessons</a></li>
            <li><a href="#" class="nav-link">Chat Bot</a></li>
            <li><a href="#" class="nav-link">Vocabulary</a></li>
        </ul>
        <div class="nav-user">
            <div class="user-info">
                <span class="user-avatar">{{ substr(Auth::user()->name, 0, 1) }}</span>
                <div>
                    <p class="user-name">{{ Auth::user()->name }}</p>
                    <p class="user-level">Beginner</p>
                </div>
            </div>
            <button class="btn-logout" id="logoutBtn">Logout</button>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="client-main">
        <!-- Welcome Section -->
        <section class="welcome-section">
            <div class="welcome-content">
                <h2>Welcome back, {{ Auth::user()->name }}! 👋</h2>
                <p>Continue your journey to master Chinese language</p>
            </div>
            <div class="welcome-stats">
                <div class="stat-card">
                    <span class="stat-icon">⭐</span>
                    <div class="stat-info">
                        <p class="stat-label">Current Streak</p>
                        <p class="stat-value">5 days</p>
                    </div>
                </div>
                <div class="stat-card">
                    <span class="stat-icon">📚</span>
                    <div class="stat-info">
                        <p class="stat-label">Words Learned</p>
                        <p class="stat-value">245</p>
                    </div>
                </div>
                <div class="stat-card">
                    <span class="stat-icon">🎯</span>
                    <div class="stat-info">
                        <p class="stat-label">Today's Goal</p>
                        <p class="stat-value">8/10 words</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Content Grid -->
        <div class="content-grid">
            <!-- Learning Progress -->
            <section class="dashboard-card learning-card">
                <h3>📖 Learning Progress</h3>
                <div class="progress-levels">
                    <div class="level-item">
                        <div class="level-badge hsk1">
                            <span class="level-name">HSK 1</span>
                            <span class="level-progress">80%</span>
                        </div>
                        <div class="level-bar">
                            <div class="level-fill" style="width: 80%"></div>
                        </div>
                    </div>
                    <div class="level-item">
                        <div class="level-badge hsk2">
                            <span class="level-name">HSK 2</span>
                            <span class="level-progress">45%</span>
                        </div>
                        <div class="level-bar">
                            <div class="level-fill" style="width: 45%"></div>
                        </div>
                    </div>
                    <div class="level-item">
                        <div class="level-badge hsk3">
                            <span class="level-name">HSK 3</span>
                            <span class="level-progress">15%</span>
                        </div>
                        <div class="level-bar">
                            <div class="level-fill" style="width: 15%"></div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Recent Activities -->
            <section class="dashboard-card activity-card">
                <h3>🔔 Recent Activity</h3>
                <div class="activity-list">
                    <div class="activity-item">
                        <div class="activity-icon">✅</div>
                        <div class="activity-details">
                            <p class="activity-title">Completed Lesson 1.5</p>
                            <p class="activity-time">2 hours ago</p>
                        </div>
                    </div>
                    <div class="activity-item">
                        <div class="activity-icon">🎯</div>
                        <div class="activity-details">
                            <p class="activity-title">Learned 10 new words</p>
                            <p class="activity-time">Yesterday</p>
                        </div>
                    </div>
                    <div class="activity-item">
                        <div class="activity-icon">🏆</div>
                        <div class="activity-details">
                            <p class="activity-title">Earned Gold Medal</p>
                            <p class="activity-time">3 days ago</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Quick Actions -->
            <section class="dashboard-card actions-card">
                <h3>⚡ Quick Actions</h3>
                <div class="action-grid">
                    <a href="#" class="action-btn primary">
                        <span class="action-icon">📚</span>
                        <span class="action-text">Start Lesson</span>
                    </a>
                    <a href="#" class="action-btn secondary">
                        <span class="action-icon">💬</span>
                        <span class="action-text">Chat with AI</span>
                    </a>
                    <a href="#" class="action-btn tertiary">
                        <span class="action-icon">🎯</span>
                        <span class="action-text">Practice</span>
                    </a>
                    <a href="#" class="action-btn quaternary">
                        <span class="action-icon">📊</span>
                        <span class="action-text">Statistics</span>
                    </a>
                </div>
            </section>

            <!-- Today's Goal -->
            <section class="dashboard-card goal-card">
                <h3>🎪 Today's Goal</h3>
                <div class="goal-item">
                    <p class="goal-title">Learn 10 new words</p>
                    <div class="goal-bar">
                        <div class="goal-fill" style="width: 80%"></div>
                    </div>
                    <p class="goal-text">8 of 10 completed</p>
                </div>
                <div class="goal-item">
                    <p class="goal-title">30 minutes practice</p>
                    <div class="goal-bar">
                        <div class="goal-fill" style="width: 60%"></div>
                    </div>
                    <p class="goal-text">18 of 30 minutes</p>
                </div>
            </section>

            <!-- Leaderboard -->
            <section class="dashboard-card leaderboard-card">
                <h3>🏅 Top Learners</h3>
                <div class="leaderboard">
                    <div class="leaderboard-item">
                        <span class="rank">1.</span>
                        <span class="name">Lisa Chen</span>
                        <span class="score">2,450 pts</span>
                    </div>
                    <div class="leaderboard-item">
                        <span class="rank">2.</span>
                        <span class="name">John Smith</span>
                        <span class="score">2,180 pts</span>
                    </div>
                    <div class="leaderboard-item">
                        <span class="rank">3.</span>
                        <span class="name">Maria Garcia</span>
                        <span class="score">1,920 pts</span>
                    </div>
                    <div class="leaderboard-item your-rank">
                        <span class="rank">12.</span>
                        <span class="name">You ({{ Auth::user()->name }})</span>
                        <span class="score">850 pts</span>
                    </div>
                </div>
            </section>

            <!-- Recommended Topics -->
            <section class="dashboard-card topics-card">
                <h3>⭐ Recommended for You</h3>
                <div class="topics-grid">
                    <div class="topic-card">
                        <div class="topic-header">Greetings & Introductions</div>
                        <p class="topic-desc">Learn basic Chinese greetings</p>
                        <button class="btn btn-small">Start</button>
                    </div>
                    <div class="topic-card">
                        <div class="topic-header">Numbers & Counting</div>
                        <p class="topic-desc">Master counting 1-100</p>
                        <button class="btn btn-small">Start</button>
                    </div>
                    <div class="topic-card">
                        <div class="topic-header">Daily Expressions</div>
                        <p class="topic-desc">Common daily phrases</p>
                        <button class="btn btn-small">Start</button>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

<style>
    /* Client Layout Styles */
    .client-container {
        min-height: 100vh;
        background: linear-gradient(135deg, #B8E8E6 0%, #9FE2E0 50%, #7CD5D4 100%);
        padding: 0;
    }

    .client-navbar {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        padding: 20px 40px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: 0 2px 16px rgba(93, 187, 186, 0.1);
        position: sticky;
        top: 0;
        z-index: 100;
    }

    .navbar-brand {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .brand-logo {
        font-size: 32px;
    }

    .navbar-brand h1 {
        font-size: 20px;
        margin: 0;
        color: #5DBBBA;
        font-weight: 700;
    }

    .nav-menu {
        list-style: none;
        display: flex;
        gap: 30px;
        margin: 0;
        padding: 0;
        flex: 1;
        justify-content: center;
    }

    .nav-link {
        text-decoration: none;
        color: #2D3748;
        font-weight: 500;
        padding: 8px 0;
        border-bottom: 2px solid transparent;
        transition: all 0.3s ease;
    }

    .nav-link:hover,
    .nav-link.active {
        color: #5DBBBA;
        border-bottom-color: #5DBBBA;
    }

    .nav-user {
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .user-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #5DBBBA 0%, #7CD5D4 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 18px;
    }

    .user-name {
        margin: 0;
        font-weight: 600;
        color: #2D3748;
        font-size: 14px;
    }

    .user-level {
        margin: 0;
        font-size: 12px;
        color: #718096;
    }

    .btn-logout {
        background: linear-gradient(135deg, #DC143C 0%, #B8060B 100%);
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.3s ease;
        font-size: 14px;
    }

    .btn-logout:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(220, 20, 60, 0.3);
    }

    .client-main {
        padding: 40px;
        max-width: 1400px;
        margin: 0 auto;
    }

    /* Welcome Section */
    .welcome-section {
        margin-bottom: 40px;
    }

    .welcome-content h2 {
        font-size: 32px;
        color: #2D3748;
        margin: 0 0 8px 0;
        font-weight: 700;
    }

    .welcome-content p {
        color: #718096;
        margin: 0 0 24px 0;
        font-size: 16px;
    }

    .welcome-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 16px;
    }

    .stat-card {
        background: white;
        padding: 20px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        gap: 16px;
        box-shadow: 0 2px 8px rgba(93, 187, 186, 0.1);
        animation: fadeIn 0.6s ease-out;
    }

    .stat-icon {
        font-size: 32px;
    }

    .stat-info {
        flex: 1;
    }

    .stat-label {
        margin: 0;
        font-size: 12px;
        color: #718096;
        text-transform: uppercase;
    }

    .stat-value {
        margin: 4px 0 0 0;
        font-size: 24px;
        font-weight: 700;
        color: #5DBBBA;
    }

    /* Content Grid */
    .content-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        gap: 24px;
        margin-top: 30px;
    }

    .dashboard-card {
        background: white;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 4px 16px rgba(93, 187, 186, 0.1);
        animation: fadeIn 0.6s ease-out;
    }

    .dashboard-card h3 {
        margin: 0 0 20px 0;
        font-size: 18px;
        color: #2D3748;
        font-weight: 700;
    }

    /* Learning Card */
    .progress-levels {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .level-item {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .level-badge {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 16px;
        border-radius: 8px;
        font-weight: 600;
        color: white;
    }

    .level-badge.hsk1 {
        background: linear-gradient(135deg, #68D391 0%, #48BB78 100%);
    }

    .level-badge.hsk2 {
        background: linear-gradient(135deg, #FFD700 0%, #FFC300 100%);
        color: #2D3748;
    }

    .level-badge.hsk3 {
        background: linear-gradient(135deg, #DC143C 0%, #B8060B 100%);
    }

    .level-progress {
        font-size: 14px;
    }

    .level-bar {
        width: 100%;
        height: 8px;
        background: #E0F2F1;
        border-radius: 4px;
        overflow: hidden;
    }

    .level-fill {
        height: 100%;
        background: linear-gradient(90deg, #5DBBBA 0%, #7CD5D4 100%);
        transition: width 0.3s ease;
    }

    /* Activity Card */
    .activity-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .activity-item {
        display: flex;
        gap: 12px;
        padding: 12px;
        background: #FAFEFE;
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .activity-item:hover {
        background: #F0F7F7;
        transform: translateX(4px);
    }

    .activity-icon {
        font-size: 20px;
        min-width: 24px;
    }

    .activity-details {
        flex: 1;
    }

    .activity-title {
        margin: 0;
        font-size: 14px;
        font-weight: 600;
        color: #2D3748;
    }

    .activity-time {
        margin: 4px 0 0 0;
        font-size: 12px;
        color: #718096;
    }

    /* Actions Card */
    .action-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }

    .action-btn {
        padding: 16px;
        border-radius: 12px;
        text-decoration: none;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
        font-weight: 600;
        color: white;
        text-align: center;
    }

    .action-btn.primary {
        background: linear-gradient(135deg, #5DBBBA 0%, #4A9A99 100%);
    }

    .action-btn.secondary {
        background: linear-gradient(135deg, #9FE2E0 0%, #7CD5D4 100%);
    }

    .action-btn.tertiary {
        background: linear-gradient(135deg, #FFD700 0%, #FFC300 100%);
        color: #2D3748;
    }

    .action-btn.quaternary {
        background: linear-gradient(135deg, #68D391 0%, #48BB78 100%);
    }

    .action-btn:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(93, 187, 186, 0.2);
    }

    .action-icon {
        font-size: 24px;
    }

    .action-text {
        font-size: 12px;
    }

    /* Goal Card */
    .goal-item {
        margin-bottom: 16px;
    }

    .goal-item:last-child {
        margin-bottom: 0;
    }

    .goal-title {
        margin: 0 0 8px 0;
        font-weight: 600;
        color: #2D3748;
        font-size: 14px;
    }

    .goal-bar {
        width: 100%;
        height: 12px;
        background: #E0F2F1;
        border-radius: 6px;
        overflow: hidden;
        margin-bottom: 8px;
    }

    .goal-fill {
        height: 100%;
        background: linear-gradient(90deg, #5DBBBA 0%, #7CD5D4 100%);
        transition: width 0.3s ease;
    }

    .goal-text {
        margin: 0;
        font-size: 12px;
        color: #718096;
    }

    /* Leaderboard Card */
    .leaderboard {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .leaderboard-item {
        display: flex;
        align-items: center;
        padding: 12px;
        background: #FAFEFE;
        border-radius: 8px;
        gap: 12px;
        transition: all 0.3s ease;
    }

    .leaderboard-item:hover {
        background: #F0F7F7;
        transform: translateX(4px);
    }

    .leaderboard-item.your-rank {
        background: linear-gradient(135deg, rgba(93, 187, 186, 0.1) 0%, rgba(124, 213, 212, 0.1) 100%);
        border: 1px solid #E0F2F1;
    }

    .rank {
        font-weight: 700;
        color: #5DBBBA;
        min-width: 30px;
    }

    .leaderboard-item .name {
        flex: 1;
        font-weight: 600;
        color: #2D3748;
        font-size: 14px;
    }

    .leaderboard-item .score {
        font-weight: 700;
        color: #5DBBBA;
        font-size: 14px;
    }

    /* Topics Card */
    .topics-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 12px;
    }

    .topic-card {
        background: #FAFEFE;
        padding: 16px;
        border-radius: 12px;
        text-align: center;
        transition: all 0.3s ease;
        border: 1px solid #E0F2F1;
    }

    .topic-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(93, 187, 186, 0.1);
        border-color: #5DBBBA;
    }

    .topic-header {
        font-weight: 600;
        color: #2D3748;
        font-size: 14px;
        margin-bottom: 8px;
    }

    .topic-desc {
        font-size: 12px;
        color: #718096;
        margin: 0 0 12px 0;
    }

    .btn.btn-small {
        padding: 6px 12px;
        font-size: 12px;
        background: linear-gradient(135deg, #5DBBBA 0%, #4A9A99 100%);
        color: white;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn.btn-small:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(93, 187, 186, 0.2);
    }

    /* Animations */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Responsive */
    @media (max-width: 1024px) {
        .client-navbar {
            padding: 16px 24px;
        }

        .nav-menu {
            gap: 20px;
        }

        .client-main {
            padding: 24px;
        }

        .content-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .client-navbar {
            flex-direction: column;
            gap: 16px;
            padding: 16px;
        }

        .navbar-brand {
            width: 100%;
        }

        .nav-menu {
            width: 100%;
            gap: 16px;
            padding: 12px 0;
            border-bottom: 1px solid #E0F2F1;
        }

        .nav-user {
            width: 100%;
            justify-content: space-between;
        }

        .welcome-content h2 {
            font-size: 24px;
        }

        .welcome-stats {
            grid-template-columns: 1fr;
        }

        .client-main {
            padding: 16px;
        }

        .action-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle logout button
        const logoutBtn = document.getElementById('logoutBtn');
        if (logoutBtn) {
            logoutBtn.addEventListener('click', async function(e) {
                e.preventDefault();
                if (confirm('Are you sure you want to logout?')) {
                    try {
                        const response = await fetch('/client/logout', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Content-Type': 'application/json'
                            }
                        });
                        if (response.ok) {
                            window.location.href = '/client';
                        }
                    } catch (error) {
                        console.error('Logout error:', error);
                        alert('Error logging out. Please try again.');
                    }
                }
            });
        }

        // Set active nav link based on current page
        const currentPage = window.location.pathname;
        document.querySelectorAll('.nav-link').forEach(link => {
            if (link.getAttribute('href') === currentPage) {
                link.classList.add('active');
            }
        });
    });
</script>
@endpush
