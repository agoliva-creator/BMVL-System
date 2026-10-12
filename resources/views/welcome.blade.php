<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BMVL Sports Center</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Google Fonts: Archivo & Public Sans -->
    <link href="https://fonts.googleapis.com/css2?family=Archivo:wght@700;800;900&family=Public+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
</head>
<body id="top">

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container-fluid px-4 px-lg-5">
            <a class="brand-logo" href="#top">
                <img src="{{ asset('images/bmvl-logo.jpg') }}" alt="BMVL Sports Center logo">
                <div class="brand-text">BMVL SPORTS CENTER<span>Community Sports Center</span></div>
            </a>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
                <i class="fa-solid fa-bars fs-4"></i>
            </button>

            <div class="collapse navbar-collapse" id="navbarContent">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="#activities">Activities</a></li>
                    <li class="nav-item"><a class="nav-link" href="#how-it-works">How it works</a></li>
                    <li class="nav-item"><a class="nav-link" href="#features">Features</a></li>
                    <li class="nav-item"><a class="nav-link" href="#rates">Rates</a></li>
                </ul>

                <div class="d-flex align-items-center gap-3 ms-auto mt-3 mt-lg-0">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="nav-link fw-bold">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="nav-link">Log in</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="btn-brand">Create account</a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero -->
    <section class="hero-section">
        <div class="container-narrow">
            <div class="row align-items-center">
                <div class="col-lg-7">
                    <h1 class="hero-title">Eleven ways to move,<br>one membership.</h1>
                    <p class="hero-subtitle">
                        BMVL Sports Center brings court sports, combat training, and group fitness together under one account. Book a session, track your reservation, and check in with a digital receipt.
                    </p>
                    <div class="d-flex flex-column flex-sm-row gap-3">
                        @if (Route::has('login'))
                            @auth
                                <a href="{{ route('bookings.index') }}" class="btn-brand">Manage your bookings</a>
                            @else
                                <a href="{{ route('register') }}" class="btn-brand">Create your account</a>
                                <a href="#activities" class="btn-brand-outline">See what's offered</a>
                            @endauth
                        @endif
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="hero-badge-wrap">
                        <div class="hero-badge-ring"></div>
                        <img src="{{ asset('images/bmvl-logo.jpg') }}" alt="BMVL Sports Center badge">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Activities -->
    <section id="activities" class="section-padding section-alt">
        <div class="container-narrow">
            <h2 class="section-title">Everything bookable from one place</h2>
            <p class="section-lede mb-5">Eleven activities across three categories, all managed through the same account and booking engine.</p>

            <div class="row g-4">
                <div class="col-md-4">
                    <div class="list-group-card">
                        <div class="list-group-header"><h4>Court sports</h4></div>
                        <div class="list-row"><i class="fa-solid fa-basketball"></i><span>Basketball</span></div>
                        <div class="list-row"><i class="fa-solid fa-table-tennis-paddle-ball"></i><span>Pickleball</span></div>
                        <div class="list-row"><i class="fa-solid fa-feather-pointed"></i><span>Badminton</span></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="list-group-card">
                        <div class="list-group-header"><h4>Combat &amp; conditioning</h4></div>
                        <div class="list-row"><i class="fa-solid fa-dumbbell"></i><span>Weight training</span></div>
                        <div class="list-row"><i class="fa-solid fa-bolt"></i><span>Circuit training</span></div>
                        <div class="list-row"><i class="fa-solid fa-hand-fist"></i><span>Boxing</span></div>
                        <div class="list-row"><i class="fa-solid fa-hand-fist"></i><span>Muay Thai</span></div>
                        <div class="list-row"><i class="fa-solid fa-hand-back-fist"></i><span>Taekwondo</span></div>
                        <div class="list-row"><i class="fa-solid fa-people-arrows"></i><span>Jiu Jitsu</span></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="list-group-card">
                        <div class="list-group-header"><h4>Mind &amp; movement</h4></div>
                        <div class="list-row"><i class="fa-solid fa-music"></i><span>Zumba</span></div>
                        <div class="list-row"><i class="fa-solid fa-spa"></i><span>Yoga</span></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section id="how-it-works" class="section-padding">
        <div class="container-narrow">
            <h2 class="section-title">Search, book, show up</h2>
            <p class="section-lede mb-5">Four steps from your phone to the front desk.</p>

            <div class="row g-5">
                <div class="col-md-6 col-lg-3">
                    <div class="step-row">
                        <div class="step-number">01</div>
                        <div>
                            <h4>Create an account</h4>
                            <p class="muted">Register as an athlete to access the booking portal.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="step-row">
                        <div class="step-number">02</div>
                        <div>
                            <h4>Choose an activity</h4>
                            <p class="muted">Filter across all eleven activities and available time slots.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="step-row">
                        <div class="step-number">03</div>
                        <div>
                            <h4>Confirm your slot</h4>
                            <p class="muted">Select a time and your reservation is booked instantly.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="step-row">
                        <div class="step-number">04</div>
                        <div>
                            <h4>Show your receipt</h4>
                            <p class="muted">Print or display your confirmation for staff at check-in.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features -->
    <section id="features" class="section-padding section-alt">
        <div class="container-narrow">
            <h2 class="section-title">What the system handles</h2>
            <p class="section-lede mb-5">Built for athletes, staff, and admins on one platform.</p>

            <div class="feature-grid">
                <div class="feature-row">
                    <div class="feature-icon"><i class="fa-solid fa-calendar-check"></i></div>
                    <div>
                        <h4>Live booking engine</h4>
                        <p>Reserve a court or session online, with availability updated in real time.</p>
                    </div>
                </div>
                <div class="feature-row">
                    <div class="feature-icon"><i class="fa-solid fa-print"></i></div>
                    <div>
                        <h4>Printable receipts</h4>
                        <p>Generate a booking confirmation for a quick front-desk check-in.</p>
                    </div>
                </div>
                <div class="feature-row">
                    <div class="feature-icon"><i class="fa-solid fa-dumbbell"></i></div>
                    <div>
                        <h4>Equipment tracking</h4>
                        <p>Staff track rental gear and update inventory as it moves.</p>
                    </div>
                </div>
                <div class="feature-row">
                    <div class="feature-icon"><i class="fa-solid fa-shield-halved"></i></div>
                    <div>
                        <h4>Role-based accounts</h4>
                        <p>Separate access levels for athletes, staff, and admins.</p>
                    </div>
                </div>
                <div class="feature-row">
                    <div class="feature-icon"><i class="fa-solid fa-bullhorn"></i></div>
                    <div>
                        <h4>Announcements</h4>
                        <p>Facility-wide alerts and schedule changes reach your account directly.</p>
                    </div>
                </div>
                <div class="feature-row">
                    <div class="feature-icon"><i class="fa-solid fa-code"></i></div>
                    <div>
                        <h4>API access</h4>
                        <p>A dedicated endpoint supplies real-time court and session availability.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Rates -->
    <section id="rates" class="section-padding">
        <div class="container-narrow">
            <h2 class="section-title section-title-wide mx-auto text-center">Rates &amp; booking types</h2>
            <p class="section-lede section-lede-centered mx-auto text-center mb-5">Pricing isn't published online yet — reach out to the front desk for current rates. Here's how each activity can be booked.</p>

            <div class="rates-note">
                <p>Contact the front desk or check available rates after logging in.</p>
                <a href="#" class="btn-brand">Contact us for rates</a>
            </div>

            <div class="row g-4 justify-content-center">
                <div class="col-md-6">
                    <div class="list-group-card">
                        <div class="list-group-header">
                            <h4>Open play or reserved</h4>
                            <p>Walk in for open play, or book a slot ahead of time</p>
                        </div>
                        <div class="list-row"><i class="fa-solid fa-table-tennis-paddle-ball"></i><span>Pickleball</span></div>
                        <div class="list-row"><i class="fa-solid fa-feather-pointed"></i><span>Badminton</span></div>
                        <div class="list-row"><i class="fa-solid fa-dumbbell"></i><span>Weight training</span></div>
                        <div class="list-row"><i class="fa-solid fa-bolt"></i><span>Circuit training</span></div>
                        <div class="list-row"><i class="fa-solid fa-hand-fist"></i><span>Boxing</span></div>
                        <div class="list-row"><i class="fa-solid fa-spa"></i><span>Yoga</span></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="list-group-card">
                        <div class="list-group-header">
                            <h4>Reserved only</h4>
                            <p>Book a slot in advance to guarantee access</p>
                        </div>
                        <div class="list-row"><i class="fa-solid fa-basketball"></i><span>Basketball</span></div>
                        <div class="list-row"><i class="fa-solid fa-hand-fist"></i><span>Muay Thai</span></div>
                        <div class="list-row"><i class="fa-solid fa-hand-back-fist"></i><span>Taekwondo</span></div>
                        <div class="list-row"><i class="fa-solid fa-people-arrows"></i><span>Jiu Jitsu</span></div>
                        <div class="list-row"><i class="fa-solid fa-music"></i><span>Zumba</span></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="site-footer">
        <div class="container-narrow">
            <div class="row g-5">
                <div class="col-lg-4 col-md-6">
                    <a class="brand-logo mb-3" href="#top">
                        <img src="{{ asset('images/bmvl-logo.jpg') }}" alt="BMVL Sports Center logo">
                        <div class="brand-text">BMVL SPORTS CENTER<span>Community Sports Center</span></div>
                    </a>
                    <p class="mt-4 pe-lg-4">A community sports center for athletes to move, train, and compete — built for the local community.</p>
                    <p class="mt-3">
                        <i class="fa-solid fa-location-dot me-2 text-gold"></i> Hilongos, Leyte
                    </p>
                </div>

                <div class="col-lg-3 col-md-6 offset-lg-1">
                    <h5>Facility</h5>
                    <div class="footer-links">
                        <a href="#activities">Our activities</a>
                        <a href="#how-it-works">How booking works</a>
                        <a href="#features">System features</a>
                        <a href="#rates">Rates &amp; booking types</a>
                    </div>
                </div>

                <div class="col-lg-2 col-md-6">
                    <h5>Account</h5>
                    <div class="footer-links">
                        @if (Route::has('login'))
                            <a href="{{ route('login') }}">Member login</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}">Create account</a>
                            @endif
                        @endif
                    </div>
                </div>
            </div>

            <div class="footer-bottom">
                <div>&copy; {{ date('Y') }} BMVL Sports Center. All rights reserved.</div>
                <div>Developed by RJV</div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const navLinks = document.querySelectorAll('.navbar-nav .nav-link');
            const menuToggle = document.getElementById('navbarContent');

            navLinks.forEach(function(link) {
                link.addEventListener('click', function() {
                    if (menuToggle.classList.contains('show')) {
                        let bsCollapse = bootstrap.Collapse.getInstance(menuToggle);
                        if (!bsCollapse) {
                            bsCollapse = new bootstrap.Collapse(menuToggle, { toggle: false });
                        }
                        bsCollapse.hide();
                    }
                });
            });
        });
    </script>
</body>
</html>
