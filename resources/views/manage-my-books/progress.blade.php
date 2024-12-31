@extends('components.layout')

{{-- Banner --}}
@include('manage-my-books.banner', [
    'title' => 'My Books Progress'])

{{-- Cabeçalho da tabela separado --}}
<div class="table-header my-4">
    <div class="row text-center">
        <div class="col-3 fw-bold text-orange">Cover</div>
        <div class="col-3 fw-bold text-orange">Title</div>
        <div class="col-3 fw-bold text-orange">Completion</div>
        <div class="col-3 fw-bold text-orange">Options</div>
    </div>
</div>

{{-- Container para os livros --}}
<div class="container my-5 main-content-favourite">
    <div class="favourites-container">
        @forelse ($books as $index => $book)
            <div class="row align-items-center favourite-item" id="book-progress-{{ $book->id }}">
                {{-- Cover --}}
                <div class="col-3 text-center">
                    <img src="{{ $book->cover_url ? asset('storage/' . $book->cover_url) : asset('images/no-cover.png') }}"
                         alt="{{ $book->title }}" class="img-fluid favourite-cover">
                </div>
                {{-- Title --}}
                <div class="col-3 text-center favourite-title">
                    {{ $book->title }}
                </div>
                {{-- Completion --}}
                <div class="col-3 text-center favourite-time">
                    <div class="progress-container">
                        <div class="progress-blocks">
                            @php
                                $totalBlocks = 10; // Número total de blocos na barra de progresso
                                $filledBlocks = round(($book->pivot->progress / 100) * $totalBlocks); // Calcula os blocos preenchidos
                            @endphp

                            @for ($i = 1; $i <= $totalBlocks; $i++)
                                <div class="progress-block {{ $i <= $filledBlocks ? 'filled' : '' }}"></div>
                            @endfor
                        </div>
                        <div class="progress-percentage">{{ $book->pivot->progress }}%</div>
                    </div>

                </div>
                {{-- Options --}}
                <div class="col-3 text-center">
                    <div class="d-flex justify-content-center align-items-center gap-4">
                        <a href="{{ url('/book-details/' . $book->id) }}" class="btn-read">Open</a>
                    </div>
                </div>
            </div>
            {{-- Linha laranja separadora (exibida apenas se não for o último item) --}}
            @if ($index < count($books) - 1)
                <div class="row">
                    <div class="col-12">
                        <hr class="favourite-divider">
                    </div>
                </div>
            @endif
        @empty
            {{-- Overlay com a mensagem e botão --}}
            <div class="favourite-overlay">
                <h4 class="st-title">No Books Found</h4>
                <p class="text-muted text-center">
                    You haven't read any books yet. Explore our collection and start reding your favourite books!
                </p>
                <a href="{{ url('/') }}" class="btn btn-orange text-white">Go to Home</a>
            </div>
        @endforelse
    </div>
</div>
