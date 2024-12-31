@extends('components.layout')

@section('title', 'Reset Password')

@section('content')
    <div class="auth-background">
        <div class="auth-container">
            <h2 id="formTitle" class="st-title">Reset Password</h2>

            <div class="alert alert-info mb-4">
                Please enter your new password below to reset your account access.
            </div>


            <div id="resetPasswordForm" class="form-section">
                <form method="POST" action="{{ route('password.store') }}">
                    @csrf

                    <!-- Hidden Token Field -->
                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                    <!-- Email Address Field -->
                    <div class="form-group mb-3">
                        <input type="email"
                               class="form-control @error('email') is-invalid @enderror"
                               name="email"
                               value="{{ old('email', $request->email) }}"
                               placeholder="Email"
                               required
                               autofocus>
                        @error('email')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <!-- New Password Field -->
                    <div class="form-group mb-3 position-relative">
                        <input type="password"
                               class="form-control @error('password') is-invalid @enderror"
                               id="new_password"
                               name="password"
                               placeholder="Password"
                               required
                               autocomplete="new-password">
                        <span id="toggleNewPasswordIcon"
                              onclick="togglePasswordVisibility('new_password', 'toggleNewPasswordIcon')"
                              class="bi bi-eye password-toggle">
                        </span>
                        @error('password')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <!-- Confirm New Password Field -->
                    <div class="form-group mb-3 position-relative">
                        <input type="password"
                               class="form-control @error('password_confirmation') is-invalid @enderror"
                               id="confirm_new_password"
                               name="password_confirmation"
                               placeholder="Confirm Password"
                               required
                               autocomplete="new-password">
                        <span id="toggleConfirmNewPasswordIcon"
                              onclick="togglePasswordVisibility('confirm_new_password', 'toggleConfirmNewPasswordIcon')"
                              class="bi bi-eye password-toggle">
                        </span>
                        @error('password_confirmation')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-between">
                        <button type="submit" class="mt-2 btn">Reset Password</button>
                        <button type="button" class="mt-2 btnSecundary" onclick="window.location.href='{{ route('login') }}'">Login</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
