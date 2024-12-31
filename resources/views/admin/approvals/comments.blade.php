@extends('admin.dashboard-layout')

@section('dashboard-content')
    <h1 class="custom-title">Comments Pending Moderation</h1>

    @if($comments->isEmpty())
        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i> No comments pending moderation.
        </div>
    @else
        <table id="comments-table" class="table table-striped">
            <thead>
            <tr>
                <th>ID</th>
                <th>User</th>
                <th>Book</th>
                <th>Comment</th>
                <th>Date</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @foreach($comments as $moderation)
                <tr>
                    <td>{{ $moderation->comment->id }}</td>
                    <td>
                        <div class="d-flex flex-column">
                            <a href="{{ route('admin.users.show', $moderation->comment->user->id) }}"
                               class="orange-link">
                                {{ $moderation->comment->user->getFullName() }}
                            </a>
                            <small class="text-muted">{{ $moderation->comment->user->email }}</small>
                        </div>
                    </td>
                    <td>
                        <a href="{{ route('admin.books.show', $moderation->comment->book_id) }}"
                           class="orange-link">
                            {{ $moderation->comment->book->title }}
                        </a>
                    </td>
                    <td>
                        <div class="text-wrap">
                            {{ $moderation->comment->comment_text }}
                        </div>
                    </td>
                    <td>{{ $moderation->comment->created_at->format('Y-m-d H:i:s') }}</td>
                    <td>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-secondary2 dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                Actions
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <form method="POST" action="{{ route('admin.approvals.comments.update', $moderation->id) }}">
                                        @csrf
                                        <input type="hidden" name="action" value="approve">
                                        <button type="submit" class="dropdown-item text-success">
                                            <i class="bi bi-check-lg"></i> Approve
                                        </button>
                                    </form>
                                </li>
                                <li>
                                    <form method="POST"
                                          action="{{ route('admin.approvals.comments.update', $moderation->id) }}"
                                          class="reject-form">
                                        @csrf
                                        <input type="hidden" name="action" value="reject">
                                        <button type="button" class="dropdown-item text-danger btn-reject-comment">
                                            <i class="bi bi-x-lg"></i> Reject
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        @push('scripts')
            <script>
                $(document).ready(function() {
                    const totalRows = $('#comments-table tbody tr').length;
                    $('#comments-table').DataTable({
                        lengthMenu: [[totalRows], [totalRows]],
                        pageLength: totalRows,
                        order: [[0, 'asc']],
                        responsive: true
                    });


                    // Confirmação ao rejeitar
                    $('.btn-reject-comment').click(function(e) {
                        e.preventDefault();
                        const form = $(this).closest('form');
                        const userName = $(this).closest('tr').find('td:nth-child(2)').text().trim();

                        swal({
                            title: "Warning",
                            text: `Are you sure you want to reject this comment?`,
                            icon: "warning",
                            buttons: true,
                            dangerMode: true,
                        }).then((willReject) => {
                            if (willReject) {
                                form.submit();
                            }
                        });
                    });
                });
            </script>
        @endpush
    @endif
@endsection
