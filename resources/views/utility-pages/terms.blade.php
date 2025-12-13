@extends('components.layout')

{{-- Banner --}}
@include('utility-pages.utility-banner', ['title' => 'Terms & Conditions'])

{{-- Container Principal --}}
<div class="container mt-5">
    <div class="terms-container">
        <h2 class="terms-title">Terms & Conditions</h2>
        
        <div class="terms-text">
            <p>Welcome to StoryTail! By accessing or using our platform, you agree to be bound by these Terms and Conditions. Please read them carefully before using our services.</p>

            <h3>1. Use of the Platform</h3>
            <p>StoryTail provides a digital library and interactive learning tools for children. You agree to use the platform only for lawful purposes and in a way that does not infringe the rights of others or restrict their use of the platform.</p>

            <h3>2. User Accounts</h3>
            <p>To access certain features, you may need to create an account. You are responsible for maintaining the confidentiality of your account information and for all activities that occur under your account.</p>

            <h3>3. Intellectual Property</h3>
            <p>All content on StoryTail, including books, images, audio, and software, is the property of StoryTail or its content suppliers and is protected by copyright laws. You may not reproduce, distribute, or create derivative works without express permission.</p>

            <h3>4. Privacy</h3>
            <p>Your privacy is important to us. Please review our Privacy Policy to understand how we collect, use, and protect your personal information.</p>

            <h3>5. Changes to Terms</h3>
            <p>We reserve the right to modify these terms at any time. Any changes will be effective immediately upon posting on the website. Your continued use of the platform constitutes acceptance of the modified terms.</p>
            
            <h3>6. Contact Us</h3>
            <p>If you have any questions about these Terms and Conditions, please contact us via our Contact page.</p>
        </div>

        <div class="d-flex justify-content-center mt-4">
            <a href="{{ route('home') }}" class="btn btn-orange">Back to Home</a>
        </div>
    </div>
</div>
