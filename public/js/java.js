// ########################### Auth Page ###########################
// Padrões de validação
const patterns = {
    email: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
    password: /^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/,
    names: /^[A-Za-zÀ-ÖØ-öø-ÿ\s]{2,}$/
};

// Mensagens de erro
const errorMessages = {
    email: 'Please enter a valid email',
    password: 'Password must have at least 8 characters, including 1 letter and 1 number',
    confirmPassword: 'Passwords do not match',
    names: 'Please enter a valid name'
};

// Validação de campo
function validateField(field, type) {
    const isValid = patterns[type].test(field.value);
    updateFieldStatus(field, isValid, errorMessages[type]);
    return isValid;
}

// Validação de confirmação de senha (atualizada)
function validateConfirmPassword(confirmField) {
    const passwordField = document.querySelector('input[placeholder="Password"]');
    const isValid = confirmField.value === passwordField.value && confirmField.value !== '';
    updateFieldStatus(confirmField, isValid, errorMessages.confirmPassword);
}

// Atualização visual do estado dos campos
function updateFieldStatus(field, isValid, errorMessage) {
    field.classList.toggle('is-valid', isValid);
    field.classList.toggle('is-invalid', !isValid);

    // Atualizar mensagem de erro
    let feedbackDiv = field.nextElementSibling;
    if (!feedbackDiv || !feedbackDiv.classList.contains('invalid-feedback')) {
        feedbackDiv = document.createElement('div');
        feedbackDiv.className = 'invalid-feedback';
        field.parentNode.insertBefore(feedbackDiv, field.nextSibling);
    }
    feedbackDiv.textContent = isValid ? '' : errorMessage;
}

// Configuração de eventos de validação em tempo real nos campos de autenticação
document.addEventListener('DOMContentLoaded', () => {
    // Verifica se estamos numa página de autenticação (login, register, reset-password ou forgot-password)
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');
    const resetPasswordForm = document.getElementById('resetPasswordForm');
    const forgotPassForm = document.getElementById('forgotPassForm');

    if (loginForm || registerForm || resetPasswordForm || forgotPassForm) {
        const emailInput = document.querySelector('input[type="email"]');
        const passwordInput = document.querySelector('input[placeholder="Password"]');
        const confirmPasswordInput = document.querySelector('input[placeholder="Confirm Password"]');

        if (emailInput) {
            emailInput.addEventListener('input', (e) => validateField(e.target, 'email'));
        }

        if (passwordInput) {
            passwordInput.addEventListener('input', (e) => validateField(e.target, 'password'));
        }

        if (confirmPasswordInput) {
            confirmPasswordInput.addEventListener('input', (e) => validateConfirmPassword(e.target));
            passwordInput.addEventListener('input', (e) => validateConfirmPassword(confirmPasswordInput));
        }
    }
});


// Função para alternar a visibilidade da senha
function togglePasswordVisibility(passwordFieldId, toggleIconId) {
    const passwordField = document.getElementById(passwordFieldId);
    const toggleIcon = document.getElementById(toggleIconId);

    if (passwordField.type === "password") {
        passwordField.type = "text";
        toggleIcon.classList.remove("bi-eye");
        toggleIcon.classList.add("bi-eye-slash");
    } else {
        passwordField.type = "password";
        toggleIcon.classList.remove("bi-eye-slash");
        toggleIcon.classList.add("bi-eye");
    }
}


// Toast de confirmação do envio de formulário para login/logout
// document.addEventListener('DOMContentLoaded', function() {
//     var toastEl = document.querySelector('.toast');
//     if (toastEl) {
//         var toast = new bootstrap.Toast(toastEl, {
//             animation: true,
//             autohide: true,
//             delay: 3000
//         });
//         toast.show();
//     }
// });




// ########################### Home Page ###########################

// Evento para inicializar a navegação entre abas e categorias
document.addEventListener('DOMContentLoaded', function () {
    const allTabs = document.querySelectorAll('.nav-link');
    const allSections = document.querySelectorAll('.content-section');
    const sortSelect = document.querySelector('.form-select');
    const searchForm = document.querySelector('.search-bar form');

    // Mostra a secção inicial "All Books" por padrão
    allSections.forEach(section => section.classList.add('hidden'));

    const allSection = document.getElementById('all-section');
    if (allSection) {
        allSection.classList.remove('hidden');
        showFilterComponent('all');
    }

    // Configura o seletor de ordenação para "A-Z" e aplica a ordenação automaticamente na aba inicial
    if (sortSelect) {
        sortSelect.value = 'asc'; // Define o valor inicial como "A-Z"
        sortBooks('asc'); // Aplica a ordenação automaticamente
    }

    // Event listener para as abas
    allTabs.forEach(tab => {
        tab.addEventListener('click', function (event) {
            event.preventDefault();

            // Remove a classe 'active' de todas as abas
            allTabs.forEach(t => t.classList.remove('active'));

            // Adiciona a classe 'active' à aba clicada
            this.classList.add('active');

            // Identifica a categoria selecionada
            const category = this.getAttribute('data-category');

            // Esconde todas as secções
            allSections.forEach(section => section.classList.add('hidden'));

            // Exibe a secção correspondente à aba clicada
            const targetSection = document.getElementById(`${category}-section`);
            if (targetSection) {
                targetSection.classList.remove('hidden');
            }


            // Atualiza filtros e configurações com base na categoria
            if (category === 'all') {
                clearFilters();
                if (sortSelect) {
                    sortSelect.value = 'asc'; // Define o seletor para "A-Z"
                    sortBooks('asc'); // Reaplica a ordenação
                }
            } else if (category === 'popular') {
                // Para "Popular", não reaplica ordenação; utiliza a ordem do backend
                clearFilters();
                if (sortSelect) {
                    sortSelect.value = ''; // Remove seleção no frontend para evitar confusão
                }
            } else if (category === 'picks') {
                // Para "Picks", aplica a lógica atual
                clearFilters();
                if (sortSelect) {
                    sortSelect.value = 'asc'; // Visualmente seleciona "A-Z"
                    sortBooks('asc'); // Reaplica a ordenação
                }
            } else {
                clearFilters(); // Limpa os filtros para outras abas
            }


            // Atualiza os componentes visuais relacionados
            showFilterComponent(category);
            toggleSortSelect(category);

            // Reseta a barra de pesquisa
            const searchInput = document.querySelector('.search-bar input[name="query"]');
            const searchButton = document.querySelector('.search-bar button');
            if (searchInput) {
                searchInput.value = '';
            }
            if (searchButton) {
                searchButton.classList.remove('btn-secondary');
                searchButton.classList.add('btn-orange');
                searchButton.innerHTML = '<i class="bi bi-search text-white bold-icon"></i>';
                searchButton.onclick = null;
            }

            // Recarrega os livros da categoria selecionada
            loadOriginalBooks(category);
        });
    });

    // Event listener para o seletor de ordenação
    if (sortSelect) {
        sortSelect.addEventListener('change', function () {
            if (this.value === '') {
                const activeFilters = {
                    plans: [],
                    ages: [],
                    tags: [],
                    authors: [],
                    readingTimes: [],
                    query: document.querySelector('.search-bar input[name="query"]')?.value.trim() || ''
                };

                if (!hasActiveFilters(activeFilters) && !activeFilters.query) {
                    const activeSection = document.querySelector('.content-section:not(.hidden)');
                    if (activeSection) {
                        loadOriginalBooks(activeSection.id.replace('-section', ''));
                    }
                } else {
                    applyFilters();
                }
            } else {
                sortBooks(this.value);
            }
        });
    }

    // Event listener para o formulário de pesquisa
    if (searchForm) {
        searchForm.addEventListener('submit', function (event) {
            event.preventDefault();

            const query = this.querySelector('input[name="query"]').value.trim();
            const submitButton = this.querySelector('button');
            const activeSection = document.querySelector('.content-section:not(.hidden)');
            const category = activeSection?.id?.replace('-section', '');

            if (!query) return;

            // Atualiza o botão de pesquisa
            submitButton.classList.remove('btn-orange');
            submitButton.classList.add('btn-secondary');
            submitButton.innerHTML = '<i class="bi bi-arrow-counterclockwise text-white bold-icon"></i>';
            submitButton.onclick = resetSearch;

            // Aplica os filtros com a pesquisa ou executa a busca simples
            if (category === 'all') {
                applyFilters();
            } else {
                searchBooks(query);
            }
        });
    }
});



