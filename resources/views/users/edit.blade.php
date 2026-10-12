@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/users-form.css') }}">
@endpush

@section('content')
<section class="users-form-page user-edit-page">
    <header class="users-form-heading">
        <div>
            <span class="users-form-eyebrow">USER MANAGEMENT</span>
            <h1><i class="fa-solid fa-user-pen"></i> Edit user</h1>
            <p>Update account details, contact information, and system access.</p>
        </div>
        <a href="{{ route('users.index') }}" class="users-form-back"><i class="fa-solid fa-arrow-left"></i> Back to users</a>
    </header>

    <div class="users-form-card user-edit-card">
        <div class="user-edit-profile">
            @if($user->avatar)
                <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="user-edit-avatar">
            @else
                <span class="user-edit-avatar user-edit-avatar-placeholder">{{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}</span>
            @endif
            <div class="user-edit-identity">
                <span class="user-edit-account-label">ACCOUNT #{{ $user->id }}</span>
                <h2>{{ $user->name }}</h2>
                <p>{{ $user->email }}</p>
            </div>
            <div class="user-edit-joined"><i class="fa-regular fa-calendar"></i> Joined {{ $user->created_at->format('M d, Y') }}</div>
        </div>

        <div class="users-form-card-heading user-edit-section-heading">
            <span class="users-form-icon"><i class="fa-regular fa-id-card"></i></span>
            <div><h2>Account information</h2><p>Changes take effect when you save this form.</p></div>
        </div>

        @if ($errors->any())
            <div class="users-form-error" role="alert">
                <strong>Please check the details below.</strong>
                <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif

        <form action="{{ route('users.update', $user->id) }}" method="POST" class="user-edit-form">
            @csrf
            @method('PUT')
            <div class="users-create-fields user-edit-fields">
                <div class="users-create-field">
                    <label for="name">Full name</label>
                    <div class="users-create-input"><i class="fa-regular fa-user"></i><input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" autocomplete="name" required></div>
                    @error('name')<small class="user-field-error">{{ $message }}</small>@enderror
                </div>
                <div class="users-create-field">
                    <label for="email">Email address</label>
                    <div class="users-create-input"><i class="fa-regular fa-envelope"></i><input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" autocomplete="email" required></div>
                    @error('email')<small class="user-field-error">{{ $message }}</small>@enderror
                </div>
                <div class="users-create-field">
                    <label for="phone_number">Phone number <span class="user-edit-optional">(optional)</span></label>
                    <div class="users-create-input"><i class="fa-solid fa-phone"></i><input id="phone_number" type="tel" name="phone_number" value="{{ old('phone_number', $user->phone_number) }}" autocomplete="tel"></div>
                    @error('phone_number')<small class="user-field-error">{{ $message }}</small>@enderror
                </div>
                <div class="users-create-field">
                    <label for="gender">Gender</label>
                    <div class="users-create-input"><i class="fa-solid fa-venus-mars"></i><select id="gender" name="gender">
                        <option value="">Unspecified</option>
                        <option value="Male" {{ old('gender', $user->gender) === 'Male' ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ old('gender', $user->gender) === 'Female' ? 'selected' : '' }}>Female</option>
                        <option value="Other" {{ old('gender', $user->gender) === 'Other' ? 'selected' : '' }}>Other</option>
                    </select></div>
                    @error('gender')<small class="user-field-error">{{ $message }}</small>@enderror
                </div>
                <div class="users-create-field">
                    <label for="date_of_birth">Date of birth</label>
                    <div class="users-create-input"><i class="fa-regular fa-calendar"></i><input id="date_of_birth" type="date" name="date_of_birth" value="{{ old('date_of_birth', $user->date_of_birth) }}"></div>
                    @error('date_of_birth')<small class="user-field-error">{{ $message }}</small>@enderror
                </div>
                <div class="users-create-field">
                    <label for="role">System role</label>
                    <div class="users-create-input"><i class="fa-solid fa-shield-halved"></i><select id="role" name="role" required>
                        <option value="athlete" {{ old('role', $user->role) === 'athlete' ? 'selected' : '' }}>Athlete</option>
                        <option value="staff" {{ old('role', $user->role) === 'staff' ? 'selected' : '' }}>Staff</option>
                        <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                    </select></div>
                    @error('role')<small class="user-field-error">{{ $message }}</small>@enderror
                </div>
                <div class="users-create-field user-edit-address-field">
                    <label for="address">Home address <span class="user-edit-optional">(optional)</span></label>
                    <div class="users-create-input"><i class="fa-solid fa-location-dot"></i><input id="address" type="text" name="address" value="{{ old('address', $user->address) }}" autocomplete="street-address"></div>
                    @error('address')<small class="user-field-error">{{ $message }}</small>@enderror
                </div>
            </div>

            @if(auth()->id() === $user->id)
                <div class="user-edit-self-note"><i class="fa-solid fa-circle-info"></i> You cannot change your own role from Admin.</div>
            @endif

            <footer class="users-create-actions user-edit-actions">
                <a href="{{ route('users.index') }}" class="users-form-cancel">Cancel</a>
                <button type="submit" class="users-form-submit"><i class="fa-solid fa-floppy-disk"></i> Save changes</button>
            </footer>
        </form>
    </div>
</section>
@endsection
