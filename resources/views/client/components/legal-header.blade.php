{{-- Legal Header Component (No Auth Required) --}}
<nav class="legal-navbar">
    <a href="{{ route('client.index') }}" class="navbar-brand">
        <h1>Chinese Learner</h1>
    </a>
    <div class="nav-actions">
        @auth
            <a href="{{ route('client.home') }}" class="btn-primary">Dashboard</a>
        @else
            <a href="{{ route('client.login') }}" class="btn-outline">Login</a>
            <a href="{{ route('client.register') }}" class="btn-primary">Sign Up</a>
        @endauth
    </div>
</nav>

<style>
    /* Legal Navbar Styles */
    .legal-navbar {
        background: rgba(255, 255, 255, 0.98);
        backdrop-filter: blur(20px);
        padding: 1.25rem 2.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        position: sticky;
        top: 0;
        z-index: 1000;
    }

    .legal-navbar .navbar-brand {
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .legal-navbar .navbar-brand h1 {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1a1a1a;
        margin: 0;
        background: linear-gradient(135deg, #2563eb 0%, #7c3aed 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .nav-actions {
        display: flex;
        gap: 12px;
        align-items: center;
    }

    .btn-outline,
    .btn-primary {
        padding: 10px 24px;
        border-radius: 10px;
        font-size: 0.9375rem;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
        display: inline-block;
    }

    .btn-outline {
        background: transparent;
        border: 2px solid #e5e7eb;
        color: #374151;
    }

    .btn-outline:hover {
        border-color: #2563eb;
        color: #2563eb;
        background: rgba(37, 99, 235, 0.05);
    }

    .btn-primary {
        background: linear-gradient(135deg, #2563eb 0%, #7c3aed 100%);
        border: none;
        color: white;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
    }

    .btn-primary:hover {
        box-shadow: 0 6px 20px rgba(37, 99, 235, 0.3);
        transform: translateY(-2px);
    }

    @media (max-width: 768px) {
        .legal-navbar {
            padding: 1rem 1.5rem;
        }

        .legal-navbar .navbar-brand h1 {
            font-size: 1.25rem;
        }

        .btn-outline,
        .btn-primary {
            padding: 8px 16px;
            font-size: 0.875rem;
        }
    }
</style>
