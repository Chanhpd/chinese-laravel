@extends('layouts.admin')

@section('title', 'Topic Details - ' . $topic->name)

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h2><i class="bi bi-folder"></i> {{ $topic->name }}</h2>
        <p class="text-muted chinese-text" style="font-size: 1.3rem;">{{ $topic->name_zh }}</p>
    </div>
    <div>
        <a href="{{ route('admin.topics.edit', $topic->id) }}" class="btn btn-warning">
            <i class="bi bi-pencil"></i> Edit Topic
        </a>
        <a href="{{ route('admin.topics.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to List
        </a>
    </div>
</div>

<!-- Topic Info -->
<div class="row mb-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-info-circle"></i> Topic Information
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-3">ID:</dt>
                    <dd class="col-sm-9">{{ $topic->id }}</dd>
                    
                    <dt class="col-sm-3">English Name:</dt>
                    <dd class="col-sm-9">{{ $topic->name }}</dd>
                    
                    <dt class="col-sm-3">Chinese Name:</dt>
                    <dd class="col-sm-9 chinese-text">{{ $topic->name_zh }}</dd>
                    
                    <dt class="col-sm-3">Description:</dt>
                    <dd class="col-sm-9">{{ $topic->description ?: 'No description' }}</dd>
                    
                    <dt class="col-sm-3">Status:</dt>
                    <dd class="col-sm-9">
                        @if($topic->is_active)
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-secondary">Inactive</span>
                        @endif
                    </dd>
                    
                    <dt class="col-sm-3">Sort Order:</dt>
                    <dd class="col-sm-9">{{ $topic->sort_order }}</dd>
                    
                    <dt class="col-sm-3">Image URL:</dt>
                    <dd class="col-sm-9">
                        @if($topic->image_url)
                            <a href="{{ $topic->image_url }}" target="_blank">{{ Str::limit($topic->image_url, 50) }}</a>
                        @else
                            <span class="text-muted">No image</span>
                        @endif
                    </dd>
                </dl>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-bar-chart"></i> Vocabulary Statistics
            </div>
            <div class="card-body">
                <h3 class="text-center mb-3">{{ $vocabularies->total() }}</h3>
                <p class="text-center text-muted mb-4">Total Vocabularies</p>
                
                <div class="list-group">
                    @foreach(['HSK1', 'HSK2', 'HSK3', 'HSK4', 'HSK5', 'HSK6'] as $lvl)
                        @php $count = $vocabCountByLevel[$lvl] ?? 0; @endphp
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <span>{{ $lvl }}</span>
                            <span class="badge bg-primary rounded-pill">{{ $count }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Translations -->
@if($topic->translations->isNotEmpty())
<div class="card mb-4">
    <div class="card-header">
        <i class="bi bi-translate"></i> Translations
    </div>
    <div class="card-body">
        <div class="row">
            @foreach($topic->translations as $translation)
            <div class="col-md-6 mb-3">
                <div class="p-3 border rounded">
                    <h6 class="text-muted mb-2">
                        <span class="badge bg-secondary">{{ strtoupper($translation->language_code) }}</span>
                    </h6>
                    <strong>{{ $translation->name }}</strong>
                    @if($translation->description)
                    <br><small class="text-muted">{{ $translation->description }}</small>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

<!-- Vocabularies List -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-book"></i> Vocabularies ({{ $vocabularies->total() }} total)</span>
        <a href="{{ route('admin.vocabularies.create', ['topic_id' => $topic->id]) }}" 
           class="btn btn-sm btn-success">
            <i class="bi bi-plus-circle"></i> Add Vocabulary to This Topic
        </a>
    </div>
    <div class="card-body">
        <!-- Level Filter -->
        <div class="mb-3">
            <div class="btn-group" role="group">
                <a href="{{ route('admin.topics.show', ['topic' => $topic->id, 'level' => 'all']) }}" 
                   class="btn btn-sm {{ $level == 'all' ? 'btn-primary' : 'btn-outline-primary' }}">
                    All Levels
                </a>
                @foreach(['HSK1', 'HSK2', 'HSK3', 'HSK4', 'HSK5', 'HSK6'] as $lvl)
                <a href="{{ route('admin.topics.show', ['topic' => $topic->id, 'level' => $lvl]) }}" 
                   class="btn btn-sm {{ $level == $lvl ? 'btn-primary' : 'btn-outline-primary' }}">
                    {{ $lvl }}
                </a>
                @endforeach
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Word</th>
                        <th>Pinyin</th>
                        <th>Meaning</th>
                        <th>Level</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vocabularies as $vocab)
                    <tr>
                        <td>{{ $vocab->id }}</td>
                        <td>
                            <span class="chinese-text">{{ $vocab->word }}</span>
                        </td>
                        <td>{{ $vocab->pinyin }}</td>
                        <td>{{ Str::limit($vocab->meaning, 50) }}</td>
                        <td>
                            <span class="badge badge-level bg-info">{{ $vocab->level }}</span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.vocabularies.show', $vocab->id) }}" 
                                   class="btn btn-outline-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.vocabularies.edit', $vocab->id) }}" 
                                   class="btn btn-outline-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                            <p class="mb-0">No vocabularies found for this topic</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($vocabularies->hasPages())
    <div class="card-footer">
        {{ $vocabularies->appends(['level' => $level])->links() }}
    </div>
    @endif
</div>
@endsection
