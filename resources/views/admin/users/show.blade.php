@extends('admin.dashboard-layout')

@section('dashboard-content')
    <h1 class="custom-title">View User #{{ $user->id }}</h1>

    <div class="row">
        <div class="col-md-6">
            <!-- Name -->
            <div class="mb-4">
                <label class="form-label fw-bold">Full Name</label>
                <div class="p-2 bg-white rounded border">{{ $user->getFullName() }}</div>
            </div>

            <!-- Birth Date -->
            <div class="mb-4">
                <label class="form-label fw-bold">Date of Birth</label>
                <div class="p-2 bg-white rounded border">
                    {{ $user->birth_date ? \Carbon\Carbon::parse($user->birth_date)->format('Y-m-d') : 'Not provided' }}
                </div>
            </div>

            <!-- Email -->
            <div class="mb-4">
                <label class="form-label fw-bold">Email</label>
                <div class="p-2 bg-white rounded border">{{ $user->email }}</div>
            </div>

            <!-- Status -->
            <div class="mb-4">
                <label class="form-label fw-bold">Status</label>
                <div class="p-2 bg-white rounded border">
                    <span class="status-badge {{ strtolower($user->status) }}">
                        {{ ucfirst($user->status) }}
                    </span>
                </div>
            </div>

            <!-- User Type -->
            <div class="mb-4">
                <label class="form-label fw-bold">User Type</label>
                <div class="p-2 bg-white rounded border">
                    <span class="badge-type {{ strtolower($user->userType->user_type) }}">
                        {{ $user->userType->user_type }}
                    </span>
                </div>
            </div>


        </div>

        <div class="col-md-6">

            <!-- Photo -->
            <div class="mb-4">
                <label class="form-label fw-bold">Profile Photo</label>
                <div class="p-2 bg-white rounded border">
                    @if($user->user_photo_url)
                        <img src="{{ Storage::url($user->user_photo_url) }}"
                             alt="Profile Photo"
                             class="rounded"
                             style="width: 100px; height: 100px; object-fit: cover;">
                    @else
                        <img src="{{ asset('images/no-photo.png') }}"
                             alt="No Photo"
                             class="rounded"
                             style="width: 100px; height: 100px; object-fit: cover;">
                    @endif
                </div>
            </div>

            <!-- Associated Plan -->
            <div class="mb-4">
                <label class="form-label fw-bold">Subscription Plan</label>
                <div class="p-2 bg-white rounded border">
                    @if($user->subscription)
                        <span class="access-badge {{ $user->subscription->plan->access_level == 2 ? 'premium' : 'free' }}">
                {{ $user->subscription->plan->name }}
            </span>
                        <p class="text-muted small mt-2">
                            Start Date:
                            {{ $user->subscription->start_date instanceof \Carbon\Carbon
                                ? $user->subscription->start_date->format('Y-m-d')
                                : \Carbon\Carbon::parse($user->subscription->start_date)->format('Y-m-d') }}<br>
                            End Date:
                            {{ $user->subscription->end_date
                                ? ($user->subscription->end_date instanceof \Carbon\Carbon
                                    ? $user->subscription->end_date->format('Y-m-d')
                                    : \Carbon\Carbon::parse($user->subscription->end_date)->format('Y-m-d'))
                                : 'Ongoing' }}
                        </p>
                    @else
                        <span class="text-muted">No active plan</span>
                    @endif
                </div>
            </div>


            <!-- Comments -->
            <div class="mb-4">
                <label class="form-label fw-bold">Comments ({{ $user->comments->count() }})</label>
                <div class="p-2 bg-white rounded border">
                    @if($user->comments->count() > 0)
                        <ul class="list-group">
                            @foreach($user->comments as $comment)
                                <li class="list-group-item border-0">
                                    <p class="mb-1">{{ $comment->comment_text }}</p>
                                    <small class="text-muted">Book:
                                        <a href="{{ route('admin.books.show', $comment->book_id) }}" class="orange-link">
                                            {{ $comment->book->title }}
                                        </a>
                                    </small>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <span class="text-muted">No comments from this user.</span>
                    @endif
                </div>
            </div>

            <!-- Registered Date -->
            <div class="mb-4">
                <label class="form-label fw-bold">Registered At</label>
                <div class="p-2 bg-white rounded border">{{ $user->created_at->format('Y-m-d H:i:s') }}</div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <a href="{{ route('admin.users.list') }}" class="btnAdminSecundary">Back to List</a>
    </div>
@endsection
