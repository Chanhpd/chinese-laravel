@extends('layouts.admin')

@section('title', 'Topics')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h2><i class="bi bi-folder"></i> Topics Management</h2>
        <p class="text-muted">Manage all learning topics organized by HSK level</p>
    </div>
    <a href="{{ route('admin.topics.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Add New Topic
    </a>
</div>

<!-- Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Filter by Level</label>
                <select name="level" class="form-select" onchange="this.form.submit()">
                    <option value="all" {{ $level == 'all' ? 'selected' : '' }}>All Levels</option>
                    <option value="HSK1" {{ $level == 'HSK1' ? 'selected' : '' }}>HSK1</option>
                    <option value="HSK2" {{ $level == 'HSK2' ? 'selected' : '' }}>HSK2</option>
                    <option value="HSK3" {{ $level == 'HSK3' ? 'selected' : '' }}>HSK3</option>
                    <option value="HSK4" {{ $level == 'HSK4' ? 'selected' : '' }}>HSK4</option>
                    <option value="HSK5" {{ $level == 'HSK5' ? 'selected' : '' }}>HSK5</option>
                    <option value="HSK6" {{ $level == 'HSK6' ? 'selected' : '' }}>HSK6</option>
                </select>
            </div>
        </form>
    </div>
</div>

<!-- Topics List -->
<div class="card">
    <div class="card-header">
        <i class="bi bi-list"></i> Topics List ({{ $topics->total() }} total)
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name (EN)</th>
                        <th>Name (ZH)</th>
                        <th>Vocabularies by Level</th>
                        <th>Status</th>
                        <th>Sort</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topics as $topic)
                    <tr>
                        <td>{{ $topic->id }}</td>
                        <td>
                            <strong>{{ $topic->name }}</strong>
                            @if($topic->description)
                            <br><small class="text-muted">{{ Str::limit($topic->description, 50) }}</small>
                            @endif
                        </td>
                        <td>
                            <span class="chinese-text" style="font-size: 1.2rem;">{{ $topic->name_zh }}</span>
                        </td>
                        <td>
                            <div class="d-flex flex-wrap gap-1">
                                @foreach(['HSK1', 'HSK2', 'HSK3', 'HSK4', 'HSK5', 'HSK6'] as $lvl)
                                    @php
                                        $count = $topic->vocab_by_level[$lvl] ?? 0;
                                    @endphp
                                    @if($count > 0)
                                        <span class="badge bg-info">{{ $lvl }}: {{ $count }}</span>
                                    @endif
                                @endforeach
                                @if($topic->vocabularies->isEmpty())
                                    <small class="text-muted">No vocabularies</small>
                                @endif
                            </div>
                        </td>
                        <td>
                            @if($topic->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </td>
                        <td>{{ $topic->sort_order }}</td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.topics.show', $topic->id) }}" 
                                   class="btn btn-outline-primary" title="View">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.topics.edit', $topic->id) }}" 
                                   class="btn btn-outline-warning" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.topics.destroy', $topic->id) }}" 
                                      method="POST" 
                                      onsubmit="return confirm('Are you sure you want to delete this topic?');"
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
                            <p class="mb-0">No topics found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($topics->hasPages())
    <div class="card-footer">
        {{ $topics->links() }}
    </div>
    @endif
</div>
@endsection
