@extends('admin.dashboard-layout')

@section('dashboard-content')
    <h1 class="custom-title">Edit Activity #{{ $activity->id }}</h1>

    <div class="mt-3 col-md-8">
        @if($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <form method="POST" action="{{ route('admin.activities.update', $activity->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-md-6">
                <!-- Title -->
                <div class="mb-3">
                    <label for="title" class="form-label fw-bold">Title</label>
                    <input type="text" class="form-control" id="title" name="title"
                           value="{{ $activity->title }}" required style="height: 47px">
                </div>

                <!-- Description -->
                <div class="mb-3">
                    <label for="description" class="form-label fw-bold">Description</label>
                    <textarea class="form-control" id="description" name="description"
                              required rows="5">{{ $activity->description }}</textarea>
                </div>

                <!-- Associated Book -->
                <div class="mb-3 mt-4">
                    <label for="book_id" class="form-label fw-bold">Associated Book</label>
                    <select class="form-select" id="book_id" name="book_id" required>
                        <option value="">Select Book</option>
                        @foreach($books as $book)
                            <option value="{{ $book->id }}"
                                {{ optional($activity->books->first())->id == $book->id ? 'selected' : '' }}>
                                {{ $book->title }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-6">
                <!-- Status -->
                <div class="mb-3">
                    <label class="form-label d-block fw-bold">Status</label>
                    <div class="form-check form-switch">
                        <input class="form-check-input border-orange" type="checkbox" id="is_active"
                               name="is_active" value="1" {{ $activity->is_active ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">Active</label>
                    </div>
                </div>

                <!-- Current Images -->
                @if($activity->activityImages->count() > 0)
                    <div class="mb-3">
                        <label class="form-label fw-bold">Current Images</label>
                        <div class="d-flex flex-wrap gap-2 mb-2">
                            @foreach($activity->activityImages as $image)
                                <div class="text-center position-relative">
                                    <div class="delete-image"
                                         onclick="removeImage({{ $activity->id }}, {{ $image->id }})">
                                        ×
                                    </div>
                                    <img src="{{ Storage::url($image->image_url) }}"
                                         alt="{{ $image->title }}"
                                         style="width: 70px; height: 100px; object-fit: cover; border-radius: 4px;">
                                    <div class="small mt-1">{{ $image->title }}</div>
                                    <div class="small text-muted">Order: {{ $image->order }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- New Images -->
                <div class="mb-3">
                    <label for="images" class="form-label fw-bold">Add New Images</label>
                    <input type="file" class="form-control" id="images" name="images[]"
                           multiple accept="image/*">
                    <div class="mt-2">
                        <div id="imagePreview" class="d-flex flex-wrap gap-2"></div>
                        <div id="imageTitles"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4 d-flex gap-4">
            <button type="submit" class="btnAdmin">Save Changes</button>
            <a href="{{ route('admin.activities.list') }}" class="btnAdminSecundary">Cancel</a>
        </div>
    </form>

    @push('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
        <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Initialize Choices.js for book select
                new Choices('#book_id', {
                    searchEnabled: true,
                    itemSelectText: '',
                    placeholder: true
                });

                // Image preview and metadata handling
                const imageInput = document.getElementById('images');
                const previewContainer = document.getElementById('imagePreview');
                const titleContainer = document.getElementById('imageTitles');

                imageInput.addEventListener('change', function(event) {
                    previewContainer.innerHTML = '';
                    titleContainer.innerHTML = '';

                    Array.from(event.target.files).forEach((file, index) => {
                        // Image preview
                        const imgContainer = document.createElement('div');
                        imgContainer.className = 'text-center position-relative';

                        const img = document.createElement('img');
                        img.src = URL.createObjectURL(file);
                        img.style.width = '70px';
                        img.style.height = '100px';
                        img.style.objectFit = 'cover';
                        img.className = 'rounded';

                        const pageNumber = document.createElement('div');
                        pageNumber.className = 'small mt-1';
                        pageNumber.textContent = `Image ${index + 1}`;

                        imgContainer.appendChild(img);
                        imgContainer.appendChild(pageNumber);
                        previewContainer.appendChild(imgContainer);

                        // Title and order inputs
                        const metaDiv = document.createElement('div');
                        metaDiv.className = 'mb-3';
                        metaDiv.innerHTML = `
                            <div class="mb-2">
                                <label class="form-label">Title for image ${index + 1}</label>
                                <input type="text" class="form-control" name="image_titles[]" required>
                            </div>
                            <input type="hidden" name="image_orders[]" value="${index + 1}">
                        `;
                        titleContainer.appendChild(metaDiv);
                    });
                });
            });


            function removeImage(activityId, imageId) {
                swal({
                    title: "Warning",
                    text: "Are you sure you want to delete this image?",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                }).then((willDelete) => {
                    if (willDelete) {
                        var form = document.createElement('form');
                        form.method = 'POST';
                        form.action = `/admin/activities/${activityId}/image/${imageId}`;

                        var csrfToken = document.createElement('input');
                        csrfToken.type = 'hidden';
                        csrfToken.name = '_token';
                        csrfToken.value = '{{ csrf_token() }}';
                        form.appendChild(csrfToken);

                        var methodField = document.createElement('input');
                        methodField.type = 'hidden';
                        methodField.name = '_method';
                        methodField.value = 'DELETE';
                        form.appendChild(methodField);

                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            }
        </script>
    @endpush
    @endsection
