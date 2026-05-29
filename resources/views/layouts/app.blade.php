<!DOCTYPE html>
<html lang="en" dir="ltr" data-bs-theme="light" data-color-theme="Blue_Theme" data-layout="vertical">

<head>
    <!-- Required meta tags -->
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <!-- Favicon icon-->
    <link rel="shortcut icon" type="image/png" href="{{ asset('') }}assets/images/logos/favicon.png" />

    <!-- Core Css -->
    <link rel="stylesheet" href="{{ asset('') }}assets/css/styles.css" />
    <link rel="stylesheet" href="{{ asset('') }}assets/libs/select2/dist/css/select2.min.css">
    <link rel="stylesheet" href="{{ asset('') }}assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css" />
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-material-datetimepicker/2.7.1/css/bootstrap-material-datetimepicker.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('') }}assets/libs/daterangepicker/daterangepicker.css">
    <link rel="stylesheet" href="{{ asset('') }}assets/libs/sweetalert2/dist/sweetalert2.min.css">
    <title>Cling Dental Klinik</title>
    <style>
        /* ===== SIDEBAR DARK NAVY THEME ===== */
        .left-sidebar {
            background: #0f1623 !important;
        }

        .brand-logo {
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
            background-color: #ffffff;
        }

        .nav-small-cap {
            color: #4a6080 !important;
            font-size: 11px;
            letter-spacing: 1.5px;
        }

        .sidebar-link {
            color: #8aa3c0 !important;
            border-radius: 10px !important;
            margin-bottom: 2px;
            transition: all 0.2s ease !important;
            position: relative;
        }

        .sidebar-link:hover {
            background: rgba(33, 150, 243, 0.1) !important;
            color: #c5d8f0 !important;
        }

        .sidebar-link i {
            color: #5a7a9a !important;
        }

        .sidebar-link.active,
        .sidebar-link[aria-expanded="true"] {
            background: #1e3a5f !important;
            color: #ffffff !important;
            font-weight: 600;
        }

        .sidebar-link.active i {
            color: #2196f3 !important;
        }

        .sidebar-link.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 3px;
            height: 60%;
            background: #2196f3;
            border-radius: 0 3px 3px 0;
        }

        .first-level .sidebar-link,
        .two-level .sidebar-link,
        .three-level .sidebar-link {
            color: #6a8aaa !important;
            font-size: 13px;
        }

        .first-level .sidebar-link.active,
        .two-level .sidebar-link.active {
            background: rgba(33, 150, 243, 0.15) !important;
            color: #90caf9 !important;
        }

        .fixed-profile {
            background: rgba(255, 255, 255, 0.04) !important;
            border: 1px solid rgba(255, 255, 255, 0.07) !important;
            border-radius: 12px !important;
        }

        .fixed-profile h6 {
            color: #d0e4f7 !important;
        }

        .fixed-profile span {
            color: #5a7a9a !important;
        }

        /* ===== HAMBURGER BUTTON ===== */
        .sidebar-hamburger {
            display: none;
            background: #1e3a5f;
            border: none;
            outline: none;
            padding: 0.4rem 0.6rem;
            font-size: 1.5rem;
            line-height: 1;
            color: #fff !important;
            cursor: pointer;
            border-radius: 8px;
            transition: background 0.2s ease, transform 0.1s ease;
            z-index: 1050;
        }

        .sidebar-hamburger:hover {
            background: #2196f3 !important;
            transform: scale(1.05);
        }

        .sidebar-hamburger:active {
            transform: scale(0.95);
        }

        /* Overlay untuk mobile */
        .sidebar-overlay {
            display: none;
            position: fixed;
            z-index: 1039;
            left: 0;
            top: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(15, 22, 35, 0.5);
            backdrop-filter: blur(2px);
            opacity: 0;
            transition: opacity 0.2s ease;
        }

        .sidebar-overlay.active {
            display: block;
            opacity: 1;
        }

        /* ===== MOBILE SIDEBAR (max-width: 767.98px) ===== */
        @media (max-width: 767.98px) {
            .sidebar-hamburger {
                display: flex !important;
                align-items: center;
                justify-content: center;
            }

            .left-sidebar.with-vertical {
                position: fixed !important;
                top: 0;
                left: 0;
                z-index: 1040;
                width: 280px;
                height: 100vh;
                transform: translateX(-100%);
                transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                background: #0f1623 !important;
                box-shadow: 4px 0 20px rgba(0, 0, 0, 0.15);
                overflow-y: auto;
            }

            .left-sidebar.with-vertical.sidebar-open {
                transform: translateX(0) !important;
            }

            /* Close button di dalam sidebar */
            .sidebartoggler.d-block.d-md-none {
                display: flex !important;
                align-items: center;
                justify-content: center;
                width: 32px;
                height: 32px;
                border-radius: 8px;
                background: rgba(255, 255, 255, 0.1);
                color: #fff !important;
                transition: background 0.2s;
            }

            .sidebartoggler.d-block.d-md-none:hover {
                background: rgba(255, 255, 255, 0.2);
            }

            /* Animasi icon hamburger */
            .sidebar-hamburger i {
                transition: transform 0.3s ease;
            }

            .sidebar-hamburger.active i {
                transform: rotate(90deg);
            }
        }

        /* FIX: Ganti breakpoint supaya sidebar selalu muncul di layar full hd (1920px width) & resolusi laptop */
        /* Turunkan max-width agar sidebar hanya hilang di device < 768px (tablet ke bawah) */
        @media (max-width: 767.98px) {
            .sidebar-hamburger {
                display: block;
            }

            .left-sidebar {
                position: fixed;
                top: 0;
                left: 0;
                z-index: 1040;
                width: 270px;
                height: 100vh;
                transform: translateX(-100%);
                transition: transform 0.2s ease;
                background: #0f1623 !important;
                box-shadow: 2px 0 8px rgba(43, 55, 75, .1);
            }

            .left-sidebar.sidebar-open {
                transform: translateX(0);
            }

            .sidebar-overlay {
                display: none;
                position: fixed;
                z-index: 1039;
                left: 0;
                top: 0;
                width: 100vw;
                height: 100vh;
                background: rgba(44, 62, 80, 0.20);
            }

            .sidebar-overlay.active {
                display: block;
            }
        }
    </style>
