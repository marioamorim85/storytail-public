@extends('components.layout')

{{-- Banner --}}
@include('utility-pages.utility-banner', ['title' => 'Contacts'])

{{-- container principal --}}
<div class="container mt-5">
    <div class="contact-us-container">
        <div class="row">
            {{-- Imagem --}}
            <div class="col-md-5 text-center">
                <img src="{{ asset('images/mailbox.png') }}" alt="Mail Box" class="contact-image">
            </div>

            {{-- Formulário de contato --}}
            <div class="col-md-7">
                <h2 class="contact-title">Contact us</h2>
                <form action="{{ route('contacts.store') }}" method="POST">
                    @csrf
                    <div class="mb-3 form-contact">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" id="email" name="email" class="form-control" placeholder="yourmail@example.com" required>
                    </div>
                    <div class="mb-3 form-contact">
                        <label for="name" class="form-label">First Name</label>
                        <input type="text" id="name" name="name" class="form-control" placeholder="Your Name" required>
                    </div>
                    <div class="mb-3 form-contact">
                        <label for="subject" class="form-label">Subject</label>
                        <input type="text" id="subject" name="subject" class="form-control" placeholder="Subject" required>
                    </div>
                    <div class="mb-3 form-contact">
                        <label for="message" class="form-label">Message</label>
                        <textarea id="message" name="message" class="form-control" rows="5" placeholder="Your doubts and questions" required></textarea>
                    </div>
                    <div class="d-flex contact-buttons">
                        <button type="submit" class="btn btn-primary">Submit</button>
                        <a href="{{ route('home') }}" class="btn btn-secondary">Back to Home</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
