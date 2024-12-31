@extends('admin.dashboard-layout')

@section('dashboard-content')
    <h1 class="custom-title">View Book #{{ $book->id }}</h1>

    <div class="row">
        <div class="col-md-6">
            <!-- Title -->
            <div class="mb-4">
                <label class="form-label fw-bold">Title</label>
                <div class="p-2 bg-white rounded border">{{ $book->title }}</div>
            </div>

            <!-- Description -->
            <div class="mb-4">
                <label class="form-label fw-bold">Description</label>
                <div class="p-2 bg-white rounded border" style="min-height: 120px;">
                    {{ $book->description }}
                </div>
            </div>

            <!-- Read Time -->
            <div class="mb-4">
                <label class="form-label fw-bold">Read Time (minutes)</label>
                <div class="p-2 bg-white rounded border">{{ $book->read_time }}</div>
            </div>

            <!-- Authors -->
            <div class="mb-4">
                <label class="form-label fw-bold">Authors</label>
                <div class="p-2 bg-white rounded border">
                    @foreach($book->authors as $author)
                        <span class="badge bg-secondary me-2">
                            {{ $author->first_name }} {{ $author->last_name }}
                        </span>
                    @endforeach
                </div>
            </div>

            <!-- Age Group -->
            <div class="mb-4">
                <label class="form-label fw-bold">Age Group</label>
                <div class="p-2 bg-white rounded border">{{ $book->ageGroup->age_group }}</div>
            </div>

            <!-- Access Level -->
            <div class="mb-4">
                <label class="form-label fw-bold">Access Level</label>
                <div class="p-2 bg-white rounded border">
                    <span class="access-badge {{ $book->access_level == 2 ? 'premium' : 'free' }}">
                        {{ $book->access_level == 2 ? 'Premium' : 'Free' }}
                    </span>
                </div>
            </div>

        </div>

        <div class="col-md-6">
            <!-- Tags -->
            <div class="mb-4">
                <label class="form-label fw-bold">Tags</label>
                <div class="p-2 bg-white rounded border">
                    @foreach($book->tags as $tag)
                        <span class="badge bg-secondary me-2">{{ $tag->name }}</span>
                    @endforeach
                </div>
            </div>

            <!-- Status -->
            <div class="mb-4">
                <label class="form-label fw-bold">Status</label>
                <div class="p-2 bg-white rounded border">
                    <span class="status-badge {{ $book->is_active ? 'active' : 'inactive' }}">
                        {{ $book->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
            </div>

            <!-- Cover Image -->
            <div class="mb-4">
                <label class="form-label fw-bold">Cover Image</label>
                <div class="p-2 bg-white rounded border">
                    @if($book->cover_url)
                        <img src="{{ Storage::url($book->cover_url) }}"
                             alt="Cover"
                             class="rounded"
                             style="width: 70px; height: 100px; object-fit: cover;">
                    @endif
                </div>
            </div>

            <!-- Book Pages -->
            <div class="mb-4">
                <label class="form-label fw-bold">Book Pages</label>
                <div class="p-2 bg-white rounded border">
                    <div class="d-flex flex-wrap gap-4">
                        @foreach($book->pages()->orderBy('page_index')->get() as $page)
                            <div class="text-center">
                                <img src="{{ Storage::url($page->page_image_url) }}"
                                     alt="Page {{ $page->page_index }}"
                                     class="rounded mb-2"
                                     style="width: 70px; height: 100px; object-fit: cover;">
                                <div class="small text-muted">Page {{ $page->page_index }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Video -->
            @if($book->video)
                <div class="mb-4">
                    <label class="form-label fw-bold">Video</label>
                    <div class="p-3 bg-white rounded border">
                        <div id="video-preview" class="mt-2 position-relative" style="width: 300px; height: 169px;">
                            <iframe width="100%" height="100%"
                                    src="https://www.youtube.com/embed/{{ $book->getYoutubeId($book->video->video_url) }}"
                                    frameborder="0" allowfullscreen>
                            </iframe>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Associated Activities -->
            <div class="mb-4">
                <label class="form-label fw-bold">Associated Activities ({{ $book->activities->count() }})</label>
                <div class="p-2 bg-white rounded border">
                    @if($book->activities->count() > 0)
                        <div class="list-group">
                            @foreach($book->activities as $activity)
                                <div class="list-group-item border-0 mb-2">
                                    <div class="d-flex align-items-center gap-3">
                                        <!-- Activity Images Preview -->
                                        <div class="d-flex gap-1">
                                            @if($activity->activityImages->count() > 0)
                                                @foreach($activity->activityImages->take(2) as $image)
                                                    <img src="{{ Storage::url($image->image_url) }}"
                                                         alt="{{ $image->title }}"
                                                         class="rounded"
                                                         style="width: 35px; height: 50px; object-fit: cover;">
                                                @endforeach
                                                @if($activity->activityImages->count() > 2)
                                                    <span class="badge bg-secondary" style="height: 20px;">
                                            +{{ $activity->activityImages->count() - 2 }}
                                        </span>
                                                @endif
                                            @else
                                                <img src="{{ asset('images/no-image.png') }}"
                                                     alt="No Image"
                                                     class="rounded"
                                                     style="width: 35px; height: 50px; object-fit: cover;">
                                            @endif
                                        </div>

                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">{{ $activity->title }}</h6>
                                            <p class="mb-0 text-muted small">{{ Str::limit($activity->description, 100) }}</p>
                                        </div>

                                        <!-- Status Badge and View Button side by side -->
                                        <div class="d-flex align-items-center gap-2">
                                <span class="status-badge {{ $activity->is_active ? 'active' : 'inactive' }}">
                                    {{ $activity->is_active ? 'Active' : 'Inactive' }}
                                </span>
                                            <a href="{{ route('admin.activities.show', $activity->id) }}"
                                               class="btn btn-sm btn-outline-secondary small-btn">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-muted">No activities associated with this book.</div>
                    @endif
                </div>
            </div>

        </div>
    </div>

    <div class="mt-4">
        <a href="{{ route('admin.books.list') }}" class="btnAdminSecundary">Back to List</a>
    </div>
@endsection
