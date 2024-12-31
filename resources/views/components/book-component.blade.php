{{-- Container que recebe os livros --}}

<div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
    <div class="card book-card">
        <img src="{{ Storage::url($book->cover_url) }}"
             class="card-img-top"
             alt="{{ $book->title }}"
             onerror="this.src='{{ asset('images/no-image.png') }}'">
        <div class="card-body text-center">
            <h5 class="card-title">{{ $book->title }}</h5>
            <button class="btn btn-primary" onclick="trackBookClick({{ $book->id }}); window.location.href='/book-details/{{ $book->id }}'">
                {{ $book->access_level == 1 ? 'READ' : 'PREMIUM' }}
            </button>
        </div>
    </div>
</div>

