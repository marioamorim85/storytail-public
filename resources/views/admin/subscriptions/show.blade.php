@extends('admin.dashboard-layout')

@section('dashboard-content')
    <h1 class="custom-title">View Subscription #{{ $subscription->id }}</h1>

    <div class="row">
        <div class="col-md-6">
            <!-- User Information -->
            <div class="mb-4">
                <label class="form-label fw-bold">User</label>
                <div class="p-2 bg-white rounded border">
                    <div class="d-flex flex-column">
                        <a href="{{ route('admin.users.show', $subscription->user_id) }}" class="orange-link">
                            {{ $subscription->user->getFullName() }}
                        </a>
                        <small class="text-muted">{{ $subscription->user->email }}</small>
                    </div>
                </div>
            </div>

            <!-- Current Plan -->
            <div class="mb-4">
                <label class="form-label fw-bold">Current Plan</label>
                <div class="p-2 bg-white rounded border">
                    <span class="access-badge {{ strtolower($subscription->plan->name) }}">
                        {{ $subscription->plan->name }}
                    </span>
                </div>
            </div>

            <!-- Status -->
            <div class="mb-4">
                <label class="form-label fw-bold">Status</label>
                <div class="p-2 bg-white rounded border">
                    <span class="status-badge {{ strtolower($subscription->status) }}">
                        {{ ucfirst($subscription->status) }}
                    </span>
                </div>
            </div>

            <!-- Date Information -->
            <div class="mb-4">
                <label class="form-label fw-bold">Duration</label>
                <div class="p-2 bg-white rounded border">
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Start Date:</strong><br>
                            {{ $subscription->start_date ? $subscription->start_date->format('F j, Y') : '-' }}
                        </div>
                        <div class="col-md-6">
                            <strong>End Date:</strong><br>
                            {{ $subscription->end_date ? $subscription->end_date->format('F j, Y') : 'Ongoing' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <!-- Pending Premium Request -->
            <div class="mb-4">
                <label class="form-label fw-bold">Premium Request Status</label>
                <div class="p-2 bg-white rounded border">
                    @if($subscription->approvals()->where('status', 'pending')->exists())
                        <span class="status-badge pending">Premium Request Pending</span>
                    @else
                        <span class="status-badge no_pending">No Pending</span>
                    @endif
                </div>
            </div>

            <!-- Approval History -->
            <div class="mb-4">
                <label class="form-label fw-bold">Approval History</label>
                <div class="p-2 bg-white rounded border">
                    @if($subscription->approvals->isNotEmpty())
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>By</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($subscription->approvals()->latest()->get() as $approval)
                                    <tr>
                                        <td>{{ $approval->created_at->format('Y-m-d H:i') }}</td>
                                        <td>
                                                <span class="status-badge {{ strtolower($approval->status) }}">
                                                    {{ ucfirst($approval->status) }}
                                                </span>
                                        </td>
                                        <td>{{ $approval->admin?->getFullName() ?? 'System' }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <span class="text-muted">No approval history available</span>
                    @endif
                </div>
            </div>

            <!-- Created/Updated Info -->
            <div class="mb-4">
                <label class="form-label fw-bold">System Information</label>
                <div class="p-2 bg-white rounded border">
                    <div><strong>Created At:</strong> {{ $subscription->created_at->format('F j, Y H:i') }}</div>
                    <div><strong>Last Updated:</strong> {{ $subscription->updated_at->format('F j, Y H:i') }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <a href="{{ route('admin.users.subscriptions.list') }}" class="btnAdminSecundary">Back to List</a>
    </div>
@endsection
