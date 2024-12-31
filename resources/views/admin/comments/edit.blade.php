@extends('admin.dashboard-layout')

@section('dashboard-content')
    <h1 class="custom-title">Edit Comment #{{ $comment->id }}</h1>

    <div class="mt-3 col-md-8">
        @if($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <form method="POST" action="{{ route('admin.users.comments.update', $comment->id) }}">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-md-6">
                <!-- Comment -->
                <div class="mb-3">
                    <label for="comment_text" class="form-label fw-bold">Comment</label>
                    <textarea class="form-control" id="comment_text" name="comment_text" rows="5" readonly>{{ $comment->comment_text }}</textarea>
                </div>

                <!-- Book -->
                <div class="mb-3">
                    <label class="form-label fw-bold">Book</label>
                    <div class="p-2 bg-light rounded border">
                        <a href="{{ route('admin.books.show', $comment->book_id) }}" class="orange-link">
                            {{ $comment->book->title }}
                        </a>
                    </div>
                </div>

                <!-- User -->
                <div class="mb-3">
                    <label class="form-label fw-bold">User</label>
                    <div class="p-2 bg-light rounded border">
                        <a href="{{ route('admin.users.show', $comment->user_id) }}" class="orange-link">
                            {{ $comment->user->getFullName() }}
                        </a>
                        <br>
                        <small class="text-muted">{{ $comment->user->email }}</small>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <!-- Status -->
                <div class="mb-3">
                    <label for="status" class="form-label fw-bold">Status</label>
                    <select class="form-control" id="status" name="status" required>
                        <option value="pending" {{ $comment->status == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ $comment->status == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ $comment->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>

                <!-- Moderation Notes -->
                <div class="mb-3">
                    <label for="notes" class="form-label fw-bold">Moderation Notes</label>
                    <textarea class="form-control" id="notes" name="notes" rows="4">{{ $comment->moderation->notes ?? 'No notes provided' }}</textarea>
                </div>

                <!-- Moderated At -->
                <div class="mb-3">
                    <label class="form-label fw-bold">Moderated At</label>
                    <div class="p-2 bg-light rounded border">
                        {{ $comment->moderation->moderation_date ? \Carbon\Carbon::parse($comment->moderation->moderation_date)->format('Y-m-d H:i:s') : 'Not moderated yet' }}
                    </div>
                </div>

                <!-- Moderated By -->
                <div class="mb-3">
                    <label class="form-label fw-bold">Moderated By</label>
                    <div class="p-2 bg-light rounded border">
                        @if($comment->moderation && $comment->moderation->user)
                            <a href="{{ route('admin.users.show', $comment->moderation->user->id) }}" class="orange-link">
                                {{ $comment->moderation->user->getFullName() }}
                            </a>
                        @else
                            <span class="text-muted">No moderator assigned</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4 d-flex gap-4">
            <button type="submit" class="btnAdmin">Save Changes</button>
            <a href="{{ route('admin.users.comments.list') }}" class="btnAdminSecundary">Cancel</a>
        </div>
    </form>

    @push('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
        <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Initialize Choices.js for the status dropdown
                const statusDropdown = document.getElementById('status');
                const notesTextarea = document.getElementById('notes');

                if (statusDropdown) {
                    const choices = new Choices(statusDropdown, {
                        searchEnabled: false,
                        itemSelectText: '',
                        shouldSort: false,
                        placeholder: true,
                    });

                    // Função para atualizar as notas baseado no status
                    function updateNotes(status) {
                        const defaultNotes = {
                            'approved': 'This comment has been reviewed and approved.',
                            'rejected': 'This comment has been reviewed and rejected due to inappropriate content.',
                            'pending': 'This comment is pending review.'
                        };

                        notesTextarea.value = defaultNotes[status] || '';
                    }

                    // Adicionar evento de change
                    statusDropdown.addEventListener('change', function(e) {
                        updateNotes(e.target.value);
                    });
                }
            });
        </script>
    @endpush
@endsection
