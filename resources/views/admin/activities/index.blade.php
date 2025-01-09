@php
    use Illuminate\Support\Facades\Storage;
@endphp
@extends('admin.dashboard-layout')

@section('dashboard-content')
    <h1 class="custom-title">Manage Activities</h1>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <a class="btnAdmin" href="{{ route('admin.activities.create') }}">
            <i class="bi bi-plus-circle"></i>&nbsp;Add New Activity
        </a>

        {{-- Search/Filter Area --}}
        <div class="d-flex gap-3">
            <select class="form-select filter-select" id="statusFilter" name="statusFilter">
                <option value="">All Status</option>
                <option value="1">Active</option>
                <option value="0">Inactive</option>
            </select>

            <select class="form-select filter-select" id="bookFilter" name="bookFilter">
                <option value="">All Books</option>
                @foreach($books as $book)
                    <option value="{{ $book->id }}">{{ $book->title }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <table id="activities-table" class="table table-striped">
        <thead>
        <tr>
            <th>ID</th>
            <th>Image</th>
            <th>Title</th>
            <th>Books</th>
            <th>Status</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        @foreach ($activities as $activity)
            <tr data-book-ids="{{ $activity->books->pluck('id')->join(',') }}">
                <td>{{ $activity->id }}</td>
                <td>
                    <div class="d-flex gap-1">
                        @if($activity->activityImages->count() > 0)
                            @foreach($activity->activityImages->take(3) as $image)
                                <img src="{{ Storage::url($image->image_url) }}"
                                     alt="{{ $image->title }}"
                                     class="activity-thumbnail"
                                     width="30" height="50"
                                     style="object-fit: cover;">
                            @endforeach
                            @if($activity->activityImages->count() > 3)
                                <span class="badge bg-secondary">
                    +{{ $activity->activityImages->count() - 3 }}
                </span>
                            @endif
                        @else
                            <img src="{{ asset('images/no-image.png') }}"
                                 alt="No Image"
                                 class="activity-thumbnail"
                                 width="30" height="50"
                                 style="object-fit: cover;">
                        @endif
                    </div>
                </td>
                <td>
                    <a href="{{ route('admin.activities.show', $activity->id) }}" class="orange-link">
                        {{ $activity->title }}
                    </a>
                </td>
                <td>
                    @if($activity->books->count() > 0)
                        <div class="d-flex flex-wrap gap-1">
                            @foreach($activity->books as $book)
                                <a href="{{ route('admin.books.show', $book->id) }}" class="orange-link">
                                    {{ $book->title }}
                                </a>{{ !$loop->last ? ',' : '' }}
                            @endforeach
                        </div>
                    @else
                        <span class="text-muted">No books assigned</span>
                    @endif
                </td>


                <td>
                    <span class="status-badge {{ $activity->is_active ? 'active' : 'inactive' }}">
                        {{ $activity->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </td>

                <td>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-secondary2 dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            Actions
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="{{ route('admin.activities.show', $activity->id) }}" class="dropdown-item">
                                    <i class="bi bi-eye"></i> View
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.activities.edit', $activity->id) }}" class="dropdown-item">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                            </li>
                            <li>
                                <form method="POST" action="{{ route('admin.activities.delete', $activity->id) }}" onsubmit="return deleteConfirm(event);">
                                    @csrf
                                    @method('DELETE')
                                    <button class="dropdown-item text-danger">
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

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
        <script>
            // Inicializa os filtros imediatamente após carregar o Choices
            document.addEventListener('DOMContentLoaded', function() {
                const filters = ['statusFilter', 'bookFilter'];
                filters.forEach(filterId => {
                    const element = document.getElementById(filterId);
                    if (element) {
                        new Choices(element, {
                            itemSelectText: '',
                            placeholder: true,
                            searchEnabled: false
                        });
                    }
                });

                // Initialize tooltips
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });
            });
        </script>
        <script src="{{ asset('js/java.js') }}"></script>
        <script>
            $(document).ready(function() {
                const totalRows = $('#activities-table tbody tr').length;
                let table = $('#activities-table').DataTable({
                    lengthMenu: [[totalRows], [totalRows]],
                    pageLength: totalRows,
                    order: [[0, 'asc']],
                    responsive: true
                });


                // Função para aplicar os filtros
                function applyFilters() {
                    // Filtering function para status
                    $.fn.dataTable.ext.search.push(
                        function(settings, data, dataIndex) {
                            let selectedStatus = $('#statusFilter').val();
                            let statusCol = $(table.cell(dataIndex, 4).node()).text().trim();

                            if (!selectedStatus) return true;  // Se nenhum filtro selecionado
                            return (selectedStatus === '1' && statusCol === 'Active') ||
                                (selectedStatus === '0' && statusCol === 'Inactive');
                        }
                    );

                    // Filtros personalizados para book
                    $.fn.dataTable.ext.search.push(
                        function(settings, data, dataIndex) {
                            let selectedBook = $('#bookFilter').val();
                            let bookIds = $(table.row(dataIndex).node()).data('book-ids')?.toString().split(',');

                            if (!selectedBook) return true;
                            return bookIds && bookIds.includes(selectedBook);
                        }
                    );

                    table.draw();
                }

                // Event listeners para os filtros
                $('#statusFilter, #bookFilter').on('change', function() {
                    $.fn.dataTable.ext.search = []; // Limpa os filtros anteriores
                    applyFilters();
                });
            });

            function deleteConfirm(e) {
                e.preventDefault();
                let activityName = $(e.target).closest('tr').find('td:nth-child(3)').text();
                swal({
                    title: "Warning",
                    text: `Are you sure you want to delete this activity?`,
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                }).then((willDelete) => {
                    if (willDelete) {
                        e.target.submit();
                    }
                });
            }
        </script>
    @endpush

    @push('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
        <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    @endpush
@endsection
