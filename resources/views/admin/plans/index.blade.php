
@extends('admin.dashboard-layout')

@section('dashboard-content')
    <h1 class="custom-title">Manage Plans</h1>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="plan-form-container">
            <button id="showPlanForm" class="btnAdmin">
                <i class="bi bi-plus-circle"></i>&nbsp;Add New Plan
            </button>

            {{-- Formulário dinâmico --}}
            <form id="planForm" class="d-none d-flex align-items-center gap-2" method="POST">
                @csrf
                <input type="hidden" id="planFormMethod" name="_method" value="POST">
                <input type="hidden" id="planId" name="id" value="">

                <div class="form-group mb-0">
                    <input type="text"
                           class="form-control"
                           id="planNameInput"
                           name="name"
                           placeholder="Enter plan name (e.g., Premium)"
                           required>
                </div>

                <div class="form-group mb-0">
                    <select class="form-control" id="accessLevelInput" name="access_level" required>
                        <option value="">Select Access Level</option>
                        <option value="1">Level 1</option>
                        <option value="2">Level 2</option>
                        <option value="3">Level 3</option>
                    </select>
                </div>

                <button type="submit" class="btnAdmin">Save</button>
                <button type="button" class="btn btnAdminSecundary" id="cancelPlanForm">Cancel</button>
            </form>
        </div>
    </div>

    <table id="plans-table" class="table table-striped">
        <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Access Level</th>
            <th>Created At</th>
            <th>Updated At</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        @foreach ($plans as $plan)
            <tr>
                <td>{{ $plan->id }}</td>
                <td>
                    <span class="access-badge {{ $plan->access_level == 2 ? 'premium' : 'free' }}">
                        {{ $plan->name }}
                    </span>
                </td>
                <td>
                    <span class="access-badge {{ $plan->access_level == 2 ? 'premium' : 'free' }}">
                        Level {{ $plan->access_level }}
                    </span>
                </td>
                <td>{{ $plan->created_at->format('Y-m-d H:i:s') }}</td>
                <td>{{ $plan->updated_at->format('Y-m-d H:i:s') }}</td>
                <td>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-secondary2 dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            Actions
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <button class="dropdown-item btn-edit-plan"
                                        data-id="{{ $plan->id }}"
                                        data-name="{{ $plan->name }}"
                                        data-access-level="{{ $plan->access_level }}">
                                    <i class="bi bi-pencil"></i> Edit
                                </button>
                            </li>
                            <li>
                                <form method="POST" action="{{ route('admin.users.plans.delete', $plan->id) }}" class="delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="dropdown-item text-danger btn-delete-plan">
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
                const totalRows = $('#plans-table tbody tr').length;
                $('#plans-table').DataTable({
                    lengthMenu: [[totalRows], [totalRows]],
                    pageLength: totalRows,
                    order: [[0, 'asc']],
                    responsive: true
                });

                // Mostra o formulário para adicionar
                $('#showPlanForm').click(function() {
                    resetForm();
                    $(this).hide();
                    $('#planForm').removeClass('d-none').addClass('d-flex');
                    $('#planNameInput').focus();
                });

                // Cancela o formulário
                $('#cancelPlanForm').click(function() {
                    resetForm();
                });

                // Popula o formulário para editar
                $('#plans-table').on('click', '.btn-edit-plan', function() {
                    const planId = $(this).data('id');
                    const planName = $(this).data('name');
                    const accessLevel = $(this).data('access-level');

                    $('#planForm').attr('action', `/admin/users/plans/${planId}`);
                    $('#planFormMethod').val('PUT');
                    $('#planId').val(planId);
                    $('#planNameInput').val(planName);
                    $('#accessLevelInput').val(accessLevel);

                    $('#showPlanForm').hide();
                    $('#planForm').removeClass('d-none').addClass('d-flex');
                });

                // Faz o reset do formulário para o estado inicial
                function resetForm() {
                    $('#planForm').attr('action', '{{ route('admin.users.plans.store') }}');
                    $('#planFormMethod').val('POST');
                    $('#planId').val('');
                    $('#planNameInput').val('');
                    $('#accessLevelInput').val('');

                    $('#planForm').removeClass('d-flex').addClass('d-none');
                    $('#showPlanForm').show();
                }

                // Confirmação ao eliminar
                $('#plans-table').on('click', '.btn-delete-plan', function(e) {
                    e.preventDefault();
                    const form = $(this).closest('form');
                    const planName = $(this).closest('tr').find('td:nth-child(2)').text();

                    swal({
                        title: "Warning",
                        text: `Are you sure you want to delete this plan?`,
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
