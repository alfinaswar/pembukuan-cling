<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Sign In | Klinik Cling - Pembukuan Keuangan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description"
        content="Klinik Cling Pembukuan Keuangan - Sistem login yang aman dan modern untuk pencatatan keuangan klinik Anda." />
    <meta name="keywords" content="Klinik Cling, pembukuan, keuangan, login, dashboard, admin" />
    <meta name="author" content="Klinik Cling" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}" />

    <!-- Theme Config Js -->
    <script src="{{ asset('assets/js/config.js') }}"></script>

    <!-- Vendor css -->
    <link href="{{ asset('assets/css/vendors.min.css') }}" rel="stylesheet" type="text/css" />

    <!-- App css -->
    <link id="app-style" href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />
</head>

<body>
    <div class="auth-box overflow-hidden align-items-center d-flex">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xxl-4 col-md-6 col-sm-8">
                    <div class="card p-4">
                        <div class="position-absolute top-0 end-0" style="width: 180px">
                            <img src="{{ asset('assets/images/auth-card-bg.svg') }}" class="auth-card-bg-img"
                                alt="auth-card-bg" />
                        </div>
                        <div class="auth-brand text-center mb-4">
                            <a href="{{ url('/') }}" class="logo-dark">
                                <img src="{{ asset('assets/images/logo-black.png') }}" alt="dark logo" />
                            </a>
                            <a href="{{ url('/') }}" class="logo-light">
                                <img src="{{ asset('assets/images/logo.png') }}" alt="logo" />
                            </a>
                            <p class="text-muted w-lg-75 mt-3 mx-auto">Masukkan email dan
                                password untuk melanjutkan ke pembukuan keuangan Klinik Cling.</p>
                        </div>
                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="userEmail" class="form-label">
                                    Email address <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                        id="userEmail" name="email" value="{{ old('email') }}"
                                        placeholder="email_kamu@gmail.com" required autocomplete="email" autofocus />
                                </div>
                                @error('email')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="userPassword" class="form-label">
                                    Password <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                                        id="userPassword" name="password" placeholder="••••••••" required
                                        autocomplete="current-password" />
                                </div>
                                @error('password')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="form-check">
                                    <input class="form-check-input form-check-input-light fs-14" type="checkbox"
                                        name="remember" id="rememberMe" {{ old('remember') ? 'checked' : '' }} />
                                    <label class="form-check-label" for="rememberMe">Keep me signed in</label>
                                </div>
                                @if (Route::has('password.request'))
                                    <a href="{{ route('password.request') }}"
                                        class="text-decoration-underline link-offset-3 text-muted">Forgot Password?</a>
                                @endif
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary fw-semibold py-2">Sign In</button>
                            </div>
                        </form>
                        <p class="text-muted text-center mt-4 mb-0">
                            New here?
                            <a href="{{ route('register') }}"
                                class="text-decoration-underline link-offset-3 fw-semibold">Create an account</a>
                        </p>
                    </div>
                    <p class="text-center text-muted mt-4 mb-0">
                        ©
                        <span data-current-year>{{ date('Y') }}</span>
                        Klinik Cling
                    </p>
                </div>
            </div>
        </div>
    </div>
    <!-- Vendor js -->
    <script src="{{ asset('assets/js/vendors.min.js') }}"></script>
    <!-- App js -->
    <script src="{{ asset('assets/js/app.js') }}"></script>
</body>

</html>
