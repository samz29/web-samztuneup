<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Favicon -->
    @if(App\Models\AppSetting::getValue('site_favicon'))
        <link rel="icon" type="image/x-icon" href="{{ asset('storage/' . App\Models\AppSetting::getValue('site_favicon')) }}">
    @else
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @endif

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    @stack('styles')
</head>
<body class="bg-dark text-white">
@php
    $headerMenus = App\Models\WebMenu::with('children')
        ->where('location', 'header')
        ->whereNull('parent_id')
        ->where('is_active', true)
        ->orderBy('sort_order')
        ->get();

    $footerMenus = App\Models\WebMenu::where('location', 'footer')
        ->where('is_active', true)
        ->orderBy('sort_order')
        ->get();
@endphp
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm border-bottom border-secondary">
            <div class="container-fluid">
                <a class="navbar-brand d-flex align-items-center text-warning fw-bold" href="{{ url('/') }}">
                    @if(App\Models\AppSetting::getValue('site_logo'))
                        <img src="{{ asset('storage/' . App\Models\AppSetting::getValue('site_logo')) }}" alt="Logo" style="height: 80px; width: auto; max-width: 250px; object-fit: contain; margin-right: 20px;">
                    @else
                        <i class="fas fa-motorcycle fa-lg me-2 text-warning"></i>
                    @endif
                    <span class="d-none d-sm-inline">SamzTune-Up</span>
                    <span class="d-inline d-sm-none">SamzTune</span>
                </a>

                <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        @foreach($headerMenus as $menu)
                        <li class="nav-item{{ $menu->children->count() > 0 ? ' dropdown' : '' }}">
                            @if($menu->children->count() > 0)
                            <a class="nav-link dropdown-toggle px-3" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="{{ $menu->icon }} me-1"></i>{{ $menu->title }}
                            </a>
                            <ul class="dropdown-menu bg-dark border-secondary">
                                @foreach($menu->children as $child)
                                <li><a class="dropdown-item text-light" href="{{ $child->url }}" target="{{ $child->target }}">
                                    <i class="{{ $child->icon }} me-2"></i>{{ $child->title }}
                                </a></li>
                                @endforeach
                            </ul>
                            @else
                            <a class="nav-link px-3 {{ request()->is(str_replace('/', '', $menu->url)) || ($menu->url == '/' && request()->is('/')) ? 'active text-warning fw-bold' : '' }}" href="{{ $menu->url }}" target="{{ $menu->target }}">
                                <i class="{{ $menu->icon }} me-1"></i>{{ $menu->title }}
                            </a>
                            @endif
                        </li>
                        @endforeach
                    </ul>

                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                        @auth
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle px-3" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user me-1"></i>{{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end bg-dark border-secondary" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item text-light" href="{{ route('admin.dashboard') }}">
                                    <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                                </a></li>
                                <li><hr class="dropdown-divider border-secondary"></li>
                                <li><a class="dropdown-item text-light" href="{{ route('logout') }}"
                                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                                </a></li>
                            </ul>
                        </li>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                        @else
                        <li class="nav-item">
                            <a class="nav-link px-3" href="{{ route('login') }}">
                                <i class="fas fa-sign-in-alt me-1"></i>Login
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-warning text-dark fw-bold ms-2 px-3" href="{{ route('booking.create') }}">
                                <i class="fas fa-calendar-plus me-1"></i>Book Service
                            </a>
                        </li>
                        @endauth
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>

        @if($footerMenus->count() > 0)
        <footer class="bg-dark border-top border-secondary mt-5 py-4">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="d-flex justify-content-center flex-wrap">
                            @foreach($footerMenus as $menu)
                            <a href="{{ $menu->url }}" class="text-decoration-none text-light me-4 mb-2" target="{{ $menu->target }}">
                                {{ $menu->title }}
                            </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </footer>
        @endif
    </div>

    @stack('scripts')
</body>
</html>
