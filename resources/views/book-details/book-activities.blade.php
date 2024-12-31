<div class="container mt-5">
    <div class="container-activity">
        @if($book->activities->count() > 0)
            <div class="row g-4">
                @foreach($book->activities as $activity)
                    <div class="col-md-6 col-lg-4">
                        <div class="card activity-card shadow-sm h-100">
                            {{-- Imagem da Atividade --}}
                            @if($activity->activityImages->count() > 0)
                                <img src="{{ Storage::url($activity->activityImages->first()->image_url) }}"
                                     alt="{{ $activity->title }}"
                                     onerror="this.src='{{ asset('images/no-image.png') }}'">
                            @else
                                <img src="{{ asset('images/no-image.png') }}"
                                     alt="{{ $activity->title }}"
                                     class="card-img-top img-fluid rounded-top"
                            @endif
                            {{-- Corpo da Atividade --}}
                            <div class="card-body text-center">
                                <h5 class="custom-title">{{ $activity->title }}</h5>
                                <p class="text-muted small">{{ $activity->description }}</p>

                                {{-- Botão de Download --}}
                                @auth
                                    <button class="btn btn-primary"
                                            data-activity-id="{{ $activity->id }}"
                                            data-book-id="{{ $book->id }}"
                                            data-image-index="0"
                                            data-total-images="{{ $activity->activityImages->count() }}"
                                            onclick="downloadAndUpdateProgress(this, '{{ Storage::url($activity->activityImages->first()->image_url) }}')">
                                        <i class="bi bi-download"></i> Download
                                    </button>
                                @else
                                    <a href="{{ route('login') }}" class="btn btn-primary">
                                        Login to Download
                                    </a>
                                @endauth
                            </div>

                            {{-- Progresso --}}
                            @auth
                                @php
                                    $progress = $activity->userProgress ?? 0;
                                    $totalImages = $activity->activityImages->count();
                                @endphp

                                <div class="progress-container">
                                    <div class="progress" style="width: 120px; height: 8px; background-color: #f1f1f1; border-radius: 10px; overflow: hidden;">
                                        <div class="progress-bar"
                                             data-activity-progress="act_{{ $activity->id }}_book_{{ $book->id }}"
                                             role="progressbar"
                                             style="width: {{ $progress }}%; background-color: orange; border-radius: 10px;"
                                             aria-valuenow="{{ $progress }}"
                                             aria-valuemin="0"
                                             aria-valuemax="100">
                                        </div>
                                    </div>
                                    <small data-progress-text="act_{{ $activity->id }}_book_{{ $book->id }}" class="mt-2">
                                        <span style="color: orange;">{{ $progress }}%</span>
                                    </small>
                                </div>
                            @endauth
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-5">
                <p class="text-muted">No activities available for this book.</p>
            </div>
        @endif
    </div>

    {{-- Seção de Livros Relacionados --}}
    @if($relatedBooks->isNotEmpty())
        <div class="container-books mt-4">
            <h3 class="section-title">Related Books</h3>
            <div class="row" id="books-list">
                @foreach($relatedBooks as $relatedBook)
                    @include('components.book-component', ['book' => $relatedBook])
                @endforeach
            </div>
        </div>
    @endif

</div>
