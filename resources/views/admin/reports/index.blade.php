@extends('admin.dashboard-layout')

@section('dashboard-content')
    <h1 class="custom-title">Reports</h1>

    <ul class="nav nav-tabs mb-4" id="reportTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active orange-link fw-bold" id="usage-tab" data-bs-toggle="tab" data-bs-target="#usage" type="button" role="tab">Usage Stats</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link orange-link fw-bold" id="top-users-tab" data-bs-toggle="tab" data-bs-target="#top-users" type="button" role="tab">Top Users</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link orange-link fw-bold" id="subscriptions-tab" data-bs-toggle="tab" data-bs-target="#subscriptions" type="button" role="tab">Subscription Stats</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link orange-link fw-bold" id="books-tab" data-bs-toggle="tab" data-bs-target="#books" type="button" role="tab">Popular Books</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link orange-link fw-bold" id="usage-times-tab" data-bs-toggle="tab" data-bs-target="#usage-times" type="button" role="tab">Peak Usage Times</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link orange-link fw-bold" id="favorites-tab" data-bs-toggle="tab" data-bs-target="#favorites" type="button" role="tab">Popular by Favorites</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link orange-link fw-bold" id="tags-tab" data-bs-toggle="tab" data-bs-target="#tags" type="button" role="tab">Tags</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link orange-link fw-bold" id="authors-tab" data-bs-toggle="tab" data-bs-target="#authors" type="button" role="tab">Popular Authors</button>
        </li>
    </ul>

    <div class="tab-content">
        <!-- Usage Stats -->
        <div class="tab-pane fade show active" id="usage" role="tabpanel" aria-labelledby="usage-tab">
            <div class="card border-light shadow-sm">
                <div class="card-body">
                    <h2 class="card-title h4 mb-4 orange-title">Usage Statistics</h2>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="border rounded bg-light p-3 shadow-sm">
                                <h6 class="text-muted mb-2">Total Users</h6>
                                <h3 class="mb-0">{{ number_format($usageStats['totalUsers']) }}</h3>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded bg-light p-3 shadow-sm">
                                <h6 class="text-muted mb-2">Active Users</h6>
                                <h3 class="mb-0">{{ number_format($usageStats['activeUsers']) }}</h3>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded bg-light p-3 shadow-sm">
                                <h6 class="text-muted mb-2">New Users (Last Month)</h6>
                                <h3 class="mb-0">{{ number_format($usageStats['newUsersLastMonth']) }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Users -->
        <div class="tab-pane fade" id="top-users" role="tabpanel" aria-labelledby="top-users-tab">
            <div class="card border-light shadow-sm">
                <div class="card-body">
                    <h2 class="card-title h4 mb-4 orange-title">Top Users Ranking</h2>
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th class="text-end" style="width: 15%">Position</th>
                            <th style="width: 40%">User Name</th>
                            <th class="text-end" style="width: 25%">Total Points</th>
                            <th class="text-end" style="width: 20%">Last Updated</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($topUsers as $index => $user)
                            <tr @if($index < 3) class="fw-bold" @endif>
                                <td class="text-end">
                            <span class="badge @if($user['current_rank'] == 1) bg-warning
                                           @elseif($user['current_rank'] == 2) bg-secondary
                                           @elseif($user['current_rank'] == 3) bg-info
                                           @elseif($user['current_rank'] == 4) bg-primary
                                           @else bg-dark @endif">
                                #{{ $user['current_rank'] }}
                            </span>
                                </td>
                                <td>{{ $user['name'] }}</td>
                                <td class="text-end">{{ number_format($user['total_points']) }}</td>
                                <td class="text-end text-muted">{{ $user['last_updated'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">No ranking data available</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Subscription Stats -->
        <div class="tab-pane fade" id="subscriptions" role="tabpanel" aria-labelledby="subscriptions-tab">
            <div class="card border-light shadow-sm">
                <div class="card-body">
                    <h2 class="card-title h4 mb-4 orange-title">Subscription Statistics</h2>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>Plan</th>
                                <th class="text-center">Total Subscribers</th>
                                <th class="text-center">Active Subscriptions</th>
                                <th class="text-center">Active Rate</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($subscriptionStats as $plan)
                                <tr>
                                    <td>
                                        <span class="badge @if($plan->access_level == \App\Models\Plan::FREE) access-badge free @else access-badge premium @endif">
                                            {{ $plan->name }}
                                        </span>
                                    </td>
                                    <td class="text-center">{{ number_format($plan->total_subscriptions_count) }}</td>
                                    <td class="text-center">{{ number_format($plan->active_subscriptions_count) }}</td>
                                    <td class="text-center">
                                        @if($plan->total_subscriptions_count > 0)
                                            {{ number_format(($plan->active_subscriptions_count / $plan->total_subscriptions_count) * 100, 1) }}%
                                        @else
                                            0%
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Popular Books -->
        <div class="tab-pane fade" id="books" role="tabpanel" aria-labelledby="books-tab">
            <div class="card border-light shadow-sm">
                <div class="card-body">
                    <h2 class="card-title h4 mb-4 orange-title">Most Popular Books</h2>
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Book Title</th>
                            <th class="text-end">Total Clicks</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($popularBooks as $index => $bookClick)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    @if($bookClick->book)
                                        <a href="{{ route('admin.books.show', $bookClick->book->id) }}" class="orange-link">{{ $bookClick->book->title }}</a>
                                    @else
                                        <span class="text-muted">Unknown Book</span>
                                    @endif
                                </td>
                                <td class="text-end">{{ number_format($bookClick->clicks_count) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted">No book click data available</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Peak Usage Times -->
        <div class="tab-pane fade" id="usage-times" role="tabpanel" aria-labelledby="usage-times-tab">
            <div class="card border-light shadow-sm">
                <div class="card-body">
                    <h2 class="card-title h4 mb-4 orange-title">Peak Usage Analysis</h2>
                    @if($peakUsageTimes->isNotEmpty())
                        <div class="alert alert-light border shadow-sm">
                            <i class="fas fa-clock me-2 text-orange"></i>
                            Peak activity time: <strong class="text-orange">{{ sprintf('%02d:00', $peakUsageTimes->first()->hour) }}</strong>
                            with <strong class="text-orange">{{ number_format($peakUsageTimes->first()->clicks_count) }}</strong> clicks
                        </div>
                    @else
                        <div class="alert alert-light border shadow-sm">No usage data available</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Favorites -->
        <div class="tab-pane fade" id="favorites" role="tabpanel" aria-labelledby="favorites-tab">
            <div class="card border-light shadow-sm">
                <div class="card-body">
                    <h2 class="card-title h4 mb-4 orange-title">Most Favorited Books</h2>
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Book Title</th>
                            <th class="text-end">Total Favorites</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($popularBooksByFavorites as $index => $book)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <a href="{{ route('admin.books.show', $book->id) }}" class="orange-link">{{ $book->title }}</a>
                                </td>
                                <td class="text-end">{{ number_format($book->favorites_count) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted">No data available</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Tags -->
        <div class="tab-pane fade" id="tags" role="tabpanel" aria-labelledby="tags-tab">
            <div class="card border-light shadow-sm">
                <div class="card-body">
                    <h2 class="card-title h3 mb-4 orange-title">Tag Statistics</h2>
                    <div class="row">
                        <!-- Total Tags -->
                        <div class="col-12 mb-4">
                            <h3 class="orange-title mb-1">Total Tags</h3>
                            <h4 class="text-dark mb-0">{{ number_format($totalTags) }}</h4>
                        </div>

                        <!-- Most Popular Tags -->
                        <div class="col-12">
                            <h3 class="orange-title mb-1">Most Popular Tags</h3>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th>Rank</th>
                                        <th>Tag Name</th>
                                        <th class="text-end">Total Books</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($popularTags as $index => $tag)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td class="text-dark">{{ $tag->name }}</td>
                                            <td class="text-end">{{ number_format($tag->books_count) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted">No tags available</td>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Authors -->
        <div class="tab-pane fade" id="authors" role="tabpanel" aria-labelledby="authors-tab">
            <div class="card border-light shadow-sm">
                <div class="card-body">
                    <h2 class="card-title h4 mb-4 orange-title">Most Popular Authors</h2>
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Author Name</th>
                            <th class="text-end">Total Books</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($popularAuthors as $index => $author)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <a href="{{ route('admin.authors.show', $author->id) }}" class="orange-link">{{ $author->name }}</a>
                                </td>
                                <td class="text-end">{{ number_format($author->books_count) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted">No author data available</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const triggerTabList = [].slice.call(document.querySelectorAll('#reportTabs button'));
                triggerTabList.forEach(function (triggerEl) {
                    const tabTrigger = new bootstrap.Tab(triggerEl);
                    triggerEl.addEventListener('click', function (event) {
                        event.preventDefault();
                        tabTrigger.show();
                    });
                });
            });
        </script>
    @endpush
@endsection
