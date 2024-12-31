@extends('admin.dashboard-layout')

@section('dashboard-content')
    <h1 class="custom-title">Approval History</h1>

    <!-- Tabs -->
    <ul class="nav nav-tabs mb-5" id="approvalHistoryTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active orange-link fw-bold" id="comments-tab" data-bs-toggle="tab" data-bs-target="#comments" type="button" role="tab">
                Comments Moderation
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link orange-link fw-bold" id="subscriptions-tab" data-bs-toggle="tab" data-bs-target="#subscriptions" type="button" role="tab">
                Subscription Approvals
            </button>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content" id="approvalHistoryTabsContent">
        <!-- Comments Moderation Tab -->
        <div class="tab-pane fade show active" id="comments" role="tabpanel" aria-labelledby="comments-tab">
            <h3 class="custom-title-h3">Comments Moderation History</h3>
        @if($commentHistory->isEmpty())
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> No comment moderation history found.
                </div>
            @else
                <table id="comments-history-table" class="table table-striped">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Book</th>
                        <th>Comment</th>
                        <th>Status</th>
                        <th>Moderator</th>
                        <th>Date</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($commentHistory as $moderation)
                        <tr>
                            <td>{{ $moderation->comment->id }}</td>
                            <td>
                                <div class="d-flex flex-column">
                                    <a href="{{ route('admin.users.show', $moderation->comment->user_id) }}" class="orange-link">
                                        {{ $moderation->comment->user->getFullName() }}
                                    </a>
                                    <small class="text-muted">{{ $moderation->comment->user->email }}</small>
                                </div>
                            </td>
                            <td>
                                <a href="{{ route('admin.books.show', $moderation->comment->book_id) }}" class="orange-link">
                                    {{ $moderation->comment->book->title }}
                                </a>
                            </td>
                            <td style="max-width: 300px;">
                                <div class="text-wrap">{{ $moderation->comment->comment_text }}</div>
                            </td>
                            <td>
                                <span class="status-badge {{ strtolower($moderation->status) }}">
                                    {{ ucfirst($moderation->status) }}
                                </span>
                            </td>
                            <td>{{ $moderation->user->getFullName() }}</td>
                            <td>{{ \Carbon\Carbon::parse($moderation->moderation_date)->format('Y-m-d H:i:s') }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <!-- Subscription Approvals Tab -->
        <div class="tab-pane fade" id="subscriptions" role="tabpanel" aria-labelledby="subscriptions-tab">
            <h3 class="custom-title-h3">Subscription Approval History</h3>
            @if($subscriptionHistory->isEmpty())
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> No subscription approval history found.
                </div>
            @else
                <table id="subscriptions-history-table" class="table table-striped">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Plan</th>
                        <th>Status</th>
                        <th>By</th>
                        <th>Date</th>
                        <th>Notes</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($subscriptionHistory as $approval)
                        <tr>
                            <td>{{ $approval->id }}</td>
                            <td>
                                <div class="d-flex flex-column">
                                    <a href="{{ route('admin.users.show', $approval->subscription->user_id) }}" class="orange-link">
                                        {{ $approval->subscription->user->getFullName() }}
                                    </a>
                                    <small class="text-muted">{{ $approval->subscription->user->email }}</small>
                                </div>
                            </td>
                            <td>
                                @if ($approval->status === 'pending' || $approval->status === 'resolved')
                                    <div class="d-flex align-items-center">
                                        <span class="access-badge free">Free</span>
                                        <span class="mx-2">→</span>
                                        <span class="access-badge premium">Premium</span>
                                    </div>
                                @else
                                    <span class="access-badge {{ strtolower($approval->plan_name) }}">
                                        {{ $approval->plan_name }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                <span class="status-badge {{ strtolower($approval->status) }}">
                                    {{ ucfirst($approval->status) }}
                                </span>
                            </td>
                            <td>
                                @if($approval->admin)
                                    {{ $approval->admin->getFullName() }}
                                @else
                                    <span class="text-muted">System</span>
                                @endif
                            </td>
                            <td>{{ $approval->created_at->format('Y-m-d H:i:s') }}</td>
                            <td>{{ $approval->notes ?: '-' }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                // Configuração genérica para tabelas com DataTables
                function configureTable(tableId, orderColumn) {
                    const tableElement = $(`#${tableId}`);
                    if (tableElement.length) {
                        const totalRows = tableElement.find('tbody tr').length;
                        tableElement.DataTable({
                            lengthMenu: [[5, 10, totalRows], [5, 10, "All"]],
                            pageLength: 10, // Padrão de 10 linhas por página
                            order: [[orderColumn, 'desc']], // Ordenação pela coluna especificada
                            responsive: true,
                            autoWidth: false, // Evitar tabelas desalinhadas
                        });
                    }
                }

                // Configuração para comments-history-table
                configureTable('comments-history-table', 6);

                // Configuração para subscriptions-history-table
                configureTable('subscriptions-history-table', 5);
            });
        </script>
    @endpush
@endsection

