<header style="background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); box-shadow: 0 2px 8px rgba(0,0,0,0.1); position: sticky; top: 0; z-index: 1000; border-bottom: 1px solid rgba(226, 232, 240, 0.8);">
    <div style="display: flex; align-items: center; justify-content: space-between; padding: 1rem 2rem; max-width: 100%;">
        <!-- Logo and Menu Toggle -->
        <div style="display: flex; align-items: center; gap: 1rem;">
            <button type="button" class="d-md-none" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu" 
                style="background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); border: none; border-radius: 0.5rem; padding: 0.5rem; color: white; cursor: pointer;">
                <i data-feather="menu" style="width: 20px; height: 20px;"></i>
            </button>
            <a href="{{ route('dashboard') }}" style="text-decoration: none; display: flex; align-items: center; gap: 0.75rem;">
                <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); border-radius: 0.75rem; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 1.25rem; box-shadow: 0 4px 6px rgba(99, 102, 241, 0.3);">
                    NL
                </div>
                <span style="font-size: 1.25rem; font-weight: 700; background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                    {{ config('devstarit.app_name', 'NextLearning') }}
                </span>
            </a>
        </div>

        <!-- User Menu -->
        <div style="position: relative;">
            <button onclick="toggleUserMenu()" style="display: flex; align-items: center; gap: 0.75rem; background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); border: 2px solid #e2e8f0; border-radius: 0.75rem; padding: 0.5rem 1rem; cursor: pointer; transition: all 0.3s;">
                <div style="width: 36px; height: 36px; background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 0.875rem;">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <span style="font-weight: 600; color: #1e293b;">{{ auth()->user()->name }}</span>
                <i data-feather="chevron-down" style="width: 16px; height: 16px; color: #64748b;"></i>
            </button>
            
            <div id="userMenu" style="display: none; position: absolute; right: 0; top: calc(100% + 0.5rem); background: white; border-radius: 0.75rem; box-shadow: 0 10px 15px rgba(0,0,0,0.1); min-width: 200px; padding: 0.5rem 0; z-index: 1000; border: 1px solid #e2e8f0;">
                <a href="{{ route('teacher.profile.index') }}" style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1rem; color: #1e293b; text-decoration: none; transition: background 0.3s;">
                    <i data-feather="user" style="width: 18px; height: 18px;"></i>
                    <span>Profile</span>
                </a>
                <a href="{{ route('teacher.password.index') }}" style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1rem; color: #1e293b; text-decoration: none; transition: background 0.3s;">
                    <i data-feather="key" style="width: 18px; height: 18px;"></i>
                    <span>Change Password</span>
                </a>
                <div style="height: 1px; background: #e2e8f0; margin: 0.5rem 0;"></div>
                <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" 
                    style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1rem; color: #ef4444; text-decoration: none; transition: background 0.3s;">
                    <i data-feather="log-out" style="width: 18px; height: 18px;"></i>
                    <span>Logout</span>
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </div>
        </div>
    </div>
</header>

<script>
    function toggleUserMenu() {
        const menu = document.getElementById('userMenu');
        menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
    }
    
    document.addEventListener('click', function(event) {
        const menu = document.getElementById('userMenu');
        const button = event.target.closest('button[onclick="toggleUserMenu()"]');
        if (menu && !menu.contains(event.target) && !button) {
            menu.style.display = 'none';
        }
    });
    
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    });
</script>

<style>
    #userMenu a:hover {
        background: #f8fafc;
    }
</style>
