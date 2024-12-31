@php
    use Illuminate\Support\Facades\Storage;
    use App\Models\UserType;
    use App\Models\Plan;
    use Carbon\Carbon;
@endphp

@extends('admin.dashboard-layout')

@section('dashboard-content')
    <h1 class="custom-title">Manage Users</h1>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <a class="btnAdmin" href="{{ route('admin.users.create') }}">
            <i class="bi bi-plus-circle"></i> Add New User
        </a>

        {{-- Search/Filter Area --}}
        <div class="d-flex gap-3">
            <select class="form-select" id="statusFilter" name="statusFilter">
                <option value="">All Status</option>
                @php
                    $statuses = ['active', 'suspended', 'inactive'];
                @endphp
                @foreach($statuses as $status)
                    <option value="{{ $status }}">
                        {{ ucfirst($status) }} ({{ $users->where('status', $status)->count() }})
                    </option>
                @endforeach
            </select>

            <select class="form-select" id="userTypeFilter" name="userTypeFilter">
                <option value="">All User Types</option>
                @foreach($userTypes as $type)
                    <option value="{{ $type->id }}">
                        {{ $type->user_type }} ({{ $users->where('user_type_id', $type->id)->count() }})
                    </option>
                @endforeach
            </select>

            <select class="form-select" id="planFilter" name="planFilter">
                <option value="">All Plans</option>
                @foreach($plans as $plan)
                    <option value="{{ $plan->id }}">
                        {{ $plan->name }} ({{ $planCounts[$plan->id] ?? 0 }})
                    </option>
                @endforeach
            </select>

            <button id="clearFilters" class="btn btn-outline-secondary d-none">
                <i class="bi bi-x-circle"></i> Clear Filters
            </button>
        </div>
    </div>

    <div class="table-responsive">
        <table id="users-table" class="table table-striped">
            <thead>
            <tr>
                <th>ID</th>
                <th>Photo</th>
                <th>Name</th>
                <th>Email</th>
                <th>Type</th>
                <th>Plan</th>
                <th>Status</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @foreach ($users as $user)
                <tr data-user-type="{{ $user->user_type_id }}" data-plan-id="{{ optional($user->subscription)->plan_id }}">
                    <td>{{ $user->id }}</td>
                    <td>
                        @if($user->user_photo_url)
                            <img src="{{ Storage::url($user->user_photo_url) }}"
                                 alt="{{ $user->getFullName() }}"
                                 class="rounded-circle"
                                 width="40" height="40"
                                 style="object-fit: cover;">
                        @else
                            <img src="{{ asset('images/no-photo.png') }}"
                                 alt="No Photo"
                                 class="rounded-circle"
                                 width="40" height="40"
                                 style="object-fit: cover;">
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.users.show', $user->id) }}" class="orange-link">
                            {{ $user->getFullName() }}
                        </a>
                    </td>

                    <td>{{ $user->email }}</td>
                    <td>
                        <span class="badge-type {{ strtolower($user->userType->user_type) }}">
                            {{ $user->userType->user_type }}
                        </span>
                    </td>
                    <td>
                        @if($user->subscription)
                            <div class="plan-badge d-flex align-items-center">
                                <span class="access-badge {{ strtolower($user->subscription->plan->name ?? 'no-plan') }}">
                                    {{ $user->subscription->plan->name ?? 'No Plan' }}
                                </span>
                                @if($user->subscription->start_date || $user->subscription->end_date)
                                    <button class="btn btn-link btn-sm p-0 info-btn ms-2"
                                            data-bs-toggle="tooltip"
                                            data-bs-placement="top"
                                            title="Start Date: {{ $user->subscription->start_date ? $user->subscription->start_date->format('Y-m-d') : 'N/A' }}
                        @if($user->subscription->end_date)
                        &#13;End Date: {{ $user->subscription->end_date->format('Y-m-d') }}
                        @endif">
                                        <i class="bi bi-info-circle small"></i>
                                    </button>
                                @endif
                            </div>
                        @else
                            <span class="text-muted">No Plan</span>
                        @endif
                    </td>

                    <td>
                        <span class="status-badge {{ strtolower($user->status) }}">
                            {{ ucfirst($user->status) }}
                            @if($user->status === 'suspended')
                                <i class="bi bi-exclamation-triangle-fill text-warning ms-1"></i>
                            @endif
                        </span>
                    </td>
                    <td>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-secondary2 dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                Actions
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="{{ route('admin.users.show', $user->id) }}" class="dropdown-item">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.users.edit', $user->id) }}" class="dropdown-item">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                </li>
                                @if($user->user_type_id !== UserType::ADMIN)
                                    <li>
                                        <form method="POST" action="{{ route('admin.users.delete', $user->id) }}" onsubmit="return deleteConfirm(event);">
                                            @csrf
                                            @method('DELETE')
                                            <button class="dropdown-item text-danger" type="submit">
                                                <i class="bi bi-trash"></i> Delete
                                            </button>
                                        </form>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    @push('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
        <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
        <script src="{{ asset('js/java.js') }}"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Initialize Choices.js
                ['statusFilter', 'userTypeFilter', 'planFilter'].forEach(filterId => {
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

            $(document).ready(function() {
                const totalRows = $('#users-table tbody tr').length;
                let table = $('#users-table').DataTable({
                    lengthMenu: [[totalRows], [totalRows]],
                    pageLength: totalRows,
                    order: [[0, 'asc']],
                    responsive: true,
                    language: {
                        search: "Search users:",
                        lengthMenu: "Show _MENU_ users per page",
                        info: "Showing _START_ to _END_ of _TOTAL_ users",
                        emptyTable: "No users found"
                    },
                    columnDefs: [
                        { targets: 0, width: "5%" },  // ID
                        { targets: 1, width: "5%" },  // Photo
                        { targets: 2, width: "15%" }, // Name
                        { targets: 3, width: "15%" }, // Email
                        { targets: 4, width: "10%" }, // Type
                        { targets: 5, width: "10%" }, // Plan
                        { targets: 6, width: "10%" }, // Status
                        { targets: 7, width: "10%" }  // Actions
                    ],
                    autoWidth: false,
                });

                function applyFilters() {
                    // Status filter
                    $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                        let selectedStatus = $('#statusFilter').val();
                        let statusCol = $(table.cell(dataIndex, 6).node()).text().trim().toLowerCase();

                        if (!selectedStatus) return true; // Exibe todas as linhas se nenhum filtro for aplicado
                        return statusCol === selectedStatus.toLowerCase();
                    });

                    // User type filter
                    $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                        let selectedType = $('#userTypeFilter').val();
                        let userType = $(table.row(dataIndex).node()).data('user-type');

                        if (!selectedType) return true; // Exibe todas as linhas se nenhum filtro for aplicado
                        return String(selectedType) === String(userType);
                    });

                    // Plan filter
                    $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                        let selectedPlan = $('#planFilter').val();
                        let planId = $(table.row(dataIndex).node()).data('plan-id');

                        if (!selectedPlan) return true; // Exibe todas as linhas se nenhum filtro for aplicado
                        return String(selectedPlan) === String(planId);
                    });

                    table.draw(); // Redesenha a tabela
                }


                // Event listeners para filtros
                $('#statusFilter, #userTypeFilter, #planFilter').on('change', function() {
                    $.fn.dataTable.ext.search = []; // Limpa os filtros anteriores
                    applyFilters(); // Aplica os novos filtros
                });
            });


            function deleteConfirm(e) {
                e.preventDefault();
                let userName = $(e.target).closest('tr').find('td:nth-child(3)').text();
                swal({
                    title: "Warning",
                    text: `Are you sure you want to delete this user?`,
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
@endsection
