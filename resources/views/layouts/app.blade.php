<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'NextLearning') }} - @yield('title', 'Dashboard')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body>
    <div id="app">
        <nav style="background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); box-shadow: 0 1px 3px rgba(0,0,0,0.1); position: sticky; top: 0; z-index: 1000;">
            <div style="max-width: 1200px; margin: 0 auto; padding: 1rem 2rem; display: flex; justify-content: space-between; align-items: center;">
                <a href="{{ url('/') }}" style="text-decoration: none; display: flex; align-items: center; gap: 0.75rem;">
                    <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); border-radius: 0.75rem; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 1.25rem;">
                        NL
                    </div>
                    <span style="font-size: 1.5rem; font-weight: 700; background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                        {{ config('app.name', 'NextLearning') }}
                    </span>
                </a>

                <div style="display: flex; align-items: center; gap: 1rem;">
                    @guest
                        @if (Route::has('login'))
                            <a href="{{ route('login') }}" style="padding: 0.625rem 1.5rem; color: #6366f1; text-decoration: none; font-weight: 600; border-radius: 0.5rem; transition: all 0.3s;">
                                Login
                            </a>
                        @endif
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" style="padding: 0.625rem 1.5rem; background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); color: white; text-decoration: none; font-weight: 600; border-radius: 0.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); transition: all 0.3s;">
                                Register
                            </a>
                        @endif
                    @else
                        <div style="position: relative;">
                            <button onclick="toggleDropdown()" style="display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; background: #f8fafc; border: none; border-radius: 0.5rem; cursor: pointer; font-weight: 500;">
                                <span>{{ Auth::user()->name }}</span>
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div id="userDropdown" style="display: none; position: absolute; right: 0; top: 100%; margin-top: 0.5rem; background: white; border-radius: 0.5rem; box-shadow: 0 10px 15px rgba(0,0,0,0.1); min-width: 150px; padding: 0.5rem 0;">
                                <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" style="display: block; padding: 0.75rem 1rem; color: #ef4444; text-decoration: none; transition: background 0.3s;">
                                    Logout
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                    @csrf
                                </form>
                            </div>
                        </div>
                    @endguest
                </div>
            </div>
        </nav>

        <main style="min-height: calc(100vh - 80px); padding: 2rem 0;">
            @yield('content')
        </main>
    </div>

    <script>
        function toggleDropdown() {
            const dropdown = document.getElementById('userDropdown');
            dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
        }
        
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('userDropdown');
            const button = event.target.closest('button');
            if (dropdown && !dropdown.contains(event.target) && button !== event.target.closest('button')) {
                dropdown.style.display = 'none';
            }
        });
    </script>
</body>

</html>