@extends('components.layout')

@section('title', 'Register')

@section('content')
    <div class="auth-background">
        <div class="auth-container">
            <h2 id="formTitle" class="st-title">Register</h2>

            <div id="registerForm" class="form-section">
                <form method="POST" action="{{ route('register') }}">
                    @csrf
                    <!-- Nome do utilizador -->
                    <div class="form-group mb-3">
                        <input type="text"
                               class="form-control @error('first_name') is-invalid @enderror"
                               name="first_name"
                               value="{{ old('first_name') }}"
                               placeholder="First Name"
                               required>
                        @error('first_name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <!-- Apelido do utilizador -->
                    <div class="form-group mb-3">
                        <input type="text"
                               class="form-control @error('last_name') is-invalid @enderror"
                               name="last_name"
                               value="{{ old('last_name') }}"
                               placeholder="Last Name"
                               required>
                        @error('last_name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <!-- Data de Nascimento -->
                    <div class="form-group mb-3">
                        <input type="text"
                               class="form-control @error('birth_date') is-invalid @enderror"
                               name="birth_date"
                               value="{{ old('birth_date') }}"
                               placeholder="Select date"
                               required>
                        @error('birth_date')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>


                    <!-- Email do utilizador -->
                    <div class="form-group mb-3">
                        <input type="email"
                               class="form-control @error('email') is-invalid @enderror"
                               name="email"
                               value="{{ old('email') }}"
                               placeholder="Email"
                               required>
                        @error('email')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="form-group mb-3 position-relative">
                        <input type="password"
                               class="form-control @error('password') is-invalid @enderror"
                               id="password"
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

                    <!-- Confirm Password -->
                    <div class="form-group mb-3 position-relative">
                        <input type="password"
                               class="form-control @error('password_confirmation') is-invalid @enderror"
                               id="password_confirmation"
                               name="password_confirmation"
                               placeholder="Confirm Password"
                               required>
                        <span id="toggleConfirmPasswordIcon"
                              onclick="togglePasswordVisibility('password_confirmation', 'toggleConfirmPasswordIcon')"
                              class="bi bi-eye password-toggle">
                        </span>
                        @error('password_confirmation')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between">
                        <button type="submit" class="mt-2 btn">Register</button>
                        <button type="button" class="mt-2 btnSecundary" onclick="window.location.href='{{ route('login') }}'">Login</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
