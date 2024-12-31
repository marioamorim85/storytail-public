@extends('admin.dashboard-layout')

@section('dashboard-content')
    <h1 class="custom-title">View Comment #{{ $comment->id }}</h1>

    <div class="row">
        <div class="col-md-6">
            <!-- User Information -->
            <div class="mb-4">
                <label class="form-label fw-bold">User</label>
                <div class="p-2 bg-white rounded border">
                    <div class="d-flex flex-column">
                        <a href="{{ route('admin.users.show', $comment->user_id) }}" class="orange-link">
                            {{ $comment->user->getFullName() }}
                        </a>
                        <small class="text-muted">{{ $comment->user->email }}</small>
                    </div>
                </div>
            </div>

            <!-- Associated Book -->
            <div class="mb-4">
                <label class="form-label fw-bold">Associated Book</label>
                <div class="p-2 bg-white rounded border">
                    @if($comment->book)
                        <div class="d-flex align-items-center gap-3">
                            <!-- Book Cover -->
                            @if($comment->book->cover_url)
                                <img src="{{ Storage::url($comment->book->cover_url) }}"
                                     alt="{{ $comment->book->title }}"
                                     class="rounded"
                                     style="width: 50px; height: 70px; object-fit: cover;">
                            @else
                                <img src="{{ asset('images/no-cover.png') }}"
                                     alt="No Cover"
                                     class="rounded"
                                     style="width: 50px; height: 70px; object-fit: cover;">
                            @endif

                            <div class="flex-grow-1">
                                <a href="{{ route('admin.books.show', $comment->book_id) }}" class="orange-link">
                                    <h6 class="mb-1">{{ $comment->book->title }}</h6>
                                </a>
                                <div class="d-flex flex-wrap gap-2">
                                    <!-- Age Group -->
                                    <span class="badge bg-secondary">{{ $comment->book->ageGroup->age_group }}</span>

                                    <!-- Access Level -->
                                    <span class="access-badge {{ $comment->book->access_level == 2 ? 'premium' : 'free' }}">
                                        {{ $comment->book->access_level == 2 ? 'Premium' : 'Free' }}
                                    </span>

                                    <!-- Status -->
                                    <span class="status-badge {{ $comment->book->is_active ? 'active' : 'inactive' }}">
                                        {{ $comment->book->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-muted">No book assigned.</div>
                    @endif
                </div>
            </div>

            <!-- Status -->
            <div class="mb-4">
                <label class="form-label fw-bold">Status</label>
                <div class="p-2 bg-white rounded border">
                    <span class="status-badge {{ $comment->status }}">
                        {{ ucfirst($comment->status) }}
                    </span>
                </div>
            </div>

            <!-- Comment Text -->
            <div class="mb-4">
                <label class="form-label fw-bold">Comment</label>
                <div class="p-2 bg-white rounded border" style="min-height: 120px;">
                    {{ $comment->comment_text }}
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <!-- Moderated At -->
            <div class="mb-4">
                <label class="form-label fw-bold">Moderated At</label>
                <div class="p-2 bg-white rounded border">
                    @if($comment->moderation && $comment->moderation->moderation_date)
                        {{ \Carbon\Carbon::parse($comment->moderation->moderation_date)->format('F j, Y H:i') }}
                    @else
                        <span class="text-muted">Not moderated yet</span>
                    @endif
                </div>
            </div>

            <!-- Moderated By -->
            <div class="mb-4">
                <label class="form-label fw-bold">Moderated By</label>
                <div class="p-2 bg-white rounded border">
                    @if($comment->moderation && $comment->moderation->user)
                        <a href="{{ route('admin.users.show', $comment->moderation->user->id) }}" class="orange-link">
                            {{ $comment->moderation->user->getFullName() }}
                        </a>
                    @else
                        <span class="text-muted">No moderator assigned</span>
                    @endif
                </div>
            </div>

            <!-- Moderation Notes -->
            <div class="mb-4">
                <label class="form-label fw-bold">Moderation Notes</label>
                <div class="p-2 bg-white rounded border" style="min-height: 100px;">
                    {{ $comment->moderation->notes ?? 'No notes provided' }}
                </div>
            </div>

            <!-- Created At -->
            <div class="mb-4">
                <label class="form-label fw-bold">Created At</label>
                <div class="p-2 bg-white rounded border">
                    {{ $comment->created_at->format('F j, Y H:i') }}
                </div>
            </div>

            <!-- Updated At -->
            <div class="mb-4">
                <label class="form-label fw-bold">Last Updated</label>
                <div class="p-2 bg-white rounded border">
                    {{ $comment->updated_at->format('F j, Y H:i') }}
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <a href="{{ route('admin.users.comments.list') }}" class="btnAdminSecundary">Back to List</a>
    </div>
@endsection
