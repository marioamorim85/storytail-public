@extends('admin.dashboard-layout')

@section('dashboard-content')
    <h1 class="custom-title">Manage Age Groups</h1>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="age-group-form-container">
            <button id="showAgeGroupForm" class="btnAdmin">
                <i class="bi bi-plus-circle"></i> Add New Age
            </button>

            {{-- Formulário dinâmico --}}
            <form id="ageGroupForm" class="d-none d-flex align-items-center gap-2" method="POST">
                @csrf
                <input type="hidden" id="ageGroupFormMethod" name="_method" value="POST">
                <input type="hidden" id="ageGroupId" name="id" value="">
                <div class="form-group mb-0">
                    <input type="text"
                           class="form-control"
                           id="ageGroupNameInput"
                           name="age_group"
                           placeholder="Enter age group (e.g., 3-4)"
                           required>
                </div>
                <button type="submit" class="btnAdmin">Save</button>
                <button type="button" class="btn btnAdminSecundary" id="cancelAgeGroupForm">Cancel</button>
            </form>
        </div>
    </div>

    <table id="age-groups-table" class="table table-striped">
        <thead>
        <tr>
            <th>ID</th>
            <th>Age Group</th>
            <th>Created At</th>
            <th>Updated At</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        @foreach ($ageGroups as $ageGroup)
            <tr>
                <td>{{ $ageGroup->id }}</td>
                <td>{{ $ageGroup->age_group }}</td>
                <td>{{ $ageGroup->created_at->format('Y-m-d H:i:s') }}</td>
                <td>{{ $ageGroup->updated_at->format('Y-m-d H:i:s') }}</td>
                <td>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-secondary2 dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            Actions
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <button class="dropdown-item btn-edit-age-group"
                                        data-id="{{ $ageGroup->id }}"
                                        data-name="{{ $ageGroup->age_group }}">
                                    <i class="bi bi-pencil"></i> Edit
                                </button>
                            </li>
                            <li>
                                <form method="POST" action="{{ route('admin.books.age-groups.delete', $ageGroup->id) }}" class="delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="dropdown-item text-danger btn-delete-age-group">
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
            $(document).ready(function() {
                const totalRows = $('#age-groups-table tbody tr').length;
                $('#age-groups-table').DataTable({
                    lengthMenu: [[totalRows], [totalRows]],
                    pageLength: totalRows,
                    order: [[0, 'asc']],
                    responsive: true
                });


                // Mostra o formulário para adicionar
                $('#showAgeGroupForm').click(function() {
                    resetForm();
                    $(this).hide();
                    $('#ageGroupForm').removeClass('d-none').addClass('d-flex');
                    $('#ageGroupNameInput').focus();
                });

                // Cancela o formulário
                $('#cancelAgeGroupForm').click(function() {
                    resetForm();
                });

                // Popula o formulário para editar
                $('#age-groups-table').on('click', '.btn-edit-age-group', function() {
                    const ageGroupId = $(this).data('id');
                    const ageGroupName = $(this).data('name');

                    $('#ageGroupForm').attr('action', `/admin/books/age-groups/${ageGroupId}`);
                    $('#ageGroupFormMethod').val('PUT');
                    $('#ageGroupId').val(ageGroupId);
                    $('#ageGroupNameInput').val(ageGroupName);

                    $('#showAgeGroupForm').hide();
                    $('#ageGroupForm').removeClass('d-none').addClass('d-flex');
                });

                // Faz o reset do formulário para o estado inicial
                function resetForm() {
                    $('#ageGroupForm').attr('action', '{{ route('admin.books.age-groups.store') }}');
                    $('#ageGroupFormMethod').val('POST');
                    $('#ageGroupId').val('');
                    $('#ageGroupNameInput').val('');

                    $('#ageGroupForm').removeClass('d-flex').addClass('d-none');
                    $('#showAgeGroupForm').show();
                }

                // Confirmação ao eliminar
                $('#age-groups-table').on('click', '.btn-delete-age-group', function(e) {
                    e.preventDefault();
                    console.log("Delete button clicked!");
                    const form = $(this).closest('form');
                    swal({
                        title: "Warning",
                        text: "Are you sure you want to delete this age group?",
                        icon: "warning",
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
