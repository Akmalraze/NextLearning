<div class="sidebar position-fixed border-right col-md-3 col-lg-2 p-0" style="z-index: 9999;">
    <div class="offcanvas-md offcanvas-start" tabindex="-1" id="sidebarMenu" aria-labelledby="sidebarMenuLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="sidebarMenuLabel">{{ config('devstarit.app_name') }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#sidebarMenu"
                aria-label="Close"></button>
        </div>

        <div class="offcanvas-body position-static sidebar-sticky d-md-flex flex-column p-0 pt-lg-3 overflow-y-auto">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ (request()->is('admin') || request()->is('teacher') || request()->is('student')) ? 'active' : '' }}"
                        aria-current="page" href="{{ route('dashboard') }}">
                        <span data-feather="home" class="align-text-bottom"></span>
                        Dashboard
                    </a>
                </li>
                {{-- Manage User, Subject & Class Menu --}}
                @can('user_access')
                <li class="nav-item">
                    <a class="nav-link collapsed" href="#manageSubmenu" data-bs-toggle="collapse"
                        aria-expanded="{{ (request()->is('admin/users*') || request()->is('admin/classes*') || request()->is('admin/subjects*')) ? 'true' : 'false' }}">
                        <span data-feather="grid" class="align-text-bottom"></span>
                        Manage User, Subject & Class
                        <span data-feather="chevron-down" class="align-text-bottom ms-auto"
                            style="width: 16px; height: 16px;"></span>
                    </a>
                    <div class="collapse {{ (request()->is('admin/users*') || request()->is('admin/classes*') || request()->is('admin/subjects*')) ? 'show' : '' }}"
                        id="manageSubmenu">
                        <ul class="nav flex-column ms-3">
                            <li class="nav-item">
                                <a class="nav-link {{ (request()->is('admin/users*')) ? 'active' : '' }}"
                                    href="{{ route('admin.users.index') }}">
                                    <span data-feather="users" class="align-text-bottom"></span>
                                    Users
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ (request()->is('admin/classes*')) ? 'active' : '' }}"
                                    href="{{ route('admin.classes.index') }}">
                                    <span data-feather="book-open" class="align-text-bottom"></span>
                                    Classes
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ (request()->is('admin/subjects*')) ? 'active' : '' }}"
                                    href="{{ route('admin.subjects.index') }}">
                                    <span data-feather="book" class="align-text-bottom"></span>
                                    Subjects
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                @endcan
                
                <li class="nav-item">
                    <a class="nav-link {{ (request()->is('assessments*')) ? 'active' : '' }}" aria-current="page"
                         href="{{ route('assessments.index') }}">
                        <span data-feather="home" class="align-text-bottom"></span>
                        Assessment
                    </a>
                </li>
                @role('Admin')
<li class="nav-item">
    <a class="nav-link {{ request()->is('admin/report') ? 'active' : '' }}"
        aria-current="page"
        href="{{ route('admin.report') }}">
        <span data-feather="home" class="align-text-bottom"></span>
        Report
    </a>
</li>
@endrole

@role('Teacher')
<li class="nav-item">
    <a class="nav-link {{ request()->is('teacher/report') ? 'active' : '' }}"
        aria-current="page"
        href="{{ route('teacher.report') }}">
        <span data-feather="home" class="align-text-bottom"></span>
        Report
    </a>
</li>
@endrole
                {{-- <li class="nav-item">
                    <a class="nav-link {{ (request()->is('admin')) ? 'active' : '' }}" aria-current="page"
                        href="{{ route('admin.index') }}">
                        <span data-feather="home" class="align-text-bottom"></span>
                        Inbox
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ (request()->is('admin')) ? 'active' : '' }}" aria-current="page"
                        href="{{ route('admin.index') }}">
                        <span data-feather="home" class="align-text-bottom"></span>
                        Setting
                    </a>
                </li> --}}


                {{-- @can('permission_access')
                <li class="nav-item">
                    <a class="nav-link {{ (request()->is('admin/permissions*')) ? 'active' : '' }}"
                        href="{{ route('admin.permissions.index') }}">
                        <span data-feather="shield" class="align-text-bottom"></span>
                        Permissions
                    </a>
                </li>
                @endcan --}}
                @can('role_access')
                <li class="nav-item">
                    <a class="nav-link {{ (request()->is('admin/roles*')) ? 'active' : '' }}"
                        href="{{ route('admin.roles.index') }}">
                        <span data-feather="disc" class="align-text-bottom"></span>
                        Roles
                    </a>
                </li>
                @endcan
                {{-- @can('post_access')
                <li class="nav-item">
                    <a class="nav-link {{ (request()->is('admin/posts*')) ? 'active' : '' }}"
                        href="{{ route('admin.posts.index') }}">
                        <span data-feather="file" class="align-text-bottom"></span>
                        Posts
                    </a>
                </li>
                @endcan
                {{-- @can('category_access')
                <li class="nav-item">
                    <a class="nav-link {{ (request()->is('admin/categories*')) ? 'active' : '' }}"
                        href="{{ route('admin.categories.index') }}">
                        <span data-feather="list" class="align-text-bottom"></span>
                        Categories
                    </a>
                </li>
                @endcan
                @can('tag_access')
                <li class="nav-item">
                    <a class="nav-link {{ (request()->is('admin/tags*')) ? 'active' : '' }}"
                        href="{{ route('admin.tags.index') }}">
                        <span data-feather="tag" class="align-text-bottom"></span>
                        Tags
                    </a>
                </li>
                @endcan --}}
            </ul>

            <h6
                class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-white-50 text-uppercase">
                <span>Setting</span>
                <a class="link-secondary" href="#" aria-label="Add a new report">
                    <span data-feather="plus-circle" class="align-text-bottom"></span>
                </a>
            </h6>
            <ul class="nav flex-column mb-2">
                {{--<li class="nav-item">
                    <a class="nav-link" href="#">
                        <span data-feather="settings" class="align-text-bottom"></span>
                        App Setting
                    </a>
                </li>--}}
                <li class="nav-item">
                    <a class="nav-link {{ (request()->is('admin/profile')) ? 'active' : '' }}"
                        href="{{ route('admin.profile.index') }}">
                        <span data-feather="user" class="align-text-bottom"></span>
                        Profile
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ (request()->is('admin/change-password')) ? 'active' : '' }}"
                        href="{{ route('admin.password.index') }}">
                        <span data-feather="key" class="align-text-bottom"></span>
                        Change Password
                    </a>
                </li>

            </ul>
        </div>
    </div>
</div>