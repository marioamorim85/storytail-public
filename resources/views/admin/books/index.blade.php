@php
    use Illuminate\Support\Facades\Storage;
@endphp
@extends('admin.dashboard-layout')

@section('dashboard-content')
    <h1 class="custom-title">Manage Books</h1>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <a class="btnAdmin" href="{{ route('admin.books.create') }}">
            <i class="bi bi-plus-circle"></i>&nbsp;Add New Book
        </a>

        {{-- Search/Filter Area --}}
        <div class="d-flex gap-3">
            <select class="form-select" id="statusFilter" name="statusFilter">
                <option value="">All Status</option>
                <option value="1">Active</option>
                <option value="0">Inactive</option>
            </select>

            <select class="form-select" id="accessFilter" name="accessFilter">
                <option value="">All Access</option>
                <option value="1">Free</option>
                <option value="2">Premium</option>
            </select>

            <select class="form-select" id="ageGroupFilter" name="ageGroupFilter">
                <option value="">All Age Groups</option>
                @foreach($ageGroups as $ageGroup)
                    <option value="{{ $ageGroup->id }}">{{ $ageGroup->age_group }}</option>
                @endforeach
            </select>


        </div>
    </div>

    <table id="books-table" class="table table-striped">
        <thead>
        <tr>
            <th>ID</th>
            <th>Cover</th>
            <th>Title</th>
            <th>Author(s)</th>
            <th>Age Group</th>
            <th>Status</th>
            <th>Access Level</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        @foreach ($books as $book)
            <tr data-age-group-id="{{ $book->ageGroup->id ?? '' }}">
                <td>{{ $book->id }}</td>
                <td>
                    @if($book->cover_url)
                        <img src="{{ $book->cover_url }}" alt="Cover" class="book-thumbnail"
                             width="50" height="70" style="object-fit: cover;">
                    @else
                        <img src="{{ asset('images/no-cover.png') }}" alt="No Cover" class="book-thumbnail"
                             width="50" height="70" style="object-fit: cover;">
                    @endif
                </td>
                <td>
                    <a href="{{ route('admin.books.show', $book->id) }}" class="orange-link">
                        {{ $book->title }}
                    </a>
                </td>
                <td>
                    @foreach($book->authors as $author)
                        <a href="{{ route('admin.authors.show', $author->id) }}" class="orange-link">
                            {{ $author->name }}
                        </a>{{ !$loop->last ? ', ' : '' }}
                    @endforeach
                </td>

                <td>{{ $book->ageGroup->age_group ?? 'No Age Group Assigned' }}</td>
                <td>
                    <span class="status-badge {{ $book->is_active ? 'active' : 'inactive' }}">
                        {{ $book->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </td>
                <td>
                    <span class="access-badge {{ $book->access_level == 2 ? 'premium' : 'free' }}">
                        {{ $book->access_level == 2 ? 'Premium' : 'Free' }}
                    </span>
                </td>
                <td>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-secondary2 dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            Actions
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="{{ route('admin.books.show', $book->id) }}" class="dropdown-item">
                                    <i class="bi bi-eye"></i> View
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.books.edit', $book->id) }}" class="dropdown-item">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                            </li>
                            <li>
                                <form method="POST" action="{{ route('admin.books.delete', $book->id) }}" onsubmit="return deleteConfirm(event);">
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
                const filters = ['statusFilter', 'accessFilter', 'ageGroupFilter'];
                filters.forEach(filterId => {
                    const element = document.getElementById(filterId);
                    if (element) {
                        // Define tamanhos no estilo inline
                        element.style.minWidth = "120px";
                        element.style.maxWidth = "200px";
                        element.style.width = "auto";

                        new Choices(element, {
                            itemSelectText: '',
                            placeholder: true,
                            searchEnabled: false
                        });
                    }
                });
            });

        </script>
        <script src="{{ asset('js/java.js') }}"></script>
        <script>
            $(document).ready(function() {
                let table = $('#books-table').DataTable({
                    order: [[0, 'asc']],
                    pageLength: 12,
                    responsive: true
                });

                // Função para aplicar os filtros
                function applyFilters() {
                    // Filtering function para status
                    $.fn.dataTable.ext.search.push(
                        function(settings, data, dataIndex) {
                            let selectedStatus = $('#statusFilter').val();
                            let statusCol = $(table.cell(dataIndex, 5).node()).text().trim();

                            if (!selectedStatus) return true;  // Se nenhum filtro selecionado
                            return (selectedStatus === '1' && statusCol === 'Active') ||
                                (selectedStatus === '0' && statusCol === 'Inactive');
                        }
                    );

                    // Filtros personalizados para access level
                    $.fn.dataTable.ext.search.push(
                        function(settings, data, dataIndex) {
                            let selectedAccess = $('#accessFilter').val();
                            let accessCol = $(table.cell(dataIndex, 6).node()).text().trim();

                            if (!selectedAccess) return true;  // Se nenhum filtro selecionado
                            return (selectedAccess === '1' && accessCol === 'Free') ||
                                (selectedAccess === '2' && accessCol === 'Premium');
                        }
                    );

                    $('#statusFilter, #accessFilter, #ageGroupFilter').on('change', function () {
                        console.log('Filter changed:', $(this).attr('id'), $(this).val()); // Debug do filtro
                        table.draw(); // Redesenha a tabela imediatamente
                    });


                    // Filtros personalizados para age group
                    $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
                        let selectedAgeGroup = $('#ageGroupFilter').val(); // Valor selecionado no dropdown
                        let ageGroupId = $(table.row(dataIndex).node()).data('age-group-id'); // Valor associado à linha

                        // Converte ambos para string para garantir a comparação correta
                        console.log('Selected Age Group:', typeof selectedAgeGroup, selectedAgeGroup);
                        console.log('Row Age Group:', typeof ageGroupId, ageGroupId);

                        if (!selectedAgeGroup) return true; // Exibe todas as linhas se nenhum filtro for aplicado
                        return String(selectedAgeGroup) === String(ageGroupId); // Comparação explícita
                    });

                    table.draw();
                }

                // Event listeners para os filtros
                $('#statusFilter, #accessFilter, #ageGroupFilter').on('change', function() {
                    $.fn.dataTable.ext.search = []; // Limpa os filtros anteriores
                    applyFilters();
                });
            });

            function deleteConfirm(e) {
                e.preventDefault();
                swal({
                    title: "Warning",
                    text: "Are you sure you want to delete this book?",
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
