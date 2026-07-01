<!DOCTYPE html>
<html lang="id" dir="ltr" data-bs-theme="light" data-color-theme="Blue_Theme" data-layout="vertical">

<head>
    <!-- Tag meta yang diperlukan -->
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <!-- Favicon icon-->
    <link rel="shortcut icon" type="image/png" href="{{ asset('assets/images/favicon.ico') }}" />
    <!-- Core Css -->
    <link rel="stylesheet" href="{{ asset('assets/css/styles.css') }}" />
    <title>Masuk | Klinik Cling - Pembukuan Keuangan</title>
    <style>
        .password-toggle-btn {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            padding: 0 0.25rem;
            cursor: pointer;
            z-index: 2;
            color: #828282;
        }

        .position-relative-password {
            position: relative;
        }
    </style>
</head>

<body>
    <!-- Preloader -->
    <div class="preloader">
        <img src="{{ asset('assets/images/favicon.ico') }}" alt="loader" class="lds-ripple img-fluid" />
    </div>
    <div id="main-wrapper" class="auth-customizer-none">
        <div
            class="position-relative overflow-hidden radial-gradient min-vh-100 w-100 d-flex align-items-center justify-content-center">
            <div class="d-flex align-items-center justify-content-center w-100">
                <div class="row justify-content-center w-100">
                    <div class="col-md-8 col-lg-6 col-xxl-3 auth-card">
                        <div class="card mb-0">
                            <div class="card-body">
                                <a href="{{ url('/') }}"
                                    class="text-nowrap logo-img text-center d-block mb-5 w-100">
                                    <img src="{{ asset('assets/images/logos/logo-cling.png') }}" width="180px"
                                        class="dark-logo" alt="Logo-Dark" />
                                    <img src="{{ asset('assets/images/logos/logo-cling.png') }}" width="180px"
                                        class="light-logo" alt="Logo-light" />
                                </a>
                                <form method="POST" action="{{ route('login') }}">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="userEmail" class="form-label">Alamat Email.</label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                                            id="userEmail" name="email" value="{{ old('email') }}" required
                                            autocomplete="email" autofocus placeholder="Masukkan email Anda">
                                        @error('email')
                                            <span class="invalid-feedback d-block" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="mb-4">
                                        <label for="userPassword" class="form-label">Kata Sandi..</label>
                                        <div class="position-relative-password">
                                            <input type="password"
                                                class="form-control @error('password') is-invalid @enderror"
                                                id="userPassword" name="password" required
                                                autocomplete="current-password" placeholder="Masukkan kata sandi">
                                            <button type="button" class="password-toggle-btn" tabindex="-1"
                                                onclick="togglePasswordVisibility()"
                                                aria-label="Tampilkan/ Sembunyikan Kata Sandi">
                                                <span id="passwordEye">
                                                    <svg width="20px" height="20px" viewBox="0 0 24 24"
                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M2 12C2 12 5.63636 5.5 12 5.5C18.3636 5.5 22 12 22 12C22 12 18.3636 18.5 12 18.5C5.63636 18.5 2 12 2 12Z"
                                                            stroke="currentColor" stroke-width="1.7" />
                                                        <circle id="eye-dot" cx="12" cy="12" r="3.2"
                                                            fill="currentColor" />
                                                    </svg>
                                                </span>
                                            </button>
                                        </div>
                                        @error('password')
                                            <span class="invalid-feedback d-block" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between mb-4">
                                        <div class="form-check">
                                            <input class="form-check-input primary" type="checkbox" name="remember"
                                                id="flexCheckChecked" {{ old('remember') ? 'checked' : '' }}>
                                            <label class="form-check-label text-dark" for="flexCheckChecked">
<<<<<<< HEAD
                                                Ingat Perangkat Ini. test berhasil OK
=======
                                                Ingat Perangkat Ini Test Deploy OK
