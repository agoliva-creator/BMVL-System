<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - BMVL Sports Center</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts: Archivo & Public Sans -->
    <link href="https://fonts.googleapis.com/css2?family=Archivo:wght@700;800;900&family=Public+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>
<body class="auth-page auth-register-page">

    <div class="container auth-container">
        <div class="row justify-content-center align-items-center auth-row">
            <div class="col-md-8 auth-column auth-register-column">
                
                <div class="auth-card">
                    <div class="auth-card-header">
                        <a href="/" class="back-link" aria-label="Back to website" title="Back to website">
                            <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                        </a>
                        <img src="{{ asset('images/bmvl-logo.jpg') }}" alt="BMVL Sports Center logo">
                        <h4>Create an account</h4>
                        <p>Join BMVL Sports Center to book courts and sessions</p>
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

                        <form id="registerForm" action="{{ route('register') }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label">Full name</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa-solid fa-user"></i></span>
                                    <input type="text" name="name" class="form-control" placeholder="Enter your full name" value="{{ old('name') }}" required autofocus>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Email address</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa-solid fa-envelope"></i></span>
                                    <input id="email" type="email" name="email" class="form-control" placeholder="Enter your email" value="{{ old('email') }}" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Phone number <span class="auth-optional">(optional)</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa-solid fa-phone"></i></span>
                                    <input type="tel" name="phone_number" class="form-control" placeholder="Enter your phone number" value="{{ old('phone_number') }}">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                                    <input id="password" type="password" name="password" class="form-control" placeholder="Enter your password" required>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Confirm password</label>
                                <!-- The name must be password_confirmation for Laravel's 'confirmed' validation rule to work -->
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                                    <input type="password" name="password_confirmation" class="form-control" placeholder="Re-enter your password" required>
                                </div>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn auth-submit btn-lg">
                                    Register
                                    <span id="registerSpinner" class="spinner-border spinner-border-sm ms-2 auth-spinner" role="status" aria-label="Creating account"></span>
                                </button>
                            </div>
                        </form>

                        <p class="auth-switch">
                            Already have an account?
                            <a href="{{ route('login') }}">Log in here</a>
                        </p>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const registerForm = document.getElementById('registerForm');
        registerForm.addEventListener('submit', async function (event) {
            event.preventDefault();

            const registerSpinner = document.getElementById('registerSpinner');
            const errorElement = document.getElementById('errTxt');
            const submitButton = registerForm.querySelector('button[type="submit"]');

            registerSpinner.style.display = 'inline-block';
            errorElement.textContent = '';
            errorElement.classList.add('d-none');
            submitButton.disabled = true;

            try {
                const response = await fetch(registerForm.action, {
                    method: 'POST',
                    headers: { 'Accept': 'application/json' },
                    body: new FormData(registerForm),
                    credentials: 'same-origin',
                });
                const data = await response.json();

                if (!response.ok) {
                    const validationError = data.errors
                        ? Object.values(data.errors).flat()[0]
                        : null;
                    throw new Error(validationError || data.message || 'Unable to create your account.');
                }

                window.location.href = data.redirect_url;
            } catch (error) {
                errorElement.textContent = error.message;
                errorElement.classList.remove('d-none');
            } finally {
                registerSpinner.style.display = 'none';
                submitButton.disabled = false;
            }
        });
    </script>
</body>
</html>
