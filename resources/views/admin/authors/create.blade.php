@extends('admin.dashboard-layout')

@section('dashboard-content')
    <h1 class="custom-title">Add New Author</h1>

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

    <form method="POST" action="{{ route('admin.authors.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-md-6">
                <!-- First Name -->
                <div class="mb-3">
                    <label for="first_name" class="form-label fw-bold">First Name</label>
                    <input type="text" class="form-control" id="first_name" name="first_name"
                           value="{{ old('first_name') }}" required style="height: 47px">
                </div>

                <!-- Last Name -->
                <div class="mb-3">
                    <label for="last_name" class="form-label fw-bold">Last Name</label>
                    <input type="text" class="form-control" id="last_name" name="last_name"
                           value="{{ old('last_name') }}" required style="height: 47px">
                </div>

                <!-- Description/Biography -->
                <div class="mb-3">
                    <label for="description" class="form-label fw-bold">Description/Biography</label>
                    <textarea class="form-control" id="description" name="description"
                              rows="5">{{ old('description') }}</textarea>
                </div>
            </div>

            <div class="col-md-6">
                <!-- Nationality -->
                <div class="mb-3">
                    <label for="nationality" class="form-label fw-bold">Nationality</label>
                    <input type="text" class="form-control" id="nationality" name="nationality"
                           value="{{ old('nationality') }}" style="height: 47px">
                </div>

                <!-- Author Photo -->
                <div class="mb-3">
                    <label for="author_photo_url" class="form-label fw-bold">Author Photo</label>
                    <input type="file" class="form-control" id="author_photo_url"
                           name="author_photo_url" accept="image/*">
                    <div class="mt-2">
                        <div id="photoPreview" class="d-flex justify-content-center"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4 d-flex gap-4">
            <button type="submit" class="btnAdmin">Save Author</button>
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
                        // Criar wrapper para a imagem e botão de remover
                        const wrapper = document.createElement('div');
                        wrapper.style.position = 'relative';

                        // Criar imagem
                        const img = document.createElement('img');
                        img.src = URL.createObjectURL(file);
                        img.style.width = '150px';
                        img.style.height = '150px';
                        img.style.objectFit = 'cover';
                        img.className = 'rounded mt-2';

                        // Criar botão de remover usando a classe existente
                        const removeButton = document.createElement('div');
                        removeButton.className = 'delete-image';
                        removeButton.innerHTML = '×';

                        // Função simplificada para remover preview
                        removeButton.onclick = function() {
                            photoPreview.innerHTML = '';
                            photoInput.value = '';
                        };

                        // Adicionar elementos ao DOM
                        wrapper.appendChild(img);
                        wrapper.appendChild(removeButton);
                        photoPreview.appendChild(wrapper);
                    }
                });
            });
        </script>
    @endpush
@endsection
