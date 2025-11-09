@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="page-header">
    <h2><i class="bi bi-speedometer2"></i> Dashboard Overview</h2>
    <p class="text-muted">Welcome to Chinese Learning App Admin Panel</p>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="card-body">
                <h6 class="card-subtitle mb-2 text-white-50">Total Topics</h6>
                <h2 class="card-title mb-0">{{ $totalTopics }}</h2>
                <small class="text-white-50">{{ $activeTopics }} active</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
            <div class="card-body">
                <h6 class="card-subtitle mb-2 text-white-50">Total Vocabularies</h6>
                <h2 class="card-title mb-0">{{ $totalVocabularies }}</h2>
                <small class="text-white-50">across all levels</small>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-bar-chart"></i> Vocabularies by HSK Level
            </div>
            <div class="card-body">
                <div class="row text-center">
                    @foreach($vocabByLevel as $level)
                    <div class="col-2">
                        <h4 class="mb-0">{{ $level->count }}</h4>
                        <small class="text-muted">{{ $level->level }}</small>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Top Topics -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-trophy"></i> Top Topics by Vocabulary Count</span>
                <a href="{{ route('admin.topics.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    @forelse($topTopics as $topic)
                    <a href="{{ route('admin.topics.show', $topic->id) }}" 
                       class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <div>
                            <strong>{{ $topic->name }}</strong>
                            <br>
                            <small class="text-muted">{{ $topic->name_zh }}</small>
                        </div>
                        <span class="badge bg-primary rounded-pill">{{ $topic->vocabularies_count }} words</span>
                    </a>
                    @empty
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                        <p class="mb-0">No topics yet</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Vocabularies -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-clock-history"></i> Recently Added Vocabularies</span>
                <a href="{{ route('admin.vocabularies.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    @forelse($recentVocabularies as $vocab)
                    <a href="{{ route('admin.vocabularies.show', $vocab->id) }}" 
                       class="list-group-item list-group-item-action">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <span class="chinese-text">{{ $vocab->word }}</span>
                                <span class="ms-2 text-muted">{{ $vocab->pinyin }}</span>
                                <br>
                                <small class="text-muted">{{ $vocab->meaning }}</small>
                            </div>
                            <div class="text-end">
                                <span class="badge badge-level bg-info">{{ $vocab->level }}</span>
                                <br>
                                <small class="text-muted">{{ $vocab->topic->name }}</small>
                            </div>
                        </div>
                    </a>
                    @empty
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                        <p class="mb-0">No vocabularies yet</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-lightning-charge"></i> Quick Actions
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3">
                        <a href="{{ route('admin.topics.create') }}" class="btn btn-outline-primary btn-lg w-100">
                            <i class="bi bi-plus-circle"></i><br>
                            <span>Add New Topic</span>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('admin.vocabularies.create') }}" class="btn btn-outline-success btn-lg w-100">
                            <i class="bi bi-plus-circle"></i><br>
                            <span>Add New Vocabulary</span>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('admin.topics.index') }}" class="btn btn-outline-info btn-lg w-100">
                            <i class="bi bi-folder"></i><br>
                            <span>Browse Topics</span>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('admin.vocabularies.index') }}" class="btn btn-outline-warning btn-lg w-100">
                            <i class="bi bi-book"></i><br>
                            <span>Browse Vocabularies</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
