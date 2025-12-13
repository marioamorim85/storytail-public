<div class="container mt-5">
    {{-- Detalhes do Livro --}}
    @include('book-details.book-info', ['book' => $book])

    {{-- Seção de Comentários --}}
    @include('book-details.book-comments', ['book' => $book])


    {{-- Seção de Livros Relacionados --}}
    @if($relatedBooks->isNotEmpty())
        <div class="container-books mt-4">
            <h3 class="section-title">Related Books</h3>
            <div class="row" id="related-books-list">
                @foreach($relatedBooks as $relatedBook)
                    @include('components.book-component', ['book' => $relatedBook, 'colClass' => 'col-6 col-sm-4 col-md-3 col-lg-2'])
                @endforeach
            </div>
        </div>
    @endif
</div>
