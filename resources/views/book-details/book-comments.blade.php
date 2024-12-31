<div class="comments-section mt-4 p-4 rounded">
    <h3 class="section-Title">Comments</h3>

    {{-- Lista de comentários aprovados --}}
    @if($book->comments->isNotEmpty())
        @foreach($book->comments as $comment)
            <div class="comment p-3 rounded mb-3">
                <div class="d-flex align-items-center">
                    @if($comment->user->user_photo_url)
                        <img src="{{ Storage::url($comment->user->user_photo_url) }}"
                             alt="{{ $comment->user->getFullName() }}"
                             class="comment-photo me-3 rounded-circle"
                             width="40" height="40"
                             style="object-fit: cover;">
                    @else
                        <img src="{{ asset('images/no-photo.png') }}"
                             alt="No Photo"
                             class="comment-photo me-3 rounded-circle"
                             width="40" height="40"
                             style="object-fit: cover;">
                    @endif
                    <div>
                        <h6 class="m-0">
                            {{ $comment->user->getFullName() }}
                            @if($comment->user->birth_date)
                                - age {{ Carbon\Carbon::parse($comment->user->birth_date)->age }}
                            @endif
                        </h6>
                        <div class="rating">
                            @php
                                $userRating = $comment->user->booksRead()
                                    ->where('book_id', $book->id)
                                    ->first()?->pivot?->rating ?? 0;
                            @endphp
                            @for ($i = 1; $i <= 5; $i++)
                                <i class="bi {{ $i <= $userRating ? 'bi-star-fill' : 'bi-star' }}"
                                   style="color: orange;"></i>
                            @endfor
                        </div>
                        <p class="comment-text">{{ $comment->comment_text }}</p>
                        <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
                    </div>
                </div>
            </div>
        @endforeach
    @else
        <p>No comments yet. Be the first to comment!</p>
    @endif

    {{-- Avisos sobre o estado do comentário --}}
    @auth
        @php
            $hasPendingComment = $book->comments()
                ->where('user_id', auth()->id())
                ->whereHas('moderation', function ($query) {
                    $query->where('status', \App\Models\CommentModeration::STATUS_PENDING);
                })->exists();

            $hasApprovedComment = $book->comments()
                ->where('user_id', auth()->id())
                ->whereHas('moderation', function ($query) {
                    $query->where('status', \App\Models\CommentModeration::STATUS_APPROVED);
                })->exists();
        @endphp

        @if($hasPendingComment)
            <div class="alert alert-info mt-4">
                <i class="bi bi-hourglass-split"></i>
                Your comment is awaiting moderation by the admin.
            </div>
        @elseif($hasApprovedComment)
            <div class="alert alert-success mt-4">
                <i class="bi bi-check-circle"></i>
                You have already commented on this book.
            </div>
        @endif
    @else
        <p class="mt-4">
            <a href="{{ route('login') }}" class="orange-link">Login</a> to view or add comments.
        </p>
    @endauth
</div>