// Função para limpar todos os filtros
function clearFilters() {
    // Desmarca todos os checkboxes nos filtros
    document.querySelectorAll('.filter-options input[type="checkbox"]').forEach(checkbox => {
        checkbox.checked = false;
    });

    // Reseta o seletor de ordenação para o valor padrão
    const sortSelect = document.querySelector('.form-select');
    if (sortSelect) {
        sortSelect.value = '';
    }
}


// Função para exibir ou ocultar o componente de filtros
function showFilterComponent(category) {
    const filterComponent = document.getElementById('filter-container');
    const toggleButton = document.getElementById('toggle-filter-btn');

    if (filterComponent && toggleButton) {
        if (category === 'all') {
            filterComponent.classList.remove('hidden');
            toggleButton.style.display = 'flex';
        } else {
            filterComponent.classList.add('hidden');
            toggleButton.style.display = 'none';
        }
    }
}

// Função para carregar livros originais de uma secção
function loadOriginalBooks(section) {
    if (section === 'popular' || section === 'picks') {
        // Essas abas já carregam os livros ordenados pelo backend
        return;
    } else if (section === 'all') {
        // Ordena "All Books" por A-Z ao carregar
        $.ajax({
            url: '/books/apply-filters',
            method: 'GET',
            data: { sort: 'asc' },
            success: function (response) {
                if (response.success && response.books) {
                    updateBooksList(response.books);
                }
            },
            error: function (xhr) {
                // Pode incluir um tratamento de erro, se necessário, sem logs
            },
        });
    } else {
        // Certifique-se de que o backend fornece os dados necessários
        updateBooksList([]); // Carrega uma lista vazia para evitar erros.
    }
}



// Função para ordenar os livros na secção ativa
function sortBooks(order) {
    const activeSection = document.querySelector('.content-section:not(.hidden)');
    const booksList = activeSection?.querySelector('#books-list');
    const books = Array.from(booksList.children);

    // Ordena os livros com base no título
    books.sort((a, b) => {
        const titleA = a.querySelector('.card-title').textContent.trim().toLowerCase();
        const titleB = b.querySelector('.card-title').textContent.trim().toLowerCase();

        if (order === 'asc') {
            return titleA.localeCompare(titleB);
        } else if (order === 'desc') {
            return titleB.localeCompare(titleA);
        }
    });

    // Atualiza a lista de livros com a nova ordem
    booksList.innerHTML = '';
    books.forEach(book => booksList.appendChild(book));
}


// Função auxiliar para verificar se há filtros ativos
function hasActiveFilters(filters) {
    return filters.plans.length > 0 ||
        filters.ages.length > 0 ||
        filters.tags.length > 0 ||
        filters.authors.length > 0 ||
        filters.readingTimes.length > 0;
}

// Função auxiliar para atualizar a lista de livros
function updateBooksList(books) {
    const activeSection = document.querySelector('.content-section:not(.hidden)');
    const booksList = activeSection?.querySelector('#books-list');

    if (booksList) {
        // Atualiza o DOM diretamente com os livros fornecidos
        booksList.innerHTML = books.length
            ? books.map(book => createBookCard(book)).join('')
            : '<div class="col-12 text-center"><p>No books found matching your criteria.</p></div>';

        // Pré-carrega as imagens para melhorar a experiência visual
        books.forEach(book => {
            const img = new Image();
            img.src = `/storage/${book.cover_url}`;
        });
    }
}


// Função para alternar a visibilidade do seletor de ordenação
function toggleSortSelect(category) {
    const filterContainer = document.querySelector('.filter');
    const sortText = filterContainer?.querySelector('span');
    const selectSort = document.querySelector('.form-select');

    if (filterContainer && selectSort) {
        if (category === 'popular') {
            // Em vez de ocultar o container inteiro, ocultamos os elementos individuais
            if (sortText) {
                // Altera o texto para mostrar a ordenação por popularidade
                sortText.textContent = 'Ordered by: Most to Least Popular';
                sortText.classList.add('custom-title');
                sortText.style.visibility = 'visible';
            }
            selectSort.style.visibility = 'hidden';
        } else {
            // Restaura o texto e visibilidade dos elementos
            if (sortText) {
                sortText.textContent = 'Sort:';
                sortText.style.visibility = 'visible';
            }
            selectSort.style.visibility = 'visible';
            selectSort.disabled = false;

            const defaultOption = selectSort.querySelector('option[value=""]');
            if (defaultOption) {
                defaultOption.textContent = 'Sort by title';
            }

            Array.from(selectSort.options).forEach(option => {
                option.style.display = '';
            });
        }
    }
}

