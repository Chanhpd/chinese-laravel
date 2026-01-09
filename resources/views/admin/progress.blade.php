@extends('layouts.admin')

@section('title', 'User Progress')

@section('content')
<div class="page-header">
    <h2><i class="bi bi-graph-up-arrow"></i> User Progress & Analytics</h2>
    <p class="text-muted">Track user learning activity and progress</p>
</div>

<!-- User Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-white" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
            <div class="card-body">
                <h6 class="card-subtitle mb-2 text-white-50">Total Users</h6>
                <h2 class="card-title mb-0">{{ $totalUsers }}</h2>
                <small class="text-white-50">{{ $newUsersThisMonth }} new this month</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
            <div class="card-body">
                <h6 class="card-subtitle mb-2 text-white-50">Active Today</h6>
                <h2 class="card-title mb-0">{{ $activeUsersToday }}</h2>
                <small class="text-white-50">{{ $activeUsersWeek }} this week</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="card-body">
                <h6 class="card-subtitle mb-2 text-white-50">Progress Records</h6>
                <h2 class="card-title mb-0">{{ $totalProgressRecords }}</h2>
                <small class="text-white-50">learning sessions</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
            <div class="card-body">
                <h6 class="card-subtitle mb-2 text-white-50">Saved Words</h6>
                <h2 class="card-title mb-0">{{ $totalSavedWords }}</h2>
                <small class="text-white-50">by all users</small>
            </div>
        </div>
    </div>
</div>

<!-- Progress Metrics -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card border-info">
            <div class="card-body text-center">
                <div class="mb-2">
                    <i class="bi bi-speedometer2" style="font-size: 2rem; color: #4facfe;"></i>
                </div>
                <h3 class="mb-0">{{ number_format($averageCompletionRate, 1) }}%</h3>
                <small class="text-muted">Average Completion Rate</small>
            </div>
        </div>
    </div>
    <div class="col-md-9">
        <div class="card border-warning">
            <div class="card-header">
                <i class="bi bi-bar-chart"></i> Mastery Level Distribution
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-3">
                        <h2 class="mb-0 text-secondary">{{ $masteryDistribution['beginner'] }}</h2>
                        <small class="text-muted">Beginner</small>
                    </div>
                    <div class="col-3">
                        <h2 class="mb-0 text-info">{{ $masteryDistribution['intermediate'] }}</h2>
                        <small class="text-muted">Intermediate</small>
                    </div>
                    <div class="col-3">
                        <h2 class="mb-0 text-primary">{{ $masteryDistribution['advanced'] }}</h2>
                        <small class="text-muted">Advanced</small>
                    </div>
                    <div class="col-3">
                        <h2 class="mb-0 text-success">{{ $masteryDistribution['mastered'] }}</h2>
                        <small class="text-muted">Mastered</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Top Learners -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-trophy-fill text-warning"></i> Top Learners</span>
                <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-primary">View All Users</a>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    @forelse($topLearners as $index => $learner)
                    <a href="{{ route('admin.users.show', $learner->id) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <span class="badge bg-{{ $index == 0 ? 'warning' : ($index == 1 ? 'secondary' : ($index == 2 ? 'info' : 'primary')) }} me-3" style="width: 35px; font-size: 0.9rem;">
                                #{{ $index + 1 }}
                            </span>
                            <div>
                                <strong>{{ $learner->name }}</strong>
                                <br>
                                <small class="text-muted">{{ $learner->email }}</small>
                            </div>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-primary rounded-pill">{{ $learner->topic_progress_count }} topics</span>
                            <br>
                            <small class="text-muted">{{ $learner->saved_vocabularies_count }} saved words</small>
                        </div>
                    </a>
                    @empty
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                        <p class="mb-0">No learning activity yet</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Learning Activity -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-clock-history"></i> Recent Learning Activity
            </div>
            <div class="card-body" style="max-height: 600px; overflow-y: auto;">
                <div class="list-group list-group-flush">
                    @forelse($recentActivity as $activity)
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <a href="{{ route('admin.users.show', $activity->user_id) }}" class="text-decoration-none">
                                    <strong>{{ $activity->user->name }}</strong>
                                </a>
                                <br>
                                <small class="text-muted">
                                    studied <strong>{{ $activity->topic->name }}</strong>
                                    <br>
                                    {{ $activity->completed_words }}/{{ $activity->total_words }} words
                                    ({{ number_format($activity->progress_percentage, 1) }}%)
                                </small>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-{{ $activity->mastery_level == 'mastered' ? 'success' : ($activity->mastery_level == 'advanced' ? 'primary' : ($activity->mastery_level == 'intermediate' ? 'info' : 'secondary')) }}">
                                    {{ ucfirst($activity->mastery_level) }}
                                </span>
                                <br>
                                <small class="text-muted">{{ $activity->last_studied_at ? $activity->last_studied_at->diffForHumans() : 'Never' }}</small>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                        <p class="mb-0">No recent activity</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
