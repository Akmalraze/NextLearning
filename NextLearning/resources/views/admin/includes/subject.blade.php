<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
    <div class="position-sticky pt-3">
        <ul class="nav flex-column">
            <!-- Iterate over all active subjects -->
            @foreach($subjects as $subject)
                <li class="nav-item">
                    <a class="nav-link {{ $subject->id == $subjectId ? 'bg-purple-300' : '' }}" href="{{ route('modules-index', ['subject_id' => $subject->id]) }}">
                        <span data-feather="home"></span>
                        {{ $subject->name }}
                    </a>
                    <!-- List modules only if the subject is selected -->
                    @if ($subject->id == $subjectId)
                        <ul class="nav flex-column ms-3">
                            @foreach($subject->modules as $module)
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('modules-view', $module->id) }}">
                                        <span data-feather="layers"></span>
                                        {{ $module->modules_name }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </li>
            @endforeach
        </ul>
    </div>
</nav>
