{{-- Client Header Component --}}
<nav class="client-navbar">
    <a href="{{ route('client.home') }}" class="navbar-brand">
        <h1>Chinese Learner</h1>
    </a>
    <ul class="nav-menu">
        <li><a href="{{ route('client.home') }}" class="nav-link {{ request()->routeIs('client.home') ? 'active' : '' }}">Home</a></li>
        <li><a href="{{ route('client.radicals.index') }}" class="nav-link {{ request()->routeIs('client.radicals.*') ? 'active' : '' }}">Characters</a></li>
        <li><a href="{{ route('client.vocabulary.index') }}" class="nav-link {{ request()->routeIs('client.vocabulary.*') ? 'active' : '' }}">Vocabulary</a></li>
        <li><a href="{{ route('client.hsk.index') }}" class="nav-link {{ request()->routeIs('client.hsk.*') ? 'active' : '' }}">HSK</a></li>
        <li><a href="{{ route('client.tocfl.index') }}" class="nav-link {{ request()->routeIs('client.tocfl.*') ? 'active' : '' }}">TOCFL</a></li>
        <li><a href="{{ route('client.chat') }}" class="nav-link {{ request()->routeIs('client.chat') ? 'active' : '' }}">AI Chat</a></li>
    </ul>
    <div class="nav-user">
        <div class="user-dropdown">
            <button class="user-info" onclick="toggleDropdown()">
                <span class="user-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                <span class="user-name">{{ Auth::user()->name }}</span>
                <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16" class="dropdown-icon">
                    <path d="M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z"/>
                </svg>
            </button>
            <div class="dropdown-menu" id="userDropdown">
                {{-- <a href="#" class="dropdown-item">
                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10z"/>
                    </svg>
                    Profile
                </a> --}}
                <form action="{{ route('client.logout') }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" class="dropdown-item logout-item">
                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0v2z"/>
                            <path fill-rule="evenodd" d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3z"/>
                        </svg>
                        Logout
                    </button>
                </form>
            </div>
        </div>
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
        text-decoration: none;
        transition: all 0.3s;
    }

    .navbar-brand:hover {
        transform: translateY(-2px);
    }

    .navbar-brand h1 {
        font-size: 1.5rem;
        background: linear-gradient(135deg, #62bfba, #95D5B2);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        font-weight: 700;
        margin: 0;
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
        position: relative;
    }

    .user-dropdown {
        position: relative;
    }

    .user-info {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        background: transparent;
        border: none;
        cursor: pointer;
        padding: 0.5rem;
        border-radius: 12px;
        transition: all 0.3s;
    }

    .user-info:hover {
        background: rgba(98, 191, 186, 0.1);
    }

    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #62bfba, #95D5B2);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1rem;
        box-shadow: 0 4px 12px rgba(98, 191, 186, 0.3);
    }

    .user-name {
        font-weight: 600;
        color: #2d3e3f;
        font-size: 0.95rem;
    }

    .dropdown-icon {
        color: #5f7172;
        transition: transform 0.3s;
    }

    .user-info:hover .dropdown-icon {
        transform: translateY(2px);
    }

    .dropdown-menu {
        position: absolute;
        top: calc(100% + 8px);
        right: 0;
        background: white;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
        min-width: 200px;
        opacity: 0;
        visibility: hidden;
        transform: translateY(-10px);
        transition: all 0.3s;
        z-index: 1000;
    }

    .dropdown-menu.show {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    .dropdown-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 20px;
        color: #2d3e3f;
        text-decoration: none;
        transition: all 0.3s;
        border: none;
        background: transparent;
        width: 100%;
        text-align: left;
        cursor: pointer;
        font-size: 0.95rem;
        font-weight: 500;
    }

    .dropdown-item:first-child {
        border-radius: 12px 12px 0 0;
    }

    .dropdown-item:last-child {
        border-radius: 0 0 12px 12px;
    }

    .dropdown-item:hover {
        background: rgba(98, 191, 186, 0.1);
        color: #62bfba;
    }

    .logout-item {
        color: #ff5252;
    }

    .logout-item:hover {
        background: rgba(255, 82, 82, 0.1);
        color: #ff5252;
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

        .dropdown-menu {
            right: 0;
            left: auto;
        }
    }
</style>

<script>
function toggleDropdown() {
    const dropdown = document.getElementById('userDropdown');
    dropdown.classList.toggle('show');
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    const dropdown = document.getElementById('userDropdown');
    const userInfo = event.target.closest('.user-info');
    
    if (!userInfo && dropdown) {
        dropdown.classList.remove('show');
    }
});
</script>