// Função para realizar a pesquisa de livros
function searchBooks(query) {
    // Muda para a aba "all" antes de fazer a pesquisa
    const allBooksTab = document.querySelector('[data-category="all"]');
    if (allBooksTab) {
        allBooksTab.click(); // Isso vai limpar filtros e preparar a aba
    }

    // Pequeno delay para garantir que a mudança de aba foi concluída
    setTimeout(() => {
        // Define o valor da pesquisa no input
        const searchInput = document.querySelector('.search-bar input[name="query"]');
        const searchButton = document.querySelector('.search-bar button');

        if (searchInput) {
            searchInput.value = query;
        }

        // Garante que o botão continue como reset após a mudança de aba
        if (searchButton) {
            updateSearchButton(searchButton, true);
        }

        // Aplica os filtros que já incluem a pesquisa
        applyFilters();
    }, 100);
}

// Função auxiliar para atualizar o estado do botão
function updateSearchButton(button, isReset) {
    if (isReset) {
        button.classList.remove('btn-orange');
        button.classList.add('btn-secondary');
        button.innerHTML = '<i class="bi bi-arrow-counterclockwise text-white bold-icon"></i>';
        button.onclick = resetSearch;
    } else {
        button.classList.remove('btn-secondary');
        button.classList.add('btn-orange');
        button.innerHTML = '<i class="bi bi-search text-white bold-icon"></i>';
        button.onclick = null;
    }
}


// Função para resetar a pesquisa
function resetSearch() {
    const form = document.querySelector('.search-bar form');
    const submitButton = form.querySelector('button');
    const activeSection = document.querySelector('.content-section:not(.hidden)');
    const category = activeSection?.id?.replace('-section', '');

    // Reseta o botão para o estado de pesquisa
    updateSearchButton(submitButton, false);

    // Limpa o campo de entrada da pesquisa
    form.querySelector('input[name="query"]').value = '';

    // Limpa todos os filtros
    clearFilters();

    // Reseta o seletor de ordenação para o padrão (A-Z)
    const sortSelect = document.querySelector('.form-select');
    if (sortSelect) {
        sortSelect.value = 'asc'; // Define para "A-Z"
    }

    // Recarrega os livros originais da seção ativa
    loadOriginalBooks(category);

    // Reaplica a ordenação (A-Z) após recarregar os livros
    if (category === 'all' || category === 'picks' || category === 'popular') {
        sortBooks('asc');
    }
}



// Integração com o formulário de pesquisa
const searchForm = document.querySelector('.search-bar form');
if (searchForm) {
    searchForm.addEventListener('submit', function (event) {
        event.preventDefault();

        const query = this.querySelector('input[name="query"]').value.trim();
        const submitButton = this.querySelector('button');
        const activeSection = document.querySelector('.content-section:not(.hidden)');
        const currentCategory = activeSection?.id?.replace('-section', '');

        if (!query) return;

        // Atualiza o botão de pesquisa
        updateSearchButton(submitButton, true);

        // Se não estiver na aba "all", muda para ela antes de pesquisar
        if (currentCategory !== 'all') {
            searchBooks(query);
        } else {
            // Se já estiver na aba "all", apenas aplica os filtros
            applyFilters();
        }
    });
}


// Função para criar os cards de livros
function createBookCard(book) {
    return `
        <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
            <div class="card book-card">
                <img src="/storage/${book.cover_url}" class="card-img-top" alt="${book.title}" onerror="this.src='/images/no-image.png'">
                <div class="card-body text-center">
                    <h5 class="card-title">${book.title}</h5>
                    <button class="btn btn-primary" onclick="trackBookClick(${book.id}); window.location.href='/book-details/${book.id}'">
                        ${book.access_level === 1 ? 'READ' : 'PREMIUM'}
                    </button>
                </div>
            </div>
        </div>`;
}


// Carregar opções de filtro
function loadFilterOptions() {
    $.ajax({
        url: '/books/filter-options',
        method: 'GET',
        success: function(response) {
            // Preencher planos
            const planOptions = document.getElementById('plan-options');
            response.plans.forEach(plan => {
                planOptions.innerHTML += `
                    <label>
                        <input type="checkbox" name="plan" value="${plan.id}"> ${plan.name}
                    </label><br>`;
            });

            // Preencher idades
            const ageOptions = document.getElementById('age-options');
            response.ages.forEach(age => {
                ageOptions.innerHTML += `
                    <label>
                        <input type="checkbox" name="age" value="${age.id}"> ${age.name}
                    </label><br>`;
            });

            // Preencher tags
            const tagOptions = document.getElementById('tags-options');
            response.tags.forEach(tag => {
                tagOptions.innerHTML += `
                    <label>
                        <input type="checkbox" name="tag" value="${tag.id}"> ${tag.name}
                    </label><br>`;
            });

            // Preencher autores
            const authorOptions = document.getElementById('author-options');
            response.authors.forEach(author => {
                authorOptions.innerHTML += `
                    <label>
                        <input type="checkbox" name="author" value="${author.id}"> ${author.name}
                    </label><br>`;
            });

            // Preencher tempos de leitura
            const readingTimeOptions = document.getElementById('reading-time-options');
            response.readingTimes.forEach(time => {
                readingTimeOptions.innerHTML += `
                    <label>
                        <input type="checkbox" name="readingTime" value="${time.id}"> ${time.name}
                    </label><br>`;
            });

            // Adicionar event listeners para os checkboxes
            document.querySelectorAll('.filter-options input[type="checkbox"]')
                .forEach(checkbox => {
                    checkbox.addEventListener('change', applyFilters);
                });
        },
        error: function(xhr) {
            console.error('Error loading filter options:', xhr.responseText);
        }
    });
}

