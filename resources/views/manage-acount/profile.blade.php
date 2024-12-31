@extends('components.layout')

{{-- Banner --}}
@include('manage-my-books.banner', [
    'title' => 'My Profile'
])

<div class="main-content">
<div class="user-profile-container">
    <form method="POST" action="{{ route('profile.update') }}" class="profile-form" enctype="multipart/form-data">
        @csrf
        @method('PATCH')
        <div class="row">
            <!-- Avatar do Utilizador -->
            <div class="col-md-6 mb-3">
                <div class="profile-avatar-section d-flex align-items-center">
                    <div class="position-relative">
                        @if($user->user_photo_url)
                            <div class="delete-image" onclick="removePhoto('{{ route('profile.removePhoto') }}')">×</div>
                        @endif
                        <img src="{{ $user->user_photo_url ? asset('storage/' . $user->user_photo_url) : asset('images/no-photo.png') }}"
                             alt="User Avatar"
                             class="profile-avatar"
                             id="photoPreviewImage">
                    </div>
                    <span class="user-ranking">{{ $user->ranking?->current_rank ?? '-' }}<sup>th</sup></span>
                </div>
            </div>

            <!-- Upload de Nova Foto -->
            <div class="col-md-6 mb-3">
                <label for="uploadPhoto" class="upload-photo-label">Upload Photo</label>
                <div class="upload-photo-input-wrapper">
                    <input type="file" id="uploadPhoto" name="user_photo_url" class="upload-photo-input" accept="image/*" onchange="previewPhoto(event)">
                    <span class="upload-photo-placeholder" id="photoFileName">Upload photo</span>
                    <i class="bi bi-camera upload-photo-icon"></i>
                </div>
            </div>

            <!-- Campos de Informação -->
            <div class="col-md-6 mb-3">
                <label for="firstName">First Name</label>
                <input type="text" id="firstName" name="first_name" class="form-control" value="{{ old('first_name', $user->first_name) }}" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="lastName">Last Name</label>
                <input type="text" id="lastName" name="last_name" class="form-control" value="{{ old('last_name', $user->last_name) }}" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="birth_date">Birth Date</label>
                <input type="text" id="birth_date" name="birth_date" class="form-control" value="{{ old('birth_date', $user->birth_date) }}" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
            </div>

            <!-- Campos de Senha -->
            <div class="col-md-4 mb-3">
                <label for="oldPassword">Old Password</label>
                <div class="position-relative">
                    <input type="password" id="oldPassword" name="old_password" class="form-control" placeholder="Insert the old password">
                    <span id="toggleOldPasswordIcon" onclick="togglePasswordVisibility('oldPassword', 'toggleOldPasswordIcon')" class="bi bi-eye password-toggle"></span>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <label for="newPassword">New Password</label>
                <div class="position-relative">
                    <input type="password" id="newPassword" name="new_password" class="form-control" placeholder="Insert the new password">
                    <span id="toggleNewPasswordIcon" onclick="togglePasswordVisibility('newPassword', 'toggleNewPasswordIcon')" class="bi bi-eye password-toggle"></span>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <label for="newPasswordConfirmation">Confirm New Password</label>
                <div class="position-relative">
                    <input type="password" id="newPasswordConfirmation" name="new_password_confirmation" class="form-control" placeholder="Confirm new password">
                    <span id="toggleConfirmPasswordIcon" onclick="togglePasswordVisibility('newPasswordConfirmation', 'toggleConfirmPasswordIcon')" class="bi bi-eye password-toggle"></span>
                </div>
            </div>
        </div>

        <!-- Subscription Management -->
        <div class="col-12 mb-3">
            <label>Subscription Status</label>
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-0">Current Plan:
                                <span class="access-badge {{ strtolower($user->subscription->plan->name ?? 'no-plan') }}">
                                    {{ $user->subscription->plan->name ?? 'No Plan' }}
                                </span>
                            </p>
                        </div>
                        @if($user->canAccessPremiumContent())
                            <button type="button"
                                    class="btn-premium"
                                    onclick="confirmDowngrade()">
                                Downgrade to Free
                            </button>
                        @else
                            @php
                                $pendingRequest = \App\Models\SubscriptionApproval::whereHas('subscription', function($query) use ($user) {
                                    $query->where('user_id', $user->id);
                                })->where('status', 'pending')->exists();
                            @endphp

                            @if($pendingRequest)
                                <span class="status-badge pending2">Premium Request Pending</span>
                            @else
                                <button type="button"
                                        class="btn-premium"
                                        onclick="requestPremium()">
                                    Request Premium Access
                                </button>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{--        <div class="col-12 mb-3 text-center">--}}
        {{--            <button type="button" class="btn btn-danger" onclick="confirmAccountDeletion()">Delete Account</button>--}}
        {{--        </div>--}}


        <div class="text-center mt-4">
            <button type="submit" class="btn btn">Save</button>
            <button type="button" class="btn btnSecundary" onclick="window.location.href='{{ url('/') }}'">Back to Home</button>
        </div>
    </form>
