<div class="book-about p-4 rounded d-flex align-items-start">
    {{-- Favorito (ícone de coração) --}}
    @auth
        <button class="favorite-btn {{ $book->userFavorite->count() > 0 ? 'active' : '' }}"
                onclick="toggleFavorite({{ $book->id }})">
            <i class="bi bi-heart{{ $book->userFavorite->count() > 0 ? '-fill' : '' }}"
               id="favorite-icon-heart"
               style="color: {{ $book->userFavorite->count() > 0 ? 'red' : 'grey' }};">
            </i>
        </button>
    @endauth

    {{-- Icon de Livro lido --}}
    @if($book->userProgress >= 90)
        <span class="status-icon ms-2">
        <i class="bi bi-check-lg"></i>
    </span>
    @endif

    {{-- Imagem da capa do livro --}}
    <img src="{{ Storage::url($book->cover_url) }}"
         alt="{{ $book->title }}"
         class="book-cover me-4"
         onerror="this.src='{{ asset('images/no-image.png') }}'">

    {{-- Informações do livro --}}
    <div class="book-info">
        <h2 class="book-title d-inline-flex align-items-center">{{ $book->title }}</h2>

        @if($book->authors->isNotEmpty())
            <p class="author-name">
                <a href="{{ route('author.show', ['id' => $book->authors->first()->id, 'book_id' => $book->id]) }}">
                    From: {{ $book->authors->first()->first_name }} {{ $book->authors->first()->last_name }}
                </a>
            </p>
        @endif

        {{-- Avaliação, páginas, tempo e idade --}}
        <div class="book-details">
            <span class="rating">
                Rating:
                @php
                    $rating = $book->avgRating->avg('rating') ?? 0;
                @endphp
                @for ($i = 1; $i <= 5; $i++)
                    @if ($i <= floor($rating))
                        <i class="bi bi-star-fill" style="color: orange;"></i>
                    @elseif ($i == ceil($rating) && $rating - floor($rating) >= 0.5)
                        <i class="bi bi-star-half" style="color: orange;"></i>
                    @else
                        <i class="bi bi-star" style="color: orange;"></i>
                    @endif
                @endfor
            </span>

            <span class="icon-wrapper pages">
                <i class="bi bi-journal-text"></i>
                {{ $book->pages->count() }} pages
            </span>

            <span class="icon-wrapper time">
                <i class="bi bi-alarm"></i>
                {{ $book->read_time ?? 0 }} min.
            </span>

            <span class="icon-wrapper age">
                <i class="bi bi-emoji-smile"></i>
                {{ $book->ageGroup->age_group ?? 'All Ages' }}
            </span>
        </div>

        {{-- Descrição --}}
        <p class="description">
            {{ $book->description ?? 'No description available.' }}
        </p>

        {{-- Botão de voltar --}}
        <a href="{{ route('home') }}" class="btn btn-orange">Back to Home</a>
    </div>
</div>



