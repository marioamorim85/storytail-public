@extends('admin.dashboard-layout')

@section('dashboard-content')
    <h1 class="custom-title">Add New Book</h1>

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

    <form method="POST" action="{{ route('admin.books.store') }}" enctype="multipart/form-data">
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
                    <textarea type="text" class="form-control" id="description" name="description" required rows="5"></textarea>
                </div>

                <!-- Read Time -->
                <div class="mb-3">
                    <label for="read_time" class="form-label fw-bold">Read Time (minutes)</label>
                    <input type="number" class="form-control" id="read_time" name="read_time" required style="height: 47px">
                </div>

                <!-- Authors -->
                <div class="mb-3 mt-4">
                    <label for="authors" class="form-label fw-bold mb-2">Authors</label>
                    <div class="d-flex align-items-start gap-2">
                        <div style="flex: 1;">
                            <select class="form-select" id="authors" name="authors[]" multiple required tabindex="0">
                                @foreach($authors as $author)
                                    <option value="{{ $author->id }}">{{ $author->first_name }} {{ $author->last_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="button" class="btn btn-orange rounded-circle d-flex align-items-center justify-content-center"
                                data-bs-toggle="modal" data-bs-target="#addAuthorModal"
                                style="width: 25px; height: 25px; min-width: 25px; margin-top: 10px;">
                            <i class="bi bi-plus-lg"></i>
                        </button>
                    </div>
                </div>


                <!-- Age Group -->
                <div class="mb-3 mt-4">
                    <label for="age_group_id" class="form-label fw-bold">Age Group</label>
                    <select class="form-select" id="age_group_id" name="age_group_id" required>
                        <option value="">Select Age Group</option>
                        @foreach($ageGroups as $ageGroup)
                            <option value="{{ $ageGroup->id }}">{{ $ageGroup->age_group }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Access Level -->
                <div class="mb-3">
                    <label for="access_level" class="form-label fw-bold">Access Level</label>
                    <select class="form-select" id="access_level" name="access_level" required>
                        <option value="">Select Access Level</option>
                        <option value="1">Free</option>
                        <option value="2">Premium</option>
                    </select>
                </div>
            </div>

            <div class="col-md-6">
                <!-- Tags -->
                <div class="mb-3">
                    <label for="tags" class="form-label fw-bold">Tags</label>
                    <select class="form-select" id="tags" name="tags[]" multiple>
                        @foreach($tags as $tag)
                            <option value="{{ $tag->id }}">{{ $tag->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Status -->
                <div class="mb-3">
                    <label class="form-label d-block fw-bold">Status</label>
                    <div class="form-check form-switch">
                        <input class="form-check-input border-orange" type="checkbox" id="is_active" name="is_active" value="1" checked>
                        <label class="form-check-label" for="is_active">Active</label>
                    </div>
                </div>

                <!-- Cover Image -->
                <div class="mb-3">
                    <label for="cover_url" class="form-label fw-bold">Cover Image</label>
                    <input type="file" class="form-control" id="cover_url" name="cover_url" accept="image/*">
                    <div id="coverPreview" class="mt-2 d-flex flex-wrap gap-2"></div>
                </div>

                <!-- Book Pages -->
                <div class="mb-3">
                    <label for="pages" class="form-label fw-bold">Book Pages</label>
                    <input type="file" class="form-control" id="pages" name="pages[]" multiple accept="image/*">
                    <div id="pagePreview" class="mt-2 d-flex flex-wrap gap-2"></div>
                </div>

                <!-- Video -->
                <div class="mb-3">
                    <label for="video" class="form-label fw-bold">YouTube Video URL</label>
                    <input type="text" class="form-control" id="video_url" name="video_url"
                           placeholder="https://www.youtube.com/watch?v=...">
                    <div id="video-preview" style="width: 300px; height: 169px; position: relative;"></div>
                </div>

            </div>
        </div>

        <div class="mt-4 d-flex gap-4">
            <button type="submit" class="btnAdmin">Save Book</button>
            <a href="{{ route('admin.books.list') }}" class="btnAdminSecundary">Cancel</a>
        </div>
    </form>

    <!-- Add Author Modal -->
    <div class="modal fade" id="addAuthorModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <!-- Header com gradiente e ícone -->
                <div class="modal-header bg-gradient" style="background: linear-gradient(45deg, #FF6B00, #FF8533); padding: 1.5rem;">
                    <div class="d-flex align-items-center gap-3">
                        <h1 class="custom-title mb-0">Add New Author</h1>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <!-- Body com sombras e espaçamento -->
                <div class="modal-body" style="padding: 2rem;">
                    <form id="authorForm">
                        <div class="row">
                            <div class="col-md-6">
                                <!-- First Name -->
                                <div class="mb-4">
                                    <label class="form-label fw-bold">
                                        <i class="bi bi-person me-2 text-orange"></i>First Name
                                    </label>
                                    <input type="text" class="form-control form-control-lg shadow-sm"
                                           id="author_first_name" required>
                                </div>
                                <!-- Last Name -->
                                <div class="mb-4">
                                    <label class="form-label fw-bold">
                                        <i class="bi bi-person me-2 text-orange"></i>Last Name
                                    </label>
                                    <input type="text" class="form-control form-control-lg shadow-sm"
                                           id="author_last_name" required>
                                </div>
                                <!-- Description -->
                                <div class="mb-4">
                                    <label class="form-label fw-bold">
                                        <i class="bi bi-card-text me-2 text-orange"></i>Description
                                    </label>
                                    <textarea class="form-control shadow-sm" id="author_description"
                                              rows="4" style="resize: none;"></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <!-- Nationality -->
                                <div class="mb-4">
                                    <label class="form-label fw-bold">
                                        <i class="bi bi-globe me-2 text-orange"></i>Nationality
                                    </label>
                                    <input type="text" class="form-control form-control-lg shadow-sm"
                                           id="author_nationality">
                                </div>
                                <!-- Photo -->
                                <div class="mb-4">
                                    <label for="author_photo" class="form-label fw-bold">
                                        <i class="bi bi-camera me-2 text-orange"></i>Photo
                                    </label>
                                    <input type="file" class="form-control form-control-lg shadow-sm"
                                           id="author_photo" accept="image/*" onchange="previewPhoto(event)">
                                    <!-- Preview Container -->
                                    <div id="photoPreviewContainer" class="position-relative mt-3"
                                         style="display: none; width: 150px; height: 150px;">
                                        <img id="photoPreviewImage" class="img-fluid rounded border"
                                             alt="Preview Image"
                                             style="width: 100%; height: 100%; object-fit: cover;">
                                        <!-- Remove Button -->
                                        <div class="delete-image" onclick="removePreviewPhoto()">×</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Footer com gradiente suave -->
                <div class="modal-footer">
                    <button type="button" class="btnAdmin" onclick="saveAuthor()">Save Author</button>
                    <button type="button" class="btnAdminSecundary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>


    @push('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
        <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
        <script>
            // Configurações do Choices.js
            const multipleSelectConfig = {
                itemSelectText: '',
                removeItemButton: true,
                maxItemCount: -1,
                searchEnabled: true,
                placeholder: true,
                placeholderValue: 'Select Authors',
                shouldSort: false
            };

            // Gestão do Choices.js
            let authorsChoices = null;

            function getAuthorsChoices() {
                const authorsSelect = document.querySelector('#authors');

                if (!authorsSelect) {
                    console.error('Select de autores não encontrado');
                    return null;
                }

                if (authorsChoices) {
                    return authorsChoices;
                }

                console.log('Inicializar nova instância do Choices.js');
                authorsChoices = new Choices(authorsSelect, multipleSelectConfig);

                authorsSelect.addEventListener('change', function() {
                    const selectedValues = Array.from(this.selectedOptions).map(opt => opt.value);
                    console.log('Valores selecionados atualizados:', selectedValues);
                });

                return authorsChoices;
            }

            // Gestão de Formulários
            function setupFormHandling() {
                const form = document.querySelector('form');
                if (!form) return;

                form.addEventListener('submit', (event) => {
                    event.preventDefault();
                    console.log('Tentar submeter o formulário...');

                    const choices = getAuthorsChoices();
                    if (!choices) {
                        console.error('Choices não inicializado');
                        return;
                    }

                    const selectedValues = choices.getValue(true);
                    console.log('Autores selecionados:', selectedValues);

                    // Captura dados do formulário
                    const formData = new FormData(form);
                    for (let [key, value] of formData.entries()) {
                        console.log(`${key}:`, value);
                    }

                    // Atualiza select de autores
                    const authorsSelect = document.querySelector('#authors');
                    authorsSelect.querySelectorAll('option').forEach(option => {
                        option.selected = selectedValues.includes(option.value);
                    });

                    console.log('Formulário válido, pronto para enviar.');
                    form.submit();
                });
            }

            // Gestão de Autores
            function updateAuthorsChoices(newOption) {
                console.log('Adicionando novo autor:', newOption);
                const choices = getAuthorsChoices();

                if (choices) {
                    const currentSelections = choices.getValue(true);
                    choices.setChoices(
                        [{ value: newOption.value, label: newOption.label }],
                        'value',
                        'label',
                        false
                    );
                    choices.setChoiceByValue([...currentSelections, newOption.value]);
                }
            }

            async function saveAuthor() {
                console.log('Iniciar processo de guardar autor...');
                const form = document.getElementById('authorForm');
                const saveButton = document.querySelector('[onclick="saveAuthor()"]');

                const formData = new FormData();
                formData.append('first_name', document.getElementById('author_first_name').value);
                formData.append('last_name', document.getElementById('author_last_name').value);
                formData.append('nationality', document.getElementById('author_nationality').value);
                formData.append('description', document.getElementById('author_description').value);

                const photoFile = document.getElementById('author_photo').files[0];
                if (photoFile) {
                    formData.append('author_photo_url', photoFile);
                }

                formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

                if (saveButton) {
                    saveButton.disabled = true;
                    saveButton.textContent = 'Saving...';
                }

                try {
                    console.log('A enviar dados para o servidor...');
                    const response = await fetch('{{ route('admin.authors.store') }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        },
                    });

                    if (!response.ok) {
                        const error = await response.json();
                        throw new Error(error.message || 'Failed to save author.');
                    }

                    const data = await response.json();

                    if (data.success) {
                        const { id, full_name } = data.author;
                        console.log('Autor guardado com sucesso:', data.author);

                        // Atualizar select de autores
                        updateAuthorsChoices({ value: id, label: full_name });

                        // Fechar modal
                        const modal = document.getElementById('addAuthorModal');
                        const modalInstance = bootstrap.Modal.getInstance(modal);
                        modalInstance.hide();

                        // Limpar formulário
                        if (form) {
                            form.reset();
                            console.log('Formulário do modal reset.');
                        }

                        showNotification('success', data.message);
                    } else {
                        console.error('Erro ao guardar autor:', data);
                    }
                } catch (error) {
                    console.error('Error:', error);
                    showNotification('error', error.message || 'Failed to create author. Please try again.');
                } finally {
                    if (saveButton) {
                        saveButton.disabled = false;
                        saveButton.textContent = 'Save Author';
                    }
                }
            }

            // Gestão de Imagens
            function previewPhoto(event) {
                const reader = new FileReader();
                const previewImage = document.getElementById('photoPreviewImage');
                const previewContainer = document.getElementById('photoPreviewContainer');

                if (previewImage && previewContainer) {
                    reader.onload = function() {
                        previewImage.src = reader.result;
                        previewContainer.style.display = 'block';
                    };

                    if (event.target.files[0]) {
                        reader.readAsDataURL(event.target.files[0]);
                    }
                }
            }

            function removePreviewPhoto() {
                const photoInput = document.getElementById('author_photo');
                const previewImage = document.getElementById('photoPreviewImage');
                const previewContainer = document.getElementById('photoPreviewContainer');

                if (photoInput && previewImage && previewContainer) {
                    photoInput.value = '';
                    previewImage.src = '';
                    previewContainer.style.display = 'none';
                }
            }

            // Sistema de Notificações
            function showNotification(type, message) {
                let notificationBox = document.querySelector('.notification-box');
                const toast = createToastElement(type, message);

                if (!notificationBox) {
                    notificationBox = document.createElement('div');
                    notificationBox.className = 'notification-box';
                    document.body.appendChild(notificationBox);
                }

                notificationBox.appendChild(toast);
                setTimeout(() => toast.remove(), 5000);
            }

            function createToastElement(type, message) {
                const toast = document.createElement('div');
                toast.className = `notification ${type}`;
                toast.innerHTML = `
                    <div class="icon">
                        <i class="bi ${type === 'success' ? 'bi-check-circle' : 'bi-x-circle'}"></i>
                    </div>
                    <div class="title">
                        <h1>${type === 'success' ? 'Success' : 'Error'}</h1>
                        <h6>${message}</h6>
                    </div>
                    <div class="close" onclick="this.parentElement.remove()">
                        <i class="bi bi-x"></i>
                    </div>
                `;
                return toast;
            }

            // Inicialização
            document.addEventListener('DOMContentLoaded', () => {
                console.log('DOM completamente carregado.');
                getAuthorsChoices();
                setupFormHandling();
            });

            // Expor funções necessárias globalmente
            window.saveAuthor = saveAuthor;
            window.previewPhoto = previewPhoto;
            window.removePreviewPhoto = removePreviewPhoto;
            window.updateAuthorsChoices = updateAuthorsChoices;
        </script>
    @endpush
@endsection
