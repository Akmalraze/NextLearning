<div class="sidebar position-fixed" style="width: 280px; height: 100vh; background: #020617; z-index: 999; overflow-y: auto; box-shadow: 4px 0 12px rgba(0,0,0,0.1);">
    <div class="offcanvas-md offcanvas-start" tabindex="-1" id="sidebarMenu" aria-labelledby="sidebarMenuLabel">
        <!-- Sidebar Header -->
        <div style="padding: 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.1);">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <a href="{{ route('dashboard') }}" style="text-decoration: none; display: flex; align-items: center; gap: 0.75rem;">
                    <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); border-radius: 0.75rem; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 1.25rem;">
                        NL
                    </div>
                    <span style="font-size: 1.125rem; font-weight: 700; color: white;">
                        {{ config('devstarit.app_name', 'NextLearning') }}
                    </span>
                </a>
                <button type="button" class="d-md-none btn-close btn-close-white" data-bs-dismiss="offcanvas" data-bs-target="#sidebarMenu" aria-label="Close"></button>
            </div>
        </div>

        <div style="padding: 1rem 0; background: transparent;">
            <ul style="list-style: none; padding: 0; margin: 0;">
                <!-- Dashboard -->
                <li style="margin-bottom: 0.25rem;">
                    <a href="{{ route('dashboard') }}" 
                        style="display: flex; align-items: center; gap: 0.75rem; padding: 0.875rem 1.5rem; color: {{ (request()->is('educator') || request()->is('learner') || request()->is('dashboard')) ? '#6366f1' : 'rgba(255,255,255,0.8)' }}; text-decoration: none; transition: all 0.3s; {{ (request()->is('educator') || request()->is('learner') || request()->is('dashboard')) ? 'background: rgba(15,23,42,0.9); border-left: 3px solid #6366f1;' : '' }}">
                        <i data-feather="home" style="width: 20px; height: 20px;"></i>
                        <span style="font-weight: 600;">Dashboard</span>
                    </a>
                </li>

                @if(auth()->check() && auth()->user()->hasRole('Educator'))
                <!-- Educator: My Courses shortcut -->
                <li style="margin-top: 0.5rem; margin-bottom: 0.25rem;">
                    <a href="{{ route('teacher.subjects.index') }}" 
                        style="display: flex; align-items: center; gap: 0.75rem; padding: 0.875rem 1.5rem; color: {{ request()->is('educator/subjects*') ? '#6366f1' : 'rgba(255,255,255,0.8)' }}; text-decoration: none; transition: all 0.3s; {{ request()->is('educator/subjects*') ? 'background: rgba(15,23,42,0.9); border-left: 3px solid #6366f1;' : '' }}">
                        <i data-feather="book-open" style="width: 20px; height: 20px;"></i>
                        <span style="font-weight: 600;">My Courses</span>
                    </a>
                </li>
                @endif

                <!-- Assessment -->
                <li style="margin-top: 0.5rem; margin-bottom: 0.25rem;">
                    <a href="{{ route('assessment') }}" 
                        style="display: flex; align-items: center; gap: 0.75rem; padding: 0.875rem 1.5rem; color: {{ request()->is('assessment*') ? '#6366f1' : 'rgba(255,255,255,0.8)' }}; text-decoration: none; transition: all 0.3s; {{ request()->is('assessment*') ? 'background: rgba(15,23,42,0.9); border-left: 3px solid #6366f1;' : '' }}">
                        <i data-feather="clipboard" style="width: 20px; height: 20px;"></i>
                        <span style="font-weight: 600;">Assessment</span>
                    </a>
                </li>
            </ul>

            <!-- Settings Section -->
            <div style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid rgba(255,255,255,0.1);">
                <div style="padding: 0 1.5rem 0.75rem; color: rgba(255,255,255,0.5); font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">
                    Settings
                </div>
                <ul style="list-style: none; padding: 0; margin: 0;">
                    <li>
                        <a href="{{ route('teacher.profile.index') }}" 
                            style="display: flex; align-items: center; gap: 0.75rem; padding: 0.875rem 1.5rem; color: {{ request()->is('teacher/profile*') ? '#6366f1' : 'rgba(255,255,255,0.8)' }}; text-decoration: none; transition: all 0.3s; {{ request()->is('teacher/profile*') ? 'background: rgba(99, 102, 241, 0.1); border-left: 3px solid #6366f1;' : '' }}">
                            <i data-feather="user" style="width: 20px; height: 20px;"></i>
                            <span style="font-weight: 600;">Profile</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('teacher.password.index') }}" 
                            style="display: flex; align-items: center; gap: 0.75rem; padding: 0.875rem 1.5rem; color: {{ request()->is('teacher/change-password*') ? '#6366f1' : 'rgba(255,255,255,0.8)' }}; text-decoration: none; transition: all 0.3s; {{ request()->is('teacher/change-password*') ? 'background: rgba(99, 102, 241, 0.1); border-left: 3px solid #6366f1;' : '' }}">
                            <i data-feather="key" style="width: 20px; height: 20px;"></i>
                            <span style="font-weight: 600;">Change Password</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
        
    });
</script>

<style>
    .sidebar a:hover {
        background: rgba(15, 23, 42, 0.9) !important;
        color: #6366f1 !important;
    }
    
    .sidebar::-webkit-scrollbar {
        width: 6px;
    }
    
    .sidebar::-webkit-scrollbar-track {
        background: rgba(0,0,0,0.2);
    }
    
    .sidebar::-webkit-scrollbar-thumb {
        background: rgba(255,255,255,0.2);
        border-radius: 3px;
    }
    
    .sidebar::-webkit-scrollbar-thumb:hover {
        background: rgba(255,255,255,0.3);
    }
</style>
