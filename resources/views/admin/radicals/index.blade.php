@extends('layouts.admin')

@section('title', 'Radicals Management')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h2><i class="bi bi-grid-3x3"></i> Radicals Management</h2>
        <p class="text-muted">Manage Chinese radicals and characters</p>
    </div>
    <a href="{{ route('admin.radicals.store') }}" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addRadicalModal">
        <i class="bi bi-plus-circle"></i> Add New Radical
    </a>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <h5 class="card-title">Total Radicals</h5>
                <h2>{{ $radicals->total() }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-success">
            <div class="card-body">
                <h5 class="card-title">HSK Levels</h5>
                <h2>{{ $levels->count() }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-info">
            <div class="card-body">
                <h5 class="card-title">Avg Stroke Count</h5>
                <h2>{{ number_format($avgStrokeCount, 1) }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-warning">
            <div class="card-body">
                <h5 class="card-title">Favorites</h5>
                <h2>{{ $favoriteCount }}</h2>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">HSK Level</label>
                <select name="level_number" class="form-select" onchange="this.form.submit()">
                    <option value="">All Levels</option>
                    @foreach($levels as $level)
                    <option value="{{ $level->level_number }}" {{ request('level_number') == $level->level_number ? 'selected' : '' }}>
                        {{ $level->level_name }} ({{ $level->radicals_count ?? 0 }})
                    </option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-md-2">
                <label class="form-label">Stroke Count</label>
                <select name="stroke_count" class="form-select" onchange="this.form.submit()">
                    <option value="">All Strokes</option>
                    @for($i = 1; $i <= 20; $i++)
                    <option value="{{ $i }}" {{ request('stroke_count') == $i ? 'selected' : '' }}>{{ $i }} strokes</option>
                    @endfor
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label">Favorites</label>
                <select name="is_favorite" class="form-select" onchange="this.form.submit()">
                    <option value="">All</option>
                    <option value="1" {{ request('is_favorite') == '1' ? 'selected' : '' }}>Favorites Only</option>
                </select>
            </div>
            
            <div class="col-md-3">
                <label class="form-label">Search</label>
                <input type="text" 
                       name="search" 
                       class="form-control" 
                       placeholder="Search by hanzi, pinyin, meaning..." 
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

<!-- Radicals Table -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-list"></i> Radicals List ({{ $radicals->total() }} total)</span>
        <div>
            <select class="form-select form-select-sm d-inline-block w-auto" onchange="window.location.href=this.value">
                <option value="?per_page=20" {{ request('per_page') == 20 ? 'selected' : '' }}>20 per page</option>
                <option value="?per_page=50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 per page</option>
                <option value="?per_page=100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 per page</option>
            </select>
        </div>
    </div>
    <div class="card-body">
        @if($radicals->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Character</th>
                        <th>Pinyin</th>
                        <th>Radical</th>
                        <th>Meaning (EN)</th>
                        <th>Meaning (VI)</th>
                        <th>Strokes</th>
                        <th>HSK Level</th>
                        <th>Rank</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($radicals as $radical)
                    <tr>
                        <td>{{ $radical->id }}</td>
                        <td>
                            <span class="chinese-text fs-4">{{ $radical->hanzi }}</span>
                            @if($radical->traditional && $radical->traditional !== $radical->hanzi)
                            <br><small class="text-muted">{{ $radical->traditional }}</small>
                            @endif
                        </td>
                        <td>{{ $radical->pinyin }}</td>
                        <td><span class="chinese-text fs-5">{{ $radical->radical }}</span></td>
                        <td>{{ Str::limit($radical->meaning_en ?? $radical->meaning, 30) }}</td>
                        <td>{{ Str::limit($radical->meaning_vi, 30) }}</td>
                        <td>
                            <span class="badge bg-secondary">{{ $radical->stroke_count }}</span>
                        </td>
                        <td>
                            @if($radical->level)
                            <span class="badge bg-info">{{ $radical->level->level_name }}</span>
                            @else
                            <span class="badge bg-secondary">N/A</span>
                            @endif
                        </td>
                        <td>{{ $radical->frequency_rank ?? 'N/A' }}</td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary" 
                                        onclick="viewRadical({{ $radical->id }})"
                                        title="View">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button class="btn btn-outline-warning" 
                                        onclick="editRadical({{ $radical->id }})"
                                        title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form action="{{ route('admin.radicals.destroy', $radical->id) }}" 
                                      method="POST" 
                                      class="d-inline"
                                      onsubmit="return confirm('Are you sure you want to delete this radical?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $radicals->links() }}
        </div>
        @else
        <div class="alert alert-info text-center">
            <i class="bi bi-info-circle"></i> No radicals found matching your criteria.
        </div>
        @endif
    </div>
</div>

<style>
.chinese-text {
    font-family: 'Noto Sans SC', 'Microsoft YaHei', sans-serif;
    font-weight: 500;
}

.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
    border: none;
    margin-bottom: 1.5rem;
}

.badge-level {
    font-size: 0.75rem;
    padding: 0.35rem 0.65rem;
}
</style>

<script>
function viewRadical(id) {
    // Implement view functionality - could open a modal or redirect to show page
    window.location.href = `/admin/radicals/${id}`;
}

function editRadical(id) {
    // Implement edit functionality - could open a modal or redirect to edit page
    window.location.href = `/admin/radicals/${id}/edit`;
}
</script>
@endsection
