<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users List</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <div class="card">
        <div class="card-header bg-secondary text-white">
            <h2 class="mb-0">User List</h2>
        </div>
        <div class="card-body">
            <div class="form-group">
                <label for="user-select">Select a User:</label>
                <select id="user-select" class="form-control">
                    <option value="">Select a user</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->first_name }} {{ $user->last_name }}</option>
                    @endforeach
                </select>
            </div>

            <div id="user-profile" style="display: none;">
                @include('api_views.partials.profile') <!-- Include the profile partial -->
            </div>

            <!-- Return to Home Button -->
            <div class="mb-4">
                <a href="{{ url('/api_views/') }}" class="btn btn-secondary">Return to Home</a>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        $('#user-select').change(function () {
            var userId = $(this).val();
            if (userId) {
                $.ajax({
                    url: '/api_views/users/' + userId + '/profile',
                    method: 'GET',
                    success: function (data) {
                        $('#user-profile').html(data).show();
                    },
                    error: function () {
                        alert('Error loading user profile.');
                    }
                });
            } else {
                $('#user-profile').hide(); // Hide the profile section
            }
        });
    });
</script>
</body>
</html>
