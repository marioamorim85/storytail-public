@php
    use Illuminate\Support\Facades\Storage;
@endphp
@extends('admin.dashboard-layout')

@section('dashboard-content')
    <h1 class="custom-title">Manage Comments</h1>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex justify-content-end align-items-center gap-3 ms-auto">
            <select class="form-select" id="statusFilter" name="statusFilter">
                <option value="">All Status</option>
                <option value="pending">Pending</option>
                <option value="approved">Approved</option>
                <option value="rejected">Rejected</option>
            </select>
        </div>
    </div>

    <table id="comments-table" class="table table-striped">
        <thead>
        <tr>
            <th>ID</th>
            <th>User</th>
            <th>Book</th>
            <th>Comment</th>
            <th>Status</th>
            <th>Date</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        @foreach ($comments as $comment)
            <tr>
                <td>{{ $comment->id }}</td>
                <td>
                    <div class="d-flex flex-column">
                        <a href="{{ route('admin.users.show', $comment->user_id) }}" class="orange-link">
                            {{ $comment->user->getFullName() }}
                        </a>
                        <small class="text-muted">{{ $comment->user->email }}</small>
                    </div>
                </td>
                <td>
                    <a href="{{ route('admin.books.show', $comment->book_id) }}" class="orange-link">
                        {{ $comment->book->title }}
                    </a>
                </td>
                <td style="max-width: 300px;">
                    <div class="text-wrap">
                        {{ $comment->comment_text }}
                    </div>
                </td>
                <td>
                    <span class="status-badge {{ $comment->status }}">
                        {{ ucfirst($comment->status) }}
                    </span>
                </td>
                <td>{{ $comment->created_at->format('Y-m-d H:i:s') }}</td>
                <td>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-secondary2 dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            Actions
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="{{ route('admin.users.comments.show', $comment->id) }}" class="dropdown-item">
                                    <i class="bi bi-eye"></i> View
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.users.comments.edit', $comment->id) }}" class="dropdown-item">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                            </li>
                            @if($comment->status === 'pending')
                                <li>
                                    <form method="POST" action="{{ route('admin.users.comments.moderate', $comment->id) }}">
                                        @csrf
                                        <input type="hidden" name="action" value="approve">
                                        <button type="submit" class="dropdown-item text-success">
                                            <i class="bi bi-check-lg"></i> Approve
                                        </button>
                                    </form>
                                </li>
                                <li>
                                    <form method="POST"
                                          action="{{ route('admin.users.comments.moderate', $comment->id) }}"
                                          class="reject-form">
                                        @csrf
                                        <input type="hidden" name="action" value="reject">
                                        <button type="button" class="dropdown-item text-danger btn-reject-comment">
                                            <i class="bi bi-x-lg"></i> Reject
                                        </button>
                                    </form>
                                </li>
                            @endif
                            <li>
                                <form method="POST"
                                      action="{{ route('admin.users.comments.delete', $comment->id) }}"
                                      class="delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="dropdown-item text-danger btn-delete-comment">
                                        <i class="bi bi-trash"></i> Delete
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

    @push('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
        <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const statusFilter = document.getElementById('statusFilter');
                if (statusFilter) {
                    new Choices(statusFilter, {
                        itemSelectText: '',
                        placeholder: true,
                        searchEnabled: false
                    });
                }

                const table = $('#comments-table').DataTable({
                    responsive: true,
                    order: [[0, 'asc']],
                });

                function applyFilters() {
                    // Remove todos os filtros anteriores
                    $.fn.dataTable.ext.search = [];

                    const selectedStatus = $('#statusFilter').val();

                    // Adiciona filtro personalizado
                    if (selectedStatus) {
                        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                            const status = data[4].trim().toLowerCase();
                            return status === selectedStatus.toLowerCase();
                        });
                    }

                    table.draw();
                }

                $('#statusFilter').on('change', function() {
                    applyFilters();
                });

                // Confirmação ao rejeitar
                $(document).on('click', '.btn-reject-comment', function(e) {
                    e.preventDefault();
                    const form = $(this).closest('form');

                    swal({
                        title: "Confirm Rejection",
                        text: `Are you sure you want to reject this comment?`,
                        icon: "warning",
                        buttons: ["Cancel", "Reject"],
                        dangerMode: true,
                    }).then((willReject) => {
                        if (willReject) {
                            form.submit(); // Submete o formulário após a confirmação
                        }
                    });
                });

                // Confirmação ao deletar
                $(document).on('click', '.btn-delete-comment', function(e) {
                    e.preventDefault();
                    const form = $(this).closest('form');

                    swal({
                        title: "Confirm Deletion",
                        text: `Are you sure you want to delete this comment?`,
                        icon: "warning",
                        buttons: ["Cancel", "Delete"],
                        dangerMode: true,
                    }).then((willDelete) => {
                        if (willDelete) {
                            form.submit();
                        }
                    });
                });
            });

        </script>
    @endpush
@endsection
