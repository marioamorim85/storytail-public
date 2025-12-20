<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'StoryTail')</title>

    @if (session()->has('success'))
        <meta name="flash-type" content="success">
        <meta name="flash-message" content="{{ session('success') }}">
    @endif
    @if (session()->has('error'))
        <meta name="flash-type" content="error">
        <meta name="flash-message" content="{{ session('error') }}">
    @endif
    @if (session()->has('warning'))
        <meta name="flash-type" content="warning">
        <meta name="flash-message" content="{{ session('warning') }}">
    @endif
    @if (session()->has('info'))
        <meta name="flash-type" content="info">
        <meta name="flash-message" content="{{ session('info') }}">
    @endif

    <!-- Favicons -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <!-- Chrome para Android -->
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('android-chrome-192x192.png') }}">
    <link rel="icon" type="image/png" sizes="512x512" href="{{ asset('android-chrome-512x512.png') }}">
    <meta name="theme-color" content="#ff6a00">

    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    {{-- DataTables --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap5.min.css">
    {{-- Flatpickr CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/material_orange.css">
    {{-- CSS --}}
    <link rel="stylesheet" href="{{ asset('css/styles.css?v=2.2') }}">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <script>
        const SORT_URL = '{{ route("books.sort") }}';
    </script>
    {{-- Stack para estilos adicionais --}}
    @stack('styles')
</head>

<body>
{{-- Skip to main content link for accessibility --}}
<a href="#main-content" class="skip-link">Skip to main content</a>

{{-- Header --}}
<header class="navbar navbar-expand-lg navbar">
    <div class="container">
        <a class="navbar-brand" href="{{ route('home') }}">
            <img src="{{ asset('images/logo.png') }}" alt="StoryTail Logo" class="logo">
        </a>
        {{-- Botão de navegação para ecrãs pequenos--}}
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <div class="navbar-nav ms-auto">
                @auth
                    {{-- Menu para utilizadores autenticados --}}
                    <div class="nav-item dropdown">
                        <a class="dropdown-toggle user-dropdown text-white" href="#" id="navbarDropdown" role="button"
                           data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle user-icon"></i> Hello {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}
                        </a>

                        <ul class="dropdown-menu custom-dropdown-menu" aria-labelledby="navbarDropdown">
                            @if(Auth::user()->user_type_id === 1)
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                        <i class="bi bi-shield-lock"></i> Admin Panel
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item logout-item"><i class="bi bi-box-arrow-right"></i> Logout</button>
                                    </form>
                                </li>
                            @else
                                <li><a class="dropdown-item" href="{{ route('profile') }}"><i class="bi bi-gear"></i> Manage Profile</a></li>
                                <li><a class="dropdown-item" href="{{ route('favourites') }}"><i class="bi bi-heart"></i> My Favourites</a></li>
                                <li><a class="dropdown-item" href="{{ route('my-books-progress') }}"><i class="bi bi-book"></i> My Books</a></li>
                                <li><a class="dropdown-item" href="{{ route('badges-index') }}"><i class="bi bi-award"></i> My Badges</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item logout-item"><i class="bi bi-box-arrow-right"></i> Logout</button>
                                    </form>
                                </li>
                            @endif
                        </ul>
                    </div>
                @else
                    {{-- Menu para visitantes --}}
                    <li class="nav-item">
                        <a href="{{ route('login') }}" class="auth-link btn me-4" onclick="window.location.href='{{ route('login') }}'">Login</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('register') }}" class="auth-link btn" onclick="window.location.href='{{ route('register') }}'">Register</a>
                    </li>
                @endauth
            </div>
        </div>
    </div>
</header>

{{-- CSRF token --}}
<meta name="csrf-token" content="{{ csrf_token() }}">


{{-- Notification Box --}}
<div class="notification-box"></div>

{{--Main Content--}}
<main id="main-content" class="py-4 main-content" role="main">
    <div class="{{ isset($fullWidth) && $fullWidth ? 'container-fluid px-0' : 'container' }}">
        @yield('content')
    </div>
</main>



{{--Footer--}}
<footer>
    <div class="footer-top">
        <div class="container">
            <a href="{{ route('home') }}" class="footer-logo">
                <img src="{{ asset('images/logo-storyTail.png') }}" alt="StoryTail Logo">
            </a>
            <div class="footer-links">
                <a href="{{ route('contacts') }}" class="footer-link">Contacts</a>
                <a href="{{ route('about') }}" class="footer-link">About</a>
                <a href="{{ route('terms') }}" class="footer-link">Terms</a>
            </div>
        </div>
    </div>
    <div class="footer-bottom py-2">
        <div class="container d-flex justify-content-center text-white position-relative align-items-center">
            <p class="mb-0">&copy; {{ date('Y') }} StoryTail. All rights reserved.</p>
            <div class="social-links position-absolute end-0">
                <a href="#" class="text-white me-3"><i class="bi bi-facebook"></i></a>
                <a href="#" class="text-white me-3"><i class="bi bi-instagram"></i></a>
                <a href="#" class="text-white"><i class="bi bi-twitter"></i></a>
            </div>
        </div>
    </div>
</footer>



{{-- Scripts de dependências --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/responsive.bootstrap5.min.js"></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://npmcdn.com/flatpickr/dist/l10n/pt.js"></script>

{{-- Tornar eventos touchstart passivos globalmente --}}
<script>
    // Garantir que jQuery foi carregado antes de usar
    if (typeof jQuery !== 'undefined') {
        jQuery.event.special.touchstart = {
            setup: function (_, ns, handle) {
                this.addEventListener("touchstart", handle, { passive: true });
            }
        };
    } else {
        console.error("jQuery não foi carregado corretamente.");
    }
</script>

{{-- Scripts personalizados --}}
<script src="{{ asset('js/turn.js') }}"></script>
<script src="{{ asset('js/java.js?v=2.1') }}"></script>

{{-- Scripts adicionais --}}
@stack('scripts')


</body>
</html>
