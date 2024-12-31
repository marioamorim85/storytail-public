@extends('admin.dashboard-layout')

@section('dashboard-content')
    <div class="custom-title">
        {{-- Header Section --}}
        <div class="custom-title">
            <h1>Welcome, Admin!</h1>
            <p>Here's what's happening with your platform today.</p>
        </div>

        {{-- Main Stats Grid --}}
        <div class="stats-grid">
            <div class="stat-card primary">
                <i class="bi bi-people"></i>
                <div class="stat-content">
                    <h3>Users</h3>
                    <p class="stat-number">{{ $totalUsers }}</p>
                </div>
            </div>

            <div class="stat-card successs">
                <i class="bi bi-book"></i>
                <div class="stat-content">
                    <h3>Books</h3>
                    <p class="stat-number">{{ $totalBooks }}</p>
                </div>
            </div>

            <div class="stat-card infoo">
                <i class="bi bi-calendar-event"></i>
                <div class="stat-content">
                    <h3>Activities</h3>
                    <p class="stat-number">{{ $totalActivities }}</p>
                </div>
            </div>

            <div class="stat-card warningg">
                <i class="bi bi-tag"></i>
                <div class="stat-content">
                    <h3>Tags</h3>
                    <p class="stat-number">{{ $totalTags }}</p>
                </div>
            </div>
        </div>

        {{-- Detailed Stats Section --}}
        <div class="detailed-stats">
            {{-- Recent Users --}}
            <div class="detail-card">
                <div class="card-header">
                    <h2><i class="bi bi-person-plus"></i> Recent Users</h2>
                </div>
                <div class="card-body">
                    <ul class="user-list">
                        @foreach($newUsers as $user)
                            <li>
                                <span class="user-avatar">{{ strtoupper(substr($user->first_name, 0, 1)) }}</span>
                                <span class="user-info">
                                    <span class="user-name">{{ $user->first_name }} {{ $user->last_name }}</span>
                                    <span class="user-date">{{ $user->created_at->format('M d, Y') }}</span>
                                </span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            {{-- Popular Books --}}
            <div class="detail-card">
                <div class="card-header">
                    <h2><i class="bi bi-star"></i> Popular Books</h2>
                </div>
                <div class="card-body">
                    <ul class="book-list">
                        @foreach($popularBooks as $book)
                            <li>
                                <span class="book-title-admin">{{ $book->title }}</span>
                                <span class="book-stats">
                                    <i class="bi bi-heart-fill"></i> {{ $book->favorites_count }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            {{-- Popular Authors --}}
            <div class="detail-card">
                <div class="card-header">
                    <h2><i class="bi bi-pen"></i> Popular Authors</h2>
                </div>
                <div class="card-body">
                    <ul class="author-list">
                        @foreach ($popularAuthors as $author)
                            <li>
                                <span class="author-name">{{ $author->name }}</span>
                                <span class="book-count">({{ $author->books_count }} books)</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            {{-- Popular Tags --}}
            <div class="detail-card">
                <div class="card-header">
                    <h2><i class="bi bi-tags"></i> Popular Tags</h2>
                </div>
                <div class="card-body">
                    <div class="tag-cloud">
                        @foreach($popularTags as $tag)
                            <span class="tag">
                                {{ $tag->name }}
                                <small>{{ $tag->books_count }}</small>
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Subscription Stats --}}
            <div class="detail-card subscription-stats">
                <div class="card-header">
                    <h2><i class="bi bi-graph-up"></i> Subscription Stats</h2>
                </div>
                <div class="card-body">
                    <div class="subscription-info">
                        <div class="subscription-total">
                            <span class="number">{{ $activeSubscriptions }}</span>
                            <span class="label">Premium Subscriptions</span>
                        </div>
                        <div class="pending-comments" onclick="window.location.href='{{ route('admin.approvals.comments') }}'">
                            <span class="number">{{ $pendingComments }}</span>
                            <span class="label">Pending Comments</span>
                        </div>
                        <div class="pending-subscriptions" onclick="window.location.href='{{ route('admin.approvals.subscriptions') }}'">
                            <span class="number">{{ $pendingSubscriptions }}</span>
                            <span class="label">Pending Premium</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
