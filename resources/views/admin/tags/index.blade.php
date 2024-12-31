@extends('admin.dashboard-layout')

@section('dashboard-content')
    <h1 class="custom-title">Manage Tags</h1>


    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="tag-form-container">
            <button id="showTagForm" class="btnAdmin">
                <i class="bi bi-plus-circle"></i> Add New Tag
            </button>

            {{-- Formulário dinâmico --}}
            <form id="tagForm" class="d-none d-flex align-items-center gap-2" method="POST">
                @csrf
                <input type="hidden" id="tagFormMethod" name="_method" value="POST">
                <input type="hidden" id="tagId" name="id" value="">
                <div class="form-group mb-0">
                    <input type="text"
                           class="form-control"
                           id="tagNameInput"
                           name="name"
                           placeholder="Enter tag name"
                           required>
                </div>
                <button type="submit" class="btnAdmin">Save</button>
                <button type="button" class="btn btnAdminSecundary" id="cancelTagForm">Cancel</button>
            </form>
        </div>
    </div>

    <table id="tags-table" class="table table-striped">
        <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Created At</th>
            <th>Updated At</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        @foreach ($tags as $tag)
            <tr>
                <td>{{ $tag->id }}</td>
                <td>{{ $tag->name }}</td>
                <td>{{ $tag->created_at->format('Y-m-d H:i:s') }}</td>
                <td>{{ $tag->updated_at->format('Y-m-d H:i:s') }}</td>
                <td>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-secondary2 dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            Actions
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <button class="dropdown-item btn-edit-tag"
                                        data-id="{{ $tag->id }}"
                                        data-name="{{ $tag->name }}">
                                    <i class="bi bi-pencil"></i> Edit
                                </button>
                            </li>
                            <li>
                                <form method="POST" action="{{ route('admin.books.tags.delete', $tag->id) }}" class="delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="dropdown-item text-danger btn-delete-tag">
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
        <script>
            $(document).ready(function () {
                const totalRows = $('#tags-table tbody tr').length;
                $('#tags-table').DataTable({
                    lengthMenu: [[totalRows], [totalRows]],
                    pageLength: totalRows,
                    order: [[0, 'asc']],
                    responsive: true
                });

                // Mostra o formulário para adicionar um Tag
                $('#showTagForm').click(function () {
                    resetForm();
                    $(this).hide();
                    $('#tagForm').removeClass('d-none').addClass('d-flex');
                    $('#tagNameInput').focus();
                });

                // Cancela o formulário
                $('#cancelTagForm').click(function () {
                    resetForm();
                });

                // Popula o formulário para edição de um Tag
                $('#tags-table').on('click', '.btn-edit-tag', function () {
                    const tagId = $(this).data('id');
                    const tagName = $(this).data('name');

                    $('#tagForm').attr('action', `/admin/books/tags/${tagId}`);
                    $('#tagFormMethod').val('PUT');
                    $('#tagId').val(tagId);
                    $('#tagNameInput').val(tagName);

                    $('#showTagForm').hide();
                    $('#tagForm').removeClass('d-none').addClass('d-flex');
                });

                // Faz o reset do formulário para o estado inicial
                function resetForm() {
                    $('#tagForm').attr('action', '{{ route('admin.books.tags.store') }}');
                    $('#tagFormMethod').val('POST');
                    $('#tagId').val('');
                    $('#tagNameInput').val('');

                    $('#tagForm').removeClass('d-flex').addClass('d-none');
                    $('#showTagForm').show();
                }

                // Confirmação ao eliminar um Tag
                $('#tags-table').on('click', '.btn-delete-tag', function (e) {
                    e.preventDefault();
                    console.log("Delete button clicked!");
                    const form = $(this).closest('form');
                    swal({
                        title: 'Warning',
                        text: 'Are you sure you want to delete this tag?',
                        icon: 'warning',
                        buttons: true,
                        dangerMode: true,
                    }).then((willDelete) => {
                        if (willDelete) {
                            console.log("Confirmed deletion.");
                            form.submit();
                        }
                    });
                });

            });
        </script>
        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    @endpush
@endsection
