@extends('admin.dashboard-layout')

@section('dashboard-content')
    <h1 class="custom-title">Subscription Approvals</h1>

    @if($subscriptions->isEmpty())
        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i> No subscriptions pending approval.
        </div>
    @else
        <table id="subscriptions-table" class="table table-striped">
            <thead>
            <tr>
                <th>ID</th>
                <th>User</th>
                <th>Plan</th>
                <th>Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach($subscriptions as $approval)
                <tr>
                    <td>{{ $approval->id }}</td>
                    <td>
                        <div class="d-flex flex-column">
                            <a href="{{ route('admin.users.show', $approval->subscription->user->id) }}" class="orange-link">
                                {{ $approval->subscription->user->getFullName() }}
                            </a>
                            <small class="text-muted">{{ $approval->subscription->user->email }}</small>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <span class="access-badge {{ $approval->status === 'pending' || $approval->status === 'resolved' ? 'free' : strtolower($approval->plan_name) }}">
                                {{ $approval->status === 'pending' || $approval->status === 'resolved' ? 'Free' : $approval->plan_name }}
                            </span>
                            @if($approval->status === 'pending' || $approval->status === 'resolved')
                                <span class="mx-2">→</span>
                                <span class="access-badge premium">Premium</span>
                            @endif
                        </div>
                    </td>
                    <td>{{ $approval->created_at->format('Y-m-d H:i:s') }}</td>
                    <td>
                        <span class="status-badge {{ strtolower($approval->status) }}">
                            {{ ucfirst($approval->status) }}
                        </span>
                    </td>
                    <td>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-secondary2 dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                Actions
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <form method="POST" action="{{ route('admin.approvals.subscriptions.update', $approval->id) }}">
                                        @csrf
                                        <input type="hidden" name="action" value="approve">
                                        <button type="submit" class="dropdown-item text-success">
                                            <i class="bi bi-check-lg"></i> Approve
                                        </button>
                                    </form>
                                </li>
                                <li>
                                    <form method="POST" action="{{ route('admin.approvals.subscriptions.update', $approval->id) }}">
                                        @csrf
                                        <input type="hidden" name="action" value="reject">
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="bi bi-x-lg"></i> Reject
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
                    const totalRows = $('#subscriptions-table tbody tr').length;
                    $('#subscriptions-table').DataTable({
                        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
                        pageLength: totalRows,
                        order: [[0, 'asc']],
                        responsive: true
                    });
                });
            </script>
        @endpush
    @endif
@endsection
