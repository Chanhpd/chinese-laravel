{{-- Client Header Component --}}
<nav class="client-navbar">
    <div class="navbar-brand">
        <div class="brand-logo">🇨🇳</div>
        <h1>Chinese Learner</h1>
    </div>
    <ul class="nav-menu">
        <li><a href="{{ route('client.home') }}" class="nav-link {{ request()->routeIs('client.home') ? 'active' : '' }}">Dashboard</a></li>
        <li><a href="{{ route('client.radicals.index') }}" class="nav-link {{ request()->routeIs('client.radicals.*') ? 'active' : '' }}">Characters</a></li>
        <li><a href="{{ route('client.vocabulary.index') }}" class="nav-link {{ request()->routeIs('client.vocabulary.*') ? 'active' : '' }}">Vocabulary</a></li>
        <li><a href="{{ route('client.hsk.index') }}" class="nav-link {{ request()->routeIs('client.hsk.*') ? 'active' : '' }}">HSK</a></li>
        <li><a href="{{ route('client.tocfl.index') }}" class="nav-link {{ request()->routeIs('client.tocfl.*') ? 'active' : '' }}">TOCFL</a></li>
        <li><a href="{{ route('client.chat') }}" class="nav-link {{ request()->routeIs('client.chat') ? 'active' : '' }}">AI Chat</a></li>
    </ul>
    <div class="nav-user">
        <div class="user-info">
            <span class="user-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
            <div>
                <p class="user-name">{{ Auth::user()->name }}</p>
                <p class="user-level">{{ Auth::user()->role ?? 'Learner' }}</p>
            </div>
        </div>
        <form action="{{ route('client.logout') }}" method="POST" style="margin: 0;">
            @csrf
            <button type="submit" class="btn-logout">Logout</button>
        </form>
    </div>
</nav>

<style>
    /* Navbar Styles */
    .client-navbar {
        background: rgba(255, 255, 255, 0.98);
        backdrop-filter: blur(20px);
        padding: 1rem 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 2px 20px rgba(98, 191, 186, 0.12);
        border-bottom: 1px solid rgba(98, 191, 186, 0.1);
        position: sticky;
        top: 0;
        z-index: 100;
        animation: slideDown 0.5s ease-out;
    }

    @keyframes slideDown {
        from {
            transform: translateY(-100%);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .navbar-brand {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .brand-logo {
        font-size: 2rem;
        animation: rotate 3s ease-in-out infinite;
    }

    @keyframes rotate {
        0%, 100% { transform: rotate(0deg); }
        50% { transform: rotate(10deg); }
    }

    .navbar-brand h1 {
        font-size: 1.5rem;
        background: linear-gradient(135deg, #62bfba, #95D5B2);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        font-weight: 700;
    }

    .nav-menu {
        display: flex;
        gap: 2rem;
        list-style: none;
    }

    .nav-link {
        text-decoration: none;
        color: #5f7172;
        font-weight: 500;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        transition: all 0.3s;
        position: relative;
    }

    .nav-link:hover {
        text-decoration: none;
    }

    .nav-link:hover {
        background: rgba(98, 191, 186, 0.1);
        color: #62bfba;
        transform: translateY(-2px);
    }

    .nav-link.active {
        background: linear-gradient(135deg, #62bfba, #95D5B2);
        color: white;
        box-shadow: 0 4px 12px rgba(98, 191, 186, 0.3);
    }

    .nav-user {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .user-info {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .user-avatar {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: linear-gradient(135deg, #62bfba, #95D5B2);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1.2rem;
        box-shadow: 0 4px 12px rgba(98, 191, 186, 0.3);
    }

    .user-name {
        font-weight: 600;
        color: #2d3e3f;
        margin: 0;
        font-size: 0.95rem;
    }

    .user-level {
        color: #5f7172;
        font-size: 0.8rem;
        margin: 0;
    }

    .btn-logout {
        background: linear-gradient(135deg, #ff8787, #ff5252);
        color: white;
        border: none;
        padding: 0.6rem 1.5rem;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.3s;
        box-shadow: 0 4px 12px rgba(255, 135, 135, 0.3);
    }

    .btn-logout:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(255, 135, 135, 0.4);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .client-navbar {
            flex-wrap: wrap;
            padding: 1rem;
        }

        .nav-menu {
            gap: 1rem;
            flex-wrap: wrap;
        }

        .nav-user {
            flex-direction: column;
            gap: 0.5rem;
            width: 100%;
            margin-top: 1rem;
        }
    }
</style>
