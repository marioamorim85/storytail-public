@extends('components.layout')

{{-- Banner --}}
@include('manage-my-books.banner', [
    'title' => 'My Favourite Books'])



{{-- Container para os livros favoritos --}}
<div class="container my-5 main-content-favourite">
    <div class="favourites-container">
        @forelse ($favourites as $book)
            <div class="favourite-item fav-glass-card d-flex align-items-center gap-4 p-4 mb-4 position-relative" id="favourite-book-{{ $book->id }}">
                
                {{-- Cover Image --}}
                <div class="fav-img-wrapper">
                    @if($book->progress >= 90)
                        <span class="status-badge-fav"><i class="bi bi-check-lg"></i> Completed</span>
                    @endif
                    <img src="{{ $book->cover_url ? asset('storage/' . $book->cover_url) : asset('images/no-cover.png') }}"
                         alt="{{ $book->title }}" class="img-fluid rounded-4 shadow-sm" style="width: 120px; height: 180px; object-fit: cover;">
                </div>

                {{-- Book Details --}}
                <div class="fav-details flex-grow-1">
                    <h4 class="fw-bold mb-2 text-dark">{{ $book->title }}</h4>
                    
                    <div class="d-flex gap-4 text-muted mb-3">
                        <span class="d-flex align-items-center gap-2">
                            <i class="bi bi-clock text-orange"></i> {{ $book->read_time }} min read
                        </span>
                        {{-- Add more metadata here if available, e.g., Author, Age --}}
                    </div>

                    {{-- Progress Bar (Optional, purely visual for now) --}}
                    @if($book->progress > 0)
                    <div class="progress" style="height: 6px; max-width: 200px; background-color: rgba(0,0,0,0.05);">
                        <div class="progress-bar bg-orange" role="progressbar" style="width: {{ $book->progress }}%" aria-valuenow="{{ $book->progress }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <small class="text-muted mt-1 d-block">{{ $book->progress }}% completed</small>
                    @endif
                </div>

                {{-- Actions --}}
                <div class="fav-actions d-flex flex-column align-items-center gap-3">
                    <a href="{{ url('/book-details/' . $book->id) }}" class="btn btn-orange px-4 py-2 rounded-pill fw-bold shadow-sm" style="min-width: 140px;">
                        Open
                    </a>
                    
                    <button type="button" onclick="toggleFavorite({{ $book->id }}, 'favourites')" class="btn btn-outline-danger px-4 py-2 rounded-pill fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2" style="min-width: 140px;">
                        <i class="bi bi-trash"></i> Remove
                    </button>
                </div>

            </div>
        @empty
            {{-- Overlay com a mensagem e botão --}}
            <div class="favourite-overlay text-center p-5 glass-overlay">
                <h4 class="st-title mb-3">No Favourite Books Found</h4>
                <p class="text-muted mb-4">
                    You haven't added any favourite books yet. Explore our collection and start adding your favourites!
                </p>
                <a href="{{ url('/') }}" class="btn btn-orange text-white">Go to Home</a>
            </div>
        @endforelse
    </div>
</div>
