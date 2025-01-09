@extends('admin.dashboard-layout')

@section('dashboard-content')
    <h1 class="custom-title">Manage User Types</h1>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="usertype-form-container">
            <button id="showUserTypeForm" class="btnAdmin">
                <i class="bi bi-plus-circle"></i>&nbsp;Add New Uer Type
            </button>

            {{-- Formulário dinâmico --}}
            <form id="userTypeForm" class="d-none d-flex align-items-center gap-2" method="POST">
                @csrf
                <input type="hidden" id="userTypeFormMethod" name="_method" value="POST">
                <input type="hidden" id="userTypeId" name="id" value="">

                <div class="form-group mb-0">
                    <input type="text"
                           class="form-control"
                           id="userTypeNameInput"
                           name="user_type"
                           placeholder="Enter user type name (e.g., Admin)"
                           required>
                </div>

                <button type="submit" class="btnAdmin">Save</button>
                <button type="button" class="btn btnAdminSecundary" id="cancelUserTypeForm">Cancel</button>
            </form>
        </div>
    </div>

    <table id="user-types-table" class="table table-striped">
        <thead>
        <tr>
            <th>ID</th>
            <th>User Type</th>
            <th>Created At</th>
            <th>Updated At</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        @foreach ($userTypes as $userType)
            <tr>
                <td>{{ $userType->id }}</td>
                <td>
                    <span class="badge-type {{ strtolower($userType->user_type) }}">
                        {{ ucfirst($userType->user_type) }}
                    </span>
                </td>
                <td>{{ $userType->created_at->format('Y-m-d H:i:s') }}</td>
                <td>{{ $userType->updated_at->format('Y-m-d H:i:s') }}</td>
                <td>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-secondary2 dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            Actions
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <button class="dropdown-item btn-edit-usertype"
                                        data-id="{{ $userType->id }}"
                                        data-name="{{ $userType->user_type }}">
                                    <i class="bi bi-pencil"></i> Edit
                                </button>
                            </li>
                            <li>
                                <form method="POST" action="{{ route('admin.users.user-types.delete', $userType->id) }}" class="delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="dropdown-item text-danger btn-delete-usertype">
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
                const totalRows = $('#user-types-table tbody tr').length;
                $('#user-types-table').DataTable({
                    lengthMenu: [[totalRows], [totalRows]],
                    pageLength: totalRows,
                    order: [[0, 'asc']],
                    responsive: true
                });

                // Mostra o formulário para adicionar
                $('#showUserTypeForm').click(function() {
                    resetForm();
                    $(this).hide();
                    $('#userTypeForm').removeClass('d-none').addClass('d-flex');
                    $('#userTypeNameInput').focus();
                });

                // Cancela o formulário
                $('#cancelUserTypeForm').click(function() {
                    resetForm();
                });

                // Popula o formulário para editar
                $('#user-types-table').on('click', '.btn-edit-usertype', function() {
                    const userTypeId = $(this).data('id');
                    const userTypeName = $(this).data('name');

                    $('#userTypeForm').attr('action', `/admin/users/user-types/${userTypeId}`);
                    $('#userTypeFormMethod').val('PUT');
                    $('#userTypeId').val(userTypeId);
                    $('#userTypeNameInput').val(userTypeName);

                    $('#showUserTypeForm').hide();
                    $('#userTypeForm').removeClass('d-none').addClass('d-flex');
                });

                // Faz o reset do formulário para o estado inicial
                function resetForm() {
                    $('#userTypeForm').attr('action', '{{ route('admin.users.user-types.store') }}');
                    $('#userTypeFormMethod').val('POST');
                    $('#userTypeId').val('');
                    $('#userTypeNameInput').val('');

                    $('#userTypeForm').removeClass('d-flex').addClass('d-none');
                    $('#showUserTypeForm').show();
                }

                // Confirmação ao eliminar
                $('#user-types-table').on('click', '.btn-delete-usertype', function(e) {
                    e.preventDefault();
                    const form = $(this).closest('form');
                    const userTypeName = $(this).closest('tr').find('td:nth-child(2)').text();

                    swal({
                        title: "Warning",
                        text: `Are you sure you want to delete this user type"?`,
                        icon: "warning",
                        buttons: true,
                        dangerMode: true,
                    }).then((willDelete) => {
                        if (willDelete) {
                            form.submit();
                        }
                    });
                });
            });
        </script>
        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    @endpush
@endsection
