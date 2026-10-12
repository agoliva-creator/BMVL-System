@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endpush

@section('content')
<div class="profile-page">
    <header class="profile-page-heading">
        <div>
            <span class="profile-eyebrow">ACCOUNT SETTINGS</span>
            <h1><i class="fa-solid fa-user-gear"></i> My Profile</h1>
            <p>Update your personal details and secure your account.</p>
        </div>
        <span class="profile-role"><i class="fa-solid fa-shield-halved"></i> {{ ucfirst(auth()->user()->role) }} account</span>
    </header>

    <form class="profile-form" id="profileForm" action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <aside class="profile-identity-card">
            <div class="profile-avatar-wrap">
                @if(auth()->user()->avatar)
                    <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="Profile picture" class="profile-avatar">
                @else
                    <div class="profile-avatar profile-avatar-placeholder"><i class="fa-solid fa-user"></i></div>
                @endif
            </div>
            <h2>{{ auth()->user()->name }}</h2>
            <p>{{ auth()->user()->email }}</p>
            <div class="profile-avatar-upload">
                <label for="avatar" class="form-label">Profile picture</label>
                <input id="avatar" type="file" name="avatar" class="form-control form-control-sm @error('avatar') is-invalid @enderror" accept="image/jpeg,image/png,image/jpg,image/webp">
                <div class="form-text">JPG or PNG, up to 2 MB</div>
                @error('avatar') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </aside>

        <div class="profile-details-card">
            <section class="profile-section">
                <div class="profile-section-heading">
                    <span class="profile-section-icon"><i class="fa-regular fa-id-card"></i></span>
                    <div><h2>Personal information</h2><p>Your contact and profile details</p></div>
                </div>
                <div class="profile-fields-grid">
                    <div class="profile-field">
                        <label class="form-label" for="name">Full name</label>
                        <input id="name" type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', auth()->user()->name) }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="profile-field">
                        <label class="form-label" for="email">Email address</label>
                        <input id="email" type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', auth()->user()->email) }}" required>
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="profile-field">
                        <label class="form-label" for="phone_number">Phone number</label>
                        <input id="phone_number" type="text" name="phone_number" class="form-control @error('phone_number') is-invalid @enderror" value="{{ old('phone_number', auth()->user()->phone_number) }}" placeholder="e.g. 0912-345-6789">
                        @error('phone_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="profile-field">
                        <label class="form-label" for="gender">Gender</label>
                        <select id="gender" name="gender" class="form-select @error('gender') is-invalid @enderror">
                            <option value="" disabled {{ !auth()->user()->gender ? 'selected' : '' }}>Select gender</option>
                            <option value="Male" {{ old('gender', auth()->user()->gender) == 'Male' ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ old('gender', auth()->user()->gender) == 'Female' ? 'selected' : '' }}>Female</option>
                            <option value="Other" {{ old('gender', auth()->user()->gender) == 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('gender') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="profile-field">
                        <label class="form-label" for="date_of_birth">Date of birth</label>
                        <input id="date_of_birth" type="date" name="date_of_birth" class="form-control @error('date_of_birth') is-invalid @enderror" value="{{ old('date_of_birth', auth()->user()->date_of_birth) }}">
                        @error('date_of_birth') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="profile-field">
                        <label class="form-label" for="address">Home address</label>
                        <input id="address" type="text" name="address" class="form-control @error('address') is-invalid @enderror" value="{{ old('address', auth()->user()->address) }}" placeholder="City or complete address">
                        @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </section>

            <section class="profile-section profile-security-section">
                <div class="profile-section-heading">
                    <span class="profile-section-icon profile-security-icon"><i class="fa-solid fa-lock"></i></span>
                    <div><h2>Security</h2><p>Current password is required to change email or password</p></div>
                </div>
                <div class="profile-fields-grid profile-security-grid">
                    <div class="profile-field">
                        <label class="form-label" for="current_password">Current password</label>
                        <input id="current_password" type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" autocomplete="current-password">
                        @error('current_password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="profile-field">
                        <label class="form-label" for="password">New password</label>
                        <input id="password" type="password" name="password" class="form-control @error('password') is-invalid @enderror" autocomplete="new-password">
                        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="profile-field">
                        <label class="form-label" for="password_confirmation">Confirm new password</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" autocomplete="new-password">
                    </div>
                </div>
            </section>

            <footer class="profile-form-footer">
                <span><i class="fa-solid fa-circle-info"></i> Leave password fields blank to keep your current password.</span>
                <button type="submit" class="profile-save-button" id="profileSaveButton">
                    <i class="fa-solid fa-check" id="profileSaveIcon"></i>
                    <span id="profileSaveText">Save changes</span>
                    <span class="spinner-border spinner-border-sm d-none" id="profileSaveSpinner" role="status" aria-label="Saving changes"></span>
                </button>
            </footer>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    const profileForm = document.getElementById('profileForm');

    if (profileForm) {
        profileForm.addEventListener('submit', function (event) {
            if (!profileForm.reportValidity()) {
                event.preventDefault();
                return;
            }

            const button = document.getElementById('profileSaveButton');
            button.disabled = true;
            document.getElementById('profileSaveIcon').classList.add('d-none');
            document.getElementById('profileSaveSpinner').classList.remove('d-none');
            document.getElementById('profileSaveText').textContent = 'Saving changes…';
        });
    }
</script>
@endsection
