@extends('components.layout')

{{-- Banner --}}
    <div class="fixed-header">
        <div class="st-book-banner">
            {{-- Título com fundo branco sobre o banner --}}
            <h1 class="st-author-title">About he author</h1>
        </div>
    </div>

<div class="main-content-author">
    <div class="container mt-5">
        {{-- Container Principal --}}
        <div class="book-about">
            {{-- Imagem do Autor --}}
            <img src="{{ $author->author_photo_url ? Storage::url($author->author_photo_url) : asset('images/no-photo.png') }}"
                 alt="{{ $author->name ?? 'No photo available' }}"
                 class="author-photo">

            {{-- Informações do Autor --}}
            <div class="book-info">
                <h2 class="book-title custom-title-h3">
                    {{ $author->first_name }} {{ $author->last_name }}
                </h2>

                @if($author->nationality)
                    <p class="author-nant">
                        <strong>Nationality:</strong> {{ $author->nationality }}
                    </p>
                @endif

                @if($author->description)
                    <p class="description">
                        {{ $author->description }}
                    </p>
                @endif
                <a href="{{ url()->previous() }}" class="btn btn-orange">Back to Book</a>
            </div>
        </div>

        {{-- Mais livros do autor --}}
        @if($author->books->isNotEmpty())
            <div class="container mt-5">
                <h4>More books by {{ $author->first_name }} {{ $author->last_name }}</h4>
                <div class="row" id="books-list">
                    @foreach($author->books as $book)
                        @include('components.book-component', ['book' => $book])
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