// Aplicar filtros, pesquisa e ordenação
function applyFilters() {
    const activeFilters = {
        plans: [],
        ages: [],
        tags: [],
        authors: [],
        readingTimes: [],
        query: '', // Pesquisa
        sort: ''   // Ordenação
    };

    // Captura os filtros selecionados
    document.querySelectorAll('#plan-options input:checked').forEach(cb => activeFilters.plans.push(cb.value));
    document.querySelectorAll('#age-options input:checked').forEach(cb => activeFilters.ages.push(cb.value));
    document.querySelectorAll('#tags-options input:checked').forEach(cb => activeFilters.tags.push(cb.value));
    document.querySelectorAll('#author-options input:checked').forEach(cb => activeFilters.authors.push(cb.value));
    document.querySelectorAll('#reading-time-options input:checked').forEach(cb => activeFilters.readingTimes.push(cb.value));

    // Captura a pesquisa
    const searchInput = document.querySelector('.search-bar input[name="query"]');
    if (searchInput) {
        activeFilters.query = searchInput.value.trim(); // Remove espaços em branco
    }

    // Captura a ordenação
    const sortSelect = document.querySelector('.form-select');
    if (sortSelect) {
        activeFilters.sort = sortSelect.value;
    }

    // Envia os filtros, pesquisa e ordenação ao servidor
    $.ajax({
        url: '/books/apply-filters',
        method: 'GET',
        data: activeFilters, // Envia todos os filtros e opções
        success: function(response) {
            const booksList = document.querySelector('#books-list');
            if (response.success && response.books) {
                booksList.innerHTML = response.books.length
                    ? response.books.map(book => createBookCard(book)).join('')
                    : '<div class="col-12 text-center"><p>No books found matching your criteria.</p></div>';
            }
        },
        error: function(xhr) {
            // Mostra uma mensagem amigável ao utilizador
            const booksList = document.querySelector('#books-list');
            booksList.innerHTML = '<div class="col-12 text-center"><p>An error occurred while applying filters. Please try again later.</p></div>';
        }
    });
}


// Carregar as opções de filtro quando a página carregar
document.addEventListener('DOMContentLoaded', function() {
    // Carregar filtros apenas na seção "All Books"
    if (document.getElementById('all-section')) {
        loadFilterOptions();

        // Adicionar event listener para o botão de reset (se existir)
        const resetButton = document.querySelector('.filter-reset');
        if (resetButton) {
            resetButton.addEventListener('click', function() {
                // Desmarcar todos os checkboxes
                document.querySelectorAll('.filter-options input[type="checkbox"]')
                    .forEach(checkbox => checkbox.checked = false);

                // Resetar o sort
                const sortSelect = document.querySelector('.form-select');
                if (sortSelect) sortSelect.value = '';

                // Recarregar os livros originais
                loadOriginalBooks('all');
            });
        }
    }
});

// Funções para controlo da barra lateral de filtros
function toggleFilterSidebar() {
    const filterContainer = document.getElementById('filter-container');
    const toggleButton = document.getElementById('toggle-filter-btn');

    if (filterContainer && toggleButton) {
        filterContainer.classList.toggle('expanded');
        toggleButton.style.display = filterContainer.classList.contains('expanded') ? 'none' : 'flex';
    }
}

function toggleFilter(filterId) {
    const filterOptions = document.getElementById(filterId);
    const button = event.currentTarget;

    if (filterOptions) {
        filterOptions.classList.toggle('active');
        button.classList.toggle('active');
    }
}

// Fechar a barra de filtros ao clicar fora
document.addEventListener('click', function(event) {
    const filterContainer = document.getElementById('filter-container');
    const toggleButton = document.getElementById('toggle-filter-btn');

    if (filterContainer && toggleButton &&
        !filterContainer.contains(event.target) &&
        !toggleButton.contains(event.target)) {
        if (filterContainer.classList.contains('expanded')) {
            filterContainer.classList.remove('expanded');
            toggleButton.style.display = 'flex';
        }
    }
});


// ########################### Book-details ###########################

/************** Book-about **************/
function toggleFavorite(bookId, context = 'book-details') {
    if (!bookId) return;

    const token = document.querySelector('meta[name="csrf-token"]').content;

    fetch(`/book-details/${bookId}/favorite`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        },
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                const toastMessage = data.message;

                // Exibir toast de sucesso
                const toast = document.createElement('div');
                toast.className = 'notification success';
                toast.innerHTML = `
                    <div class="icon">
                        <i class="bi bi-check-circle"></i>
                    </div>
                    <div class="title">
                        <h1>Success</h1>
                        <h6>${toastMessage}</h6>
                    </div>
                    <div class="close" onclick="this.parentElement.remove()">
                        <i class="bi bi-x"></i>
                    </div>
                `;

                let notificationBox = document.querySelector('.notification-box');
                if (!notificationBox) {
                    notificationBox = document.createElement('div');
                    notificationBox.className = 'notification-box';
                    document.body.appendChild(notificationBox);
                }
                notificationBox.appendChild(toast);

                setTimeout(() => toast.remove(), 5000);

                // Atualizar UI de acordo com o contexto
                if (context === 'book-details') {
                    const icon = document.getElementById('favorite-icon-heart');
                    if (data.status === 'added') {
                        icon.classList.replace('bi-heart', 'bi-heart-fill');
                        icon.style.color = 'red';
                    } else {
                        icon.classList.replace('bi-heart-fill', 'bi-heart');
                        icon.style.color = 'grey';
                    }
                } else if (context === 'favourites') {
                    if (data.status === 'removed') {
                        const bookRow = document.getElementById(`favourite-book-${bookId}`);
                        if (bookRow) bookRow.remove();

                        // Verificar se ainda há livros favoritos
                        const favouritesContainer = document.querySelector('.favourites-container');
                        const remainingItems = favouritesContainer.querySelectorAll('.favourite-item').length;

                        if (remainingItems === 0) {
                            favouritesContainer.innerHTML = `
                                <div class="row">
                                    <div class="col-12 text-center">No favourite books found.</div>
                                </div>
                            `;
                        }
                    }
                }
            } else {
                throw new Error(data.message);
            }
        })
        .catch((error) => {
            // Exibir toast de erro
            const toast = document.createElement('div');
            toast.className = 'notification error';
            toast.innerHTML = `
                <div class="icon">
                    <i class="bi bi-x-circle"></i>
                </div>
                <div class="title">
                    <h1>Error</h1>
                    <h6>${error.message || 'An unexpected error occurred. Please try again.'}</h6>
                </div>
                <div class="close" onclick="this.parentElement.remove()">
                    <i class="bi bi-x"></i>
                </div>
            `;

            let notificationBox = document.querySelector('.notification-box');
            if (!notificationBox) {
                notificationBox = document.createElement('div');
                notificationBox.className = 'notification-box';
                document.body.appendChild(notificationBox);
            }
            notificationBox.appendChild(toast);

            setTimeout(() => toast.remove(), 5000);
        });

}


