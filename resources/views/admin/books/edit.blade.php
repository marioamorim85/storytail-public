@extends('admin.dashboard-layout')

@section('dashboard-content')
    <h1 class="custom-title">Edit Book #{{ $book->id }}</h1>

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

    <form method="POST" action="{{ route('admin.books.update', $book->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-md-6">
                <!-- Title -->
                <div class="mb-3">
                    <label for="title" class="form-label fw-bold">Title</label>
                    <input type="text" class="form-control" id="title" name="title"
                           value="{{ $book->title }}" required style="height: 47px">
                </div>

                <!-- Description -->
                <div class="mb-3">
                    <label for="description" class="form-label fw-bold">Description</label>
                    <textarea type="text" class="form-control" id="description" name="description"
                              required rows="5">{{ $book->description }}</textarea>
                </div>

                <!-- Read Time -->
                <div class="mb-3">
                    <label for="read_time" class="form-label fw-bold">Read Time (minutes)</label>
                    <input type="number" class="form-control" id="read_time" name="read_time"
                           value="{{ $book->read_time }}" required style="height: 47px">
                </div>


                <!-- Authors -->
                <div class="mb-3 mt-4">
                    <label for="authors" class="form-label fw-bold mb-2">Authors</label>
                    <div class="d-flex align-items-start gap-2">
                        <div style="flex: 1;">
                            <select class="form-select" id="authors" name="authors[]" multiple required tabindex="0">
                                @foreach($authors as $author)
                                    <option value="{{ $author->id }}"
                                        {{ in_array($author->id, $book->authors->pluck('id')->toArray()) ? 'selected' : '' }}>
                                        {{ $author->first_name }} {{ $author->last_name }}
                                    </option>
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
                            <option value="{{ $ageGroup->id }}"
                                {{ $book->age_group_id == $ageGroup->id ? 'selected' : '' }}>
                                {{ $ageGroup->age_group }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Access Level -->
                <div class="mb-3">
                    <label for="access_level" class="form-label fw-bold">Access Level</label>
                    <select class="form-select" id="access_level" name="access_level" required>
                        <option value="">Select Access Level</option>
                        <option value="1" {{ $book->access_level == 1 ? 'selected' : '' }}>Free</option>
                        <option value="2" {{ $book->access_level == 2 ? 'selected' : '' }}>Premium</option>
                    </select>
                </div>
            </div>

            <div class="col-md-6">
                <!-- Tags -->
                <div class="mb-3">
                    <label for="tags" class="form-label fw-bold">Tags</label>
                    <select class="form-select" id="tags" name="tags[]" multiple>
                        @foreach($tags as $tag)
                            <option value="{{ $tag->id }}"
                                {{ in_array($tag->id, $book->tags->pluck('id')->toArray()) ? 'selected' : '' }}>
                                {{ $tag->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Status -->
                <div class="mb-3">
                    <label class="form-label d-block fw-bold">Status</label>
                    <div class="form-check form-switch">
                        <input class="form-check-input border-orange" type="checkbox" id="is_active"
                               name="is_active" value="1" {{ $book->is_active ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">Active</label>
                    </div>
                </div>

                <!-- Current Cover Image -->
                @if($book->cover_url)
                    <div class="mb-3">
                        <label class="form-label fw-bold">Current Cover</label>
                        <div class="mb-2">
                            <img src="{{ Storage::url($book->cover_url) }}"
                                 alt="Current Cover"
                                 style="width: 70px; height: 100px; object-fit: cover; border-radius: 4px;">
                        </div>
                    </div>
                @endif

                <!-- Cover Image -->
                <div class="mb-3">
                    <label for="cover_url" class="form-label fw-bold">New Cover Image</label>
                    <input type="file" class="form-control" id="cover_url" name="cover_url" accept="image/*">
                    <div id="coverPreview" class="mt-2 d-flex flex-wrap gap-2"></div>
                </div>

                <!-- Current Pages -->
                @if($book->pages->count() > 0)
                    <div class="mb-3">
                        <label class="form-label fw-bold">Current Pages</label>
                        <div class="d-flex flex-wrap gap-2 mb-2">
                            @foreach($book->pages()->orderBy('page_index')->get() as $page)
                                <div class="text-center position-relative">
                                    <!-- Adiciona o botão "X" para excluir a imagem -->
                                    <div class="delete-image"
                                         onclick="removePage({{ $page->id }})">
                                        ×
                                    </div>
                                    <img src="{{ Storage::url($page->page_image_url) }}"
                                         alt="Page {{ $page->page_index }}"
                                         style="width: 70px; height: 100px; object-fit: cover; border-radius: 4px;">
                                    <div class="small mt-1">Page {{ $page->page_index }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Book Pages -->
                <div class="mb-3">
                    <label for="pages" class="form-label fw-bold">New Book Pages</label>
                    <input type="file" class="form-control" id="pages" name="pages[]" multiple accept="image/*">
                    <div id="pagePreview" class="mt-2 d-flex flex-wrap gap-2"></div>
                </div>

                <!-- Current Video -->
                @if($book->video)
                    <div class="mb-3">
                        <label class="form-label fw-bold">Current Video</label>
                        <div class="mb-2 position-relative" style="width: 300px; height: 169px;">
                            <iframe width="100%" height="100%"
                                    src="https://www.youtube.com/embed/{{ $book->getYoutubeId($book->video->video_url) }}"
                                    frameborder="0" allowfullscreen>
                            </iframe>
                        </div>
                    </div>
                @endif

                <!-- New YouTube Video -->
                <div class="mb-3">
                    <label for="video" class="form-label fw-bold">New YouTube Video URL</label>
                    <input type="text" class="form-control" id="video_url" name="video_url"
                           placeholder="https://www.youtube.com/watch?v=...">
                    <div id="video-preview" style="width: 300px; height: 169px; position: relative;"></div>
                </div>

            </div>
        </div>

        <div class="mt-4 d-flex gap-4">
            <button type="submit" class="btn btnAdmin">Save Changes</button>
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

                <!-- Body com sombras e espaçamento melhorado -->
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
            document.addEventListener('DOMContentLoaded', () => {
                console.log('DOM completamente carregado.');

                // Configuração para select múltiplo de autores
                const multipleSelectConfig = {
                    itemSelectText: '',
                    removeItemButton: true,
                    maxItemCount: -1,
                    searchEnabled: true,
                    placeholder: true,
                    placeholderValue: 'Select Authors',
                    shouldSort: false
                };

                // Variável global para o Choices
                window.authorsChoices = null;

                // Função para inicializar ou obter o Choices existente
                function getAuthorsChoices() {
                    const authorsSelect = document.querySelector('#authors');

                    if (!authorsSelect) {
                        console.error('Select de autores não encontrado');
                        return null;
                    }

                    // Se já existe uma instância, retorna essa
                    if (window.authorsChoices) {
                        return window.authorsChoices;
                    }

                    // Se não existe, cria uma nova instância
                    console.log('Inicializar nova instância do Choices.js');
                    window.authorsChoices = new Choices(authorsSelect, multipleSelectConfig);

                    // Adiciona listener de mudança
                    authorsSelect.addEventListener('change', function() {
                        const selectedValues = Array.from(this.selectedOptions).map(opt => opt.value);
                        console.log('Valores selecionados atualizados:', selectedValues);
                    });

                    return window.authorsChoices;
                }

                // Inicialização inicial
                const authorsChoices = getAuthorsChoices();

                // Validação do formulário e captura dos dados no console
                const form = document.querySelector('form');
                if (form) {
                    form.addEventListener('submit', (event) => {
                        event.preventDefault();
                        console.log('Tentar submeter o formulário...');

                        const authorsChoices = getAuthorsChoices();
                        if (!authorsChoices) {
                            console.error('Choices não inicializado');
                            return;
                        }

                        const selectedValues = authorsChoices.getValue(true);
                        console.log('Autores selecionados:', selectedValues);

                        // Captura todos os dados do formulário para depuração
                        console.log('Dados do formulário completos:');
                        const formData = new FormData(form);
                        for (let [key, value] of formData.entries()) {
                            console.log(`${key}:`, value);
                        }

                        // Atualizar o select original antes de enviar
                        const authorsSelect = document.querySelector('#authors');
                        authorsSelect.querySelectorAll('option').forEach(option => {
                            option.selected = selectedValues.includes(option.value);
                        });

                        console.log('Formulário válido, pronto para enviar.');
                        form.submit();
                    });
                }

                // Função para adicionar novo autor
                window.updateAuthorsChoices = function(newOption) {
                    console.log('Adicionar novo autor:', newOption);
                    const authorsChoices = getAuthorsChoices();

                    if (authorsChoices) {
                        // Pegar seleções atuais antes de adicionar nova opção
                        const currentSelections = authorsChoices.getValue(true);

                        // Adicionar nova opção
                        authorsChoices.setChoices(
                            [{ value: newOption.value, label: newOption.label }],
                            'value',
                            'label',
                            false
                        );

                        // Restaurar seleções anteriores e incluir a nova
                        authorsChoices.setChoiceByValue([...currentSelections, newOption.value]);
                    }
                };

                // Função para salvar um novo autor
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
                        console.log('Enviar dados para o servidor...');
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

                            // Atualizar select de autores com o novo autor
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
                        console.error('Erro na requisição ao guardar autor:', error);
                        showNotification('error', error.message || 'Failed to create author. Please try again.');
                    } finally {
                        if (saveButton) {
                            saveButton.disabled = false;
                            saveButton.textContent = 'Save Author';
                        }
                    }
                }

                // Gestão de imagens e previews
                window.previewPhoto = function(event) {
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
                };

                window.removePreviewPhoto = function() {
                    const photoInput = document.getElementById('author_photo');
                    const previewImage = document.getElementById('photoPreviewImage');
                    const previewContainer = document.getElementById('photoPreviewContainer');

                    if (photoInput && previewImage && previewContainer) {
                        photoInput.value = '';
                        previewImage.src = '';
                        previewContainer.style.display = 'none';
                    }
                };

                // Gestão de páginas do livro
                const bookId = @json($book->id ?? null);

                window.removePage = function(pageId) {
                    swal({
                        title: "Warning",
                        text: "Are you sure you want to remove this page?",
                        icon: "warning",
                        buttons: true,
                        dangerMode: true,
                    }).then((willDelete) => {
                        if (willDelete) {
                            const form = document.createElement('form');
                            form.method = 'POST';
                            form.action = `/admin/books/${bookId}/page/${pageId}`;

                            // Token CSRF
                            const csrfToken = document.createElement('input');
                            csrfToken.type = 'hidden';
                            csrfToken.name = '_token';
                            csrfToken.value = '{{ csrf_token() }}';
                            form.appendChild(csrfToken);

                            // Método DELETE
                            const methodField = document.createElement('input');
                            methodField.type = 'hidden';
                            methodField.name = '_method';
                            methodField.value = 'DELETE';
                            form.appendChild(methodField);

                            document.body.appendChild(form);
                            form.submit();
                        }
                    });
                };

                // Sistema de notificações
                function showNotification(type, message) {
                    console.log(`Exibindo notificação [${type}]:`, message);
                    const notificationBox = document.querySelector('.notification-box');
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

                    if (!notificationBox) {
                        const newNotificationBox = document.createElement('div');
                        newNotificationBox.className = 'notification-box';
                        document.body.appendChild(newNotificationBox);
                        newNotificationBox.appendChild(toast);
                    } else {
                        notificationBox.appendChild(toast);
                    }

                    setTimeout(() => toast.remove(), 5000);
                }

                // Tornar funções globais acessíveis
                window.saveAuthor = saveAuthor;
            });
        </script>
    @endpush
@endsection
