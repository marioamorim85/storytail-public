@extends('components.layout')

@section('title', 'Login')

@section('content')
    <div class="auth-background">
        <div class="auth-container">
            <h2 id="formTitle" class="st-title">Login</h2>

            <!-- Form Login -->
            <div id="loginForm" class="form-section">
                <form method="POST" action="">
                    @csrf
                    <div class="form-group mb-3">
                        <input type="email" id="email" class="form-control" name="email" placeholder="E-mail" required autofocus>
                    </div>
                    <div class="form-group mb-3">
                        <input type="password" id="password" class="form-control" name="password" placeholder="Password" required>
                    </div>
                    <div class="form-group text-end mb-3">
                        <a class="forgot-password" onclick="showForm('forgotPassForm')">Forgot Password?</a>
                    </div>
                    <div class="d-flex justify-content-between">
                        <button type="submit" class="mt-2 btn">Login</button>
                        <button type="button" class="mt-2 btnSecundary" onclick="showForm('registerForm')">Register</button>
                    </div>
                </form>
            </div>

            <!-- Form Register -->
            <div id="registerForm" class="form-section hidden">
                <form method="POST" action="">
                    @csrf
                    <div class="form-group mb-3">
                        <input type="text" class="form-control" placeholder="First Name" required>
                    </div>
                    <div class="form-group mb-3">
                        <input type="text" class="form-control" placeholder="Last Name" required>
                    </div>
                    <div class="form-group mb-3">
                        <input type="email" class="form-control" placeholder="Email" required>
                    </div>
                    <div class="form-group mb-3">
                        <input type="date" class="form-control" placeholder="Birthday Date" required>
                    </div>
                    <div class="form-group mb-3">
                        <input type="password" class="form-control" placeholder="Password" required>
                    </div>
                    <div class="form-group mb-3">
                        <input type="password" class="form-control" placeholder="Confirm Password" required>
                    </div>
                    <div class="d-flex justify-content-between">
                        <button type="submit" class="mt-2 btn">Register</button>
                        <button type="button" class="mt-2 btnSecundary" onclick="showForm('loginForm')">Login</button>
                    </div>
                </form>
            </div>

            <!-- Form Forgot Password -->
            <div id="forgotPassForm" class="form-section hidden">
                <form method="POST" action="">
                    @csrf
                    <div class="form-group mb-3">
                        <input type="email" class="form-control" placeholder="Email" required>
                    </div>
                    <div class="d-flex justify-content-between">
                        <button type="submit" class="mt-2 btn">Recover</button>
                        <button type="button" class="mt-2 btnSecundary" onclick="showForm('loginForm')">Login</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
