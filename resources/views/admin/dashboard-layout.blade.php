@extends('components.layout', ['fullWidth' => true])

@section('content')
    <div class="dashboard-layout">
        {{-- Sidebar --}}
        @include('admin.components.sidebar')

        {{-- Conteúdo Principal --}}
        <div class="dashboard-content">
            @yield('dashboard-content')
        </div>
    </div>
@endsection



