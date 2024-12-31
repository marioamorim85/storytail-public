<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StoryTail</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2>Welcome to the Storytail Dashboard</h2>
    <div class="mt-4">
        <a href="{{ url('/api_views/books') }}" class="btn btn-secondary">View Books</a>
        <a href="{{ url('/api_views/admin') }}" class="btn btn-secondary">Admin Dashboard</a>
        <a href="{{ url('/api_views/users') }}" class="btn btn-secondary">User Profiles</a>
    </div>
</div>
</body>
</html>

