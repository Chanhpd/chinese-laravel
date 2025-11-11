<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') - Chinese Learning App</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        * {
            box-sizing: border-box;
        }
        body {
            min-height: 100vh;
            background-color: #f8f9fa;
            overflow-x: hidden;
            position: relative;
        }
        /* Hide any decorative pseudo elements */
        body::before,
        body::after {
            display: none !important;
        }
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 12px 20px;
            margin: 4px 0;
            border-radius: 8px;
            transition: all 0.3s;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background-color: rgba(255, 255, 255, 0.2);
            color: white;
        }
        .sidebar .nav-link i {
            width: 20px;
            margin-right: 10px;
        }
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            margin-bottom: 24px;
        }
        .card-header {
            background-color: white;
            border-bottom: 2px solid #f0f0f0;
            padding: 16px 24px;
            font-weight: 600;
        }
        .badge-level {
            font-size: 0.75rem;
            padding: 4px 10px;
            font-weight: 600;
        }
        .chinese-text {
            font-size: 1.5rem;
            font-weight: 500;
            color: #2d3748;
        }
        .btn-group-sm .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
        .alert {
            border-radius: 10px;
            border: none;
        }
        .table th {
            font-weight: 600;
            color: #4a5568;
            border-bottom: 2px solid #e2e8f0;
        }
        .page-header {
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e2e8f0;
        }
        /* Fix pagination styles */
        .pagination {
            margin-bottom: 0;
        }
        .pagination .page-link {
            color: #667eea;
            border: 1px solid #e2e8f0;
        }
        .pagination .page-link:hover {
            color: #764ba2;
            background-color: #f8f9fa;
            border-color: #e2e8f0;
        }
        .pagination .page-item.active .page-link {
            background-color: #667eea;
            border-color: #667eea;
            color: white;
        }
        .pagination .page-item.disabled .page-link {
            color: #cbd5e0;
        }
        .pagination i {
            font-size: 0.875rem;
        }
        .card-footer {
            background-color: white;
            padding: 15px 24px;
        }
        /* Prevent overflow and hide decorative elements */
        body {
            overflow-x: hidden;
            position: relative;
        }
        /* Hide any large pseudo elements that might be decorative */
        body::before,
        body::after,
        .container-fluid::before,
        .container-fluid::after,
        main::before,
        main::after {
            display: none !important;
        }
        /* Ensure no absolute positioned elements overflow */
        body > *:not(.container-fluid) {
            display: none;
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-2 d-md-block sidebar p-3">
                <div class="position-sticky">
                    <div class="mb-4 pb-3 border-bottom border-light">
                        <h4 class="text-white mb-0">
                            <i class="bi bi-translate"></i> Chinese Learn
                        </h4>
                        <small class="text-white-50">Admin Panel</small>
                    </div>
                    
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" 
                               href="{{ route('admin.dashboard') }}">
                                <i class="bi bi-speedometer2"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.topics.*') ? 'active' : '' }}" 
                               href="{{ route('admin.topics.index') }}">
                                <i class="bi bi-folder"></i> Topics
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.vocabularies.*') ? 'active' : '' }}" 
                               href="{{ route('admin.vocabularies.index') }}">
                                <i class="bi bi-book"></i> Vocabularies
                            </a>
                        </li>
                        
                        <li class="nav-item mt-4 pt-3 border-top border-light">
                            <small class="text-white-50 px-3">HSK LEVELS</small>
                        </li>
                        @foreach(['HSK1', 'HSK2', 'HSK3', 'HSK4', 'HSK5', 'HSK6'] as $level)
                        <li class="nav-item">
                            <a class="nav-link {{ request('level') == $level ? 'active' : '' }}" 
                               href="{{ route('admin.vocabularies.index', ['level' => $level]) }}">
                                <i class="bi bi-circle-fill" style="font-size: 8px;"></i> {{ $level }}
                            </a>
                        </li>
                        @endforeach
                        
                        <li class="nav-item mt-4 pt-3 border-top border-light">
                            <a class="nav-link" href="/" target="_blank">
                                <i class="bi bi-box-arrow-up-right"></i> View API Docs
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-10 ms-sm-auto px-md-4 py-4">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    @stack('scripts')
</body>
</html>
