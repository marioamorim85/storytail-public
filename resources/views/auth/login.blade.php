@extends('components.layout')

@section('title', 'Login')

@section('content')
    <div class="auth-background">
        <div class="auth-container">
            <h2 id="formTitle" class="st-title">Login</h2>


            <!-- Form Login -->
            <div id="loginForm" class="form-section">
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="form-group mb-3">
                        <input type="email"
                               id="email"
                               class="form-control @error('email') is-invalid @enderror"
                               name="email"
                               value="{{ old('email') }}"
                               placeholder="E-mail"
                               required
                               autofocus>
                        @error('email')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <div class="form-group mb-3 position-relative">
                        <input type="password"
                               id="password"
                               class="form-control @error('password') is-invalid @enderror"
                               name="password"
                               placeholder="Password"
                               required>
                        <span id="togglePasswordIcon"
                              onclick="togglePasswordVisibility('password', 'togglePasswordIcon')"
                              class="bi bi-eye password-toggle">
                        </span>
                        @error('password')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <div class="form-group text-end mb-3">
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="forgot-password">
                                Forgot Password?
                            </a>
                        @endif
                    </div>

                    <div class="d-flex justify-content-between">
                        <button type="submit" class="mt-2 btn">Login</button>
                        <button type="button" class="mt-2 btnSecundary" onclick="window.location.href='{{ route('register') }}'">Register</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
