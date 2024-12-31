@extends('components.layout')

@section('title', 'Verify Email')

@section('content')
    <div class="auth-background">
        <div class="auth-container">
            <h2 id="formTitle" class="st-title">
                <i class="bi bi-envelope-check me-2"></i>Verify Email
            </h2>

            <div class="alert alert-info mb-4">
                <i class="bi bi-info-circle-fill me-2"></i>
                Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn't receive the email, we will gladly send you another.
            </div>

            <div class="form-section">
                <div class="d-flex justify-content-between align-items-center">
                    <!-- Resend Verification Email Form -->
                    <form method="POST" action="{{ route('verification.send') }}">
                        @csrf
                        <button type="submit" class="btn">
                            <i class="bi bi-send me-2"></i>Resend Verification Email
                        </button>
                    </form>

                    <!-- Logout Form -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btnSecundary">
                            <i class="bi bi-box-arrow-right me-2"></i>Log Out
                        </button>
                    </form>
                </div>

                <!-- Additional Information -->
                <div class="mt-4 text-center">
                    <small class="text-muted">
                        <i class="bi bi-envelope me-1"></i>
                        Please check your spam folder if you don't see the email in your inbox.
                    </small>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .alert-info {
            border-left: 4px solid #0dcaf0;
        }

        .form-section {
            margin-top: 2rem;
        }

        .btn i, .btnSecundary i {
            font-size: 1.1em;
        }

        .text-muted {
            opacity: 0.8;
        }
    </style>
@endpush
