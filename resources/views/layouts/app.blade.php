<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'BMVL Community Sports Center')</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <!-- Google Fonts: Archivo & Public Sans -->
    <link href="https://fonts.googleapis.com/css2?family=Archivo:wght@700;800;900&family=Public+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">


    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @stack('styles')
    <link rel="stylesheet" href="{{ asset('css/responsive.css') }}">
</head>
<body class="@auth has-sidebar @endauth">

    <!-- Top Bar -->
    <header class="topbar">
        <div class="topbar-left">
            @auth
                <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle menu">
                    <i class="fa-solid fa-bars"></i>
                </button>
            @endauth
            <a class="brand-logo" href="@auth{{ auth()->user()->role === 'admin' ? route('dashboard') : route('bookings.index') }}@else{{ url('/') }}@endauth">
                <img src="{{ asset('images/bmvl-logo.jpg') }}" alt="BMVL Sports Center logo">
                BMVL Sports Center
            </a>
        </div>

        <div class="topbar-actions d-flex align-items-center gap-3">
            @auth
                <span class="user-badge">
                    <i class="fa-solid fa-circle-user"></i>
                    {{ auth()->user()->name }} ({{ ucfirst(auth()->user()->role) }})
                </span>

                <a href="{{ route('profile.edit') }}" class="topbar-profile-link btn btn-outline-secondary btn-sm" style="border-radius: 6px; font-weight: 600; padding: 6px 12px; color: var(--ink); border-color: var(--border);">
                    <i class="fa-solid fa-gear me-1"></i> Profile
                </a>

                <form action="{{ route('logout') }}" method="POST" class="d-inline m-0" onsubmit="return confirm('Are you sure you want to log out?');">
                    @csrf
                    <button type="submit" class="topbar-logout-button btn btn-outline-danger btn-sm" style="border-radius: 6px; font-weight: 600; padding: 6px 12px;">
                        <i class="fa-solid fa-right-from-bracket me-1"></i> Logout
                    </button>
                </form>
            @endauth

            @guest
                <a href="{{ route('login') }}" class="btn btn-outline-danger btn-sm">
                    <i class="fa-solid fa-user me-1"></i> Login
                </a>
            @endguest
        </div>
    </header>

    @auth
        <!-- Sidebar overlay (mobile only) -->
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <!-- Sidebar Navigation -->
        <aside class="sidebar" id="sidebar">

            <!-- ONLY ADMINS SEE THIS -->
            @if(auth()->user()->role === 'admin')
                <a class="sidebar-nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                    <i class="fa-solid fa-chart-line"></i> Dashboard
                </a>
            @endif

            <!-- ADMINS & STAFF ONLY: User Management -->
            @if(auth()->user()->role === 'admin' || auth()->user()->role === 'staff')
                <a class="sidebar-nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">
                    <i class="fa-solid fa-users"></i> Users
                </a>
                <hr class="sidebar-divider">
            @endif

            <!-- EVERYONE SEES THESE (Courts, Equipment, Announcements) -->
            <a class="sidebar-nav-link {{ request()->routeIs('courts.*') ? 'active' : '' }}" href="{{ route('courts.index') }}">
                <i class="fa-solid fa-map"></i> Courts
            </a>
            <a class="sidebar-nav-link {{ request()->routeIs('equipment.*') ? 'active' : '' }}" href="{{ route('equipment.index') }}">
                <i class="fa-solid fa-dumbbell"></i> Equipment
            </a>
            <a class="sidebar-nav-link {{ request()->routeIs('announcements.*') ? 'active' : '' }}" href="{{ route('announcements.index') }}">
                <i class="fa-solid fa-bullhorn"></i> Announcements
            </a>

            <hr class="sidebar-divider">

            <!-- EVERYONE SEES BOOKINGS -->
            <a class="sidebar-nav-link {{ request()->routeIs('bookings.*') ? 'active' : '' }}" href="{{ route('bookings.index') }}">
                <i class="fa-solid fa-calendar-check"></i> Bookings
            </a>
        </aside>
    @endauth

    <!-- Main Content Area -->
    <div class="main-wrap">
        <main class="container">
            @yield('content')
        </main>
    </div>

    <!-- Core JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script>
        // Mobile sidebar toggle
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', () => document.body.classList.toggle('sidebar-open'));
        }
        if (sidebarOverlay) {
            sidebarOverlay.addEventListener('click', () => document.body.classList.remove('sidebar-open'));
        }
    </script>

    <!-- Global Flash Messages -->
    @if(session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: "{{ session('success') }}",
                iconColor: '#0E4B4E',
                confirmButtonColor: '#0E4B4E',
                timer: 3000,
                showConfirmButton: false
            });
        </script>
    @endif

    @if(session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: "{{ session('error') }}",
                iconColor: '#A6392F',
                confirmButtonColor: '#0E4B4E',
            });
        </script>
    @endif

    @yield('scripts')
</body>
</html>
