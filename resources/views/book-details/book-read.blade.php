
@php use Illuminate\Support\Facades\Storage; @endphp

@if($hasAccess)
    @php
        // Define um progresso padrão
        $currentProgress = 0;

        // Verifica se o utilizador está autenticado antes de tentar buscar o progresso
        if (auth()->check()) {
            $currentProgress = auth()->user()->booksRead()
                ->where('book_id', $book->id)
                ->first()?->pivot?->progress ?? 0;
        }
    @endphp

    <body data-user-logged-in="{{ auth()->check() ? 'true' : 'false' }}">


    {{-- Barra de Progresso --}}
    <div class="progress-container">
        <div class="progress" style="width: 120px; height: 8px; background-color: #f1f1f1; border-radius: 10px; overflow: hidden;">
            <div class="progress-bar"
                 role="progressbar"
                 style="width: {{ $currentProgress }}%; background-color: orange; border-radius: 10px;"
                 aria-valuenow="{{ $currentProgress }}"
                 aria-valuemin="0"
                 aria-valuemax="100"
                 id="reading-progress-bar">
            </div>
        </div>
        <small id="progress-text" class="text-orange mt-2">{{ $currentProgress }}%</small>
    </div>

    <div class="book-slider-container">
        <div id="book-slider" class="book-slider" data-book-id="{{ $book->id }}">
            {{-- Páginas do Livro --}}
            @forelse($book->pages as $page)
                <div class="page">
                    <div class="image-container">
                        <img
                            src="{{ $page->page_image_url ? Storage::url($page->page_image_url) : asset('images/no-image.png') }}"
                            alt="Page {{ $page->page_index }}"
                            class="book-page"
                            @if($page->audio_url)
                                data-audio="{{ Storage::url($page->audio_url) }}"
                            @endif
                            onerror="this.src='{{ asset('images/no-image.png') }}'">
                    </div>
                </div>
            @empty
                <div class="page">
                    <div class="image-container">
                        <img src="{{ asset('images/no-image.png') }}"
                             alt="No pages available"
                             class="book-page">
                    </div>
                </div>
            @endforelse

            {{-- Última Página: Formulário de Comentários --}}
            @auth
                @php
                    $hasSubmittedComment = $book->comments()
                        ->where('user_id', auth()->id())
                        ->exists();
                @endphp

                @if (!$hasSubmittedComment)
                    <div class="page d-flex justify-content-center align-items-center text-center">
                        <div class="add-comment p-4">
                            <h4 class="st-title mb-4">Add Your Comment</h4>
                            <form action="{{ route('book.comment', $book->id) }}" method="POST">
                                @csrf
                                {{-- Avaliação por estrelas --}}
                                <div class="form-group mb-4">
                                    <div class="star-rating d-flex align-items-center justify-content-center">
                                        <label for="rating" class="rating-label mb-0">Rating</label>
                                        <input type="hidden" name="rating" id="rating-value" value="" required>
                                        @for ($i = 1; $i <= 5; $i++)
                                            <i class="bi bi-star rating-star"
                                               data-value="{{ $i }}"
                                               style="color: #ccc; cursor: pointer; font-size: 1.5rem;"></i>
                                        @endfor
                                    </div>
                                </div>

                                {{-- Campo de comentário --}}
                                <div class="form-group mb-4">
                                    <label for="comment" class="comment-label">Your Comment</label>
                                    <textarea name="comment_text" id="comment" rows="3" placeholder="How did your book adventure go?"
                                              class="form-control comment-area" required></textarea>
                                </div>

                                {{-- Botão de submissão --}}
                                <button type="submit" class="btn btn-primary mt-4">Submit Comment</button>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="page d-flex justify-content-center align-items-center text-center">
                        <div class="comment-container p-4 text-center">
                            <h4 class="st-title mb-4">You have already submitted a comment!</h4>
                            <p class="text-muted text-center">Thank you for sharing your feedback!</p>
                        </div>
                    </div>
                @endif
            @endauth

            {{-- Última Página: Login para visitantes --}}
            @guest
                <div class="page d-flex justify-content-center align-items-center text-center">
                    <div class="login-prompt p-4">
                        <h4 class="st-title mb-4">Did you enjoy the book?</h4>
                        <p class="mb-4">Create an account to track your reading progress, earn badges, and leave comments!</p>
                        <div class="d-flex justify-content-center gap-3">
                            <a href="{{ route('register') }}" class="btn btn-primary" onclick="window.location.href='{{ route('register') }}'">Register</a>
                            <a href="{{ route('login') }}" class="btn btn-secondary" onclick="window.location.href='{{ route('login') }}'">Login</a>
                        </div>
                    </div>
                </div>
            @endguest

        </div>

        {{-- Botões de Navegação --}}
        @if($book->pages->count() > 1)
            <button class="slider-nav prev" id="prev-slide"><i class="bi bi-chevron-left"></i></button>
            <button class="slider-nav next" id="next-slide"><i class="bi bi-chevron-right"></i></button>
        @endif
    </div>

    {{-- Adicionar o progresso inicial para o JavaScript --}}
    <script>
        window.initialProgress = {{ $currentProgress ?? 0 }};
        window.totalBookPages = {{ $book->pages->count() }};
    </script>
@endif
