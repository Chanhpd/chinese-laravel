@extends('layouts.admin')

@section('title', 'Exams Management')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h2><i class="bi bi-clipboard-check"></i> Exams Management</h2>
        <p class="text-muted">Manage HSK exam tests and questions</p>
    </div>
    <a href="{{ route('admin.exams.store') }}" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addExamModal">
        <i class="bi bi-plus-circle"></i> Create New Exam
    </a>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <h5 class="card-title">Total Exams</h5>
                <h2>{{ $exams->total() }}</h2>
                <small>All levels</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-success">
            <div class="card-body">
                <h5 class="card-title">Active Exams</h5>
                <h2>{{ $activeCount }}</h2>
                <small>Currently available</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-info">
            <div class="card-body">
                <h5 class="card-title">Total Attempts</h5>
                <h2>{{ $totalAttempts }}</h2>
                <small>By all users</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-warning">
            <div class="card-body">
                <h5 class="card-title">Avg Completion</h5>
                <h2>{{ number_format($avgCompletion, 1) }}%</h2>
                <small>Success rate</small>
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
                <select name="level" class="form-select" onchange="this.form.submit()">
                    <option value="">All Levels</option>
                    @foreach(['HSK1', 'HSK2', 'HSK3', 'HSK4', 'HSK5', 'HSK6'] as $level)
                    <option value="{{ $level }}" {{ request('level') == $level ? 'selected' : '' }}>{{ $level }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="is_active" class="form-select" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            
            <div class="col-md-4">
                <label class="form-label">Search</label>
                <input type="text" 
                       name="search" 
                       class="form-control" 
                       placeholder="Search by exam title..." 
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

<!-- Exams Table -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-list"></i> Exams List ({{ $exams->total() }} total)</span>
        <div>
            <select class="form-select form-select-sm d-inline-block w-auto" onchange="window.location.href=this.value">
                <option value="?per_page=10" {{ request('per_page') == 10 ? 'selected' : '' }}>10 per page</option>
                <option value="?per_page=20" {{ request('per_page') == 20 ? 'selected' : '' }}>20 per page</option>
                <option value="?per_page=50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 per page</option>
            </select>
        </div>
    </div>
    <div class="card-body">
        @if($exams->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Level</th>
                        <th>Duration</th>
                        <th>Parts</th>
                        <th>Attempts</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($exams as $exam)
                    <tr>
                        <td>{{ $exam->id }}</td>
                        <td>
                            <strong>{{ $exam->title }}</strong>
                        </td>
                        <td>
                            <span class="badge bg-info">{{ $exam->level }}</span>
                        </td>
                        <td>
                            <i class="bi bi-clock"></i> {{ $exam->time }} min
                        </td>
                        <td>
                            <span class="badge bg-secondary">{{ $exam->parts_count ?? 0 }} parts</span>
                        </td>
                        <td>
                            <span class="badge bg-primary">{{ $exam->attempts_count ?? 0 }} attempts</span>
                        </td>
                        <td>
                            @if($exam->is_active)
                            <span class="badge bg-success">Active</span>
                            @else
                            <span class="badge bg-danger">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <small class="text-muted">{{ $exam->created_at->format('Y-m-d') }}</small>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.exams.show', $exam->id) }}" 
                                   class="btn btn-outline-primary" 
                                   title="View Details">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.exams.statistics', $exam->id) }}" 
                                   class="btn btn-outline-info" 
                                   title="Statistics">
                                    <i class="bi bi-graph-up"></i>
                                </a>
                                <form action="{{ route('admin.exams.toggle-active', $exam->id) }}" 
                                      method="POST" 
                                      class="d-inline">
                                    @csrf
                                    <button type="submit" 
                                            class="btn btn-outline-{{ $exam->is_active ? 'warning' : 'success' }}" 
                                            title="{{ $exam->is_active ? 'Deactivate' : 'Activate' }}">
                                        <i class="bi bi-{{ $exam->is_active ? 'pause' : 'play' }}-circle"></i>
                                    </button>
                                </form>
                                <button class="btn btn-outline-secondary" 
                                        onclick="duplicateExam({{ $exam->id }})"
                                        title="Duplicate">
                                    <i class="bi bi-files"></i>
                                </button>
                                <form action="{{ route('admin.exams.destroy', $exam->id) }}" 
                                      method="POST" 
                                      class="d-inline"
                                      onsubmit="return confirm('Are you sure you want to delete this exam? This action cannot be undone.')">
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
            {{ $exams->links() }}
        </div>
        @else
        <div class="alert alert-info text-center">
            <i class="bi bi-info-circle"></i> No exams found. Click "Create New Exam" to get started.
        </div>
        @endif
    </div>
</div>

<style>
.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
    border: none;
    margin-bottom: 1.5rem;
}

.badge {
    font-size: 0.75rem;
    padding: 0.35rem 0.65rem;
}

.btn-group-sm > .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}
</style>

<script>
function duplicateExam(id) {
    if (confirm('Do you want to duplicate this exam?')) {
        fetch(`/admin/exams/${id}/duplicate`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Exam duplicated successfully!');
                window.location.reload();
            }
        })
        .catch(error => {
            alert('Error duplicating exam');
            console.error(error);
        });
    }
}
</script>
@endsection
