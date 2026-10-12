@extends('layouts.app')

@section('content')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/users.css') }}">
@endpush


<div class="users-page">
<div class="row users-heading align-items-center">
    <div class="col-md-8">
        <h2 class="users-title"><i class="fa-solid fa-users me-2"></i> Manage Users</h2>
        <p class="users-subtitle">View registered members and manage system roles.</p>
    </div>
    <div class="col-md-4 text-md-end mt-3 mt-md-0">
        <!-- ONLY ADMINS CAN CREATE NEW USERS -->
        @if(auth()->user()->role === 'admin')
            <a href="{{ route('users.create') }}" class="btn users-create-button">
                <i class="fa-solid fa-user-plus me-1"></i> Create user
            </a>
        @endif
    </div>
</div>

<div class="dash-panel users-panel">
    <div class="table-responsive users-table-wrap">
        <table class="table dash-table align-middle" id="usersTable">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Email</th>
                    <th>Contact Info</th>
                    <th>Role</th>
                    <th>Joined date</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <!-- BYPASSING BLADE COMPILER WITH RAW PHP -->
                <?php foreach($users as$user): ?>
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-3 users-identity">
                            @if($user->avatar)
                                <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="user-avatar-sm">
                            @else
                                <div class="user-avatar-placeholder">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                            @endif
                            <div class="users-identity-details">
                                <strong class="users-name {{ mb_strlen($user->name) > 26 ? 'users-name-long' : (mb_strlen($user->name) > 18 ? 'users-name-medium' : '') }}" title="{{ $user->name }}">{{ $user->name }}</strong>
                                <!-- FIX: Using 'or' instead of vertical pipes to prevent formatting errors -->
                                @if($user->gender or$user->date_of_birth)
                                    <div class="users-demographics">
                                        {{ $user->gender ?? 'Unspecified' }} 
                                        @if($user->date_of_birth)
                                            • {{ \Carbon\Carbon::parse($user->date_of_birth)->age }} yrs old
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td>{{ $user->email }}</td>
                    <td>
                        <div class="contact-info-text">
                            @if($user->phone_number)
                                <div><i class="fa-solid fa-phone me-1 contact-icon"></i> {{ $user->phone_number }}</div>
                            @endif
                            @if($user->address)
                                <div class="mt-1 text-truncate user-address" title="{{ $user->address }}"><i class="fa-solid fa-location-dot me-1 contact-icon"></i> {{ $user->address }}</div>
                            @endif
                            @if(!$user->phone_number && !$user->address)
                                <i>No contact data</i>
                            @endif
                        </div>
                    </td>
                    <td data-order="{{ $user->role === 'admin' ? 1 : ($user->role === 'staff' ? 2 : 3) }}">
                        @if($user->role === 'admin')
                            <span class="dash-badge dash-badge-admin">Admin</span>
                        @elseif($user->role === 'staff')
                            <span class="dash-badge dash-badge-staff">Staff</span>
                        @else
                            <span class="dash-badge dash-badge-athlete">Athlete</span>
                        @endif
                    </td>
                    <td>{{ $user->created_at->format('M d, Y') }}</td>
                    <td class="text-center">
                        <div class="d-inline-flex gap-2">
                            <!-- ONLY ADMINS CAN EDIT OR DELETE -->
                            @if(auth()->user()->role === 'admin')
                                <a href="{{ route('users.edit', $user->id) }}" class="action-btn action-btn-edit" title="Edit">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>

                                <!-- Admins can delete anyone EXCEPT themselves -->
                                @if(auth()->id() !== $user->id)
                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline delete-user-form" data-user-name="{{ $user->name }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="action-btn action-btn-delete" title="Delete">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            @else
                                <!-- STAFF ONLY SEES THIS -->
                                <span class="dash-badge dash-badge-readonly">
                                    <i class="fa-solid fa-lock me-1 readonly-icon"></i> Read-Only
                                </span>
                            @endif
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
</div>
@if(session('generated_password'))
    <div id="generatedCredentialsData" hidden
        data-email="{{ session('generated_email') }}"
        data-password="{{ session('generated_password') }}"
        data-email-failed="{{ session('error') ? 'true' : 'false' }}"></div>
@endif
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        const initializeUsersTable = function () {
            if ($.fn.DataTable.isDataTable('#usersTable')) {
                $('#usersTable').DataTable().destroy();
            }

            $('#usersTable').DataTable({
                pageLength: 10,
                paging: true,
                lengthChange: false,
                info: true,
                ordering: true,
                order: [[3, 'asc']],
                scrollX: window.matchMedia('(max-width: 1199.98px)').matches,
                scrollY: Math.max(160, $('.users-panel').innerHeight() - 145) + 'px',
                scrollCollapse: true
            });
        };

        initializeUsersTable();
        let usersResizeTimer;
        $(window).on('resize', function () {
            clearTimeout(usersResizeTimer);
            usersResizeTimer = setTimeout(initializeUsersTable, 150);
        });

        $(document).on('submit', '.delete-user-form', function (e) {
            e.preventDefault();
            const form = this;
            const userName = $(form).data('user-name');

            Swal.fire({
                title: 'Delete this user?',
                html: `<strong>${userName}</strong> will be permanently removed from the system. This can't be undone.`,
                icon: 'warning',
                iconColor: '#A6392F',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete user',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#A6392F',
                cancelButtonColor: '#57635F',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
        
        const credentialsData = document.getElementById('generatedCredentialsData');
        if (credentialsData) {
            const email = credentialsData.dataset.email;
            const password = credentialsData.dataset.password;
            const emailFailed = credentialsData.dataset.emailFailed === 'true';
            const note = emailFailed
                ? 'The account was created, but the email could not be sent. Share this password securely; it will not be shown again.'
                : 'This password will not be shown again. Share it securely with the new user.';

            Swal.fire({
                title: emailFailed ? 'Email delivery failed' : 'User created',
                html: `
                    <div class="generated-credentials">
                        <p class="generated-credentials-note">${note}</p>
                        <div class="generated-credentials-card">
                            <div class="generated-credentials-label">Email</div>
                            <div class="generated-credentials-email"></div>
                            <div class="generated-credentials-label">Temporary Password</div>
                            <div class="generated-credentials-password"></div>
                        </div>
                    </div>
                `,
                icon: emailFailed ? 'warning' : 'success',
                iconColor: '#0E4B4E',
                confirmButtonText: 'Copy password & close',
                confirmButtonColor: '#0E4B4E',
                allowOutsideClick: false,
                didOpen: (popup) => {
                    popup.querySelector('.generated-credentials-email').textContent = email;
                    popup.querySelector('.generated-credentials-password').textContent = password;
                }
            }).then(() => {
                navigator.clipboard.writeText(password);
            });
        }
    });
</script>
@endsection
