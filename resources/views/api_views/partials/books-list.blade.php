@if($books->isEmpty())
    <p>No books found.</p>
@else
    <div class="row">
        @foreach($books as $book)
            <div class="col-md-4 mb-3 d-flex align-items-stretch">
                <div class="card flex-fill">
                    <div class="card-body">
                        <!-- Título do livro -->
                        <h5 class="card-title">{{ $book->title }}</h5>

                        <!-- Nome do autor ou autores -->
                        <p class="card-text">Author:
                            {{ $book->authors->map(function($author) {
                                return $author->first_name . ' ' . $author->last_name;
                            })->join(', ') }}
                        </p>

                        <!-- Descrição do livro -->
                        <p class="card-text">{{ $book->description }}</p>

                        <!-- Tempo estimado de leitura -->
                        <p class="card-text">Read Time: {{ $book->read_time }} mins</p>

                        <!-- Grupo etário recomendado -->
                        <p class="card-text">Age Group: {{ $book->ageGroup->age_group }}</p>

                        <!-- Botão para alternar a visibilidade das atividades -->
                        <button class="btn btn-info toggle-activities" data-book-id="{{ $book->id }}">See Activities</button>

                        <!-- Lista de atividades relacionadas ao livro -->
                        <div class="activities-list mt-3" id="activities-{{ $book->id }}" style="display: none;">
                            @if($book->activities->isEmpty())
                                <p>No activities available for this book.</p>
                            @else
                                @foreach($book->activities as $activity)
                                    <div class="activity-item">
                                        <!-- Título da atividade -->
                                        <h6>{{ $activity->title }}</h6>

                                        <!-- Descrição da atividade -->
                                        <p>{{ $activity->description }}</p>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
