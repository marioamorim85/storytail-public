@php
    use Illuminate\Support\Facades\Storage;
@endphp

@extends('admin.dashboard-layout')

@section('dashboard-content')
    <h1 class="custom-title">Edit User #{{ $user->id }}</h1>

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

    <form method="POST" action="{{ route('admin.users.update', $user->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-md-6">
                <!-- First Name -->
                <div class="mb-3">
                    <label for="first_name" class="form-label fw-bold">First Name</label>
                    <input type="text" class="form-control" id="first_name" name="first_name"
                           value="{{ $user->first_name }}" required style="height: 47px">
                </div>

                <!-- Last Name -->
                <div class="mb-3">
                    <label for="last_name" class="form-label fw-bold">Last Name</label>
                    <input type="text" class="form-control" id="last_name" name="last_name"
                           value="{{ $user->last_name }}" required style="height: 47px">
                </div>

                <!-- Birth Date -->
                <div class="mb-3">
                    <label for="birth_date" class="form-label fw-bold">Date of Birth</label>
                    <input type="text" class="form-control" id="birth_date" name="birth_date"
                           value="{{ $user->birth_date ? \Carbon\Carbon::parse($user->birth_date)->format('Y-m-d') : '' }}"
                           placeholder="Select date" style="height: 47px">
                </div>

                <!-- Email -->
                <div class="mb-3">
                    <label for="email" class="form-label fw-bold">Email</label>
                    <input type="email" class="form-control" id="email" name="email"
                           value="{{ $user->email }}" required style="height: 47px">
                </div>

                <!-- User Type -->
                <div class="mb-3">
                    <label for="user_type_id" class="form-label fw-bold">User Type</label>
                    <select class="form-control" id="user_type_id" name="user_type_id" required style="height: 47px">
                        @foreach($userTypes as $type)
                            <option value="{{ $type->id }}" {{ $user->user_type_id == $type->id ? 'selected' : '' }}>
                                {{ $type->user_type }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Status -->
                <div class="mb-3">
                    <label for="status" class="form-label fw-bold">Status</label>
                    <select class="form-control" id="status" name="status" required style="height: 47px">
                        <option value="active" {{ $user->status == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="suspended" {{ $user->status == 'suspended' ? 'selected' : '' }}>Suspended</option>
                        <option value="inactive" {{ $user->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </div>

            <div class="col-md-6">

                <!-- Plan -->
                <div class="mb-3">
                    <label for="plan_id" class="form-label fw-bold">Plan</label>

                    @php
                        // Verificar se existe algum pedido pendente na tabela subscription_approvals
                        $hasPendingApproval = $user->subscription &&
                            $user->subscription->approvals()
                                ->where('status', 'pending')
                                ->exists();
                    @endphp

                    <select class="form-control" id="plan_id" name="plan_id" style="height: 47px"
                        {{ $hasPendingApproval ? 'disabled' : '' }}>
                        @foreach($plans as $plan)
                            <option value="{{ $plan->id }}"
                                {{ optional($user->subscription)->plan_id == $plan->id ? 'selected' : '' }}>
                                {{ $plan->name }}
                            </option>
                        @endforeach
                    </select>

                    @if($user->subscription && $user->subscription->start_date)
                        <small class="text-muted d-block mt-1">
                            Current subscription started: {{ Carbon\Carbon::parse($user->subscription->start_date)->format('Y-m-d') }}
                        </small>
                    @endif

                    @if($hasPendingApproval)
                        <small class="text-danger d-block mt-1">
                            This user has a pending subscription request for Premium. Plan changes are disabled.
                        </small>
                    @endif
                </div>



                <!-- Current Photo -->
                @if($user->user_photo_url)
                    <div class="mb-3">
                        <div class="d-flex flex-column align-items-start">
                            <label class="form-label fw-bold mb-2">Current Photo</label>
                            <div class="position-relative">
                                <div class="delete-image"
                                     onclick="removePhoto({{ $user->id }})">
                                    ×
                                </div>
                                <img src="{{ Storage::url($user->user_photo_url) }}"
                                     alt="{{ $user->first_name }}"
                                     class="rounded"
                                     style="width: 150px; height: 150px; object-fit: cover;">
                            </div>
                        </div>
                    </div>
                @endif

                <!-- New Photo -->
                <div class="mb-3">
                    <label for="user_photo_url" class="form-label fw-bold">
                        {{ $user->user_photo_url ? 'Update Photo' : 'Add Photo' }}
                    </label>
                    <input type="file" class="form-control" id="user_photo_url"
                           name="user_photo_url" accept="image/*">
                    <div class="mt-2">
                        <div id="photoPreview" class="d-flex justify-content-center"></div>
                    </div>
                </div>

                <!-- Current Plan (if exists) -->
                @if($user->subscription)
                    <div class="mb-3">
                        <label class="form-label fw-bold">Current Plan</label>
                        <div class="p-2 bg-light rounded">
            <span class="access-badge {{ $user->subscription->plan->access_level == 2 ? 'premium' : 'free' }}">
                {{ $user->subscription->plan->name }}
            </span>
                            @if($user->subscription->start_date)
                                <br>
                                <small class="text-muted">
                                    Started:
                                    {{ $user->subscription->start_date instanceof \Carbon\Carbon
                                        ? $user->subscription->start_date->format('Y-m-d')
                                        : \Carbon\Carbon::parse($user->subscription->start_date)->format('Y-m-d') }}
                                </small>
                            @endif
                            @if($user->subscription->end_date)
                                <br>
                                <small class="text-muted">
                                    Ends:
                                    {{ $user->subscription->end_date instanceof \Carbon\Carbon
                                        ? $user->subscription->end_date->format('Y-m-d')
                                        : \Carbon\Carbon::parse($user->subscription->end_date)->format('Y-m-d') }}
                                </small>
                            @else
                                <br>
                                <small class="text-muted">
                                    Ends: Ongoing
                                </small>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div class="mt-4 d-flex gap-4">
            <button type="submit" class="btnAdmin">Save Changes</button>
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

                // Photo preview (código existente)
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

            function removePhoto(userId) {
                swal({
                    title: "Warning",
                    text: "Are you sure you want to delete this photo?",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                }).then((willDelete) => {
                    if (willDelete) {
                        var form = document.createElement('form');
                        form.method = 'POST';
                        form.action = `{{ route('admin.users.removePhoto', ':id') }}`.replace(':id', userId);

                        var csrfToken = document.createElement('input');
                        csrfToken.type = 'hidden';
                        csrfToken.name = '_token';
                        csrfToken.value = '{{ csrf_token() }}';
                        form.appendChild(csrfToken);

                        var methodField = document.createElement('input');
                        methodField.type = 'hidden';
                        methodField.name = '_method';
                        methodField.value = 'DELETE';
                        form.appendChild(methodField);

                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            }
        </script>
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                flatpickr("#birth_date", {
                    dateFormat: "Y-m-d",
                    maxDate: "today",
                    minDate: "1900-01-01",
                    defaultDate: "{{ $user->birth_date ? \Carbon\Carbon::parse($user->birth_date)->format('Y-m-d') : '' }}",
                    monthSelectorType: "static"
                });
            });
        </script>
    @endpush
@endsection
