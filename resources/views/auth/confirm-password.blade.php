@extends('components.layout')

@section('title', 'Confirm Password')

@section('content')
    <div class="auth-background">
        <div class="auth-container">
            <h2 class="st-title">Confirm Password</h2>

            <div class="form-section">
                <form method="POST" action="{{ route('password.confirm') }}">
                    @csrf

                    <div class="form-group mb-3">
                        <p class="text-muted">
                            This is a secure area of the application. Please confirm your password before continuing.
                        </p>
                    </div>

                    <div class="form-group mb-3 position-relative">
                        <input type="password"
                               id="password"
                               name="password"
                               class="form-control @error('password') is-invalid @enderror"
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

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn">
                            Confirm Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
