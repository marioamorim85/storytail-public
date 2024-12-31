@extends('admin.dashboard-layout')

@section('dashboard-content')
    <h1 class="custom-title">Add New Activity</h1>

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

    <form method="POST" action="{{ route('admin.activities.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-md-6">
                <!-- Title -->
                <div class="mb-3">
                    <label for="title" class="form-label fw-bold">Title</label>
                    <input type="text" class="form-control" id="title" name="title" required style="height: 47px">
                </div>

                <!-- Description -->
                <div class="mb-3">
                    <label for="description" class="form-label fw-bold">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="5" required></textarea>
                </div>

                <!-- Book -->
                <div class="mb-3 mt-4">
                    <label for="book_id" class="form-label fw-bold">Associated Book</label>
                    <select class="form-select" id="book_id" name="book_id" required>
                        <option value="">Select Book</option>
                        @foreach($books as $book)
                            <option value="{{ $book->id }}">{{ $book->title }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-6">
                <!-- Status -->
                <div class="mb-3">
                    <label class="form-label d-block fw-bold">Status</label>
                    <div class="form-check form-switch">
                        <input class="form-check-input border-orange" type="checkbox" id="is_active" name="is_active" value="1" checked>
                        <label class="form-check-label" for="is_active">Active</label>
                    </div>
                </div>

                <!-- Activity Images -->
                <div class="mb-3">
                    <label for="images" class="form-label fw-bold">Activity Images</label>
                    <input type="file" class="form-control" id="images" name="images[]" multiple accept="image/*">
                    <div class="mt-2">
                        <!-- Preview Container -->
                        <div id="imagePreview" class="d-flex flex-wrap gap-2"></div>
                        <!-- Image Metadata Container -->
                        <div id="imageTitles"></div>
                    </div>
                </div>

            </div>
        </div>

        <div class="mt-4 d-flex gap-4">
            <button type="submit" class="btnAdmin">Save Activity</button>
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

                // Gestão de visualização e meta-dados de imagens
                const imageInput = document.getElementById('images');
                const previewContainer = document.getElementById('imagePreview');
                const titleContainer = document.getElementById('imageTitles');
                let files = [];

                imageInput.addEventListener('change', function(event) {
                    files = Array.from(event.target.files);
                    updatePreview();
                });

                function updatePreview() {
                    previewContainer.innerHTML = '';
                    titleContainer.innerHTML = '';

                    files.forEach((file, index) => {
                        // Criar o container para cada imagem
                        const imgContainer = document.createElement('div');
                        imgContainer.className = 'text-center position-relative';

                        // Adicionar botão de remoção
                        const removeBtn = document.createElement('div');
                        removeBtn.className = 'delete-image';
                        removeBtn.textContent = '×';
                        removeBtn.onclick = () => {
                            files = files.filter((_, i) => i !== index);
                            updatePreview();
                        };
                        imgContainer.appendChild(removeBtn);

                        // Adicionar a imagem
                        const img = document.createElement('img');
                        img.src = URL.createObjectURL(file);
                        img.style.width = '70px';
                        img.style.height = '100px';
                        img.style.objectFit = 'cover';
                        img.className = 'rounded';

                        // Exibir o número da imagem
                        const pageNumber = document.createElement('div');
                        pageNumber.className = 'small mt-1';
                        pageNumber.textContent = `Image ${index + 1}`;

                        imgContainer.appendChild(img);
                        imgContainer.appendChild(pageNumber);
                        previewContainer.appendChild(imgContainer);

                        // Criar o campo de título para a imagem
                        const titleDiv = document.createElement('div');
                        titleDiv.className = 'mb-2';
                        titleDiv.innerHTML = `
                        <label class="form-label">Image ${index + 1} Title</label>
                        <input type="text" class="form-control" name="image_titles[]" required>
                        <input type="hidden" name="image_orders[]" value="${index + 1}">
                    `;
                        titleContainer.appendChild(titleDiv);
                    });
                }
            });
        </script>
    @endpush
@endsection

