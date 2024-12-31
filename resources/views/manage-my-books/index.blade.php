@extends('components.layout')

{{-- Banner --}}
    @include('manage-my-books.banner', [
        'title' => 'My Badges'
    ])

{{-- Menu --}}
@section('menu')
    <div class="menu-main-content">
        <div class="menu text-center my-4">
            <ul class="nav nav-tabs justify-content-center">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('book-badges') ? 'active' : '' }}"
                       href="{{ route('book-badges') }}">Book Badges</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('activity-badges') ? 'active' : '' }}"
                       href="{{ route('activity-badges') }}">Activity Badges</a>
                </li>
            </ul>
        </div>
    </div>


{{-- Conteúdo Principal --}}
    <div class="main-content">
        @yield('badges-content')
    </div>

