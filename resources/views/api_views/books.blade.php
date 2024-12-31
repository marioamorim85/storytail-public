<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Books List with Filters</title>
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">Books List</h2>

    <!-- Filters Form -->
    <form id="filters-form">
        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="title">Book Title</label>
                <input type="text" class="form-control" id="title" name="title" placeholder="Book title">
            </div>
            <div class="form-group col-md-4">
                <label for="author_name">Author Name</label>
                <input type="text" class="form-control" id="author_name" name="author_name" placeholder="Author name">
            </div>
            <div class="form-group col-md-4">
                <label for="age_group_id">Age Group</label>
                <select id="age_group_id" name="age_group_id" class="form-control">
                    <option value="">Select Age Group</option>
                    <!-- Age groups will be loaded here dynamically -->
                </select>
            </div>
        </div>

        <!-- New Tag Filter -->
        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="tag_id">Tag</label>
                <select id="tag_id" name="tag_id" class="form-control">
                    <option value="">Select a Tag</option>
                    <!-- Tags will be loaded here dynamically -->
                </select>
            </div>

            <div class="form-group col-md-4">
                <label for="access_level">Access Level</label>
                <select id="access_level" name="access_level" class="form-control">
                    <option value="">Select Access Level</option>
                    <option value="1">Free</option>
                    <option value="2">Premium</option>
                </select>
            </div>
            <div class="form-group col-md-4">
                <label for="is_active">Active Status</label>
                <select id="is_active" name="is_active" class="form-control">
                    <option value="">Select Status</option>
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="min_read_time">Min Read Time (minutes)</label>
                <input type="number" class="form-control" id="min_read_time" name="min_read_time" placeholder="Min read time">
            </div>
            <div class="form-group col-md-4">
                <label for="max_read_time">Max Read Time (minutes)</label>
                <input type="number" class="form-control" id="max_read_time" name="max_read_time" placeholder="Max read time">
            </div>
        </div>

        <!-- Apply and Reset Buttons -->
        <div class="form-row">
            <div class="form-group col-md-2">
                <button type="button" id="apply-filters" class="btn btn-secondary btn-block">Apply Filters</button>
            </div>
            <div class="form-group col-md-2">
                <button type="button" id="reset-filters" class="btn btn-secondary btn-block">Reset Filters</button>
            </div>
        </div>
    </form>

    <!-- Books List -->
    <div id="books-list" class="mt-5">
        <!-- Books will be loaded here -->
    </div>

    <!-- Return to Home Button -->
    <div class="mb-4">
        <a href="{{ url('/api_views/') }}" class="btn btn-secondary">Return to Home</a>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        function loadBooks() {
            $.ajax({
                url: "{{ route('books.filter') }}",
                method: 'GET',
                data: $('#filters-form').serialize(),
                success: function (response) {
                    $('#books-list').html(response);
                }
            });
        }

        // Função para carregar as tags
        function loadTags() {
            $.ajax({
                url: "{{ route('tags') }}", // Rota para buscar tags
                method: 'GET',
                success: function (data) {
                    let tagSelect = $('#tag_id');
                    tagSelect.empty();
                    tagSelect.append('<option value="">Select a Tag</option>');
                    data.forEach(function (tag) {
                        tagSelect.append('<option value="' + tag.id + '">' + tag.name + '</option>');
                    });
                }
            });
        }

        // Carrega os livros e as tags ao carregar a página
        loadBooks();
        loadTags();

        // Função para carregar os grupos etários
        function loadAgeGroups() {
            $.ajax({
                url: "{{ route('age_groups') }}", // Rota para buscar grupos etários
                method: 'GET',
                success: function (data) {
                    let ageGroupSelect = $('#age_group_id');
                    ageGroupSelect.empty();
                    ageGroupSelect.append('<option value="">Select Age Group</option>');
                    data.forEach(function (ageGroup) {
                        ageGroupSelect.append('<option value="' + ageGroup.id + '">' + ageGroup.age_group + '</option>');
                    });
                }
            });
        }

        // Aplica filtros quando o botão "Apply Filters" é clicado
        $('#apply-filters').on('click', function () {
            loadBooks();
        });

        // Reseta os filtros quando o botão "Reset Filters" é clicado
        $('#reset-filters').on('click', function () {
            $('#filters-form')[0].reset(); // Reseta o formulário
            loadBooks(); // Recarrega os livros sem filtros
        });

        // Carrega os grupos etários
        loadAgeGroups();
    });

    // Script para alternar as atividades
    $(document).on('click', '.toggle-activities', function () {
        var bookId = $(this).data('book-id');
        var activitiesDiv = $('#activities-' + bookId);
        activitiesDiv.toggle(); // Alterna a visibilidade das atividades

        // Muda o texto do botão com base na visibilidade das atividades
        if (activitiesDiv.is(':visible')) {
            $(this).text('Hide Activities');
        } else {
            $(this).text('See Activities');
        }
    });
</script>
</body>
</html>