>>>>>>> bc87025c3f1fb3fa14d08c75d4d78754f4fdac45
                                            </label>
                                        </div>
                                        {{-- @if (Route::has('password.request'))
                                            <a class="text-primary fw-medium"
                                                href="{{ route('password.request') }}">Lupa Kata Sandi?</a>
                                        @endif --}}
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100 py-8 mb-4 rounded-2">
                                        Masuk
                                    </button>
                                    {{-- <div class="d-flex align-items-center justify-content-center">
                                        <p class="fs-4 mb-0 fw-medium">Belum punya akun di Klinik Cling?</p>
                                        <a class="text-primary fw-medium ms-2" href="{{ route('register') }}">Buat
                                            akun</a>
                                    </div> --}}
                                </form>
                            </div>
                        </div>
                        <p class="text-center text-muted mt-4 mb-0">
                            &copy; <span data-current-year>{{ date('Y') }}</span> Klinik Cling
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <script>
            function handleColorTheme(e) {
                document.documentElement.setAttribute("data-color-theme", e);
            }

            function togglePasswordVisibility() {
                const passwordInput = document.getElementById('userPassword');
                const eyeIcon = document.getElementById('passwordEye');
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    eyeIcon.innerHTML = `<svg width="20px" height="20px" viewBox="0 0 24 24" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path d="M17.94 17.94C16.0332 19.2025 14.0192 19.8886 12 19.8886C5.63636 19.8886 2 12.9999 2 12.9999C2.86847 11.2852 4.07053 9.77042 5.58187 8.59851M9.87944 6.46489C10.5615 6.31853 11.2809 6.22217 12 6.22217C18.3636 6.22217 22 13.1109 22 13.1109C21.4976 14.1048 20.8698 15.0122 20.1213 15.8354M16.2432 12.6871C16.2432 14.368 14.7965 15.733 12.9999 15.733C11.2034 15.733 9.75674 14.368 9.75674 12.6871C9.75674 11.0062 11.2034 9.64123 12.9999 9.64123C14.7965 9.64123 16.2432 11.0062 16.2432 12.6871Z" stroke="currentColor" stroke-width="1.7"/>
                        <line x1="3.4142" y1="3.99988" x2="19.9999" y2="20.5856" stroke="currentColor" stroke-width="1.7" />
                    </svg>`;
                } else {
                    passwordInput.type = 'password';
                    eyeIcon.innerHTML = `<svg width="20px" height="20px" viewBox="0 0 24 24" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path d="M2 12C2 12 5.63636 5.5 12 5.5C18.3636 5.5 22 12 22 12C22 12 18.3636 18.5 12 18.5C5.63636 18.5 2 12 2 12Z" stroke="currentColor" stroke-width="1.7"/>
                        <circle id="eye-dot" cx="12" cy="12" r="3.2" fill="currentColor"/>
                    </svg>`;
                }
            }
        </script>
        <button
            class="btn btn-primary p-3 rounded-circle d-flex align-items-center justify-content-center customizer-btn"
            type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasExample"
            aria-controls="offcanvasExample">
            <i class="icon ti ti-settings fs-7"></i>
        </button>
        <div class="offcanvas customizer offcanvas-end" tabindex="-1" id="offcanvasExample"
            aria-labelledby="offcanvasExampleLabel">
            <div class="d-flex align-items-center justify-content-between p-3 border-bottom">
                <h4 class="offcanvas-title fw-semibold" id="offcanvasExampleLabel">
                    Pengaturan
                </h4>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Tutup"></button>
            </div>
            <div class="offcanvas-body h-n80" data-simplebar>
                <h6 class="fw-semibold fs-4 mb-2">Tema</h6>
                <div class="d-flex flex-row gap-3 customizer-box" role="group">
                    <input type="radio" class="btn-check light-layout" name="theme-layout" id="light-layout"
                        autocomplete="off" />
                    <label class="btn p-9 btn-outline-primary rounded-2" for="light-layout">
                        <i class="icon ti ti-brightness-up fs-7 me-2"></i>Terang
                    </label>
                    <input type="radio" class="btn-check dark-layout" name="theme-layout" id="dark-layout"
                        autocomplete="off" />
                    <label class="btn p-9 btn-outline-primary rounded-2" for="dark-layout">
                        <i class="icon ti ti-moon fs-7 me-2"></i>Gelap
                    </label>
                </div>
                <h6 class="mt-5 fw-semibold fs-4 mb-2">Arah Tema</h6>
                <div class="d-flex flex-row gap-3 customizer-box" role="group">
                    <input type="radio" class="btn-check" name="direction-l" id="ltr-layout"
                        autocomplete="off" />
                    <label class="btn p-9 btn-outline-primary" for="ltr-layout">
                        <i class="icon ti ti-text-direction-ltr fs-7 me-2"></i>Kiri ke Kanan
                    </label>
                    <input type="radio" class="btn-check" name="direction-l" id="rtl-layout"
                        autocomplete="off" />
                    <label class="btn p-9 btn-outline-primary" for="rtl-layout">
                        <i class="icon ti ti-text-direction-rtl fs-7 me-2"></i>Kanan ke Kiri
                    </label>
                </div>
                <h6 class="mt-5 fw-semibold fs-4 mb-2">Warna Tema</h6>
                <div class="d-flex flex-row flex-wrap gap-3 customizer-box color-pallete" role="group">
                    <input type="radio" class="btn-check" name="color-theme-layout" id="Blue_Theme"
                        autocomplete="off" />
                    <label class="btn p-9 btn-outline-primary d-flex align-items-center justify-content-center"
                        onclick="handleColorTheme('Blue_Theme')" for="Blue_Theme" data-bs-toggle="tooltip"
                        data-bs-placement="top" data-bs-title="BIRU">
                        <div class="color-box rounded-circle d-flex align-items-center justify-content-center skin-1">
                            <i class="ti ti-check text-white d-flex icon fs-5"></i>
                        </div>
                    </label>
                    <input type="radio" class="btn-check" name="color-theme-layout" id="Aqua_Theme"
                        autocomplete="off" />
                    <label class="btn p-9 btn-outline-primary d-flex align-items-center justify-content-center"
                        onclick="handleColorTheme('Aqua_Theme')" for="Aqua_Theme" data-bs-toggle="tooltip"
                        data-bs-placement="top" data-bs-title="AQUA">
                        <div class="color-box rounded-circle d-flex align-items-center justify-content-center skin-2">
                            <i class="ti ti-check text-white d-flex icon fs-5"></i>
                        </div>
                    </label>
                    <input type="radio" class="btn-check" name="color-theme-layout" id="Purple_Theme"
                        autocomplete="off" />
                    <label class="btn p-9 btn-outline-primary d-flex align-items-center justify-content-center"
                        onclick="handleColorTheme('Purple_Theme')" for="Purple_Theme" data-bs-toggle="tooltip"
                        data-bs-placement="top" data-bs-title="UNGU">
                        <div class="color-box rounded-circle d-flex align-items-center justify-content-center skin-3">
                            <i class="ti ti-check text-white d-flex icon fs-5"></i>
                        </div>
                    </label>
                    <input type="radio" class="btn-check" name="color-theme-layout" id="green-theme-layout"
                        autocomplete="off" />
                    <label class="btn p-9 btn-outline-primary d-flex align-items-center justify-content-center"
                        onclick="handleColorTheme('Green_Theme')" for="green-theme-layout" data-bs-toggle="tooltip"
                        data-bs-placement="top" data-bs-title="HIJAU">
                        <div class="color-box rounded-circle d-flex align-items-center justify-content-center skin-4">
                            <i class="ti ti-check text-white d-flex icon fs-5"></i>
                        </div>
                    </label>
                    <input type="radio" class="btn-check" name="color-theme-layout" id="cyan-theme-layout"
                        autocomplete="off" />
                    <label class="btn p-9 btn-outline-primary d-flex align-items-center justify-content-center"
                        onclick="handleColorTheme('Cyan_Theme')" for="cyan-theme-layout" data-bs-toggle="tooltip"
                        data-bs-placement="top" data-bs-title="CYAN">
                        <div class="color-box rounded-circle d-flex align-items-center justify-content-center skin-5">
                            <i class="ti ti-check text-white d-flex icon fs-5"></i>
                        </div>
                    </label>
                    <input type="radio" class="btn-check" name="color-theme-layout" id="orange-theme-layout"
                        autocomplete="off" />
                    <label class="btn p-9 btn-outline-primary d-flex align-items-center justify-content-center"
                        onclick="handleColorTheme('Orange_Theme')" for="orange-theme-layout" data-bs-toggle="tooltip"
                        data-bs-placement="top" data-bs-title="ORANYE">
                        <div class="color-box rounded-circle d-flex align-items-center justify-content-center skin-6">
                            <i class="ti ti-check text-white d-flex icon fs-5"></i>
                        </div>
                    </label>
                </div>
                <h6 class="mt-5 fw-semibold fs-4 mb-2">Tipe Tata Letak</h6>
                <div class="d-flex flex-row gap-3 customizer-box" role="group">
                    <div>
                        <input type="radio" class="btn-check" name="page-layout" id="vertical-layout"
                            autocomplete="off" />
                        <label class="btn p-9 btn-outline-primary" for="vertical-layout">
                            <i class="icon ti ti-layout-sidebar-right fs-7 me-2"></i>Vertikal
                        </label>
                    </div>
                    <div>
                        <input type="radio" class="btn-check" name="page-layout" id="horizontal-layout"
                            autocomplete="off" />
                        <label class="btn p-9 btn-outline-primary" for="horizontal-layout">
                            <i class="icon ti ti-layout-navbar fs-7 me-2"></i>Horizontal
                        </label>
                    </div>
                </div>
                <h6 class="mt-5 fw-semibold fs-4 mb-2">Opsi Kontainer</h6>
                <div class="d-flex flex-row gap-3 customizer-box" role="group">
                    <input type="radio" class="btn-check" name="layout" id="boxed-layout" autocomplete="off" />
                    <label class="btn p-9 btn-outline-primary" for="boxed-layout">
                        <i class="icon ti ti-layout-distribute-vertical fs-7 me-2"></i>Terkotak
                    </label>
                    <input type="radio" class="btn-check" name="layout" id="full-layout" autocomplete="off" />
                    <label class="btn p-9 btn-outline-primary" for="full-layout">
                        <i class="icon ti ti-layout-distribute-horizontal fs-7 me-2"></i>Penuh
                    </label>
                </div>
                <h6 class="fw-semibold fs-4 mb-2 mt-5">Tipe Sidebar</h6>
                <div class="d-flex flex-row gap-3 customizer-box" role="group">
                    <a href="javascript:void(0)" class="fullsidebar">
                        <input type="radio" class="btn-check" name="sidebar-type" id="full-sidebar"
                            autocomplete="off" />
                        <label class="btn p-9 btn-outline-primary" for="full-sidebar">
                            <i class="icon ti ti-layout-sidebar-right fs-7 me-2"></i>Penuh
                        </label>
                    </a>
                    <div>
                        <input type="radio" class="btn-check " name="sidebar-type" id="mini-sidebar"
                            autocomplete="off" />
                        <label class="btn p-9 btn-outline-primary" for="mini-sidebar">
                            <i class="icon ti ti-layout-sidebar fs-7 me-2"></i>Lipat
                        </label>
                    </div>
                </div>
                <h6 class="mt-5 fw-semibold fs-4 mb-2">Tampilan Kartu</h6>
                <div class="d-flex flex-row gap-3 customizer-box" role="group">
                    <input type="radio" class="btn-check" name="card-layout" id="card-with-border"
                        autocomplete="off" />
                    <label class="btn p-9 btn-outline-primary" for="card-with-border">
                        <i class="icon ti ti-border-outer fs-7 me-2"></i>Dengan Border
                    </label>
                    <input type="radio" class="btn-check" name="card-layout" id="card-without-border"
                        autocomplete="off" />
                    <label class="btn p-9 btn-outline-primary" for="card-without-border">
                        <i class="icon ti ti-border-none fs-7 me-2"></i>Tanpa Border
                    </label>
                </div>
            </div>
        </div>
    </div>
    <div class="dark-transparent sidebartoggler"></div>
    <!-- Import Js Files -->
    <script src="{{ asset('assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/libs/simplebar/dist/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/js/theme/app.init.js') }}"></script>
    <script src="{{ asset('assets/js/theme/theme.js') }}"></script>
    <script src="{{ asset('assets/js/theme/app.min.js') }}"></script>
    <!-- solar icons -->
    <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>
</body>

</html>
