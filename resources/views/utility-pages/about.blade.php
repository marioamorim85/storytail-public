@extends('components.layout')

{{-- Banner --}}
@include('utility-pages.utility-banner', ['title' => 'About'])

{{-- Container Principal --}}
<div class="container mt-5">
    <div class="about-us-container">
        <div class="row">
            {{-- Imagem --}}
            <div class="col-md-3 text-center">
                <img src="../images/storytail-logo-02.png" alt="StoryTail Logo" class="about-image">
            </div>

            {{-- About--}}
            <div class="col-md-8">
                <h2 class="st-title mb-8">About us</h2>
                <div class="about-text">
                    <p>StoryTail is an innovative digital platform designed to inspire children aged 3 to 9 to learn English through interactive reading experiences.
                        Our mission is to create an engaging environment that fosters a love for reading and language learning. With a carefully curated library of
                        children's books available in text, audio, and video formats, StoryTail provides a multi-sensory approach to language development.</p>
                    <p>At StoryTail, we believe that learning should be fun, immersive, and tailored to each child's needs. Our platform offers a variety of interactive
                        activities, including quizzes and themed challenges, that are designed to reinforce comprehension and vocabulary in a playful manner. StoryTail
                        also enables children to track their reading progress, mark favorite books, and explore educational content that aligns with their interests and
                        learning pace.</p>
                    <p>Parents and educators play a vital role in the StoryTail experience. Our platform allows them to monitor the child's progress, offering insights
                        into their learning journey and areas for growth. For administrators, StoryTail includes back-office systems for managing and analyzing user engagement,
                        ensuring that our library remains fresh, relevant, and effective.</p>
                </div>

                <div class="d-flex justify-content-end mt-1">
                    <a href="{{ route('home') }}" class="btn btn-orange">Back to Home</a>
                </div>
            </div>
        </div>
    </div>
</div>