</head>

<body style="background-color: #FAFBFE;">
    <!-- Preloader -->
    <div class="preloader">
        <img src="{{ asset('') }}assets/images/logos/favicon.png" alt="loader" class="lds-ripple img-fluid" />
    </div>
    <div id="main-wrapper">

        <!-- Hamburger button for mobile sidebar toggle -->
        <!-- Hamburger button for mobile sidebar toggle -->
        <button class="sidebar-hamburger position-fixed top-0 start-0 z-1031 m-3" id="sidebarHamburgerBtn"
            aria-label="Toggle sidebar" aria-expanded="false" aria-controls="sidebarMain">
            <i class="ti ti-menu-2" id="hamburgerIcon"></i>
        </button>
        <div class="sidebar-overlay" id="sidebarOverlay" tabindex="-1"></div>

        <!-- Sidebar Start -->
        <aside class="left-sidebar with-vertical" id="sidebarMain">
            <div>
                <div class="brand-logo d-flex align-items-center justify-content-between">
                    <a href="{{ route('home') }}" class="text-nowrap logo-img">
                        <img src="{{ asset('assets/images/logos/logo-cling.png') }}" width="120px" class="dark-logo"
                            alt="Logo-Dark" />
                        <img src="{{ asset('assets/images/logos/logo-cling.png') }}" width="120px" class="light-logo"
                            alt="Logo-light" />
                    </a>
                    <a href="javascript:void(0)"
                        class="sidebartoggler ms-auto text-decoration-none fs-5 d-block d-md-none" id="sidebarCloseBtn"
                        aria-label="Close sidebar">
                        <i class="ti ti-x"></i>
                    </a>
                </div>
                <nav class="sidebar-nav scroll-sidebar" data-simplebar>
                    <ul id="sidebarnav">
                        <li class="nav-small-cap">
                            <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                            <span class="hide-menu">Menu</span>
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link" href="{{ route('home') }}" aria-expanded="false">
                                <span>
                                    <i class="ti ti-layout-dashboard"></i>
                                </span>
                                <span class="hide-menu">Dashboard</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link" href="#" aria-expanded="false">
                                <span>
                                    <i class="ti ti-user"></i>
                                </span>
                                <span class="hide-menu">Pasien</span>
                            </a>
                        </li>
                        @can('pembayaran-index')
                            <li class="sidebar-item">
                                <a class="sidebar-link" href="{{ route('Transaksi.index') }}" aria-expanded="false">
                                    <span>
                                        <i class="ti ti-cash"></i>
                                    </span>
                                    <span class="hide-menu">Pembayaran</span>
                                </a>
                            </li>
                        @endcan
                        @can('laporan')
                            <li class="sidebar-item">
                                <a class="sidebar-link has-arrow" href="javascript:void(0)" aria-expanded="false">
                                    <span>
                                        <i class="ti ti-file-chart"></i>
                                    </span>
                                    <span class="hide-menu">Laporan</span>
                                </a>
                                <ul aria-expanded="false" class="collapse first-level">
                                    @can('laporan-umum')
                                        <li class="sidebar-item">
                                            <a href="{{ route('laporan-umum.index') }}" class="sidebar-link">
                                                <div class="round-16 d-flex align-items-center justify-content-center">
                                                    <i class="ti ti-report"></i>
                                                </div>
                                                <span class="hide-menu">Laporan Umum</span>
                                            </a>
                                        </li>
                                    @endcan
                                    @can('laporan-perawat')
                                        <li class="sidebar-item">
                                            <a href="{{ route('laporan-perawat.index') }}" class="sidebar-link">
                                                <div class="round-16 d-flex align-items-center justify-content-center">
                                                    <i class="ti ti-user-nurse"></i>
                                                </div>
                                                <span class="hide-menu">Laporan Perawat</span>
                                            </a>
                                        </li>
                                    @endcan
                                    @can('laporan-dokter')
                                        <li class="sidebar-item">
                                            <a href="{{ route('laporan-dokter.index') }}" class="sidebar-link">
                                                <div class="round-16 d-flex align-items-center justify-content-center">
                                                    <i class="ti ti-stethoscope"></i>
                                                </div>
                                                <span class="hide-menu">Laporan Dokter</span>
                                            </a>
                                        </li>
                                    @endcan
                                    @can('laporan-resepsionis')
                                        <li class="sidebar-item">
                                            <a href="{{ route('laporan-resepsionis.index') }}" class="sidebar-link">
                                                <div class="round-16 d-flex align-items-center justify-content-center">
                                                    <i class="ti ti-notebook"></i>
                                                </div>
                                                <span class="hide-menu">Laporan Resepsionis</span>
                                            </a>
                                        </li>
                                    @endcan
                                </ul>
                            </li>
                        @endcan
                        @can('masterdata')
                            <li class="sidebar-item">
                                <a class="sidebar-link has-arrow" href="javascript:void(0)" aria-expanded="false">
                                    <span>
                                        <i class="ti ti-database"></i>
                                    </span>
                                    <span class="hide-menu">Data Master</span>
                                </a>
                                <ul aria-expanded="false" class="collapse first-level">
                                    @can('master-perawatan')
                                        <li class="sidebar-item">
                                            <a href="{{ route('JenisPerawatan.index') }}" class="sidebar-link">
                                                <div class="round-16 d-flex align-items-center justify-content-center">
                                                    <i class="ti ti-bandage"></i>
                                                </div>
                                                <span class="hide-menu">Jenis Perawatan</span>
                                            </a>
                                        </li>
                                    @endcan
                                    @can('master-klinik')
                                        <li class="sidebar-item">
                                            <a href="{{ route('Klinik.index') }}" class="sidebar-link">
                                                <div class="round-16 d-flex align-items-center justify-content-center">
                                                    <i class="ti ti-building-hospital"></i>
                                                </div>
                                                <span class="hide-menu">Klinik</span>
                                            </a>
                                        </li>
                                    @endcan
                                    @can('master-pembayaran')
                                        <li class="sidebar-item">
                                            <a href="{{ route('MetodePembayaran.index') }}" class="sidebar-link">
                                                <div class="round-16 d-flex align-items-center justify-content-center">
                                                    <i class="ti ti-credit-card"></i>
                                                </div>
                                                <span class="hide-menu">Metode Pembayaran</span>
                                            </a>
                                        </li>
                                    @endcan
                                    @can('master-shift')
                                        <li class="sidebar-item">
                                            <a href="{{ route('MasterShift.index') }}" class="sidebar-link">
                                                <div class="round-16 d-flex align-items-center justify-content-center">
                                                    <i class="ti ti-clock"></i>
                                                </div>
                                                <span class="hide-menu">Shift</span>
                                            </a>
                                        </li>
                                    @endcan
                                </ul>
                            </li>
                        @endcan
                        @can('insentif')
                            <li class="sidebar-item">
                                <a class="sidebar-link" href="#" aria-expanded="false">
                                    <span>
                                        <i class="ti ti-cash"></i>
                                    </span>
                                    <span class="hide-menu">Insentif</span>
                                </a>
                            </li>
                        @endcan
                        @can('pengaturan')
                            <li class="sidebar-item">
                                <a class="sidebar-link" href="#" aria-expanded="false">
                                    <span>
                                        <i class="ti ti-settings"></i>
                                    </span>
                                    <span class="hide-menu">Pengaturan</span>
                                </a>
                            </li>
                        @endcan
                    </ul>
                </nav>
                <div class="fixed-profile p-3 mx-4 mb-2 bg-secondary-subtle rounded mt-3">
                    <div class="hstack gap-3">
                        <div class="john-img">
                            <img src="{{ asset('') }}assets/images/profile/user-1.jpg" class="rounded-circle"
                                width="40" height="40" alt="modernize-img" />
                        </div>
                        <div class="john-title">
                            <h6 class="mb-0 fs-4 fw-semibold">{{ session('name', 'User') }}</h6>
                            <span class="fs-2">{{ session('role', '') }}</span>
                        </div>
                        <button class="border-0 bg-transparent text-primary ms-auto" tabindex="0" type="button"
                            aria-label="logout" data-bs-toggle="tooltip" data-bs-placement="top"
                            data-bs-title="logout">
                            <i class="ti ti-power fs-6"></i>
                        </button>
                    </div>
                </div>
                <!-- ---------------------------------- -->
                <!-- Start Vertical Layout Sidebar -->
                <!-- ---------------------------------- -->
            </div>
        </aside>
        <!--  Sidebar End -->
        <div class="page-wrapper">
            <!--  Header Start -->
            <header class="topbar">
                <div class="with-vertical">
                    <nav class="navbar navbar-expand-lg p-0">
                        <ul class="navbar-nav"></ul>
                        <div class="d-block d-md-none py-4">
                            <div class="d-flex align-items-center">
                                <a href="{{ route('home') }}" class="text-nowrap logo-img me-2">
                                    <img src="{{ asset('assets/images/logos/logo-cling.png') }}" width="120px"
                                        class="dark-logo" alt="Logo-Dark" />
                                    <img src="{{ asset('assets/images/logos/logo-cling.png') }}" width="120px"
                                        class="light-logo" alt="Logo-light" />
                                </a>
                            </div>
                        </div>
                        <!-- Hamburger for mobile in header -->
                        <button class="sidebar-hamburger d-md-none ms-3" id="sidebarHamburgerBtnHeader"
                            aria-label="Toggle sidebar">
                            <i class="ti ti-menu-2"></i>
                        </button>

                        <a class="navbar-toggler nav-icon-hover-bg rounded-circle p-0 mx-0 border-0"
                            href="javascript:void(0)" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                            <i class="ti ti-dots fs-7"></i>
                        </a>

                        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                            <div class="d-flex align-items-center justify-content-between">
                                <a href="javascript:void(0)"
                                    class="nav-link nav-icon-hover-bg rounded-circle mx-0 ms-n1 d-flex d-md-none align-items-center justify-content-center"
                                    type="button" data-bs-toggle="offcanvas" data-bs-target="#mobilenavbar"
                                    aria-controls="offcanvasWithBothOptions">
                                    <i class="ti ti-align-justified fs-7"></i>
                                </a>
                                <div class="me-4 d-flex align-items-center">
                                    <span id="currentDateTime" class="fw-semibold" style="font-size: 15px;"></span>
                                </div>
                                <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-center">
                                    <!-- ... Remaining Icons ... -->
                                    <li class="nav-item dropdown">
                                        <a class="nav-link pe-0" href="javascript:void(0)" id="drop1"
                                            aria-expanded="false">
                                            <div class="d-flex align-items-center">
                                                <div class="user-profile-img">
                                                    <img src="{{ asset('') }}assets/images/profile/user-1.jpg"
                                                        class="rounded-circle" width="35" height="35"
                                                        alt="modernize-img" />
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </nav>
                    <!-- Mobilenavbar complete menu -->
                    <div class="offcanvas offcanvas-start" data-bs-scroll="true" tabindex="-1" id="mobilenavbar"
                        aria-labelledby="offcanvasWithBothOptionsLabel">
                        <nav class="sidebar-nav scroll-sidebar">
                            <div class="offcanvas-header justify-content-between">
                                <img src="{{ asset('') }}assets/images/logos/favicon.ico" alt="modernize-img"
                                    class="img-fluid" />
                                <button type="button" class="btn-close" data-bs-dismiss="offcanvas"
                                    aria-label="Close"></button>
                            </div>
                            <div class="offcanvas-body h-n80" data-simplebar>
                                <ul id="sidebarnav">
                                    <!-- ... unchanged ... -->
                                    <li class="nav-small-cap">
                                        <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                                        <span class="hide-menu">Menu</span>
                                    </li>
                                    <li class="sidebar-item">
                                        <a class="sidebar-link" href="{{ route('home') }}" aria-expanded="false">
                                            <span>
                                                <i class="ti ti-layout-dashboard"></i>
                                            </span>
                                            <span class="hide-menu">Dashboard</span>
                                        </a>
                                    </li>
                                    <li class="sidebar-item">
                                        <a class="sidebar-link" href="#" aria-expanded="false">
                                            <span>
                                                <i class="ti ti-user"></i>
                                            </span>
                                            <span class="hide-menu">Pasien</span>
                                        </a>
                                    </li>

                                    @can('pembayaran-index')
                                        <li class="sidebar-item">
                                            <a class="sidebar-link" href="{{ route('Transaksi.index') }}"
                                                aria-expanded="false">
                                                <span>
                                                    <i class="ti ti-cash"></i>
                                                </span>
                                                <span class="hide-menu">Pembayaran</span>
                                            </a>
                                        </li>
                                    @endcan

                                    @can('laporan')
                                        <li class="sidebar-item">
                                            <a class="sidebar-link has-arrow" href="javascript:void(0)"
                                                aria-expanded="false">
                                                <span>
                                                    <i class="ti ti-file-chart"></i>
                                                </span>
                                                <span class="hide-menu">Laporan</span>
                                            </a>
                                            <ul aria-expanded="false" class="collapse first-level">
                                                @can('laporan-umum')
                                                    <li class="sidebar-item">
                                                        <a href="{{ route('laporan-umum.index') }}" class="sidebar-link">
                                                            <div
                                                                class="round-16 d-flex align-items-center justify-content-center">
                                                                <i class="ti ti-report"></i>
                                                            </div>
                                                            <span class="hide-menu">Laporan Umum</span>
                                                        </a>
                                                    </li>
                                                @endcan
                                                @can('laporan-perawat')
                                                    <li class="sidebar-item">
                                                        <a href="{{ route('laporan-perawat.index') }}" class="sidebar-link">
                                                            <div
                                                                class="round-16 d-flex align-items-center justify-content-center">
                                                                <i class="ti ti-user-nurse"></i>
                                                            </div>
                                                            <span class="hide-menu">Laporan Perawat</span>
                                                        </a>
                                                    </li>
                                                @endcan
                                                @can('laporan-dokter')
                                                    <li class="sidebar-item">
                                                        <a href="{{ route('laporan-dokter.index') }}" class="sidebar-link">
                                                            <div
                                                                class="round-16 d-flex align-items-center justify-content-center">
                                                                <i class="ti ti-stethoscope"></i>
                                                            </div>
                                                            <span class="hide-menu">Laporan Dokter</span>
                                                        </a>
                                                    </li>
                                                @endcan
                                                @can('laporan-resepsionis')
                                                    <li class="sidebar-item">
                                                        <a href="{{ route('laporan-resepsionis.index') }}"
                                                            class="sidebar-link">
                                                            <div
                                                                class="round-16 d-flex align-items-center justify-content-center">
                                                                <i class="ti ti-notebook"></i>
                                                            </div>
                                                            <span class="hide-menu">Laporan Resepsionis</span>
                                                        </a>
                                                    </li>
                                                @endcan
                                            </ul>
                                        </li>
                                    @endcan

                                    @can('masterdata')
                                        <li class="sidebar-item">
                                            <a class="sidebar-link has-arrow" href="javascript:void(0)"
                                                aria-expanded="false">
                                                <span>
                                                    <i class="ti ti-database"></i>
                                                </span>
                                                <span class="hide-menu">Data Master</span>
                                            </a>
                                            <ul aria-expanded="false" class="collapse first-level">
                                                @can('master-perawatan')
                                                    <li class="sidebar-item">
                                                        <a href="{{ route('JenisPerawatan.index') }}" class="sidebar-link">
                                                            <div
                                                                class="round-16 d-flex align-items-center justify-content-center">
                                                                <i class="ti ti-bandage"></i>
                                                            </div>
                                                            <span class="hide-menu">Jenis Perawatan</span>
                                                        </a>
                                                    </li>
                                                @endcan
                                                @can('master-klinik')
                                                    <li class="sidebar-item">
                                                        <a href="{{ route('Klinik.index') }}" class="sidebar-link">
                                                            <div
                                                                class="round-16 d-flex align-items-center justify-content-center">
                                                                <i class="ti ti-building-hospital"></i>
                                                            </div>
                                                            <span class="hide-menu">Klinik</span>
                                                        </a>
                                                    </li>
                                                @endcan
                                                @can('master-metode')
                                                    <li class="sidebar-item">
                                                        <a href="{{ route('MetodePembayaran.index') }}" class="sidebar-link">
                                                            <div
                                                                class="round-16 d-flex align-items-center justify-content-center">
                                                                <i class="ti ti-credit-card"></i>
                                                            </div>
                                                            <span class="hide-menu">Metode Pembayaran</span>
                                                        </a>
                                                    </li>
                                                @endcan
                                                @can('master-shift')
                                                    <li class="sidebar-item">
                                                        <a href="{{ route('MasterShift.index') }}" class="sidebar-link">
                                                            <div
                                                                class="round-16 d-flex align-items-center justify-content-center">
                                                                <i class="ti ti-clock"></i>
                                                            </div>
                                                            <span class="hide-menu">Shift</span>
                                                        </a>
                                                    </li>
                                                @endcan
                                            </ul>
                                        </li>
                                    @endcan

                                    <li class="sidebar-item">
                                        <a class="sidebar-link" href="#" aria-expanded="false">
                                            <span>
                                                <i class="ti ti-cash"></i>
                                            </span>
                                            <span class="hide-menu">Insentif</span>
                                        </a>
                                    </li>
                                    <li class="sidebar-item">
                                        <a class="sidebar-link" href="#" aria-expanded="false">
                                            <span>
                                                <i class="ti ti-settings"></i>
                                            </span>
                                            <span class="hide-menu">Pengaturan</span>
                                        </a>
                                    </li>
                                    <li class="sidebar-item">
                                        <form action="{{ route('logout') }}" method="POST" class="m-0 p-0">
                                            @csrf
                                            <button class="sidebar-link border-0 bg-transparent w-100 text-start"
                                                tabindex="0" type="submit" aria-label="logout"
                                                style="color: #8aa3c0;">
                                                <span><i class="ti ti-power"></i></span>
                                                <span class="hide-menu">Logout</span>
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </nav>
                    </div>

                </div>
            </header>
            <!--  Header End -->
            {{-- Sidebar Samping --}}
            <aside class="left-sidebar with-vertical" id="sidebarSamping">
                <div>
                    <div class="brand-logo d-flex align-items-center justify-content-between">
                        <a href="{{ route('home') }}" class="text-nowrap logo-img">
                            <img src="{{ asset('assets/images/logos/logo-cling.png') }}" width="120px"
                                class="dark-logo" alt="Logo-Dark" />
                            <img src="{{ asset('assets/images/logos/logo-cling.png') }}" width="120px"
                                class="light-logo" alt="Logo-light" />
                        </a>
                        <a href="javascript:void(0)"
                            class="sidebartoggler ms-auto text-decoration-none fs-5 d-block d-md-none"
                            id="sidebarCloseBtnSamping" style="display: none;">
                            <i class="ti ti-x"></i>
                        </a>
                    </div>
                    <nav class="sidebar-nav scroll-sidebar" data-simplebar>
                        <ul id="sidebarnav">
                            <li class="nav-small-cap">
                                <i class="ti ti-home nav-small-cap-icon fs-4"></i>
                                <span class="hide-menu">Home</span>
                            </li>
                            <!-- <li class="sidebar-item">
                                <a class="sidebar-link" href="{{ route('home') }}" id="get-url"
                                    aria-expanded="false">
                                    <span>
                                        <i class="ti ti-layout-dashboard"></i>
                                    </span>
                                    <span class="hide-menu">Dashboard</span>
                                </a>
                            </li> -->

                            <li class="sidebar-item">
                                <a class="sidebar-link" href="{{ route('home') }}" id="get-url"
                                    aria-expanded="false">
                                    <span>
                                        <i class="ti ti-layout-dashboard"></i>
                                    </span>
                                    <span class="hide-menu">Dashboard</span>
                                </a>
                            </li>
                            @can('pembayaran')
                                <li class="sidebar-item">
                                    <a class="sidebar-link {{ Str::contains(Request::path(), 'transaksi/kasir') ? 'active' : '' }}"
                                        href="{{ route('Transaksi.index') }}" aria-expanded="false">
                                        <span>
                                            <i class="ti ti-cash"></i>
                                        </span>
                                        <span class="hide-menu">Pembayaran</span>
                                    </a>
                                </li>
                            @endcan
                            @can('laporan')
                                <li class="sidebar-item">
                                    <a class="sidebar-link has-arrow {{ request()->segment(1) == 'laporan' ? 'active' : '' }}"
                                        href="javascript:void(0)" aria-expanded="false">
                                        <span class="d-flex">
                                            <i class="ti ti-file-chart"></i>
                                        </span>
                                        <span class="hide-menu">Laporan</span>
                                    </a>
                                    <ul aria-expanded="false" class="collapse first-level">
                                        @can('laporan-umum')
                                            <li class="sidebar-item">
                                                <a href="{{ route('laporan-umum.index') }}"
                                                    class="sidebar-link {{ request()->segment(1) === 'laporan' && (request()->segment(2) === 'umum' || request()->segment(2) === 'data-umum') ? 'active' : '' }}">

                                                    <div class="round-16 d-flex justify-content-center align-items-center"
                                                        style="height: 32px; width: 32px;">
                                                        <span
                                                            style="font-size: 32px; line-height: 32px; color: #bbb; display: flex; align-items: center; justify-content: center;">•</span>
                                                    </div>
                                                    <span class="hide-menu">
                                                        Laporan Umum
                                                    </span>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('laporan-perawat')
                                            <li class="sidebar-item">
                                                <a href="{{ route('laporan-perawat.index') }}"
                                                    class="sidebar-link {{ request()->segment(1) === 'laporan' && (request()->segment(2) === 'perawat' || request()->segment(2) === 'cari-data-perawat') ? 'active' : '' }}">
                                                    <div class="round-16 d-flex justify-content-center align-items-center"
                                                        style="height: 32px; width: 32px;">
                                                        <span
                                                            style="font-size: 32px; line-height: 32px; color: #bbb; display: flex; align-items: center; justify-content: center;">•</span>
                                                    </div>
                                                    <span class="hide-menu">Laporan Perawat</span>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('laporan-dokter')
                                            <li class="sidebar-item">
                                                <a href="{{ route('laporan-dokter.index') }}"
                                                    class="sidebar-link {{ request()->segment(1) === 'laporan' && request()->segment(2) === 'dokter' ? 'active' : '' }}">
                                                    <div class="round-16 d-flex justify-content-center align-items-center"
                                                        style="height: 32px; width: 32px;">
                                                        <span
                                                            style="font-size: 32px; line-height: 32px; color: #bbb; display: flex; align-items: center; justify-content: center;">•</span>
                                                    </div>
                                                    <span class="hide-menu">Laporan Dokter</span>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('laporan-resepsionis')
                                            <li class="sidebar-item">
                                                <a href="{{ route('laporan-resepsionis.index') }}"
                                                    class="sidebar-link {{ request()->segment(1) === 'laporan' && request()->segment(2) === 'resepsionis' ? 'active' : '' }}">
                                                    <div class="round-16 d-flex justify-content-center align-items-center"
                                                        style="height: 32px; width: 32px;">
                                                        <span
                                                            style="font-size: 32px; line-height: 32px; color: #bbb; display: flex; align-items: center; justify-content: center;">•</span>
                                                    </div>
                                                    <span class="hide-menu">Laporan Resepsionis</span>
                                                </a>
                                            </li>
                                        @endcan
                                    </ul>
                                </li>
                            @endcan
                            @can('masterdata')
                                <li class="sidebar-item">
                                    <a class="sidebar-link has-arrow {{ request()->segment(1) === 'master' ? 'active' : '' }}"
                                        href="javascript:void(0)" aria-expanded="false">
                                        <span class="d-flex">
                                            <i class="ti ti-database"></i>
                                        </span>
                                        <span class="hide-menu">Data Master</span>
                                    </a>
                                    <ul aria-expanded="false" class="collapse first-level">
                                        @can('master-perawatan')
                                            <li class="sidebar-item">
                                                <a href="{{ route('JenisPerawatan.index') }}"
                                                    class="sidebar-link {{ request()->segment(1) === 'master' && request()->segment(2) === 'jenis-perawatan' ? 'active' : '' }}">
                                                    <div class="round-16 d-flex align-items-center justify-content-center">
                                                        <i class="ti ti-bandage"></i>
                                                    </div>
                                                    <span class="hide-menu">Jenis Perawatan</span>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('master-klinik')
                                            <li class="sidebar-item">
                                                <a href="{{ route('Klinik.index') }}"
                                                    class="sidebar-link {{ request()->segment(1) === 'master' && request()->segment(2) === 'klinik' ? 'active' : '' }}">
                                                    <div class="round-16 d-flex align-items-center justify-content-center">
                                                        <i class="ti ti-building-hospital"></i>
                                                    </div>
                                                    <span class="hide-menu">Klinik</span>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('master-pembayaran')
                                            <li class="sidebar-item">
                                                <a href="{{ route('MetodePembayaran.index') }}"
                                                    class="sidebar-link {{ request()->segment(1) === 'master' && request()->segment(2) === 'metode-pembayaran' ? 'active' : '' }}">
                                                    <div class="round-16 d-flex align-items-center justify-content-center">
                                                        <i class="ti ti-credit-card"></i>
                                                    </div>
                                                    <span class="hide-menu">Metode Pembayaran</span>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('master-shift')
                                            <li class="sidebar-item">
                                                <a href="{{ route('MasterShift.index') }}"
                                                    class="sidebar-link {{ request()->segment(1) === 'master' && request()->segment(2) === 'shift' ? 'active' : '' }}">
                                                    <div class="round-16 d-flex align-items-center justify-content-center">
                                                        <i class="ti ti-clock"></i>
                                                    </div>
                                                    <span class="hide-menu">Shift</span>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('master-user')
                                            <li class="sidebar-item">
                                                <a href="{{ route('users.index') }}"
                                                    class="sidebar-link {{ request()->segment(1) === 'master' && request()->segment(2) === 'pengguna' ? 'active' : '' }}">
                                                    <div class="round-16 d-flex align-items-center justify-content-center">
                                                        <i class="ti ti-clock"></i>
                                                    </div>
                                                    <span class="hide-menu">Pengguna</span>
                                                </a>
                                            </li>
                                        @endcan
                                    </ul>
                                </li>
                            @endcan
                            @can('insentif')
                                <li class="sidebar-item">
                                    <a class="sidebar-link" href="{{ route('Insentif.index') }}" aria-expanded="false">
                                        <span>
                                            <i class="ti ti-cash"></i>
                                        </span>
                                        <span class="hide-menu">Insentif</span>
                                    </a>
                                </li>
                            @endcan
                            @can('pengaturan')
                                <li class="sidebar-item">
                                    <a class="sidebar-link" href="{{ route('roles.index') }}" aria-expanded="false">
                                        <span>
                                            <i class="ti ti-settings"></i>
                                        </span>
                                        <span class="hide-menu">Pengaturan</span>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </nav>
                    <div class="fixed-profile p-3 mx-4 mb-1 bg-secondary-subtle rounded" style="margin-top: -30px;">
                        <div class="hstack gap-3">
                            <div class="john-img">
                                <img src="{{ asset('assets/images/profile/user-1.jpg') }}" class="rounded-circle"
                                    width="40" height="40" alt="modernize-img" />
                            </div>
                            <div class="john-title">
                                @php
                                    $userName = Auth::user()->name ?? 'User';
                                    $displayName =
                                        mb_strlen($userName) > 15 ? mb_substr($userName, 0, 15) . '...' : $userName;
                                @endphp
                                <h6 class="mb-0 fs-4 fw-semibold">
                                    {{ $displayName }}
                                </h6>
                                <span class="fs-2">{{ Auth::user()->role ?? '' }}</span>
                            </div>
                            <form action="{{ route('logout') }}" method="POST" class="ms-auto m-0 p-0">
                                @csrf
                                <button class="border-0 bg-transparent text-primary" tabindex="0" type="submit"
                                    aria-label="logout" data-bs-toggle="tooltip" data-bs-placement="top"
                                    data-bs-title="logout">
                                    <i class="ti ti-power fs-6"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </aside>

            <div class="body-wrapper">
                <div class="container-fluid">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>

    <div class="dark-transparent sidebartoggler"></div>
    <script src="{{ asset('') }}assets/js/vendor.min.js"></script>
    <!-- Import Js Files -->
    <script src="{{ asset('') }}assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('') }}assets/libs/simplebar/dist/simplebar.min.js"></script>
    <script src="{{ asset('') }}assets/js/theme/app.init.js"></script>
    <script src="{{ asset('') }}assets/js/theme/theme.js"></script>
    <script src="{{ asset('') }}assets/js/theme/app.min.js"></script>
    <script src="{{ asset('') }}assets/js/theme/sidebarmenu.js"></script>

    <!-- solar icons -->
    <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>

    <script src="{{ asset('') }}assets/js/highlights/highlight.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>
    <script src="{{ asset('') }}assets/js/dashboards/dashboard4.js"></script>
    <script src="{{ asset('') }}assets/libs/select2/dist/js/select2.full.min.js"></script>
    <script src="{{ asset('') }}assets/libs/select2/dist/js/select2.min.js"></script>
    <script src="{{ asset('') }}assets/js/forms/select2.init.js"></script>
    <script src="{{ asset('') }}assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="{{ asset('') }}assets/js/datatable/datatable-basic.init.js"></script>
    <script src="{{ asset('') }}assets/js/extra-libs/moment/moment.min.js"></script>
    <script
        src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-material-datetimepicker/2.7.1/js/bootstrap-material-datetimepicker.min.js">
    </script>
    <script src="{{ asset('') }}assets/js/forms/material-datepicker-init.js"></script>

    <script src="{{ asset('') }}assets/libs/daterangepicker/daterangepicker.js"></script>
    <script src="{{ asset('') }}assets/js/forms/daterangepicker-init.js"></script>
    <script src="{{ asset('') }}assets/libs/sweetalert2/dist/sweetalert2.min.js"></script>
    <script src="{{ asset('') }}assets/js/forms/sweet-alert.init.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Elements
            const sidebars = ['sidebarMain', 'sidebarSamping'].map(id => document.getElementById(id)).filter(
                Boolean);
            const hamburgerBtns = ['sidebarHamburgerBtn', 'sidebarHamburgerBtnHeader'].map(id => document
                .getElementById(id)).filter(Boolean);
            const closeBtns = ['sidebarCloseBtn', 'sidebarCloseBtnMain', 'sidebarCloseBtnSamping'].map(id =>
                document.getElementById(id)).filter(Boolean);
            const overlay = document.getElementById('sidebarOverlay');
            const hamburgerIcons = document.querySelectorAll('#hamburgerIcon');

            // State
            let isSidebarOpen = false;

            // Toggle function
            function toggleSidebar(forceState = null) {
                const newState = forceState !== null ? forceState : !isSidebarOpen;

                sidebars.forEach(sidebar => {
                    if (newState) {
                        sidebar.classList.add('sidebar-open');
                    } else {
                        sidebar.classList.remove('sidebar-open');
                    }
                });

                // Toggle overlay
                if (overlay) {
                    overlay.classList.toggle('active', newState);
                    overlay.setAttribute('aria-hidden', newState ? 'false' : 'true');
                }

                // Toggle hamburger icon animation
                hamburgerIcons.forEach(icon => {
                    icon.closest('.sidebar-hamburger')?.classList.toggle('active', newState);
                });

                // Update aria-expanded
                hamburgerBtns.forEach(btn => btn.setAttribute('aria-expanded', newState));

                // Focus management
                if (newState) {
                    // Focus first focusable element in sidebar when opened
                    const firstFocusable = sidebars[0]?.querySelector(
                        'a[href], button:not([disabled]), input:not([disabled])');
                    if (firstFocusable) setTimeout(() => firstFocusable.focus(), 300);
                    // Prevent body scroll on mobile
                    document.body.style.overflow = 'hidden';
                } else {
                    // Return focus to hamburger when closed
                    hamburgerBtns[0]?.focus();
                    document.body.style.overflow = '';
                }

                isSidebarOpen = newState;
            }

            // Event Listeners
            hamburgerBtns.forEach(btn => {
                btn?.addEventListener('click', (e) => {
                    e.stopPropagation();
                    toggleSidebar();
                });
            });

            closeBtns.forEach(btn => {
                btn?.addEventListener('click', (e) => {
                    e.stopPropagation();
                    toggleSidebar(false);
                });
            });

            // Close on overlay click
            overlay?.addEventListener('click', (e) => {
                if (e.target === overlay) {
                    toggleSidebar(false);
                }
            });

            // Close on Escape key
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && isSidebarOpen) {
                    e.preventDefault();
                    toggleSidebar(false);
                }
            });

            // Close sidebar when clicking a nav link (mobile)
            document.querySelectorAll('.left-sidebar .sidebar-link[href]').forEach(link => {
                link.addEventListener('click', function() {
                    // Only close if it's not a dropdown toggle
                    if (!this.classList.contains('has-arrow') && window.innerWidth < 768) {
                        setTimeout(() => toggleSidebar(false), 150);
                    }
                });
            });

            // Handle window resize - auto close sidebar when switching to desktop
            let resizeTimer;
            window.addEventListener('resize', function() {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(() => {
                    if (window.innerWidth >= 768 && isSidebarOpen) {
                        toggleSidebar(false);
                    }
                }, 150);
            });

            // Initialize: ensure sidebar is closed on load for mobile
            if (window.innerWidth < 768) {
                toggleSidebar(false);
            }
        });
    </script>
</body>
@stack('scripts')

</html>
<script>
    function updateDateTimeID() {
        // Bahasa Indonesia
        const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        const months = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];
        const now = new Date();
        const dayName = days[now.getDay()];
        const date = now.getDate();
        const month = months[now.getMonth()];
        const year = now.getFullYear();
        let hours = now.getHours();
        let minutes = now.getMinutes();
        let seconds = now.getSeconds();
        hours = hours < 10 ? '0' + hours : hours;
        minutes = minutes < 10 ? '0' + minutes : minutes;
        seconds = seconds < 10 ? '0' + seconds : seconds;
        const formatted = dayName + ', ' + date + ' ' + month + ' ' + year + ' | ' + hours + ':' + minutes + ':' +
            seconds;
        const el = document.getElementById('currentDateTime');
        if (el) {
            el.textContent = formatted;
        }
    }
    updateDateTimeID();
    setInterval(updateDateTimeID, 1000);
</script>