/************** Book-read **************/
document.addEventListener("DOMContentLoaded", () => {
    // Verificar se jQuery está carregado
    if (typeof jQuery === 'undefined') {
        // Carregar jQuery dinamicamente
        const script = document.createElement('script');
        script.src = 'https://code.jquery.com/jquery-3.6.0.min.js';
        script.onload = initializeBookReader;
        document.head.appendChild(script);
        return;
    }

    initializeBookReader();
});

function initializeBookReader() {

    const tabs = document.querySelectorAll("#book-menu-tabs .nav-link");
    const sections = document.querySelectorAll(".content-section");
    let isReadingSliderInitialized = false;
    let currentProgress = window.initialProgress || 0;
    let lastSavedProgress = window.initialProgress || 0;
    let activeReadingTab = false;
    let saveProgressTimeout = null;
    let resizeTimeout = null;

    // Função para calcular dimensões ideais do livro
    const calculateBookDimensions = () => {
        const container = document.querySelector('.book-slider-container');
        if (!container) return { width: 800, height: 600 };

        const padding = 40;
        const containerWidth = container.offsetWidth - padding;
        const containerHeight = container.offsetHeight - padding;
        const ratio = 4/3;

        let width = containerWidth;
        let height = width / ratio;

        if (height > containerHeight) {
            height = containerHeight;
            width = height * ratio;
        }

        return {
            width: Math.floor(width),
            height: Math.floor(height)
        };
    };


    const initReadingSlider = () => {
        if (isReadingSliderInitialized) {
            return;
        }

        const slider = jQuery("#book-slider");
        const prevButton = document.getElementById("prev-slide");
        const nextButton = document.getElementById("next-slide");
        const progressBar = document.getElementById("reading-progress-bar");
        const progressText = document.getElementById("progress-text");


        if (!slider.length || !prevButton || !nextButton) {
            return;
        }

        // Inicializar Turn.js
        try {
            const totalPages = slider.children('.page').length;
            const startPage = Math.max(1, Math.floor((currentProgress / 100) * totalPages));
            const dimensions = calculateBookDimensions();

            slider.turn({
                ...dimensions,
                autoCenter: true,
                duration: 1000,
                gradients: true,
                elevation: 100,
                page: startPage,
                display: 'double',
                acceleration: true,
                peel: false,
                when: {
                    turning: function(e, page) {
                        // Verifica se está indo para a última página (página de comentários)
                        if (page === totalPages) {
                            e.preventDefault(); // Previne a virada normal
                            $(this).turn('size', dimensions.width * 2, dimensions.height);
                            setTimeout(() => {
                                $(this).turn('page', totalPages);
                            }, 100);
                        } else if ((page === totalPages - 1) && e.target === this) {
                            // Se estiver indo para a penúltima página, vai direto para a última
                            e.preventDefault();
                            $(this).turn('size', dimensions.width * 2, dimensions.height);
                            setTimeout(() => {
                                $(this).turn('page', totalPages);
                            }, 100);
                        } else {
                            // Retorna ao tamanho normal para outras páginas
                            $(this).turn('size', dimensions.width, dimensions.height);
                        }
                    },
                    start: function(e, pageObject) {
                        // Aplica o layout correto na inicialização
                        if (pageObject.page === totalPages) {
                            $(this).turn('size', dimensions.width * 2, dimensions.height);
                        }
                    },
                    turned: function(e, page) {
                        // Garante o layout correto após a página ser virada
                        if (page === totalPages) {
                            $(this).turn('size', dimensions.width * 2, dimensions.height);
                        }
                    }
                }
            });

            // Verificação inicial para página de comentários
            if (startPage === totalPages || startPage === totalPages - 1) {
                slider.turn('size', dimensions.width * 2, dimensions.height);
                if (startPage === totalPages - 1) {
                    setTimeout(() => {
                        slider.turn('page', totalPages);
                    }, 100);
                }
            }


            // Ajustar o livro quando a janela for redimensionada
            window.addEventListener('resize', () => {
                clearTimeout(resizeTimeout);
                resizeTimeout = setTimeout(() => {
                    const dimensions = calculateBookDimensions();
                    slider.turn('size', dimensions.width, dimensions.height);
                }, 200);
            });

            const calculateProgress = (currentPage) => {
                // Se a página atual for maior que o total de páginas reais do livro,
                // significa que estamos na página de comentários
                if (currentPage > totalBookPages) {
                    return 100; // Retorna 100% pois o livro foi completamente lido
                }
                return Math.round((currentPage / totalBookPages) * 100);
            };

            const updateProgressDisplay = (page) => {
                const progress = calculateProgress(page);

                if (progressBar && progressText) {
                    progressBar.style.width = `${progress}%`;
                    progressBar.setAttribute("aria-valuenow", progress);
                    progressText.innerHTML = `<span style="color: orange;">${progress}%</span>`;
                }
                currentProgress = progress;

                if (progress > lastSavedProgress) {
                    if (saveProgressTimeout) {
                        clearTimeout(saveProgressTimeout);
                    }
                    saveProgressTimeout = setTimeout(() => {
                        saveProgressToServer(progress);
                    }, 2000);
                }
            };

            const saveProgressToServer = (progress) => {
                const isLoggedIn = document.body.dataset.userLoggedIn === "true";

                if (!isLoggedIn || progress <= lastSavedProgress) {
                    return;
                }

                const token = document.querySelector('meta[name="csrf-token"]').content;
                const bookId = slider.data("bookId");

                fetch(`/book-details/${bookId}/progress`, {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": token,
                        "Content-Type": "application/json",
                        Accept: "application/json",
                    },
                    body: JSON.stringify({ progress })
                })
                    .then(response => response.ok ? response.json() : Promise.reject('Network response was not ok'))
                    .then(data => {
                        if (data.success) {
                            lastSavedProgress = progress;
                        }
                    })
                    .catch(error => console.error("Error saving progress:", error));
            };

            const nextPage = () => {
                if (slider.turn('page') < totalPages) {
                    slider.turn("next");
                }
            };

            const prevPage = () => {
                if (slider.turn('page') > 1) {
                    slider.turn("previous");
                }
            };

            // Adicionar eventos de clique nos botões
            nextButton.addEventListener("click", nextPage);
            prevButton.addEventListener("click", prevPage);

            // Eventos de teclado
            document.addEventListener("keydown", (e) => {
                if (!activeReadingTab) return;

                if (e.key === "ArrowRight" || e.key === "Space") {
                    nextPage();
                }
                if (e.key === "ArrowLeft") {
                    prevPage();
                }
            });

            // Suporte a gestos touch
            let touchStartX = 0;
            slider.on('touchstart', function(e) {
                touchStartX = e.originalEvent.touches[0].pageX;
            });

            slider.on('touchmove', function(e) {
                if (!touchStartX) return;

                const touchEndX = e.originalEvent.touches[0].pageX;
                const diff = touchStartX - touchEndX;

                if (Math.abs(diff) > 50) {
                    if (diff > 0) nextPage();
                    else prevPage();
                    touchStartX = 0;
                }
            });

            // Atualizar progresso ao virar páginas
            slider.on("turned", function(event, page) {
                updateProgressDisplay(page);
            });

            isReadingSliderInitialized = true;

            // Garantir que o progresso seja salvo ao sair
            window.addEventListener('beforeunload', () => {
                if (currentProgress > lastSavedProgress) {
                    saveProgressToServer(currentProgress);
                }
            });

            // Inicializar com o progresso correto
            updateProgressDisplay(startPage);

        } catch (error) {

        }
    };

    tabs.forEach((tab) => {
        tab.addEventListener("click", (event) => {
            event.preventDefault();
            const category = tab.getAttribute("data-category");

            const isUserLoggedIn = document.body.dataset.userLoggedIn === "true";

            if (isUserLoggedIn) {
                if (activeReadingTab && category !== "read" && currentProgress > lastSavedProgress) {
                    if (saveProgressTimeout) {
                        clearTimeout(saveProgressTimeout);
                    }
                    saveProgressToServer(currentProgress);
                }
            }

            activeReadingTab = category === "read";

            tabs.forEach((t) => t.classList.remove("active"));
            tab.classList.add("active");

            sections.forEach((section) => {
                section.classList.add("hidden");
                if (section.id === `${category}-section`) {
                    section.classList.remove("hidden");
                    if (category === "read") {
                        initReadingSlider();
                    }
                }
            });
        });
    });

    // Ativar a aba inicial
    const initialActiveTab = document.querySelector("#book-menu-tabs .nav-link.active");
    if (initialActiveTab) {
        initialActiveTab.click();
    }

}