</div>
</div>

@push('scripts')
    <script>
        function previewPhoto(event) {
            const reader = new FileReader();
            const previewImage = document.getElementById('photoPreviewImage');
            const uploadInput = document.getElementById('uploadPhoto');
            const fileNameSpan = document.getElementById('photoFileName');

            // Atualizar o nome do arquivo
            if (event.target.files[0]) {
                fileNameSpan.textContent = event.target.files[0].name;
            }

            if (previewImage) {
                reader.onload = function() {
                    // Salvar a URL original
                    if (!previewImage.dataset.originalSrc) {
                        previewImage.dataset.originalSrc = previewImage.src;
                    }

                    // Atualizar preview
                    previewImage.src = reader.result;

                    // Adicionar botão de remover preview se ainda não existir
                    if (!document.querySelector('.remove-preview')) {
                        const removeButton = document.createElement('div');
                        removeButton.className = 'delete-image remove-preview';
                        removeButton.innerHTML = '×';
                        removeButton.onclick = function() {
                            // Restaurar imagem original
                            previewImage.src = previewImage.dataset.originalSrc;
                            // Limpar input file
                            uploadInput.value = '';
                            // Restaurar texto original do placeholder
                            fileNameSpan.textContent = 'Upload photo';
                            // Remover o botão
                            removeButton.remove();
                        };

                        previewImage.parentElement.appendChild(removeButton);
                    }
                };

                if (event.target.files[0]) {
                    reader.readAsDataURL(event.target.files[0]);
                }
            }
        }

        function removePhoto(url) {
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
                    form.action = url;

                    var csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';
                    form.appendChild(csrfToken);

                    var methodField = document.createElement('input');
                    methodField.type = 'hidden';
                    methodField.name = '_method';
                    methodField.value = 'PATCH';
                    form.appendChild(methodField);

                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            flatpickr("#birth_date", {
                dateFormat: "Y-m-d",
                maxDate: "today",
                minDate: "1900-01-01",
                defaultDate: "{{ $user->birth_date }}",
                monthSelectorType: "static"
            });
        });

        function requestPremium() {
            swal({
                title: "Request Premium Access",
                text: "Would you like to request Premium access? An administrator will review your request.",
                icon: "info",
                buttons: true,
            }).then((willRequest) => {
                if (willRequest) {
                    var form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ route("profile.requestPremium") }}';

                    var csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';
                    form.appendChild(csrfToken);

                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }

        function confirmDowngrade() {
            swal({
                title: "Downgrade to Free Plan",
                text: "Are you sure you want to downgrade to the Free plan? You will lose access to premium content.",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willDowngrade) => {
                if (willDowngrade) {
                    var form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ route("profile.downgrade") }}';

                    var csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';
                    form.appendChild(csrfToken);

                    var methodField = document.createElement('input');
                    methodField.type = 'hidden';
                    methodField.name = '_method';
                    methodField.value = 'PATCH';
                    form.appendChild(methodField);

                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }

        {{--function confirmAccountDeletion() {--}}
        {{--    swal({--}}
        {{--        title: "Delete Account",--}}
        {{--        text: "Are you sure you want to delete your account? This action cannot be undone.",--}}
        {{--        icon: "warning",--}}
        {{--        buttons: true,--}}
        {{--        dangerMode: true,--}}
        {{--    }).then((willDelete) => {--}}
        {{--        if (willDelete) {--}}
        {{--            var form = document.createElement('form');--}}
        {{--            form.method = 'POST';--}}
        {{--            form.action = '{{ route("profile.destroy") }}';--}}

        {{--            var csrfToken = document.createElement('input');--}}
        {{--            csrfToken.type = 'hidden';--}}
        {{--            csrfToken.name = '_token';--}}
        {{--            csrfToken.value = '{{ csrf_token() }}';--}}
        {{--            form.appendChild(csrfToken);--}}

        {{--            var methodField = document.createElement('input');--}}
        {{--            methodField.type = 'hidden';--}}
        {{--            methodField.name = '_method';--}}
        {{--            methodField.value = 'DELETE';--}}
        {{--            form.appendChild(methodField);--}}

        {{--            document.body.appendChild(form);--}}
        {{--            form.submit();--}}
        {{--        }--}}
        {{--    });--}}
        {{--}--}}
    </script>
@endpush

