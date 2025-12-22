<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
    <div class="position-sticky pt-3">

        @if(!empty($subjectId) && $subjects->where('id', $subjectId)->count())
            <ul class="nav flex-column">

                @foreach($subjects->where('id', $subjectId) as $subject)
                    <li class="nav-item">

                        {{-- Current Subject --}}
                        <a class="nav-link fw-bold bg-light text-primary"
                           href="{{ route('modules-list', $subjectId) }}">
                            <span data-feather="book-open"></span>
                            {{ $subject->name }}
                        </a>

                        @if(!auth()->user()->hasRole('Student')) 

                        {{-- View All Modules Button --}}
                        <div class="px-3 mt-2 mb-2">
                            <a href="{{ route('modules-list', $subjectId) }}"
                               class="btn btn-sm btn-outline-primary w-100">
                                <span data-feather="list"></span>
                                View All Modules
                            </a>
                            
                        </div>
                        {{-- View All Modules Button --}}
                        <div class="px-3 mt-2 mb-2">
                            <a href="{{ route('modules-index', ['subject_id' => $subjectId]) }}" 
                                class="btn btn-sm btn-outline-primary w-100">
                                    <span data-feather="home"></span>
                                    Add New Module
                            </a>

                            
                        </div>

                        @endif

                        {{-- Modules --}}
                        <ul class="nav flex-column ms-3 mt-1">
                            @foreach($subject->modules as $module)
                                <li class="nav-item">
                                    <a class="nav-link
                                        {{ $module->id == $currentModuleId
                                            ? 'fw-bold text-primary bg-light rounded'
                                            : '' }}"
                                       href="{{ route('modules-view', $module->id) }}">
                                        <span data-feather="layers"></span>
                                        {{ $module->modules_name }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                        

                    </li>
                @endforeach

            </ul>
        @else
            <p class="text-muted px-3">
                Please select a subject to view its modules.
            </p>
        @endif

    </div>
</nav>
