<!DOCTYPE html>
<html lang="en" data-bs-theme="auto">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#712cf9">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('devstarit.app_name') }} - {{ config('devstarit.app_desc') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- ✅ Bootstrap CSS (manual) -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/bootstrap/bootstrap.min.css') }}">

    <!-- ✅ Your custom admin styles -->
    <link href="{{ asset('build/css/dashboard.css') }}?v={{ time() }}" rel="stylesheet">
    <link href="{{ asset('build/css/custom.css') }}?v={{ time() }}" rel="stylesheet">
    <link href="{{ asset('build/css/sidebar-theme.css') }}?v={{ time() }}" rel="stylesheet">

    <!-- ✅ Color modes JS -->
    <script src="{{ asset('build/js/color-modes.js') }}?v={{ time() }}"></script>

    @yield('styles')
</head>

<body>

    <!-- Page Loader -->
    <div id="page-loader" class="page-loader">
        <img src="{{ asset('assets/loader.gif') }}" alt="Loading...">
        <div class="page-loader-text">Loading...</div>
    </div>

    <style>
        .page-loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            z-index: 99999;
            transition: opacity 0.3s ease-out;
        }

        [data-bs-theme="light"] .page-loader {
            background: rgba(255, 255, 255, 0.98);
        }

        [data-bs-theme="dark"] .page-loader {
            background: rgba(32, 44, 70, 0.98);
        }

        .page-loader.hidden {
            opacity: 0;
            pointer-events: none;
        }

        .page-loader img {
            max-width: 80px;
            max-height: 80px;
        }

        .page-loader-text {
            margin-top: 16px;
            font-size: 14px;
            font-weight: 500;
        }

        [data-bs-theme="light"] .page-loader-text {
            color: #333333;
        }

        [data-bs-theme="dark"] .page-loader-text {
            color: #ffffff;
        }
    </style>

    <script>
        window.addEventListener('load', function() {
            const loader = document.getElementById('page-loader');
            if (loader) {
                loader.classList.add('hidden');
                setTimeout(() => loader.remove(), 300);
            }
        });
    </script>


    <!-- Navbar -->
    @include('admin.includes.navbar')

    <div class="container-fluid">
        <div class="row">
            @php
                $routeName = Route::currentRouteName();
            @endphp

            @if (Str::startsWith($routeName, 'modules-') || Str::startsWith($routeName, 'materials-'))
                @include('admin.includes.subject')
            @else
                @include('admin.includes.sidebar')
            @endif

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 position-relative">
                @include('admin.includes.breadcrumb')
                @include('admin.includes.flash')
                @yield('content')
            </main>
        </div>
    </div>

    <!-- ✅ Bootstrap JS -->
    <script src="{{ asset('assets/plugins/bootstrap/bootstrap.min.js') }}"></script>

    <!-- Feather icons -->
    <script src="https://unpkg.com/feather-icons"></script>
    <script>
        feather.replace()
    </script>

    @yield('scripts')

</body>
</html>
