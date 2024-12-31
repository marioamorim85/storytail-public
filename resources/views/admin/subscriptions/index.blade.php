@extends('admin.dashboard-layout')

@section('dashboard-content')
    <h1 class="custom-title">Manage Subscriptions</h1>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex justify-content-end align-items-center gap-3 ms-auto">
            <select class="form-select" id="statusFilter" name="statusFilter">
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="expired">Expired</option>
                <option value="canceled">Canceled</option>
            </select>
        </div>
    </div>

    <table id="subscriptions-table" class="table table-striped">
        <thead>
        <tr>
            <th>ID</th>
            <th>User</th>
            <th>Plan</th>
            <th>Status</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Pending Requests to Premium</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($subscriptions as $subscription)
            <tr>
                <td>{{ $subscription->id }}</td>
                <td>
                    <a href="{{ route('admin.users.show', $subscription->user->id) }}" class="orange-link">
                        {{ $subscription->user->first_name }} {{ $subscription->user->last_name }}
                    </a>
                    <small class="text-muted d-block">{{ $subscription->user->email }}</small>
                </td>
                <td>
                    <span class="access-badge {{ strtolower($subscription->plan->name) }}">
                        {{ $subscription->plan->name }}
                    </span>
                </td>
                <td>
                    <span class="status-badge {{ strtolower($subscription->status) }}">
                        {{ ucfirst($subscription->status) }}
                    </span>
                </td>
                <td>{{ $subscription->start_date ? $subscription->start_date->format('Y-m-d') : '-' }}</td>
                <td>{{ $subscription->end_date ? $subscription->end_date->format('Y-m-d') : 'Ongoing' }}</td>
                <td>
                    @php
                        $pendingRequest = $subscription->approvals()
                            ->where('status', 'pending')
                            ->first();
                    @endphp
                    @if ($pendingRequest)
                        <span class="status-badge pending">Pending</span>
                    @else
                        <span class="status-badge no_pending">No Pending</span>
                    @endif
                </td>
                <td>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-secondary2 dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            Actions
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="{{ route('admin.users.subscriptions.show', $subscription->id) }}" class="dropdown-item">
                                    <i class="bi bi-eye"></i> View
                                </a>
                            </li>
                            @if ($pendingRequest)
                                <li>
                                    <form method="POST" action="{{ route('admin.users.subscriptions.moderate', $pendingRequest->id) }}">
                                        @csrf
                                        <input type="hidden" name="action" value="approve">
                                        <button type="submit" class="dropdown-item text-success">
                                            <i class="bi bi-check-lg"></i> Approve
                                        </button>
                                    </form>
                                </li>
                                <li>
                                    <form method="POST" action="{{ route('admin.users.subscriptions.moderate', $pendingRequest->id) }}">
                                        @csrf
                                        <input type="hidden" name="action" value="reject">
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="bi bi-x-lg"></i> Reject
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

                const table = $('#subscriptions-table').DataTable({
                    responsive: true,
                    order: [[0, 'asc']],
                });

                function applyFilters() {
                    $.fn.dataTable.ext.search = [];

                    const selectedStatus = $('#statusFilter').val();

                    if (selectedStatus) {
                        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                            const status = data[3].trim().toLowerCase();
                            return status === selectedStatus.toLowerCase();
                        });
                    }

                    table.draw();
                }

                $('#statusFilter').on('change', function() {
                    applyFilters();
                });
            });
        </script>
    @endpush
@endsection
