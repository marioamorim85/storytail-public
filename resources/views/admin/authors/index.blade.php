@php
    use Illuminate\Support\Facades\Storage;
@endphp
@extends('admin.dashboard-layout')

@section('dashboard-content')
    <h1 class="custom-title">Manage Authors</h1>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <a class="btnAdmin" href="{{ route('admin.authors.create') }}">
            <i class="bi bi-plus-circle"></i> Add New Author
        </a>

        {{-- Search/Filter Area --}}
        <div class="d-flex gap-3">
            <select class="form-select" id="nationalityFilter" name="nationalityFilter" style="width: 200px !important;">
                <option value="">All Nationalities</option>
                @foreach($authors->pluck('nationality')->unique()->filter() as $nationality)
                    <option value="{{ $nationality }}">{{ $nationality }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <table id="authors-table" class="table table-striped">
        <thead>
        <tr>
            <th>ID</th>
            <th>Photo</th>
            <th>Name</th>
            <th>Nationality</th>
            <th>Books</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        @foreach ($authors as $author)
            <tr>
                <td>{{ $author->id }}</td>
                <td>
                    @if($author->author_photo_url)
                        <img src="{{ Storage::url($author->author_photo_url) }}"
                             alt="{{ $author->name }}"
                             class="rounded-circle"
                             width="50" height="50"
                             style="object-fit: cover;">
                    @else
                        <img src="{{ asset('images/no-photo.png') }}"
                             alt="No Photo"
                             class="rounded-circle"
                             width="50" height="50"
                             style="object-fit: cover;">
                    @endif
                </td>
                <td>
                    <a href="{{ route('admin.authors.show', $author->id) }}" class="orange-link">
                        {{ $author->name }}
                    </a>
                </td>
                <td>{{ $author->nationality ?? 'Not Specified' }}</td>
                <td>
                    @if($author->books->count() > 0)
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($author->books as $book)
                                <a href="{{ route('admin.books.show', $book->id) }}" class="orange-link">{{ $book->title }}</a>{{ !$loop->last ? ',' : '' }}
                            @endforeach
                        </div>
                    @else
                        <span class="text-muted">No books</span>
                    @endif
                </td>
                <td>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-secondary2 dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            Actions
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="{{ route('admin.authors.show', $author->id) }}" class="dropdown-item">
                                    <i class="bi bi-eye"></i> View
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.authors.edit', $author->id) }}" class="dropdown-item">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                            </li>
                            <li>
                                <form method="POST" action="{{ route('admin.authors.delete', $author->id) }}" onsubmit="return deleteConfirm(event);">
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
            document.addEventListener('DOMContentLoaded', function() {
                // Initialize Choices.js
                new Choices('#nationalityFilter', {
                    itemSelectText: '',
                    placeholder: true,
                    searchEnabled: false
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
                const totalRows = $('#authors-table tbody tr').length;
                let table = $('#authors-table').DataTable({
                    lengthMenu: [[totalRows], [totalRows]],
                    pageLength: totalRows,
                    order: [[0, 'asc']],
                    responsive: true
                });


                // Função para aplicar os filtros
                function applyFilters() {
                    // Filter by nationality
                    $.fn.dataTable.ext.search.push(
                        function(settings, data, dataIndex) {
                            let selectedNationality = $('#nationalityFilter').val();
                            let nationalityCol = $(table.cell(dataIndex, 3).node()).text().trim();

                            if (!selectedNationality) return true;
                            return nationalityCol === selectedNationality;
                        }
                    );

                    table.draw();
                }

                // Event listeners para os filtros
                $('#nationalityFilter').on('change', function() {
                    $.fn.dataTable.ext.search = []; // Limpa os filtros anteriores
                    applyFilters();
                });
            });

            function deleteConfirm(e) {
                e.preventDefault();
                let authorName = $(e.target).closest('tr').find('td:nth-child(3)').text();
                swal({
                    title: "Warning",
                    text: `Are you sure you want to delete this author?`,
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
