<div class="sidebar position-fixed" style="width: 280px; height: 100vh; background: #020617; z-index: 999; overflow-y: auto; box-shadow: 4px 0 12px rgba(0,0,0,0.1);">
    <div class="offcanvas-md offcanvas-start" tabindex="-1" id="sidebarMenuLearner" aria-labelledby="sidebarMenuLearnerLabel">
        <!-- Sidebar Header -->
        <div style="padding: 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.1);">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <a href="{{ route('dashboard') }}" style="text-decoration: none; display: flex; align-items: center; gap: 0.75rem;">
                    <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #6366f1 0%, #22c55e 100%); border-radius: 0.75rem; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 1.25rem;">
                        NL
                    </div>
                    <span style="font-size: 1.125rem; font-weight: 700; color: white;">
                        {{ config('devstarit.app_name', 'NextLearning') }}
                    </span>
                </a>
                <button type="button" class="d-md-none btn-close btn-close-white" data-bs-dismiss="offcanvas" data-bs-target="#sidebarMenuLearner" aria-label="Close"></button>
            </div>
        </div>

        <div style="padding: 1rem 0; background: transparent;">
            <ul style="list-style: none; padding: 0; margin: 0;">
                <!-- Dashboard -->
                <li style="margin-bottom: 0.25rem;">
                    <a href="{{ route('dashboard') }}" 
                        style="display: flex; align-items: center; gap: 0.75rem; padding: 0.875rem 1.5rem; color: {{ (request()->is('learner') || request()->is('dashboard')) ? '#6366f1' : 'rgba(255,255,255,0.8)' }}; text-decoration: none; transition: all 0.3s; {{ (request()->is('learner') || request()->is('dashboard')) ? 'background: rgba(15,23,42,0.9); border-left: 3px solid #6366f1;' : '' }}">
                        <i data-feather="home" style="width: 20px; height: 20px;"></i>
                        <span style="font-weight: 600;">Dashboard</span>
                    </a>
                </li>

                <!-- Browse Courses -->
                <li style="margin-top: 0.5rem; margin-bottom: 0.25rem;">
                    <a href="{{ route('courses.index') }}" 
                        style="display: flex; align-items: center; gap: 0.75rem; padding: 0.875rem 1.5rem; color: {{ request()->is('courses*') ? '#6366f1' : 'rgba(255,255,255,0.8)' }}; text-decoration: none; transition: all 0.3s; {{ request()->is('courses*') ? 'background: rgba(15,23,42,0.9); border-left: 3px solid #6366f1;' : '' }}">
                        <i data-feather="compass" style="width: 20px; height: 20px;"></i>
                        <span style="font-weight: 600;">Browse Courses</span>
                    </a>
                </li>

                <!-- My Courses (enrolled) -->
                <li style="margin-top: 0.5rem; margin-bottom: 0.25rem;">
                    <a href="{{ route('courses.my-courses') }}" 
                        style="display: flex; align-items: center; gap: 0.75rem; padding: 0.875rem 1.5rem; color: {{ request()->is('my-courses*') ? '#6366f1' : 'rgba(255,255,255,0.8)' }}; text-decoration: none; transition: all 0.3s; {{ request()->is('my-courses*') ? 'background: rgba(15,23,42,0.9); border-left: 3px solid #6366f1;' : '' }}">
                        <i data-feather="book" style="width: 20px; height: 20px;"></i>
                        <span style="font-weight: 600;">My Courses</span>
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
                            style="display: flex; align-items: center; gap: 0.75rem; padding: 0.875rem 1.5rem; color: rgba(255,255,255,0.8); text-decoration: none; transition: all 0.3s;">
                            <i data-feather="user" style="width: 20px; height: 20px;"></i>
                            <span style="font-weight: 600;">Profile</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('teacher.password.index') }}" 
                            style="display: flex; align-items: center; gap: 0.75rem; padding: 0.875rem 1.5rem; color: rgba(255,255,255,0.8); text-decoration: none; transition: all 0.3s;">
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


