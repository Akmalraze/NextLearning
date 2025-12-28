<header class="navbar sticky-top bg-body-tertiary border-bottom flex-md-nowrap p-0 shadow">
    <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3 fs-6 d-flex align-items-center" href="{{ route('dashboard') }}">
        <!-- Logo -->
        <img src="/images/logo.png" alt="App Logo" class="logo-img" style="height: 20px; margin-right: 8px;">
        <!-- App Name -->
        <span class="fw-bold">{{ config('devstarit.app_name') }}</span>
    </a>

    <!-- Mobile View Icon -->
    <ul class="navbar-nav flex-row d-md-none">
        <li class="nav-item text-nowrap">
            <button class="nav-link px-3 text-white" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSearch" aria-controls="navbarSearch" aria-expanded="false"
                aria-label="Toggle search">
                <svg class="bi text-white">
                    <use xlink:href="#search" />
                </svg>
            </button>
        </li>
        <li class="nav-item text-nowrap">
            <button class="nav-link px-3 text-dark" type="button" data-bs-toggle="offcanvas"
                data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false"
                aria-label="Toggle navigation">
                <svg class="bi text-dark">
                    <use xlink:href="#list" />
                </svg>
            </button>
        </li>
    </ul>
    
    <!-- User Profile Dropdown -->
    <div class="dropdown me-3 m-auto">
        <a href="javascript:void(0)" class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
            {{ auth()->user()->name }}
        </a>
        <ul class="dropdown-menu dropdown-menu-lg-start border-0 shadow-sm rounded-0">
            <li><a class="dropdown-item" href="{{ route('admin.profile.index') }}">Profile</a></li>
            <li><a class="dropdown-item" href="{{ route('admin.password.index') }}">Change Password</a></li>
            <li>
                <hr class="dropdown-divider">
            </li>
            <li>
                <a class="dropdown-item" href="{{ route('logout') }}"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    {{ __('Logout') }}
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </li>
        </ul>
    </div>
</header>
