<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5">
    <h2 class="mb-4">Admin Dashboard</h2>

    <div class="row">
        <!-- Cartão de Popular Books -->
        <div class="col-md-6 mb-3 d-flex align-items-stretch">
            <div class="card flex-fill">
                <div class="card-body">
                    <h5 class="card-title">Popular Books</h5>
                    <p class="card-text">View the most popular books in the last 3 months.</p>
                    <button id="toggle-popular-books" class="btn btn-secondary">View Report</button>
                    <div id="popular-books-list" class="mt-3" style="display: none;"></div>
                </div>
            </div>
        </div>

        <!-- Cartão de Peak Usage Times -->
        <div class="col-md-6 mb-3 d-flex align-items-stretch">
            <div class="card flex-fill">
                <div class="card-body">
                    <h5 class="card-title">Peak Usage Times</h5>
                    <p class="card-text">View the peak usage times based on clicks.</p>
                    <button id="toggle-peak-times" class="btn btn-secondary">View Report</button>
                    <div id="peak-usage-times-list" class="mt-3" style="display: none;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Return to Home Button -->
    <div class="mb-4 mt-3">
        <a href="{{ url('/api_views/') }}" class="btn btn-secondary">Return to Home</a>
    </div>

</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        // Toggle Popular Books report visibility
        $('#toggle-popular-books').on('click', function () {
            var booksDiv = $('#popular-books-list');
            booksDiv.toggle();  // Toggle the visibility

            if (booksDiv.is(':visible')) {
                $('#toggle-popular-books').text('Hide Report');
                $.ajax({
                    url: "{{ route('admin.popular-books.json') }}",
                    method: 'GET',
                    success: function (response) {
                        let booksList = '<ul>';
                        response.forEach(function (book) {
                            booksList += '<li>' + book.book.title + ' - ' + book.clicks_count + ' clicks</li>';
                        });
                        booksList += '</ul>';
                        $('#popular-books-list').html(booksList);
                    }
                });
            } else {
                $('#toggle-popular-books').text('View Report');
            }
        });

        // Toggle Peak Usage Times report visibility
        $('#toggle-peak-times').on('click', function () {
            var timesDiv = $('#peak-usage-times-list');
            timesDiv.toggle();  // Toggle the visibility

            if (timesDiv.is(':visible')) {
                $('#toggle-peak-times').text('Hide Report');
                $.ajax({
                    url: "{{ route('admin.peak-usage-times.json') }}",
                    method: 'GET',
                    success: function (response) {
                        let timesList = '<ul>';
                        response.forEach(function (time) {
                            var nextHour = (parseInt(time.hour) + 1) % 24;  // Calcula a hora seguinte, considerando 24 horas
                            timesList += '<li>' + time.hour + ':00/' + nextHour + ':00 - ' + time.clicks_count + ' clicks</li>';
                        });

                        timesList += '</ul>';
                        $('#peak-usage-times-list').html(timesList);
                    }
                });
            } else {
                $('#toggle-peak-times').text('View Report');
            }
        });
    });
</script>

</body>
</html>
