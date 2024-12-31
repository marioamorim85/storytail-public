@extends('components.layout')

@section('title', 'Reset Password')

@section('content')
    <div class="auth-background">
        <div class="auth-container">
            <h2 id="formTitle" class="st-title">Reset Password</h2>

            <div class="alert alert-info mb-4">
                Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.
            </div>


            <div id="forgotPassForm" class="form-section">
                <form method="POST" action="{{ route('password.email') }}">
                    @csrf
                    <div class="form-group mb-3">
                        <input type="email"
                               class="form-control @error('email') is-invalid @enderror"
                               name="email"
                               value="{{ old('email') }}"
                               placeholder="Email"
                               required
                               autofocus>
                        @error('email')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between">
                        <button type="submit" class="mt-2 btn">Send Reset Link</button>
                        <button type="button" class="mt-2 btnSecundary" onclick="window.location.href='{{ route('login') }}'">Login</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
