@extends('admin.dashboard-layout')

@section('dashboard-content')
    <h1 class="custom-title">View Activity #{{ $activity->id }}</h1>

    <div class="row">
        <div class="col-md-6">
            <!-- Title -->
            <div class="mb-4">
                <label class="form-label fw-bold">Title</label>
                <div class="p-2 bg-white rounded border">{{ $activity->title }}</div>
            </div>

            <!-- Description -->
            <div class="mb-4">
                <label class="form-label fw-bold">Description</label>
                <div class="p-2 bg-white rounded border" style="min-height: 120px;">
                    {{ $activity->description }}
                </div>
            </div>

            <!-- Associated Book -->
            <div class="mb-4">
                <label class="form-label fw-bold">Associated Book</label>
                <div class="p-2 bg-white rounded border">
                    @if($activity->books->isNotEmpty())
                        <div class="list-group">
                            @foreach($activity->books as $book)
                                <div class="list-group-item border-0 mb-2">
                                    <div class="d-flex align-items-center gap-3">
                                        <!-- Book Cover -->
                                        @if($book->cover_url)
                                            <img src="{{ Storage::url($book->cover_url) }}"
                                                 alt="{{ $book->title }}"
                                                 class="rounded"
                                                 style="width: 50px; height: 70px; object-fit: cover;">
                                        @else
                                            <img src="{{ asset('images/no-cover.png') }}"
                                                 alt="No Cover"
                                                 class="rounded"
                                                 style="width: 50px; height: 70px; object-fit: cover;">
                                        @endif

                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">{{ $book->title }}</h6>
                                            <div class="d-flex flex-wrap gap-2">
                                                <!-- Age Group -->
                                                <span class="badge bg-secondary">
                                        {{ $book->ageGroup->age_group }}
                                    </span>

                                                <!-- Access Level -->
                                                <span class="access-badge {{ $book->access_level == 2 ? 'premium' : 'free' }}">
                                        {{ $book->access_level == 2 ? 'Premium' : 'Free' }}
                                    </span>

                                                <!-- Status -->
                                                <span class="status-badge {{ $book->is_active ? 'active' : 'inactive' }}">
                                        {{ $book->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                            </div>
                                        </div>

                                        <!-- View Book Link -->
                                        <div>
                                            <a href="{{ route('admin.books.show', $book->id) }}"
                                               class="btn btn-sm btn-outline-secondary small-btn">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-muted">No book assigned.</div>
                    @endif
                </div>
            </div>

            <!-- Status -->
            <div class="mb-4">
                <label class="form-label fw-bold">Status</label>
                <div class="p-2 bg-white rounded border">
                    <span class="status-badge {{ $activity->is_active ? 'active' : 'inactive' }}">
                        {{ $activity->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <!-- Activity Images -->
            <div class="mb-4">
                <label class="form-label fw-bold">Activity Images</label>
                <div class="p-2 bg-white rounded border">
                    <div class="d-flex flex-wrap gap-4">
                        @if($activity->activityImages->isNotEmpty())
                            @foreach($activity->activityImages->sortBy('order') as $image)
                                <div class="text-center">
                                    <img src="{{ Storage::url($image->image_url) }}"
                                         alt="{{ $image->title }}"
                                         class="rounded mb-2"
                                         style="width: 70px; height: 100px; object-fit: cover;">
                                    <div class="small text-muted">{{ $image->title }}</div>
                                    <div class="small text-muted">Order: {{ $image->order }}</div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center w-100">
                                <img src="{{ asset('images/no-image.png') }}"
                                     alt="No Image"
                                     class="rounded"
                                     style="width: 70px; height: 100px; object-fit: cover;">
                                <div class="small text-muted mt-2">No images available</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Created At -->
            <div class="mb-4">
                <label class="form-label fw-bold">Created At</label>
                <div class="p-2 bg-white rounded border">
                    {{ $activity->created_at->format('F j, Y H:i') }}
                </div>
            </div>

            <!-- Updated At -->
            <div class="mb-4">
                <label class="form-label fw-bold">Last Updated</label>
                <div class="p-2 bg-white rounded border">
                    {{ $activity->updated_at->format('F j, Y H:i') }}
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <a href="{{ route('admin.activities.list') }}" class="btnAdminSecundary">Back to List</a>
    </div>
@endsection