// estrelas de avaliação
document.addEventListener('DOMContentLoaded', function() {
    const stars = document.querySelectorAll('.rating-star');
    const ratingInput = document.getElementById('rating-value');

    // Função para atualizar a visualização das estrelas
    function updateStars(selectedValue) {
        stars.forEach(star => {
            const value = parseInt(star.dataset.value);
            if (value <= selectedValue) {
                star.classList.remove('bi-star');
                star.classList.add('bi-star-fill');
                star.style.color = 'orange';
            } else {
                star.classList.remove('bi-star-fill');
                star.classList.add('bi-star');
                star.style.color = '#ccc';
            }
        });
    }

    // Event listeners para cada estrela
    stars.forEach(star => {
        // Evento de hover
        star.addEventListener('mouseover', () => {
            const value = parseInt(star.dataset.value);
            updateStars(value);
        });

        // Evento de click
        star.addEventListener('click', () => {
            const value = parseInt(star.dataset.value);
            if (value === parseInt(ratingInput.value)) {
                // Desmarca a avaliação se clicar novamente na mesma estrela
                ratingInput.value = 0;
                updateStars(0);
            } else {
                // Define a nova avaliação
                ratingInput.value = value;
                updateStars(value);
            }
        });
    });

    // Reset ao tirar o mouse
    const starContainer = document.querySelector('.star-rating');
    if (starContainer) { // Verifica se o elemento existe
        starContainer.addEventListener('mouseleave', () => {
            const selectedRating = parseInt(ratingInput.value) || 0;
            updateStars(selectedRating);
        });
    }
});


// Função para contar um click
function trackBookClick(bookId) {
    fetch(`/books/${bookId}/click`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    });
}



