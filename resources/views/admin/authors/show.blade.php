@extends('admin.dashboard-layout')

@section('dashboard-content')
    <h1 class="custom-title">View Author #{{ $author->id }}</h1>

    <div class="row">
        <div class="col-md-6">
            <!-- Author Name -->
            <div class="mb-4">
                <label class="form-label fw-bold">Author Name</label>
                <div class="p-2 bg-white rounded border">{{ $author->first_name }} {{ $author->last_name }}</div>
            </div>

            <!-- Description/Biography -->
            <div class="mb-4">
                <label class="form-label fw-bold">Description/Biography</label>
                <div class="p-2 bg-white rounded border" style="min-height: 120px;">
                    {{ $author->description ?? 'No description available.' }}
                </div>
            </div>

            <!-- Nationality -->
            <div class="mb-4">
                <label class="form-label fw-bold">Nationality</label>
                <div class="p-2 bg-white rounded border">
                    {{ $author->nationality ?? 'Not specified' }}
                </div>
            </div>

            <!-- Created At -->
            <div class="mb-4">
                <label class="form-label fw-bold">Registered Since</label>
                <div class="p-2 bg-white rounded border">
                    {{ $author->created_at->format('F j, Y') }}
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <!-- Author Photo -->
            <div class="mb-4">
                <label class="form-label fw-bold">Author Photo</label>
                <div class="p-2 bg-white rounded border">
                    @if($author->author_photo_url)
                        <img src="{{ Storage::url($author->author_photo_url) }}"
                             alt="{{ $author->name }}"
                             class="rounded"
                             style="width: 150px; height: 150px; object-fit: cover;">
                    @else
                        <img src="{{ asset('images/no-photo.png') }}"
                             alt="No Photo"
                             class="rounded"
                             style="width: 150px; height: 150px; object-fit: cover;">
                    @endif
                </div>
            </div>

            <!-- Associated Books -->
            <div class="mb-4">
                <label class="form-label fw-bold">Associated Books ({{ $author->books->count() }})</label>
                <div class="p-2 bg-white rounded border">
                    @if($author->books->count() > 0)
                        <div class="list-group">
                            @foreach($author->books as $book)
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
                        <div class="text-muted">No books associated with this author.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <a href="{{ route('admin.authors.list') }}" class="btnAdminSecundary">Back to List</a>
    </div>
@endsection
