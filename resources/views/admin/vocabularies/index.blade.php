@extends('layouts.admin')

@section('title', 'Vocabularies')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h2><i class="bi bi-book"></i> Vocabularies Management</h2>
        <p class="text-muted">Manage all Chinese vocabulary words</p>
    </div>
    <a href="{{ route('admin.vocabularies.create') }}" class="btn btn-success">
        <i class="bi bi-plus-circle"></i> Add New Vocabulary
    </a>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Level</label>
                <select name="level" class="form-select" onchange="this.form.submit()">
                    <option value="all" {{ request('level') == 'all' ? 'selected' : '' }}>All Levels</option>
                    @foreach(['HSK1', 'HSK2', 'HSK3', 'HSK4', 'HSK5', 'HSK6'] as $level)
                    <option value="{{ $level }}" {{ request('level') == $level ? 'selected' : '' }}>{{ $level }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-md-3">
                <label class="form-label">Topic</label>
                <select name="topic_id" class="form-select" onchange="this.form.submit()">
                    <option value="all" {{ request('topic_id') == 'all' ? 'selected' : '' }}>All Topics</option>
                    @foreach($topics as $topic)
                    <option value="{{ $topic->id }}" {{ request('topic_id') == $topic->id ? 'selected' : '' }}>
                        {{ $topic->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-md-4">
                <label class="form-label">Search</label>
                <input type="text" 
                       name="search" 
                       class="form-control" 
                       placeholder="Search by word, pinyin, or meaning..." 
                       value="{{ request('search') }}">
            </div>
            
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search"></i> Search
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Vocabularies List -->
<div class="card">
    <div class="card-header">
        <i class="bi bi-list"></i> Vocabularies ({{ $vocabularies->total() }} total)
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Word</th>
                        <th>Pinyin</th>
                        <th>Meaning</th>
                        <th>Topic</th>
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
                            @if($vocab->traditional && $vocab->traditional !== $vocab->simplified)
                            <br><small class="text-muted">Trad: {{ $vocab->traditional }}</small>
                            @endif
                        </td>
                        <td>{{ $vocab->pinyin }}</td>
                        <td>{{ Str::limit($vocab->meaning, 50) }}</td>
                        <td>
                            <a href="{{ route('admin.topics.show', $vocab->topic_id) }}" class="text-decoration-none">
                                {{ $vocab->topic->name }}
                            </a>
                        </td>
                        <td>
                            <span class="badge badge-level bg-info">{{ $vocab->level }}</span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.vocabularies.show', $vocab->id) }}" 
                                   class="btn btn-outline-primary" title="View">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.vocabularies.edit', $vocab->id) }}" 
                                   class="btn btn-outline-warning" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.vocabularies.destroy', $vocab->id) }}" 
                                      method="POST" 
                                      onsubmit="return confirm('Delete this vocabulary?');"
                                      class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                            <p class="mb-0">No vocabularies found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($vocabularies->hasPages())
    <div class="card-footer">
        {{ $vocabularies->appends(request()->query())->links() }}
    </div>
    @endif
</div>
@endsection
