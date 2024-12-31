@extends('admin.dashboard-layout')

@section('dashboard-content')
    <h1 class="custom-title">Add New User</h1>

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

    <form method="POST" action="{{ route('admin.users.store') }}" enctype="multipart/form-data">
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

                <!-- Birth Date -->
                <div class="mb-3">
                    <label for="birth_date" class="form-label fw-bold">Date of Birth</label>
                    <input type="text" class="form-control" id="birth_date" name="birth_date"
                           value="{{ old('birth_date') }}" placeholder="Select date" required style="height: 47px">
                </div>


                <!-- Email -->
                <div class="mb-3">
                    <label for="email" class="form-label fw-bold">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required style="height: 47px">
                </div>

                <!-- Password -->
                <div class="form-group mb-3">
                    <label for="password" class="form-label fw-bold">Password</label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="password" name="password" required>
                        <span class="input-group-text toggle-password" onclick="togglePasswordVisibility('password', 'togglePasswordIcon')">
                            <i id="togglePasswordIcon" class="bi bi-eye"></i>
                        </span>
                    </div>
                </div>

                <!-- Confirm Password -->
                <div class="form-group mb-3">
                    <label for="password_confirmation" class="form-label fw-bold">Confirm Password</label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                        <span class="input-group-text toggle-password" onclick="togglePasswordVisibility('password_confirmation', 'toggleConfirmPasswordIcon')">
                            <i id="toggleConfirmPasswordIcon" class="bi bi-eye"></i>
                        </span>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <!-- User Type -->
                <div class="mb-3">
                    <label for="user_type_id" class="form-label fw-bold">User Type</label>
                    <select class="form-control" id="user_type_id" name="user_type_id" required style="height: 47px">
                        <option value="">Select User Type</option>
                        @foreach($userTypes as $type)
                            <option value="{{ $type->id }}" {{ old('user_type_id') == $type->id ? 'selected' : '' }}>
                                {{ $type->user_type }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Status -->
                <div class="mb-3">
                    <label for="status" class="form-label fw-bold">Status</label>
                    <select class="form-control" id="status" name="status" required style="height: 47px">
                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="suspended" {{ old('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <!-- Plan -->
                <div class="mb-3">
                    <label for="plan_id" class="form-label fw-bold">Plan</label>
                    <select class="form-control" id="plan_id" name="plan_id" style="height: 47px">
                        <option value="">No Plan</option>
                        @foreach($plans as $plan)
                            <option value="{{ $plan->id }}" {{ old('plan_id') == $plan->id ? 'selected' : '' }}>
                                {{ $plan->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Photo -->
                <div class="mb-3">
                    <label for="user_photo_url" class="form-label fw-bold">Profile Photo</label>
                    <input type="file" class="form-control" id="user_photo_url"
                           name="user_photo_url" accept="image/*">
                    <div class="mt-2">
                        <div id="photoPreview" class="d-flex justify-content-center"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4 d-flex gap-4">
            <button type="submit" class="btnAdmin">Create User</button>
            <a href="{{ route('admin.users.list') }}" class="btnAdminSecundary">Cancel</a>
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
                // Initialize Choices.js for selects
                ['user_type_id', 'status', 'plan_id'].forEach(selectId => {
                    new Choices(`#${selectId}`, {
                        searchEnabled: true,
                        itemSelectText: '',
                        placeholder: true
                    });
                });

                // Photo preview
                const photoInput = document.getElementById('user_photo_url');
                const photoPreview = document.getElementById('photoPreview');

                photoInput.addEventListener('change', function(e) {
                    photoPreview.innerHTML = '';
                    const file = e.target.files[0];

                    if (file) {
                        const img = document.createElement('img');
                        img.src = URL.createObjectURL(file);
                        img.style.width = '150px';
                        img.style.height = '150px';
                        img.style.objectFit = 'cover';
                        img.className = 'rounded mt-2';
                        photoPreview.appendChild(img);
                    }
                });
            });
        </script>
    @endpush
@endsection