/************** Book Activity Progress **************/
document.addEventListener("DOMContentLoaded", () => {
    let isActivitiesInitialized = false;

    // Inicializar progresso das atividades quando a aba é aberta
    const initActivitiesProgress = () => {
        if (isActivitiesInitialized) return;

        const activitiesSection = document.querySelector("#activities-section");
        if (!activitiesSection) return;

        const progressBars = activitiesSection.querySelectorAll('[data-activity-progress]');

        progressBars.forEach(progressBar => {
            const progressId = progressBar.dataset.activityProgress;
            const [_, activityId, __, bookId] = progressId.split('_');

            fetch(`/activities/${activityId}/check-progress?book_id=${bookId}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        progressBar.style.width = `${data.progress}%`;
                        progressBar.setAttribute('aria-valuenow', data.progress);

                        const progressText = document.querySelector(`[data-progress-text="${progressId}"]`);
                        if (progressText) {
                            progressText.innerHTML = `<span style="color: orange;">${data.progress}%</span>`;
                        }
                    }
                })
        });

        isActivitiesInitialized = true;
    };

    // Adicionar listener para a aba de atividades
    const tabs = document.querySelectorAll("#book-menu-tabs .nav-link");
    tabs.forEach(tab => {
        tab.addEventListener("click", (event) => {
            const category = tab.getAttribute("data-category");
            if (category === "activities") {
                initActivitiesProgress();
            }
        });
    });
});


// Função para download e atualização de progresso
function downloadAndUpdateProgress(button, imageUrl) {
    const activityId = button.dataset.activityId;
    const bookId = button.dataset.bookId;
    const imageIndex = parseInt(button.dataset.imageIndex);
    const totalImages = parseInt(button.dataset.totalImages);


    // Calcular progresso
    const progressPerImage = 100 / totalImages;
    const progress = Math.min(100, Math.round((imageIndex + 1) * progressPerImage));

    // Iniciar download
    fetch(imageUrl)
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.blob();
        })
        .then(blob => {
            // Criar link de download
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'activity-' + (imageIndex + 1) + '.jpg';
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            a.remove();

            // Atualizar progresso
            return updateActivityProgress(activityId, bookId, progress);
        })
        .catch(error => {
            console.error('Download failed:', error);
            alert('Failed to download the activity. Please try again.');
        });
}

// Função para atualizar o progresso
function updateActivityProgress(activityId, bookId, progress) {
    const token = document.querySelector('meta[name="csrf-token"]').content;
    const progressId = `act_${activityId}_book_${bookId}`;


    return fetch(`/activities/${activityId}/progress`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            progress: progress,
            book_id: bookId
        })
    })
        .then(async response => {
            const data = await response.json();
            if (!response.ok) {
                throw new Error(data.message || 'Failed to update progress');
            }
            return data;
        })
        .then(data => {
            if (data.success) {
                const progressBar = document.querySelector(`[data-activity-progress="${progressId}"]`);
                const progressText = document.querySelector(`[data-progress-text="${progressId}"]`);

                if (progressBar && progressText) {
                    progressBar.style.width = `${progress}%`;
                    progressBar.setAttribute('aria-valuenow', progress);
                    progressText.innerHTML = `<span style="color: orange;">${progress}%</span>`;
                }

                // Salvar localmente o último progresso
                localStorage.setItem(`activity_${activityId}_progress`, progress);
            }
        })
        .catch(error => {
            console.error('Error updating progress:', error);
            alert('Failed to update progress. Please try again.');
        });
}

// Adicionar função para carregar o progresso ao carregar a página
document.addEventListener('DOMContentLoaded', () => {
    const activityCards = document.querySelectorAll('.activity-card');

    activityCards.forEach(card => {
        const button = card.querySelector('button[data-activity-id]');
        if (button) {
            const activityId = button.dataset.activityId;
            const bookId = button.dataset.bookId;
            const progressId = `act_${activityId}_book_${bookId}`;

            // Verificar progresso salvo
            fetch(`/activities/${activityId}/check-progress?book_id=${bookId}`, {
                headers: {
                    'Accept': 'application/json'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const progressBar = document.querySelector(`[data-activity-progress="${progressId}"]`);
                        const progressText = document.querySelector(`[data-progress-text="${progressId}"]`);

                        if (progressBar && progressText) {
                            progressBar.style.width = `${data.progress}%`;
                            progressBar.setAttribute('aria-valuenow', data.progress);
                            progressText.innerHTML = `<span style="color: orange;">${data.progress}%</span>`;
                        }
                    }
                })
                .catch(console.error);
        }
    });
});


// ########################### ADMIN JAVA ###########################

/************** ADMIN **************/

/************** Sidebar Admin **************/
function toggleSidebar() {
    const sidebar = document.querySelector('.sidebar');
    sidebar.classList.toggle('show');
}

/************** Formulários **************/

document.addEventListener('DOMContentLoaded', function() {
    // Configurações base para todos os selects
    const commonConfig = {
        itemSelectText: '',
        placeholder: true,
    };

    // Configurações para selects simples
    const singleSelectConfig = {
        ...commonConfig,
        searchEnabled: false,
        shouldSort: false,
    };

    // Configurações para selects múltiplos
    const multipleSelectConfig = {
        ...commonConfig,
        removeItemButton: true,
        maxItemCount: -1,
        searchEnabled: true,
    };

    // Função para inicializar Choices.js apenas se ainda não foi inicializado
    function initializeChoices(selector, config) {
        const element = document.querySelector(selector);
        if (element && !element.choicesInstance) {
            element.choicesInstance = new Choices(element, config);
        }
    }

    // Tornar a função de inicialização globalmente acessível
    window.initializeChoices = initializeChoices;

    // Inicializar selects com as configurações adequadas
    const ageGroupSelect = document.querySelector('#age_group_id');
    if (ageGroupSelect) {
        initializeChoices('#age_group_id', {
            ...singleSelectConfig,
            placeholderValue: 'Select Age Group',
        });
    }

    const accessLevelSelect = document.querySelector('#access_level');
    if (accessLevelSelect) {
        initializeChoices('#access_level', {
            ...singleSelectConfig,
            placeholderValue: 'Select Access Level',
        });
    }

    const tagsSelect = document.querySelector('#tags');
    if (tagsSelect) {
        initializeChoices('#tags', {
            ...multipleSelectConfig,
            placeholderValue: 'Select Tags',
        });
    }

    // Função de preview de imagens atualizada
    function createImagePreview(file, container, template) {
        if (!file || !container) return;

        const reader = new FileReader();
        reader.onload = function(e) {
            container.insertAdjacentHTML('beforeend', template(e.target.result));
        };
        reader.readAsDataURL(file);
    }

    // Preview da capa
    const coverInput = document.getElementById('cover_url');
    const coverPreviewContainer = document.getElementById('coverPreview');
    if (coverInput && coverPreviewContainer) {
        coverInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            const template = (src) => `
                <div class="position-relative">
                    <div class="delete-image" onclick="removeCoverPreview(this)">×</div>
                    <img src="${src}"
                        alt="Cover preview"
                        style="width: 70px; height: 100px; object-fit: cover; border-radius: 4px;">
                </div>
            `;
            createImagePreview(file, coverPreviewContainer, template);
        });
    }

    // Preview das páginas
    const pagesInput = document.getElementById('pages');
    const pagePreviewDiv = document.getElementById('pagePreview');
    if (pagesInput && pagePreviewDiv) {
        pagesInput.addEventListener('change', function(e) {
            pagePreviewDiv.innerHTML = ''; // Limpa previews anteriores

            Array.from(e.target.files).forEach((file, index) => {
                const template = (src) => `
                    <div class="position-relative">
                        <div class="delete-image" onclick="removePagePreview(this, ${index})">×</div>
                        <img src="${src}"
                             alt="Page preview"
                             style="width: 70px; height: 100px; object-fit: cover; border-radius: 4px;">
                        <input type="text"
                               class="form-control form-control-sm mt-1"
                               name="page_index[]"
                               placeholder="Page"
                               required
                               style="width: 70px;">
                    </div>
                `;
                createImagePreview(file, pagePreviewDiv, template);
            });
        });
    }

    // Preview do vídeo do YouTube
    const videoInput = document.getElementById('video_url');
    const videoPreviewContainer = document.getElementById('video-preview');
    if (videoInput && videoPreviewContainer) {
        videoInput.addEventListener('input', function () {
            const videoUrl = this.value;
            const videoId = getYoutubeId(videoUrl);

            // Limpa previews anteriores
            const existingPreviews = videoPreviewContainer.querySelectorAll('.video-preview');
            existingPreviews.forEach(preview => preview.remove());

            if (videoId) {
                const preview = document.createElement('div');
                preview.className = 'video-preview mt-2 position-relative';
                preview.innerHTML = `
                    <div class="delete-image" onclick="removeVideoPreview()">×</div>
                    <iframe width="300" height="169"
                            src="https://www.youtube.com/embed/${videoId}"
                            frameborder="0" allowfullscreen>
                    </iframe>
                `;
                videoPreviewContainer.appendChild(preview);
            }
        });
    }

    function getYoutubeId(url) {
        if (!url) return null;
        const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|&v=)([^#&?]*).*/;
        const match = url.match(regExp);
        return (match && match[2].length === 11) ? match[2] : null;
    }

    // Funções globais para remover previews
    window.removeCoverPreview = function(element) {
        const coverInput = document.getElementById('cover_url');
        if (coverInput) {
            coverInput.value = '';
        }
        element.closest('.position-relative').remove();
    };

    window.removePagePreview = function(element, index) {
        const pagesInput = document.getElementById('pages');
        if (pagesInput) {
            const dt = new DataTransfer();
            const { files } = pagesInput;
            for (let i = 0; i < files.length; i++) {
                if (i !== index) {
                    dt.items.add(files[i]);
                }
            }
            pagesInput.files = dt.files;
        }
        element.closest('.position-relative').remove();
    };

    window.removeVideoPreview = function() {
        const videoInput = document.getElementById('video_url');
        const videoPreviewContainer = document.getElementById('video-preview');
        if (videoInput) {
            videoInput.value = '';
        }
        if (videoPreviewContainer) {
            const preview = videoPreviewContainer.querySelector('.video-preview');
            if (preview) {
                preview.remove();
            }
        }
    };
});

// Funções de remoção das pré-visualizações
function removeCoverPreview(element) {
    element.closest('.position-relative').remove();
    document.getElementById('cover_url').value = '';
}

function removePagePreview(element) {
    const previewContainer = document.getElementById('pagePreview');

    // Remove o elemento de pré-visualização selecionado
    element.closest('.position-relative').remove();

    // Verifica se ainda existem pré-visualizações
    const remainingPreviews = previewContainer.querySelectorAll('.position-relative');
    if (remainingPreviews.length === 0) {
        // Reseta o valor do input de arquivos se não houver mais pré-visualizações
        document.getElementById('pages').value = '';
    } else {
        // Atualiza os placeholders das páginas restantes
        remainingPreviews.forEach((div, index) => {
            const input = div.querySelector('input');
            input.placeholder = `Page ${index + 1} order`;
        });
    }
}

// Função para remover o preview do vídeo
function removeVideoPreview() {
    document.getElementById('video_url').value = ''; // Limpa o input
    const previewContainer = document.querySelector('.video-preview');
    if (previewContainer) {
        previewContainer.remove(); // Remove o preview
    }
}

// Validação de formulário
document.querySelector('form').addEventListener('submit', function (e) {
    const requiredFields = document.querySelectorAll('[required]');
    let hasErrors = false;

    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            hasErrors = true;
            field.classList.add('is-invalid');
            const error = document.createElement('div');
            error.classList.add('invalid-feedback');
            error.textContent = 'This field is required.';
            if (!field.nextElementSibling) {
                field.parentElement.appendChild(error);
            }
        } else {
            field.classList.remove('is-invalid');
            if (field.nextElementSibling) {
                field.parentElement.removeChild(field.nextElementSibling);
            }
        }
    });

    if (hasErrors) {
        e.preventDefault();
    }
});

// TOAST para avisos de login/logout e mensagens de sucesso Admin
const createToast = (type, message) => {
    const box = document.querySelector(".notification-box");
    if (!box) return;

    let icon, title;
    switch(type) {
        case 'success':
            icon = "fa-solid fa-circle-check";
            title = "Success";
            break;
        case 'error':
            icon = "fa-solid fa-circle-exclamation";
            title = "Error";
            break;
        case 'warning':
            icon = "fa-solid fa-triangle-exclamation";
            title = "Warning";
            break;
        case 'info':
            icon = "fa-solid fa-circle-info";
            title = "Info";
            break;
    }

    const element = document.createElement("div");
    element.innerHTML = `
        <div class="notification ${type}">
            <div class="icon">
                <i class="${icon}"></i>
            </div>
            <div class="title">
                <h1>${title}</h1>
                <h6>${message}</h6>
            </div>
            <div class="close" onclick="(this.parentElement.parentElement).remove()">
                <i class="fa-solid fa-xmark"></i>
            </div>
        </div>
    `;

    box.appendChild(element);
    setTimeout(() => element.remove(), 5000);
};

// Inicialização das toast notifications
document.addEventListener('DOMContentLoaded', () => {
    const flashType = document.querySelector('meta[name="flash-type"]')?.content;
    const flashMessage = document.querySelector('meta[name="flash-message"]')?.content;

    if (flashType && flashMessage) {
        createToast(flashType, flashMessage);
    }
});


// DataPicker para a data de nascimento
document.addEventListener('DOMContentLoaded', function() {
    const birthDateInput = document.querySelector('input[name="birth_date"]');
    if (birthDateInput) {
        flatpickr(birthDateInput, {
            dateFormat: "Y-m-d",
            maxDate: "today",
            minDate: "1900-01-01",
            disableMobile: true,
            monthSelectorType: "static",
            allowInput: true,
            locale: "en",
            onChange: function(selectedDates, dateStr) {
                // Callback para manipular a data selecionada, caso necessário
            }
        });
    }
});
