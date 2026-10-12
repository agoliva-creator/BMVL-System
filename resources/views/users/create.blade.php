@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/users-form.css') }}">
@endpush

@section('content')
<section class="users-form-page">
    <header class="users-form-heading">
        <div>
            <span class="users-form-eyebrow">USER MANAGEMENT</span>
            <h1><i class="fa-solid fa-user-plus"></i> Create user</h1>
            <p>Set up a BMVL Sports Center account and email its temporary sign-in password.</p>
        </div>
        <a href="{{ route('users.index') }}" class="users-form-back"><i class="fa-solid fa-arrow-left"></i> Back to users</a>
    </header>

    <div class="users-form-card">
        <div class="users-form-card-heading">
            <span class="users-form-icon"><i class="fa-regular fa-id-card"></i></span>
            <div>
                <h2>Account details</h2>
                <p>The new user will receive their temporary password by email.</p>
            </div>
        </div>

        @if ($errors->any())
            <div class="users-form-error" role="alert">
                <strong>Please check the details below.</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('users.store') }}" method="POST" class="users-create-form">
            @csrf
            <div class="users-create-fields">
                <div class="users-create-field">
                    <label for="name">Full name</label>
                    <div class="users-create-input"><i class="fa-regular fa-user"></i><input id="name" type="text" name="name" placeholder="Enter full name" value="{{ old('name') }}" autocomplete="name" required autofocus></div>
                </div>
                <div class="users-create-field">
                    <label for="email">Email address</label>
                    <div class="users-create-input"><i class="fa-regular fa-envelope"></i><input id="email" type="email" name="email" placeholder="name@example.com" value="{{ old('email') }}" autocomplete="email" required></div>
                </div>
                <div class="users-create-field users-create-role-field">
                    <label for="role">System role</label>
                    <div class="users-create-input"><i class="fa-solid fa-shield-halved"></i><select id="role" name="role" required>
                        <option value="athlete" {{ old('role', 'athlete') === 'athlete' ? 'selected' : '' }}>Athlete</option>
                        <option value="staff" {{ old('role') === 'staff' ? 'selected' : '' }}>Staff</option>
                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                    </select></div>
                    <small>Choose the access level this account should have.</small>
                </div>
            </div>

            <aside class="users-password-notice">
                <i class="fa-solid fa-envelope-circle-check"></i>
                <p>A secure temporary password is generated automatically and sent to the email address above. It is not shown after a successful email delivery.</p>
            </aside>

            <footer class="users-create-actions">
                <a href="{{ route('users.index') }}" class="users-form-cancel">Cancel</a>
                <button type="submit" class="users-form-submit" id="createUserButton">
                    <i class="fa-solid fa-user-plus create-user-icon"></i>
                    <span class="create-user-button-text">Create account</span>
                    <span class="spinner-border spinner-border-sm d-none" id="createUserSpinner" role="status" aria-label="Creating account"></span>
                </button>
            </footer>
        </form>
    </div>
</section>
@endsection

@section('scripts')
<script>
    const createUserForm = document.querySelector('.users-create-form');

    if (createUserForm) {
        createUserForm.addEventListener('submit', function (event) {
            if (!createUserForm.reportValidity()) {
                event.preventDefault();
                return;
            }

            const submitButton = document.getElementById('createUserButton');
            const spinner = document.getElementById('createUserSpinner');
            const icon = submitButton.querySelector('.create-user-icon');
            const buttonText = submitButton.querySelector('.create-user-button-text');

            submitButton.disabled = true;
            spinner.classList.remove('d-none');
            icon.classList.add('d-none');
            buttonText.textContent = 'Creating account…';
        });
    }
</script>
@endsection
