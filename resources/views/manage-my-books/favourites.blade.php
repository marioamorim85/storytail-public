@extends('components.layout')

{{-- Banner --}}
@include('manage-my-books.banner', [
    'title' => 'My Favourite Books'])

{{-- Cabeçalho da tabela separado --}}
<div class="table-header my-4">
    <div class="row text-center">
        <div class="col-3 fw-bold text-orange">Cover</div>
        <div class="col-3 fw-bold text-orange">Title</div>
        <div class="col-3 fw-bold text-orange">Read Time</div>
        <div class="col-3 fw-bold text-orange">Options</div>
    </div>
</div>

{{-- Container para os livros favoritos --}}
<div class="container my-5 main-content-favourite">
    <div class="favourites-container">
        @forelse ($favourites as $index => $book)
            <div class="row align-items-center favourite-item" id="favourite-book-{{ $book->id }}">
                {{-- Cover --}}
                <div class="col-3 text-center position-relative">
                    {{-- Icon de Livro lido --}}
                    @if($book->progress >= 90)
                        <span class="status-icon-fav"><i class="bi bi-check-lg"></i></span>
                    @endif
                    <img src="{{ $book->cover_url ? asset('storage/' . $book->cover_url) : asset('images/no-cover.png') }}"
                         alt="{{ $book->title }}" class="img-fluid favourite-cover">
                </div>
                {{-- Title --}}
                <div class="col-3 text-center favourite-title">
                    {{ $book->title }}
                </div>
                {{-- Read Time --}}
                <div class="col-3 text-center favourite-time">
                    {{ $book->read_time }} min.
                </div>
                {{-- Options --}}
                <div class="col-3 text-center">
                    <div class="d-flex justify-content-center align-items-center gap-4">
                        <button type="button" onclick="toggleFavorite({{ $book->id }}, 'favourites')" class="delete-icon-btn">
                            <i class="bi bi-trash text-danger"></i>
                        </button>
                        <a href="{{ url('/book-details/' . $book->id) }}" class="btn-read">Open</a>
                    </div>
                </div>
            </div>
            {{-- Linha laranja separadora (exibida apenas se não for o último item) --}}
            @if ($index < count($favourites) - 1)
                <div class="row">
                    <div class="col-12">
                        <hr class="favourite-divider">
                    </div>
                </div>
            @endif
        @empty
            {{-- Overlay com a mensagem e botão --}}
            <div class="favourite-overlay">
                <h4 class="st-title">No Favourite Books Found</h4>
                <p class="text-muted text-center">
                    You haven't added any favourite books yet. Explore our collection and start adding your favourites!
                </p>
                <a href="{{ url('/') }}" class="btn btn-orange text-white">Go to Home</a>
            </div>
        @endforelse
    </div>
</div>
