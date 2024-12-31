@extends('components.layout')

{{-- Banner do Livro --}}
@include('book-details.book-banner', [
    'title' => $book->title,
    'ageGroup' => $book->ageGroup->name ?? 'All Ages'
])

{{-- Menu do Livro --}}
<div class="book-main-content">
    <div class="home-menu text-center my-4">
        <ul class="nav nav-tabs justify-content-center" id="book-menu-tabs">
            <li class="nav-item">
                <a class="nav-link active" href="#" data-category="about">About the Book</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" data-category="read">
                    Read Now
                </a>
            </li>
            @if($book->video)
                <li class="nav-item">
                    <a class="nav-link" href="#" data-category="video">See Video</a>
                </li>
            @endif
            @if($book->activities->count() > 0)
                <li class="nav-item">
                    <a class="nav-link" href="#" data-category="activities">Activities</a>
                </li>
            @endif
        </ul>
    </div>
</div>

<div class="main-content-book">
    {{-- About the Book --}}
    <div id="about-section" class="content-section hidden">
        @include('book-details.book-about', [
            'book' => $book,
            'hasAccess' => $hasAccess,
            'authors' => $book->authors,
            'averageRating' => $book->averageRating
        ])
    </div>

    {{-- Read Now --}}
    <div id="read-section" class="content-section hidden">
        @if($hasAccess)
            @if($book->pages->count() > 0)
                @include('book-details.book-read', [
                    'book' => $book,
                    'pages' => $book->pages->sortBy('page_index')
                ])
            @else
                <div class="alert alert-info text-center">
                    <p>No pages available for this book yet.</p>
                </div>
            @endif
        @else
            <div class="premium-overlay container text-center p-5">
                <h3 class="custom-title-h3">Oops! This Book is for Premium Members Only</h3>
                <p class="text-muted text-center">
                    It looks like the book you've selected is part of our Premium collection.
                    To read this book and explore all its exciting content, you'll need to subscribe to our Premium plan.
                </p>
                <p class="text-muted text-center">
                    Ask an adult to help you, and you'll soon have access to even more amazing stories!
                </p>
                <a href="{{ route('profile') }}" class="btn btn-primary mt-4">Subscribe</a>
            </div>
        @endif
    </div>

    {{-- See Video --}}
    @if($book->video)
        <div id="video-section" class="content-section hidden">
            @if($hasAccess)
                @include('book-details.book-video', [
                    'video' => $book->video
                ])
            @else
                <div class="premium-overlay container text-center p-5">
                    <h3 class="custom-title-h3">Oops! This Video is for Premium Members Only</h3>
                    <p class="text-muted text-center">
                        It looks like the video you've selected is part of our Premium collection.
                        To watch this video and explore all its exciting content, you'll need to subscribe to our Premium plan.
                    </p>
                    <p class="text-muted text-center">
                        Ask an adult to help you, and you'll soon have access to even more amazing stories and videos!
                    </p>
                    <a href="{{ route('profile') }}" class="btn btn-primary mt-4">Subscribe</a>
                </div>
            @endif
        </div>
    @endif

    {{-- Activities --}}
    @if($book->activities->count() > 0)
        <div id="activities-section" class="content-section hidden">
            @if($hasAccess)
                @include('book-details.book-activities', [
                    'activities' => $book->activities
                ])
            @else
                <div class="premium-overlay container text-center p-5">
                    <h3 class="custom-title-h3">Oops! These Activities are for Premium Members Only</h3>
                    <p class="text-muted text-center">
                        It looks like the activities you've selected are part of our Premium collection.
                        To access these activities and explore all their exciting content, you'll need to subscribe to our Premium plan.
                    </p>
                    <p class="text-muted text-center">
                        Ask an adult to help you, and you'll soon have access to even more amazing activities and content!
                    </p>
                    <a href="{{ route('profile') }}" class="btn btn-primary mt-4">Subscribe</a>
                </div>
            @endif
        </div>
    @endif
</div>
