<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log in - BMVL Sports Center</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts: Archivo & Public Sans -->
    <link href="https://fonts.googleapis.com/css2?family=Archivo:wght@700;800;900&family=Public+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>
<body class="auth-page">

    <div class="container auth-container">
        <div class="row justify-content-center align-items-center auth-row">
            <div class="col-md-7 auth-column auth-login-column">
                
                <div class="auth-card">
                    <div class="auth-card-header">
                        <a href="/" class="back-link" aria-label="Back to website" title="Back to website">
                            <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                        </a>
                        <img src="{{ asset('images/bmvl-logo.jpg') }}" alt="BMVL Sports Center logo">
                        <h4>BMVL Sports Center</h4>
                        <p>Please log in to your account</p>
                    </div>
                    <div class="auth-card-body">

                        @if ($errors->any())
                            <div class="alert alert-danger auth-error">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div id="errTxt" class="alert alert-danger auth-error d-none" role="alert" aria-live="polite"></div>

                        <form id="loginForm" action="{{ url('/login') }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label">Email address</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa-solid fa-envelope"></i></span>
                                    <input id="email" type="email" name="email" class="form-control" placeholder="Enter your email" value="{{ old('email') }}" required autofocus>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                                    <input id="password" type="password" name="password" class="form-control" placeholder="Enter your password" required>
                                </div>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn auth-submit btn-lg">
                                    Log in
                                    <span id="loginSpinner" class="spinner-border spinner-border-sm ms-2 auth-spinner" role="status" aria-label="Signing in"></span>
                                </button>
                            </div>
                        </form>

                        @if (Route::has('register'))
                            <p class="auth-switch">
                                Don't have an account?
                                <a href="{{ route('register') }}">Create one</a>
                            </p>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const loginForm = document.getElementById('loginForm');
        loginForm.addEventListener('submit', async function (event) {
            event.preventDefault();

            const loginSpinner = document.getElementById('loginSpinner');
            const errorElement = document.getElementById('errTxt');
            const submitButton = loginForm.querySelector('button[type="submit"]');

            loginSpinner.style.display = 'inline-block';
            errorElement.textContent = '';
            errorElement.classList.add('d-none');
            submitButton.disabled = true;

            try {
                const response = await fetch(loginForm.action, {
                    method: 'POST',
                    headers: { 'Accept': 'application/json' },
                    body: new FormData(loginForm),
                    credentials: 'same-origin',
                });
                const data = await response.json();

                if (!response.ok) {
                    const validationError = data.errors
                        ? Object.values(data.errors).flat()[0]
                        : null;
                    throw new Error(validationError || data.message || 'Unable to log in.');
                }

                window.location.href = data.redirect_url;
            } catch (error) {
                errorElement.textContent = error.message;
                errorElement.classList.remove('d-none');
            } finally {
                loginSpinner.style.display = 'none';
                submitButton.disabled = false;
            }
        });
    </script>
</body>
</html>
