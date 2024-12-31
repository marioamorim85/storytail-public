@extends('admin.dashboard-layout')

@section('dashboard-content')
    <h1 class="custom-title">Edit Author #{{ $author->id }}</h1>

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

    <form method="POST" action="{{ route('admin.authors.update', $author->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-md-6">
                <!-- First Name -->
                <div class="mb-3">
                    <label for="first_name" class="form-label fw-bold">First Name</label>
                    <input type="text" class="form-control" id="first_name" name="first_name"
                           value="{{ $author->first_name }}" required style="height: 47px">
                </div>

                <!-- Last Name -->
                <div class="mb-3">
                    <label for="last_name" class="form-label fw-bold">Last Name</label>
                    <input type="text" class="form-control" id="last_name" name="last_name"
                           value="{{ $author->last_name }}" required style="height: 47px">
                </div>

                <!-- Description/Biography -->
                <div class="mb-3">
                    <label for="description" class="form-label fw-bold">Description/Biography</label>
                    <textarea class="form-control" id="description" name="description"
                              rows="5">{{ $author->description }}</textarea>
                </div>

                <!-- Nationality -->
                <div class="mb-3">
                    <label for="nationality" class="form-label fw-bold">Nationality</label>
                    <input type="text" class="form-control" id="nationality" name="nationality"
                           value="{{ $author->nationality }}" style="height: 47px">
                </div>
            </div>

            <div class="col-md-6">
                <!-- Current Photo -->
                @if($author->author_photo_url)
                    <div class="mb-3">
                        <label class="form-label fw-bold">Current Photo</label>
                        <div class="position-relative d-inline-block">
                            <div class="delete-image"
                                 onclick="removePhoto({{ $author->id }})">
                                ×
                            </div>
                            <img src="{{ Storage::url($author->author_photo_url) }}"
                                 alt="{{ $author->name }}"
                                 class="rounded"
                                 style="width: 150px; height: 150px; object-fit: cover;">
                        </div>
                    </div>
                @endif

                <!-- New Photo -->
                <div class="mb-3">
                    <label for="author_photo_url" class="form-label fw-bold">
                        {{ $author->author_photo_url ? 'Update Photo' : 'Add Photo' }}
                    </label>
                    <input type="file" class="form-control" id="author_photo_url"
                           name="author_photo_url" accept="image/*">
                    <div class="mt-2">
                        <div id="photoPreview" class="d-flex justify-content-center"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4 d-flex gap-4">
            <button type="submit" class="btnAdmin">Save Changes</button>
            <a href="{{ route('admin.authors.list') }}" class="btnAdminSecundary">Cancel</a>
        </div>
    </form>

    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    @endpush

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const photoInput = document.getElementById('author_photo_url');
                const photoPreview = document.getElementById('photoPreview');

                photoInput.addEventListener('change', function(e) {
                    photoPreview.innerHTML = '';
                    const file = e.target.files[0];

                    if (file) {
                        const img = document.createElement('img');
                        img.src = URL.createObjectURL(file);
                        img.style.width = '150px';
                        img.style.height = '150px';
                        img.style.objectFit = 'cover';
                        img.className = 'rounded mt-2';
                        photoPreview.appendChild(img);
                    }
                });
            });

            function removePhoto(authorId) {
                swal({
                    title: "Warning",
                    text: "Are you sure you want to delete this photo?",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                }).then((willDelete) => {
                    if (willDelete) {
                        var form = document.createElement('form');
                        form.method = 'POST';
                        form.action = `{{ route('admin.authors.removePhoto', ':id') }}`.replace(':id', authorId);

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
