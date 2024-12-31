<div class="container mt-4">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Books Read</h4>
            <ul id="books-read" class="list-group">
                <!-- Will be filled by AJAX -->
            </ul>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-body">
            <h4 class="card-title">Favorite Books</h4>
            <ul id="favorite-books" class="list-group">
                <!-- Will be filled by AJAX -->
            </ul>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-body">
            <h4 class="card-title">Suggested Books</h4>
            <ul id="suggested-books" class="list-group">
                <!-- Will be filled by AJAX -->
            </ul>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        let timeout;
        $('#user-select').change(function () {
            clearTimeout(timeout);
            var userId = $(this).val();
            if (userId) {
                $('#books-read').html('<li class="list-group-item">Loading...</li>');
                $('#favorite-books').html('<li class="list-group-item">Loading...</li>');
                $('#suggested-books').html('<li class="list-group-item">Loading...</li>');

                timeout = setTimeout(() => {
                    $.when(
                        $.ajax({ url: '/api/users/' + userId + '/books' }),
                        $.ajax({ url: '/api/users/' + userId + '/suggested-books' })
                    ).done(function(booksData, suggestedBooksData) {
                        const books = booksData[0];
                        $('#books-read').empty();
                        if (books.books_read.length === 0) {
                            $('#books-read').append('<li class="list-group-item">No books read yet.</li>');
                        } else {
                            $.each(books.books_read, function (index, book) {
                                $('#books-read').append('<li class="list-group-item">' + book.title + '</li>');
                            });
                        }

                        $('#favorite-books').empty();
                        if (books.favorite_books.length === 0) {
                            $('#favorite-books').append('<li class="list-group-item">No favorite books yet.</li>');
                        } else {
                            $.each(books.favorite_books, function (index, book) {
                                $('#favorite-books').append('<li class="list-group-item">' + book.title + '</li>');
                            });
                        }

                        const suggestedBooks = suggestedBooksData[0];
                        $('#suggested-books').empty();
                        if (suggestedBooks.length === 0) {
                            $('#suggested-books').append('<li class="list-group-item">No suggestions available.</li>');
                        } else {
                            $.each(suggestedBooks, function (index, book) {
                                $('#suggested-books').append('<li class="list-group-item">' + book.title + '</li>');
                            });
                        }
                    }).fail(function() {
                        alert('Error loading user data.');
                    });
                }, 300);
            } else {
                $('#books-read, #favorite-books, #suggested-books').empty();
            }
        });

        $('#user-select').trigger('change');
    });
</script>
