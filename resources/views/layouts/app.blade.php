<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Dashboard | UBold - Multipurpose Admin & Dashboard Template</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description"
        content="UBold is a modern, responsive admin dashboard available on ThemeForest. Ideal for building CRM, CMS, project management tools, and custom web applications with a clean UI, flexible layouts, and rich features." />
    <meta name="keywords"
        content="UBold, admin dashboard, ThemeForest, Bootstrap 5 admin, Tailwind CSS, responsive admin, CRM dashboard, CMS admin, web app UI, admin theme, premium admin template" />
    <meta name="author" content="Coderthemes" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('') }}assets/images/favicon.ico" />
    <!-- Theme Config Js -->
    <script src="{{ asset('') }}assets/js/config.js"></script>

    <!-- Vendor css -->
    <link href="{{ asset('') }}assets/css/vendors.min.css" rel="stylesheet" type="text/css" />

    <link href="{{ asset('') }}assets/plugins/datatables/responsive.bootstrap5.min.css" rel="stylesheet"
        type="text/css" />
    <!-- App css -->
    <link id="app-style" href="{{ asset('') }}assets/css/app.min.css" rel="stylesheet" type="text/css" />
    <!-- Sweet Alert css-->
    <link href="{{ asset('') }}assets/plugins/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css" />
    <!-- Select Plugin CSS -->
    <link rel="stylesheet" href="{{ asset('') }}assets/plugins/select2/select2.min.css" />

</head>

<body>
    <!-- Begin page -->
    <div class="wrapper">
        <header class="app-topbar">
            <div class="container-fluid topbar-menu">
                <div class="d-flex align-items-center gap-2">
                    <!-- Topbar Brand Logo -->
                    <div class="logo-topbar">
                        <!-- Logo light -->
                        <a href="index.html" class="logo-light">
                            <span class="logo-lg">
                                <img src="{{ asset('') }}assets/images/logo.png" alt="logo" />
                            </span>
                            <span class="logo-sm">
                                <img src="{{ asset('') }}assets/images/logo-sm.png" alt="small logo" />
                            </span>
                        </a>

                        <!-- Logo Dark -->
                        <a href="index.html" class="logo-dark">
                            <span class="logo-lg">
                                <img src="{{ asset('') }}assets/images/logo-black.png" alt="dark logo" />
                            </span>
                            <span class="logo-sm">
                                <img src="{{ asset('') }}assets/images/logo-sm.png" alt="small logo" />
                            </span>
                        </a>
                    </div>

                    <!-- Sidebar Menu Toggle Button -->
                    <button class="sidenav-toggle-button btn btn-default btn-icon">
                        <i data-lucide="menu"></i>
                    </button>

                    <!-- Horizontal Menu Toggle Button -->
                    <button class="topnav-toggle-button px-2" data-bs-toggle="collapse" data-bs-target="#topnav-menu">
                        <i data-lucide="menu"></i>
                    </button>

                    <div id="loot-box" class="topbar-item d-none d-xl-flex">
                        <div class="dropdown">
                            <a href="#!"
                                class="topbar-link btn shadow-none btn-link dropdown-toggle drop-arrow-none px-2"
                                data-bs-toggle="dropdown">
                                Loot Box
                                <i data-lucide="chevron-down" class="ms-1"></i>
                            </a>
                            <div class="dropdown-menu">
                                <!-- My Profile -->
                                <a href="javascript:void(0);" class="dropdown-item">
                                    <i data-lucide="circle-user-round" class="me-1 fs-lg align-middle"></i>
                                    <span class="align-middle">Secret Identity</span>
                                </a>

                                <!-- Settings -->
                                <a href="javascript:void(0);" class="dropdown-item">
                                    <i data-lucide="bolt" class="me-1 fs-lg align-middle"></i>
                                    <span class="align-middle">Control Panel</span>
                                </a>

                                <!-- Support -->
                                <a href="javascript:void(0);" class="dropdown-item">
                                    <i data-lucide="headset" class="me-1 fs-lg align-middle"></i>
                                    <span class="align-middle">Help Squad</span>
                                </a>
                            </div>
                            <!-- end dropdown-menu-->
                        </div>
                        <!-- end dropdown-->
                    </div>

                    <div id="megamenu-pages" class="topbar-item d-none d-md-flex">
                        <div class="dropdown">
                            <button class="topbar-link btn fw-medium btn-link dropdown-toggle drop-arrow-none px-2"
                                data-bs-toggle="dropdown" type="button" aria-haspopup="false" aria-expanded="false">
                                Pages
                                <i data-lucide="chevron-down" class="ms-1"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-xxl p-0">
                                <div class="h-100" style="max-height: 380px" data-simplebar="">
                                    <div class="row g-0">
                                        <!-- Dashboard & Analytics -->
                                        <div class="col-md-4">
                                            <div class="p-2">
                                                <h5 class="mb-1 fw-semibold fs-sm dropdown-header">Dashboard &amp;
                                                    Analytics</h5>
                                                <ul class="list-unstyled megamenu-list">
                                                    <li>
                                                        <a href="javascript:void(0);" class="dropdown-item">
                                                            <i data-lucide="chart-line"
                                                                class="align-middle me-2 fs-16"></i>
                                                            Sales Dashboard
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="javascript:void(0);" class="dropdown-item">
                                                            <i data-lucide="lightbulb"
                                                                class="align-middle me-2 fs-16"></i>
                                                            Marketing Dashboard
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="javascript:void(0);" class="dropdown-item">
                                                            <i data-lucide="dollar-sign"
                                                                class="align-middle me-2 fs-16"></i>
                                                            Finance Overview
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="javascript:void(0);" class="dropdown-item">
                                                            <i data-lucide="users"
                                                                class="align-middle me-2 fs-16"></i>
                                                            User Analytics
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="javascript:void(0);" class="dropdown-item">
                                                            <i data-lucide="activity"
                                                                class="align-middle me-2 fs-16"></i>
                                                            Traffic Insights
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>

                                        <!-- Project Management -->
                                        <div class="col-md-4">
                                            <div class="p-2">
                                                <h5 class="mb-1 fw-semibold fs-sm dropdown-header">Project Management
                                                </h5>
                                                <ul class="list-unstyled megamenu-list">
                                                    <li>
                                                        <a href="javascript:void(0);" class="dropdown-item">
                                                            <i data-lucide="kanban"
                                                                class="align-middle me-2 fs-16"></i>
                                                            Kanban Workflow
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="javascript:void(0);" class="dropdown-item">
                                                            <i data-lucide="calendar-clock"
                                                                class="align-middle me-2 fs-16"></i>
                                                            Project Timeline
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="javascript:void(0);" class="dropdown-item">
                                                            <i data-lucide="list-check"
                                                                class="align-middle me-2 fs-16"></i>
                                                            Task Management
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="javascript:void(0);" class="dropdown-item">
                                                            <i data-lucide="users-round"
                                                                class="align-middle me-2 fs-16"></i>
                                                            Team Members
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="javascript:void(0);" class="dropdown-item">
                                                            <i data-lucide="clipboard-type"
                                                                class="align-middle me-2 fs-16"></i>
                                                            Assignments
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>

                                        <!-- User Management -->
                                        <div class="col-md-4">
                                            <div class="p-2">
                                                <h5 class="mb-1 fw-semibold fs-sm dropdown-header">User Management</h5>
                                                <ul class="list-unstyled megamenu-list">
                                                    <li>
                                                        <a href="javascript:void(0);" class="dropdown-item">
                                                            <i data-lucide="circle-user-round"
                                                                class="align-middle me-2 fs-16"></i>
                                                            User Profiles
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="javascript:void(0);" class="dropdown-item">
                                                            <i data-lucide="lock-keyhole"
                                                                class="align-middle me-2 fs-16"></i>
                                                            Access Control
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="javascript:void(0);" class="dropdown-item">
                                                            <i data-lucide="settings"
                                                                class="align-middle me-2 fs-16"></i>
                                                            Security Settings
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="javascript:void(0);" class="dropdown-item">
                                                            <i data-lucide="users"
                                                                class="align-middle me-2 fs-16"></i>
                                                            User Groups
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="javascript:void(0);" class="dropdown-item">
                                                            <i data-lucide="key" class="align-middle me-2 fs-16"></i>
                                                            Authentication
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- end .h-100-->
                            </div>
                            <!-- .dropdown-menu-->
                        </div>
                        <!-- .dropdown-->
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <div id="search-box-rounded-right" class="app-search d-none d-xl-flex">
                        <input type="search" class="form-control rounded-pill topbar-search" name="search"
                            placeholder="Quick Search..." />
                        <i data-lucide="search" class="app-search-icon text-muted"></i>
                    </div>

                    <div id="theme-dropdown" class="topbar-item d-none d-sm-flex">
                        <div class="dropdown">
                            <button class="topbar-link" data-bs-toggle="dropdown" type="button"
                                aria-haspopup="false" aria-expanded="false">
                                <i data-lucide="sun" class="topbar-link-icon d-none" id="theme-icon-light"></i>
                                <i data-lucide="moon" class="topbar-link-icon d-none" id="theme-icon-dark"></i>
                                <i data-lucide="sun-moon" class="topbar-link-icon d-none" id="theme-icon-system"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end" data-thememode="dropdown">
                                <label class="dropdown-item cursor-pointer">
                                    <input class="form-check-input" type="radio" name="data-bs-theme"
                                        value="light" style="display: none" />
                                    <i data-lucide="sun" class="align-middle me-1 fs-16"></i>
                                    <span class="align-middle">Light</span>
                                </label>
                                <label class="dropdown-item cursor-pointer">
                                    <input class="form-check-input" type="radio" name="data-bs-theme"
                                        value="dark" style="display: none" />
                                    <i data-lucide="moon" class="align-middle me-1 fs-16"></i>
                                    <span class="align-middle">Dark</span>
                                </label>
                                <label class="dropdown-item cursor-pointer">
                                    <input class="form-check-input" type="radio" name="data-bs-theme"
                                        value="system" style="display: none" />
                                    <i data-lucide="sun-moon" class="align-middle me-1 fs-16"></i>
                                    <span class="align-middle">System</span>
                                </label>
                            </div>
                            <!-- end dropdown-menu-->
                        </div>
                        <!-- end dropdown-->
                    </div>

                    <div id="apps-dropdown-grid" class="topbar-item d-none d-xl-flex">
                        <div class="dropdown">
                            <button class="topbar-link dropdown-toggle drop-arrow-none" data-bs-toggle="dropdown"
                                type="button" data-bs-auto-close="outside" aria-haspopup="false"
                                aria-expanded="false">
                                <i data-lucide="layout-grid" class="topbar-link-icon"></i>
                            </button>

                            <div class="dropdown-menu dropdown-menu-lg p-2 dropdown-menu-end">
                                <div class="row align-items-center g-1">
                                    <div class="col-4">
                                        <a href="javascript:void(0);"
                                            class="dropdown-item border border-dashed rounded text-center py-2">
                                            <span class="avatar-sm d-block mx-auto mb-1">
                                                <span class="avatar-title text-bg-light rounded-circle">
                                                    <img src="{{ asset('') }}assets/images/logos/google.svg"
                                                        alt="Google Logo" height="18" />
                                                </span>
                                            </span>
                                            <span class="align-middle fw-medium">Google</span>
                                        </a>
                                    </div>

                                    <div class="col-4">
                                        <a href="javascript:void(0);"
                                            class="dropdown-item border border-dashed rounded text-center py-2">
                                            <span class="avatar-sm d-block mx-auto mb-1">
                                                <span class="avatar-title text-bg-light rounded-circle">
                                                    <img src="{{ asset('') }}assets/images/logos/figma.svg"
                                                        alt="Figma Logo" height="18" />
                                                </span>
                                            </span>
                                            <span class="align-middle fw-medium">Figma</span>
                                        </a>
                                    </div>

                                    <div class="col-4">
                                        <a href="javascript:void(0);"
                                            class="dropdown-item border border-dashed rounded text-center py-2">
                                            <span class="avatar-sm d-block mx-auto mb-1">
                                                <span class="avatar-title text-bg-light rounded-circle">
                                                    <img src="{{ asset('') }}assets/images/logos/slack.svg"
                                                        alt="Slack Logo" height="18" />
                                                </span>
                                            </span>
                                            <span class="align-middle fw-medium">Slack</span>
                                        </a>
                                    </div>

                                    <div class="col-4">
                                        <a href="javascript:void(0);"
                                            class="dropdown-item border border-dashed rounded text-center py-2">
                                            <span class="avatar-sm d-block mx-auto mb-1">
                                                <span class="avatar-title text-bg-light rounded-circle">
                                                    <img src="{{ asset('') }}assets/images/logos/dropbox.svg"
                                                        alt="Dropbox Logo" height="18" />
                                                </span>
                                            </span>
                                            <span class="align-middle fw-medium">Dropbox</span>
                                        </a>
                                    </div>

                                    <div class="col-4 text-center">
                                        <a href="javascript:void(0);"
                                            class="btn btn-sm rounded-circle btn-icon btn-danger">
                                            <i data-lucide="circle-plus" class="fs-18"></i>
                                        </a>
                                    </div>

                                    <div class="col-4">
                                        <a href="javascript:void(0);"
                                            class="dropdown-item border border-dashed rounded text-center py-2">
                                            <span class="avatar-sm d-block mx-auto mb-1">
                                                <span
                                                    class="avatar-title bg-primary-subtle text-primary rounded-circle">
                                                    <i data-lucide="calendar" class="fs-18"></i>
                                                </span>
                                            </span>
                                            <span class="align-middle fw-medium">Calendar</span>
                                        </a>
                                    </div>

                                    <div class="col-4">
                                        <a href="javascript:void(0);"
                                            class="dropdown-item border border-dashed rounded text-center py-2">
                                            <span class="avatar-sm d-block mx-auto mb-1">
                                                <span
                                                    class="avatar-title bg-primary-subtle text-primary rounded-circle">
                                                    <i data-lucide="message-circle" class="fs-18"></i>
                                                </span>
                                            </span>
                                            <span class="align-middle fw-medium">Chat</span>
                                        </a>
                                    </div>

                                    <div class="col-4">
                                        <a href="javascript:void(0);"
                                            class="dropdown-item border border-dashed rounded text-center py-2">
                                            <span class="avatar-sm d-block mx-auto mb-1">
                                                <span
                                                    class="avatar-title bg-primary-subtle text-primary rounded-circle">
                                                    <i data-lucide="folder" class="fs-18"></i>
                                                </span>
                                            </span>
                                            <span class="align-middle fw-medium">Files</span>
                                        </a>
                                    </div>

                                    <div class="col-4">
                                        <a href="javascript:void(0);"
                                            class="dropdown-item border border-dashed rounded text-center py-2">
                                            <span class="avatar-sm d-block mx-auto mb-1">
                                                <span
                                                    class="avatar-title bg-primary-subtle text-primary rounded-circle">
                                                    <i data-lucide="users" class="fs-18"></i>
                                                </span>
                                            </span>
                                            <span class="align-middle fw-medium">Team</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <!-- End dropdown-menu -->
                        </div>
                        <!-- end dropdown-->
                    </div>

                    <div id="notification-dropdown-people" class="topbar-item">
                        <div class="dropdown">
                            <button class="topbar-link dropdown-toggle drop-arrow-none" data-bs-toggle="dropdown"
                                type="button" data-bs-auto-close="outside" aria-haspopup="false"
                                aria-expanded="false">
                                <i data-lucide="bell" class="topbar-link-icon animate-ring"></i>
                                <span class="badge text-bg-danger badge-circle topbar-badge">5</span>
                            </button>

                            <div class="dropdown-menu p-0 dropdown-menu-end dropdown-menu-lg">
                                <div class="px-3 py-2 border-bottom">
                                    <div class="row align-items-center">
                                        <div class="col">
                                            <h6 class="m-0 fs-md fw-semibold">Notifications</h6>
                                        </div>
                                        <div class="col text-end">
                                            <a href="#!" class="badge badge-soft-success badge-label py-1">07
                                                Notifications</a>
                                        </div>
                                    </div>
                                </div>

                                <div style="max-height: 300px" data-simplebar="">
                                    <!-- Notification 1 -->
                                    <div class="dropdown-item notification-item py-2 text-wrap" id="message-1">
                                        <span class="d-flex align-items-center gap-3">
                                            <span class="flex-shrink-0 position-relative">
                                                <img src="{{ asset('') }}assets/images/users/user-4.jpg"
                                                    class="avatar-md rounded-circle" alt="User Avatar" />
                                                <span
                                                    class="position-absolute rounded-pill bg-success notification-badge">
                                                    <i data-lucide="bell" class="align-middle"></i>
                                                    <span class="visually-hidden">unread notification</span>
                                                </span>
                                            </span>
                                            <span class="flex-grow-1 text-muted">
                                                <span class="fw-medium text-body">Emily Johnson</span>
                                                commented on a task in
                                                <span class="fw-medium text-body">Design Sprint</span>
                                                <br />
                                                <span class="fs-xs">12 minutes ago</span>
                                            </span>
                                            <button type="button"
                                                class="flex-shrink-0 text-muted btn btn-link p-0 position-absolute end-0 me-2 d-none noti-close-btn"
                                                data-dismissible="#message-1">
                                                <i data-lucide="x-square" class="fs-xxl"></i>
                                            </button>
                                        </span>
                                    </div>

                                    <!-- Notification 2 -->
                                    <div class="dropdown-item notification-item py-2 text-wrap" id="message-2">
                                        <span class="d-flex align-items-center gap-3">
                                            <span class="flex-shrink-0 position-relative">
                                                <img src="{{ asset('') }}assets/images/users/user-5.jpg"
                                                    class="avatar-md rounded-circle" alt="User Avatar" />
                                                <span
                                                    class="position-absolute rounded-pill bg-info notification-badge">
                                                    <i data-lucide="cloud-upload" class="align-middle"></i>
                                                    <span class="visually-hidden">upload notification</span>
                                                </span>
                                            </span>
                                            <span class="flex-grow-1 text-muted">
                                                <span class="fw-medium text-body">Michael Lee</span>
                                                uploaded files to
                                                <span class="fw-medium text-body">Marketing Assets</span>
                                                <br />
                                                <span class="fs-xs">25 minutes ago</span>
                                            </span>
                                            <button type="button"
                                                class="flex-shrink-0 text-muted btn btn-link p-0 position-absolute end-0 me-2 d-none noti-close-btn"
                                                data-dismissible="#message-2">
                                                <i data-lucide="x-square" class="fs-xxl"></i>
                                            </button>
                                        </span>
                                    </div>

                                    <!-- Notification 3 - Server CPU Alert -->
                                    <div class="dropdown-item notification-item py-2 text-wrap" id="message-6">
                                        <span class="d-flex align-items-center gap-3">
                                            <span class="flex-shrink-0 position-relative">
                                                <span
                                                    class="avatar-md rounded-circle bg-light d-flex align-items-center justify-content-center">
                                                    <i data-lucide="database" class="fs-4"></i>
                                                </span>
                                                <span
                                                    class="position-absolute rounded-pill bg-danger notification-badge">
                                                    <i data-lucide="circle-alert" class="align-middle"></i>
                                                    <span class="visually-hidden">server alert</span>
                                                </span>
                                            </span>
                                            <span class="flex-grow-1 text-muted">
                                                <span class="fw-medium text-body">Server #3</span>
                                                CPU usage exceeded 90%
                                                <br />
                                                <span class="fs-xs">Just now</span>
                                            </span>
                                            <button type="button"
                                                class="flex-shrink-0 text-muted btn btn-link p-0 position-absolute end-0 me-2 d-none noti-close-btn"
                                                data-dismissible="#message-6">
                                                <i data-lucide="x-square" class="fs-xxl"></i>
                                            </button>
                                        </span>
                                    </div>

                                    <!-- Notification 4 -->
                                    <div class="dropdown-item notification-item py-2 text-wrap" id="message-3">
                                        <span class="d-flex align-items-center gap-3">
                                            <span class="flex-shrink-0 position-relative">
                                                <img src="{{ asset('') }}assets/images/users/user-6.jpg"
                                                    class="avatar-md rounded-circle" alt="User Avatar" />
                                                <span
                                                    class="position-absolute rounded-pill bg-warning notification-badge">
                                                    <i data-lucide="alert-triangle" class="align-middle"></i>
                                                    <span class="visually-hidden">alert</span>
                                                </span>
                                            </span>
                                            <span class="flex-grow-1 text-muted">
                                                <span class="fw-medium text-body">Sophia Ray</span>
                                                flagged an issue in
                                                <span class="fw-medium text-body">Bug Tracker</span>
                                                <br />
                                                <span class="fs-xs">40 minutes ago</span>
                                            </span>
                                            <button type="button"
                                                class="flex-shrink-0 text-muted btn btn-link p-0 position-absolute end-0 me-2 d-none noti-close-btn"
                                                data-dismissible="#message-3">
                                                <i data-lucide="x-square" class="fs-xxl"></i>
                                            </button>
                                        </span>
                                    </div>

                                    <!-- Notification 5 -->
                                    <div class="dropdown-item notification-item py-2 text-wrap" id="message-4">
                                        <span class="d-flex align-items-center gap-3">
                                            <span class="flex-shrink-0 position-relative">
                                                <img src="{{ asset('') }}assets/images/users/user-7.jpg"
                                                    class="avatar-md rounded-circle" alt="User Avatar" />
                                                <span
                                                    class="position-absolute rounded-pill bg-primary notification-badge">
                                                    <i data-lucide="calendar-check" class="align-middle"></i>
                                                    <span class="visually-hidden">event notification</span>
                                                </span>
                                            </span>
                                            <span class="flex-grow-1 text-muted">
                                                <span class="fw-medium text-body">David Kim</span>
                                                scheduled a meeting for
                                                <span class="fw-medium text-body">UX Review</span>
                                                <br />
                                                <span class="fs-xs">1 hour ago</span>
                                            </span>
                                            <button type="button"
                                                class="flex-shrink-0 text-muted btn btn-link p-0 position-absolute end-0 me-2 d-none noti-close-btn"
                                                data-dismissible="#message-4">
                                                <i data-lucide="x-square" class="fs-xxl"></i>
                                            </button>
                                        </span>
                                    </div>

                                    <!-- Notification 6 -->
                                    <div class="dropdown-item notification-item py-2 text-wrap" id="message-5">
                                        <span class="d-flex align-items-center gap-3">
                                            <span class="flex-shrink-0 position-relative">
                                                <img src="{{ asset('') }}assets/images/users/user-8.jpg"
                                                    class="avatar-md rounded-circle" alt="User Avatar" />
                                                <span
                                                    class="position-absolute rounded-pill bg-secondary notification-badge">
                                                    <i data-lucide="square-pen" class="align-middle"></i>
                                                    <span class="visually-hidden">edit</span>
                                                </span>
                                            </span>
                                            <span class="flex-grow-1 text-muted">
                                                <span class="fw-medium text-body">Isabella White</span>
                                                updated the document in
                                                <span class="fw-medium text-body">Product Specs</span>
                                                <br />
                                                <span class="fs-xs">2 hours ago</span>
                                            </span>
                                            <button type="button"
                                                class="flex-shrink-0 text-muted btn btn-link p-0 position-absolute end-0 me-2 d-none noti-close-btn"
                                                data-dismissible="#message-5">
                                                <i data-lucide="x-square" class="fs-xxl"></i>
                                            </button>
                                        </span>
                                    </div>

                                    <!-- Notification 7 - Deployment Success -->
                                    <div class="dropdown-item notification-item py-2 text-wrap" id="message-7">
                                        <span class="d-flex align-items-center gap-3">
                                            <span class="flex-shrink-0 position-relative">
                                                <span
                                                    class="avatar-md rounded-circle bg-light d-flex align-items-center justify-content-center">
                                                    <i data-lucide="rocket" class="fs-4"></i>
                                                </span>
                                                <span
                                                    class="position-absolute rounded-pill bg-success notification-badge">
                                                    <i data-lucide="check" class="align-middle"></i>
                                                    <span class="visually-hidden">deployment</span>
                                                </span>
                                            </span>
                                            <span class="flex-grow-1 text-muted">
                                                <span class="fw-medium text-body">Production Server</span>
                                                deployment completed successfully
                                                <br />
                                                <span class="fs-xs">30 minutes ago</span>
                                            </span>
                                            <button type="button"
                                                class="flex-shrink-0 text-muted btn btn-link p-0 position-absolute end-0 me-2 d-none noti-close-btn"
                                                data-dismissible="#message-7">
                                                <i data-lucide="x-square" class="fs-xxl"></i>
                                            </button>
                                        </span>
                                    </div>
                                </div>

                                <!-- All-->
                                <a href="javascript:void(0);"
                                    class="dropdown-item text-center text-reset text-decoration-underline link-offset-2 fw-bold notify-item border-top border-light py-2">Read
                                    All Messages</a>
                            </div>
                            <!-- End dropdown-menu -->
                        </div>
                        <!-- end dropdown-->
                    </div>

                    <div id="fullscreen-toggler" class="topbar-item d-none d-md-flex">
                        <button class="topbar-link" type="button" data-toggle="fullscreen">
                            <i data-lucide="maximize" class="topbar-link-icon"></i>
                            <i data-lucide="minimize" class="topbar-link-icon d-none"></i>
                        </button>
                    </div>

                    <div id="monochrome-toggler" class="topbar-item d-none d-xl-flex">
                        <button id="monochrome-mode" class="topbar-link" type="button" data-toggle="monochrome">
                            <i data-lucide="palette" class="topbar-link-icon"></i>
                        </button>
                    </div>

                    <div class="topbar-item d-none d-sm-flex">
                        <button class="topbar-link btn-theme-setting" data-bs-toggle="offcanvas"
                            data-bs-target="#theme-settings-offcanvas" type="button">
                            <i data-lucide="settings" class="topbar-link-icon"></i>
                        </button>
                    </div>

                    <div id="language-selector-rounded" class="topbar-item">
                        <div class="dropdown">
                            <button class="topbar-link fw-bold" data-bs-toggle="dropdown" type="button"
                                aria-haspopup="false" aria-expanded="false">
                                <img src="{{ asset('') }}assets/images/flags/us.svg" alt="user-image"
                                    class="rounded-circle me-2" height="18" id="selected-language-image" />
                                <span id="selected-language-code">EN</span>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a href="javascript:void(0);" class="dropdown-item" data-translator-lang="en"
                                    title="English">
                                    <img src="{{ asset('') }}assets/images/flags/us.svg" alt="English"
                                        class="me-1 rounded-circle" height="18" data-translator-image="" />
                                    <span class="align-middle">English</span>
                                </a>
                                <a href="javascript:void(0);" class="dropdown-item" data-translator-lang="de"
                                    title="German">
                                    <img src="{{ asset('') }}assets/images/flags/de.svg" alt="German"
                                        class="me-1 rounded-circle" height="18" data-translator-image="" />
                                    <span class="align-middle">Deutsch</span>
                                </a>
                                <a href="javascript:void(0);" class="dropdown-item" data-translator-lang="it"
                                    title="Italian">
                                    <img src="{{ asset('') }}assets/images/flags/it.svg" alt="Italian"
                                        class="me-1 rounded-circle" height="18" data-translator-image="" />
                                    <span class="align-middle">Italiano</span>
                                </a>
                                <a href="javascript:void(0);" class="dropdown-item" data-translator-lang="es"
                                    title="Spanish">
                                    <img src="{{ asset('') }}assets/images/flags/es.svg" alt="Spanish"
                                        class="me-1 rounded-circle" height="18" data-translator-image="" />
                                    <span class="align-middle">Español</span>
                                </a>
                                <a href="javascript:void(0);" class="dropdown-item" data-translator-lang="ru"
                                    title="Russian">
                                    <img src="{{ asset('') }}assets/images/flags/ru.svg" alt="Russian"
                                        class="me-1 rounded-circle" height="18" data-translator-image="" />
                                    <span class="align-middle">Русский</span>
                                </a>
                                <a href="javascript:void(0);" class="dropdown-item" data-translator-lang="hi"
                                    title="Hindi">
                                    <img src="{{ asset('') }}assets/images/flags/in.svg" alt="Hindi"
                                        class="me-1 rounded-circle" height="18" data-translator-image="" />
                                    <span class="align-middle">हिन्दी</span>
                                </a>
                                <a href="javascript:void(0);" class="dropdown-item" data-translator-lang="ar"
                                    title="Arabic">
                                    <img src="{{ asset('') }}assets/images/flags/sa.svg" alt="Arabic"
                                        class="me-1 rounded-circle" height="18" data-translator-image="" />
                                    <span class="align-middle">عربي</span>
                                </a>
                            </div>
                            <!-- end dropdown-menu-->
                        </div>
                        <!-- end dropdown-->
                    </div>

                    <div id="simple-user-dropdown" class="topbar-item nav-user">
                        <div class="dropdown">
                            <a class="topbar-link dropdown-toggle drop-arrow-none px-2" data-bs-toggle="dropdown"
                                href="#!" aria-haspopup="false" aria-expanded="false">
                                <img src="{{ asset('') }}assets/images/users/user-1.jpg" width="32"
                                    class="rounded-circle me-lg-2 d-flex" alt="user-image" />
                                <div class="d-lg-flex align-items-center gap-1 d-none">
                                    <h5 class="my-0">Geneva K.</h5>
                                    <i data-lucide="chevron-down" class="align-middle"></i>
                                </div>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <!-- Header -->
                                <div class="dropdown-header noti-title">
                                    <h6 class="text-overflow m-0">Welcome back!</h6>
                                </div>

                                <!-- My Profile -->
                                <a href="#!" class="dropdown-item">
                                    <i data-lucide="circle-user-round" class="me-1 fs-lg align-middle"></i>
                                    <span class="align-middle">Profile</span>
                                </a>

                                <!-- Notifications -->
                                <a href="javascript:void(0);" class="dropdown-item">
                                    <i data-lucide="bell-ring" class="me-1 fs-lg align-middle"></i>
                                    <span class="align-middle">Notifications</span>
                                </a>

                                <!-- Wallet -->
                                <a href="javascript:void(0);" class="dropdown-item">
                                    <i data-lucide="credit-card" class="me-1 fs-lg align-middle"></i>
                                    <span class="align-middle">
                                        Balance:
                                        <span class="fw-semibold">$985.25</span>
                                    </span>
                                </a>

                                <!-- Settings -->
                                <a href="javascript:void(0);" class="dropdown-item">
                                    <i data-lucide="bolt" class="me-1 fs-lg align-middle"></i>
                                    <span class="align-middle">Account Settings</span>
                                </a>

                                <!-- Support -->
                                <a href="javascript:void(0);" class="dropdown-item">
                                    <i data-lucide="headset" class="me-1 fs-lg align-middle"></i>
                                    <span class="align-middle">Support Center</span>
                                </a>

                                <!-- Divider -->
                                <div class="dropdown-divider"></div>

                                <!-- Lock -->
                                <a href="auth-lock-screen.html" class="dropdown-item">
                                    <i data-lucide="lock-keyhole" class="me-1 fs-lg align-middle"></i>
                                    <span class="align-middle">Lock Screen</span>
                                </a>

                                <!-- Logout -->
                                <a href="javascript:void(0);" class="dropdown-item text-danger fw-semibold">
                                    <i data-lucide="log-out" class="me-1 fs-lg align-middle"></i>
                                    <span class="align-middle">Log Out</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <!-- Topbar End -->

        <div class="modal fade" id="searchModal" tabindex="-1" role="dialog" aria-labelledby="searchModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content bg-transparent">
                    <form>
                        <div class="card mb-1">
                            <div class="px-3 py-2 d-flex flex-row align-items-center" id="top-search">
                                <i data-lucide="search" class="fs-22"></i>
                                <input type="search" class="form-control border-0" id="search-modal-input"
                                    placeholder="Search for actions, people," />
                                <button type="submit" class="btn p-0" data-bs-dismiss="modal"
                                    aria-label="Close">[esc]</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="sidenav-menu">
            <!-- Brand Logo -->
            <a href="index.html" class="logo">
                <span class="logo logo-light">
                    <span class="logo-lg"><img src="{{ asset('') }}assets/images/logo.png"
                            alt="logo" /></span>
                    <span class="logo-sm"><img src="{{ asset('') }}assets/images/logo-sm.png"
                            alt="small logo" /></span>
                </span>

                <span class="logo logo-dark">
                    <span class="logo-lg"><img src="{{ asset('') }}assets/images/logo-black.png"
                            alt="dark logo" /></span>
                    <span class="logo-sm"><img src="{{ asset('') }}assets/images/logo-sm.png"
                            alt="small logo" /></span>
                </span>
            </a>

            <!-- Sidebar Hover Menu Toggle Button -->
            <button class="button-on-hover">
                <span class="btn-on-hover-icon"></span>
            </button>

            <!-- Full Sidebar Menu Close Button -->
            <button class="button-close-offcanvas">
                <i data-lucide="menu" class="align-middle"></i>
            </button>

            <div class="scrollbar" data-simplebar="">
                <div id="user-profile-settings" class="sidenav-user"
                    style="background: url({{ asset('') }}assets/images/user-bg-pattern.svg)">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <a href="#!" class="link-reset">
                                <img src="{{ asset('') }}assets/images/users/user-1.jpg" alt="user-image"
                                    class="rounded-circle mb-2 avatar-md" />
                                <span class="sidenav-user-name fw-bold">Geneva K.</span>
                                <span class="fs-12 fw-semibold" data-lang="user-role">Art Director</span>
                            </a>
                        </div>
                        <div>
                            <a class="dropdown-toggle drop-arrow-none link-reset sidenav-user-set-icon"
                                data-bs-toggle="dropdown" data-bs-offset="0,12" href="#!" aria-haspopup="false"
                                aria-expanded="false">
                                <i data-lucide="settings" class="fs-24 align-middle ms-1"></i>
                            </a>

                            <div class="dropdown-menu">
                                <!-- Header -->
                                <div class="dropdown-header noti-title">
                                    <h6 class="text-overflow m-0">Welcome back!</h6>
                                </div>

                                <!-- My Profile -->
                                <a href="#!" class="dropdown-item">
                                    <i data-lucide="circle-user-round" class="me-1 fs-lg align-middle"></i>
                                    <span class="align-middle">Profile</span>
                                </a>

                                <!-- Settings -->
                                <a href="javascript:void(0);" class="dropdown-item">
                                    <i data-lucide="bolt" class="me-1 fs-lg align-middle"></i>
                                    <span class="align-middle">Account Settings</span>
                                </a>

                                <!-- Lock -->
                                <a href="auth-lock-screen.html" class="dropdown-item">
                                    <i data-lucide="lock-keyhole" class="me-1 fs-lg align-middle"></i>
                                    <span class="align-middle">Lock Screen</span>
                                </a>

                                <!-- Logout -->
                                <a href="javascript:void(0);" class="dropdown-item text-danger fw-semibold">
                                    <i data-lucide="log-out" class="me-1 fs-lg align-middle"></i>
                                    <span class="align-middle">Log Out</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!--- Sidenav Menu -->
                <div id="sidenav-menu">
                    <ul class="side-nav">
                        <li class="side-nav-title mt-2" data-lang="main">Menu</li>

                        <li class="side-nav-item">
                            <a href="#" class="side-nav-link">
                                <span class="menu-icon"><i data-lucide="layout-dashboard"></i></span>
                                <span class="menu-text">Dashboard Report</span>
                            </a>
                        </li>
                        <li class="side-nav-item">
                            <a href="{{ route('Transaksi.create') }}" class="side-nav-link">
                                <span class="menu-icon"><i data-lucide="shopping-cart"></i></span>
                                <span class="menu-text">Transaksi Kasir</span>
                            </a>
                        </li>
                        <li class="side-nav-item">
                            <a href="#" class="side-nav-link">
                                <span class="menu-icon"><i data-lucide="users"></i></span>
                                <span class="menu-text">Pasien</span>
                            </a>
                        </li>
                        <li class="side-nav-item">
                            <a href="#" class="side-nav-link">
                                <span class="menu-icon"><i data-lucide="heart-pulse"></i></span>
                                <span class="menu-text">Data Perawatan</span>
                            </a>
                        </li>
                        <li class="side-nav-item">
                            <a href="#" class="side-nav-link">
                                <span class="menu-icon"><i data-lucide="file-text"></i></span>
                                <span class="menu-text">Laporan</span>
                            </a>
                        </li>
                        <li class="side-nav-item">
                            <a href="#" class="side-nav-link">
                                <span class="menu-icon"><i data-lucide="gift"></i></span>
                                <span class="menu-text">Insentif</span>
                            </a>
                        </li>
                        <li class="side-nav-item">
                            <a data-bs-toggle="collapse" href="#data-master" aria-expanded="false"
                                aria-controls="data-master" class="side-nav-link">
                                <span class="menu-icon"><i data-lucide="database"></i></span>
                                <span class="menu-text" data-lang="data-master">Data Master</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <div class="collapse" id="data-master">
                                <ul class="sub-menu">
                                    <li class="side-nav-item">
                                        <a href="{{ route('JenisPerawatan.index') }}" class="side-nav-link">
                                            <span class="menu-icon"><i data-lucide="package"></i></span>
                                            <span class="menu-text" data-lang="master-produk">Jenis Perawatan</span>
                                        </a>
                                    </li>
                                    <li class="side-nav-item">
                                        <a href="{{ route('Klinik.index') }}" class="side-nav-link">
                                            <span class="menu-icon"><i data-lucide="user"></i></span>
                                            <span class="menu-text" data-lang="master-pasien">Master Klinik</span>
                                        </a>
                                    </li>
                                    <li class="side-nav-item">
                                        <a href="{{ route('MetodePembayaran.index') }}" class="side-nav-link">
                                            <span class="menu-icon"><i data-lucide="stethoscope"></i></span>
                                            <span class="menu-text" data-lang="master-dokter">Metode Pembayaran</span>
                                        </a>
                                    </li>
                                    <li class="side-nav-item">
                                        <a href="{{ route('MasterShift.index') }}" class="side-nav-link">
                                            <span class="menu-icon"><i data-lucide="users"></i></span>
                                            <span class="menu-text" data-lang="master-karyawan">Shift Kerja</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>


                        <li class="side-nav-item">
                            <a href="#" class="side-nav-link">
                                <span class="menu-icon"><i data-lucide="settings"></i></span>
                                <span class="menu-text">Pengaturan</span>
                            </a>
                        </li>
                    </ul>

                </div>
            </div>
        </div>
        <!-- Sidenav Menu End -->


        <!-- ============================================================== -->
        <!-- Start Main Content -->
        <!-- ============================================================== -->

        <div class="content-page">
            <div class="container-fluid">
                @yield('content')
            </div>
            <!-- end table-responsive-->
        </div>
        <!-- end card-body-->

        <div class="card-footer border-0">
            <div class="align-items-center justify-content-between row text-center text-sm-start">
                <div class="col-sm">
                    <div data-table-pagination-info="orders"></div>
                </div>
                <div class="col-sm-auto mt-3 mt-sm-0">
                    <div data-table-pagination></div>
                </div>
                <!-- end col-->
            </div>
            <!-- end row-->
        </div>
        <!-- end card-footer-->
    </div>
    <!-- end card-->
    </div>
    <!-- end col-->
    </div>
    <!-- end row-->
    </div>
    <!-- container -->

    <!-- Footer Start -->
    <footer class="footer">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 text-center">
                    ©
                    <span data-current-year></span>
                    UBold By <span class="fw-semibold">Coderthemes</span>
                </div>
            </div>
        </div>
    </footer>
    <!-- end Footer -->

    </div>

    <!-- ============================================================== -->
    <!-- End of Main Content -->
    <!-- ============================================================== -->
    </div>
    <!-- END wrapper -->

    <div class="offcanvas offcanvas-end overflow-hidden" tabindex="-1" id="theme-settings-offcanvas">
        <div class="d-flex justify-content-between text-bg-primary gap-2 p-3"
            style="background-image: url({{ asset('') }}assets/images/settings-bg.png)">
            <div>
                <h5 class="mb-1 fw-bold text-white text-uppercase">Admin Customizer</h5>
                <p class="text-white text-opacity-75 fst-italic fw-medium mb-0">Easily configure layout, styles, and
                    preferences for your admin interface.</p>
            </div>

            <div class="flex-grow-0">
                <button type="button"
                    class="d-block btn btn-sm bg-white bg-opacity-25 text-white rounded-circle btn-icon"
                    data-bs-dismiss="offcanvas">
                    <i data-lucide="x" class="fs-lg"></i>
                </button>
            </div>
        </div>

        <div class="offcanvas-body theme-customizer-bar p-0 h-100" data-simplebar="">
            <div id="skin" class="p-3 border-bottom border-dashed">
                <h5 class="mb-3 fw-bold">Select Theme</h5>
                <div class="row g-3">
                    <div class="col-6" id="skin-default">
                        <div class="form-check card-radio">
                            <input class="form-check-input" type="radio" name="data-skin" id="demo-skin-default"
                                value="default" />
                            <label class="form-check-label p-0 w-100" for="demo-skin-default">
                                <img src="{{ asset('') }}assets/images/layouts/skin-default.png" alt="layout-img"
                                    class="img-fluid" />
                            </label>
                        </div>
                        <h5 class="text-center text-muted mt-2 mb-0">Default</h5>
                    </div>

                    <div class="col-6" id="skin-minimal">
                        <div class="form-check card-radio">
                            <input class="form-check-input" type="radio" name="data-skin" id="demo-skin-minimal"
                                value="minimal" />
                            <label class="form-check-label p-0 w-100" for="demo-skin-minimal">
                                <img src="{{ asset('') }}assets/images/layouts/skin-minimal.png" alt="layout-img"
                                    class="img-fluid" />
                            </label>
                        </div>
                        <h5 class="text-center text-muted mt-2 mb-0">Minimal</h5>
                    </div>

                    <div class="col-6" id="skin-modern">
                        <div class="form-check card-radio">
                            <input class="form-check-input" type="radio" name="data-skin" id="demo-skin-modern"
                                value="modern" />
                            <label class="form-check-label p-0 w-100" for="demo-skin-modern">
                                <img src="{{ asset('') }}assets/images/layouts/skin-modern.png" alt="layout-img"
                                    class="img-fluid" />
                            </label>
                        </div>
                        <h5 class="text-center text-muted mt-2 mb-0">Modern</h5>
                    </div>

                    <div class="col-6" id="skin-material">
                        <div class="form-check card-radio">
                            <input class="form-check-input" type="radio" name="data-skin" id="demo-skin-material"
                                value="material" />
                            <label class="form-check-label p-0 w-100" for="demo-skin-material">
                                <img src="{{ asset('') }}assets/images/layouts/skin-material.png"
                                    alt="layout-img" class="img-fluid" />
                            </label>
                        </div>
                        <h5 class="text-center text-muted mt-2 mb-0">Material</h5>
                    </div>

                    <div class="col-6" id="skin-saas">
                        <div class="form-check card-radio">
                            <input class="form-check-input" type="radio" name="data-skin" id="demo-skin-saas"
                                value="saas" />
                            <label class="form-check-label p-0 w-100" for="demo-skin-saas">
                                <img src="{{ asset('') }}assets/images/layouts/skin-saas.png" alt="layout-img"
                                    class="img-fluid" />
                            </label>
                        </div>
                        <h5 class="text-center text-muted mt-2 mb-0">SaaS</h5>
                    </div>

                    <div class="col-6" id="skin-flat">
                        <div class="form-check card-radio">
                            <input class="form-check-input" type="radio" name="data-skin" id="demo-skin-flat"
                                value="flat" />
                            <label class="form-check-label p-0 w-100" for="demo-skin-flat">
                                <img src="{{ asset('') }}assets/images/layouts/skin-flat.png" alt="layout-img"
                                    class="img-fluid" />
                            </label>
                        </div>
                        <h5 class="text-center text-muted mt-2 mb-0">Flat</h5>
                    </div>

                    <div class="col-6" id="skin-galaxy">
                        <div class="form-check card-radio">
                            <input class="form-check-input" type="radio" name="data-skin" id="demo-skin-galaxy"
                                value="galaxy" />
                            <label class="form-check-label p-0 w-100" for="demo-skin-galaxy">
                                <img src="{{ asset('') }}assets/images/layouts/skin-galaxy.png" alt="layout-img"
                                    class="img-fluid" />
                            </label>
                        </div>
                        <h5 class="text-center text-muted mt-2 mb-0">Galaxy</h5>
                    </div>

                    <div class="col-6" id="skin-retro">
                        <div class="form-check card-radio">
                            <input class="form-check-input" type="radio" name="data-skin" id="demo-skin-retro"
                                value="retro" />
                            <label class="form-check-label p-0 w-100" for="demo-skin-retro">
                                <img src="{{ asset('') }}assets/images/layouts/skin-retro.png" alt="layout-img"
                                    class="img-fluid" />
                            </label>
                        </div>
                        <h5 class="text-center text-muted mt-2 mb-0">Retro</h5>
                    </div>

                    <div class="col-6" id="skin-neon">
                        <div class="form-check card-radio">
                            <input class="form-check-input" type="radio" name="data-skin" id="demo-skin-neon"
                                value="neon" />
                            <label class="form-check-label p-0 w-100" for="demo-skin-neon">
                                <img src="{{ asset('') }}assets/images/layouts/skin-neon.png" alt="layout-img"
                                    class="img-fluid" />
                            </label>
                        </div>
                        <h5 class="text-center text-muted mt-2 mb-0">Neon</h5>
                    </div>

                    <div class="col-6" id="skin-pixel">
                        <div class="form-check card-radio">
                            <input class="form-check-input" type="radio" name="data-skin" id="demo-skin-pixel"
                                value="pixel" />
                            <label class="form-check-label p-0 w-100" for="demo-skin-pixel">
                                <img src="{{ asset('') }}assets/images/layouts/skin-pixel.png" alt="layout-img"
                                    class="img-fluid" />
                            </label>
                        </div>
                        <h5 class="text-center text-muted mt-2 mb-0">Pixel</h5>
                    </div>
                </div>
            </div>

            <div id="theme" class="p-3 border-bottom border-dashed">
                <h5 class="mb-3 fw-bold">Color Scheme</h5>
                <div class="row">
                    <div class="col-4" id="theme-light">
                        <div class="form-check card-radio">
                            <input class="form-check-input" type="radio" name="data-bs-theme"
                                id="layout-color-light" value="light" />
                            <label class="form-check-label p-0 w-100" for="layout-color-light">
                                <img src="{{ asset('') }}assets/images/layouts/theme-light.png" alt="layout-img"
                                    class="img-fluid" />
                            </label>
                        </div>
                        <h5 class="text-center text-muted mt-2 mb-0">Light</h5>
                    </div>

                    <div class="col-4" id="theme-dark">
                        <div class="form-check card-radio">
                            <input class="form-check-input" type="radio" name="data-bs-theme"
                                id="layout-color-dark" value="dark" />
                            <label class="form-check-label p-0 w-100" for="layout-color-dark">
                                <img src="{{ asset('') }}assets/images/layouts/theme-dark.png" alt="layout-img"
                                    class="img-fluid" />
                            </label>
                        </div>
                        <h5 class="text-center text-muted mt-2 mb-0">Dark</h5>
                    </div>

                    <div class="col-4" id="theme-system">
                        <div class="form-check card-radio">
                            <input class="form-check-input" type="radio" name="data-bs-theme"
                                id="layout-color-system" value="system" />
                            <label class="form-check-label p-0 w-100" for="layout-color-system">
                                <img src="{{ asset('') }}assets/images/layouts/theme-system.png"
                                    alt="layout-img" class="img-fluid" />
                            </label>
                        </div>
                        <h5 class="text-center text-muted mt-2 mb-0">System</h5>
                    </div>
                </div>
            </div>

            <div id="topbar-color" class="p-3 border-bottom border-dashed">
                <h5 class="mb-3 fw-bold">Topbar Color</h5>

                <div class="row g-3">
                    <div class="col-4" id="topbar-color-light">
                        <div class="form-check card-radio">
                            <input class="form-check-input" type="radio" name="data-topbar-color"
                                id="layout-topbar-color-light" value="light" />
                            <label class="form-check-label p-0 w-100" for="layout-topbar-color-light">
                                <img src="{{ asset('') }}assets/images/layouts/topbar-color-light.png"
                                    alt="layout-img" class="img-fluid" />
                            </label>
                        </div>
                        <h5 class="text-center text-muted mt-2 mb-0">Light</h5>
                    </div>

                    <div class="col-4" id="topbar-color-dark">
                        <div class="form-check card-radio">
                            <input class="form-check-input" type="radio" name="data-topbar-color"
                                id="layout-topbar-color-dark" value="dark" />
                            <label class="form-check-label p-0 w-100" for="layout-topbar-color-dark">
                                <img src="{{ asset('') }}assets/images/layouts/topbar-color-dark.png"
                                    alt="layout-img" class="img-fluid" />
                            </label>
                        </div>
                        <h5 class="fs-sm text-center text-muted mt-2 mb-0">Dark</h5>
                    </div>

                    <div class="col-4" id="topbar-color-gray">
                        <div class="form-check card-radio">
                            <input class="form-check-input" type="radio" name="data-topbar-color"
                                id="layout-topbar-color-gray" value="gray" />
                            <label class="form-check-label p-0 w-100" for="layout-topbar-color-gray">
                                <img src="{{ asset('') }}assets/images/layouts/topbar-color-gray.png"
                                    alt="layout-img" class="img-fluid" />
                            </label>
                        </div>
                        <h5 class="fs-sm text-center text-muted mt-2 mb-0">Gray</h5>
                    </div>

                    <div class="col-4" id="topbar-color-gradient">
                        <div class="form-check card-radio">
                            <input class="form-check-input" type="radio" name="data-topbar-color"
                                id="layout-topbar-color-gradient" value="gradient" />
                            <label class="form-check-label p-0 w-100" for="layout-topbar-color-gradient">
                                <img src="{{ asset('') }}assets/images/layouts/topbar-color-gradient.png"
                                    alt="layout-img" class="img-fluid" />
                            </label>
                        </div>
                        <h5 class="fs-sm text-center text-muted mt-2 mb-0">Gradient</h5>
                    </div>
                </div>
            </div>

            <div id="sidenav-color" class="p-3 border-bottom border-dashed">
                <h5 class="mb-3 fw-bold">Sidenav Color</h5>

                <div class="row g-3">
                    <div class="col-4" id="sidenav-color-light">
                        <div class="form-check sidebar-setting card-radio">
                            <input class="form-check-input" type="radio" name="data-menu-color"
                                id="layout-sidenav-color-light" value="light" />
                            <label class="form-check-label p-0 w-100" for="layout-sidenav-color-light">
                                <img src="{{ asset('') }}assets/images/layouts/sidenav-color-light.png"
                                    alt="layout-img" class="img-fluid" />
                            </label>
                        </div>
                        <h5 class="fs-sm text-center text-muted mt-2 mb-0">Light</h5>
                    </div>

                    <div class="col-4" id="sidenav-color-dark">
                        <div class="form-check sidebar-setting card-radio">
                            <input class="form-check-input" type="radio" name="data-menu-color"
                                id="layout-sidenav-color-dark" value="dark" />
                            <label class="form-check-label p-0 w-100" for="layout-sidenav-color-dark">
                                <img src="{{ asset('') }}assets/images/layouts/sidenav-color-dark.png"
                                    alt="layout-img" class="img-fluid" />
                            </label>
                        </div>
                        <h5 class="fs-sm text-center text-muted mt-2 mb-0">Dark</h5>
                    </div>

                    <div class="col-4" id="sidenav-color-gray">
                        <div class="form-check sidebar-setting card-radio">
                            <input class="form-check-input" type="radio" name="data-menu-color"
                                id="layout-sidenav-color-gray" value="gray" />
                            <label class="form-check-label p-0 w-100" for="layout-sidenav-color-gray">
                                <img src="{{ asset('') }}assets/images/layouts/sidenav-color-gray.png"
                                    alt="layout-img" class="img-fluid" />
                            </label>
                        </div>
                        <h5 class="fs-sm text-center text-muted mt-2 mb-0">Gray</h5>
                    </div>

                    <div class="col-4" id="sidenav-color-gradient">
                        <div class="form-check sidebar-setting card-radio">
                            <input class="form-check-input" type="radio" name="data-menu-color"
                                id="layout-sidenav-color-gradient" value="gradient" />
                            <label class="form-check-label p-0 w-100" for="layout-sidenav-color-gradient">
                                <img src="{{ asset('') }}assets/images/layouts/sidenav-color-gradient.png"
                                    alt="layout-img" class="img-fluid" />
                            </label>
                        </div>
                        <h5 class="fs-sm text-center text-muted mt-2 mb-0">Gradient</h5>
                    </div>
                    <div class="col-4" id="sidenav-color-image">
                        <div class="form-check sidebar-setting card-radio">
                            <input class="form-check-input" type="radio" name="data-menu-color"
                                id="layout-sidenav-color-image" value="image" />
                            <label class="form-check-label p-0 w-100" for="layout-sidenav-color-image">
                                <img src="{{ asset('') }}assets/images/layouts/sidenav-color-image.png"
                                    alt="layout-img" class="img-fluid" />
                            </label>
                        </div>
                        <h5 class="fs-sm text-center text-muted mt-2 mb-0">Image</h5>
                    </div>
                </div>
            </div>

            <div id="sidenav-size" class="p-3 border-bottom border-dashed">
                <h5 class="mb-3 fw-bold">Sidebar Size</h5>

                <div class="row g-3">
                    <div class="col-4" id="sidenav-size-default">
                        <div class="form-check sidebar-setting card-radio">
                            <input class="form-check-input" type="radio" name="data-sidenav-size"
                                id="layout-sidenav-size-default" value="default" />
                            <label class="form-check-label p-0 w-100" for="layout-sidenav-size-default">
                                <img src="{{ asset('') }}assets/images/layouts/sidenav-size-default.png"
                                    alt="layout-img" class="img-fluid" />
                            </label>
                        </div>
                        <h5 class="mb-0 text-center text-muted mt-2">Default</h5>
                    </div>

                    <div class="col-4" id="sidenav-size-compact">
                        <div class="form-check sidebar-setting card-radio">
                            <input class="form-check-input" type="radio" name="data-sidenav-size"
                                id="layout-sidenav-size-compact" value="compact" />
                            <label class="form-check-label p-0 w-100" for="layout-sidenav-size-compact">
                                <img src="{{ asset('') }}assets/images/layouts/sidenav-size-compact.png"
                                    alt="layout-img" class="img-fluid" />
                            </label>
                        </div>
                        <h5 class="mb-0 text-center text-muted mt-2">Compact</h5>
                    </div>

                    <div class="col-4" id="sidenav-size-condensed">
                        <div class="form-check sidebar-setting card-radio">
                            <input class="form-check-input" type="radio" name="data-sidenav-size"
                                id="layout-sidenav-size-condensed" value="condensed" />
                            <label class="form-check-label p-0 w-100" for="layout-sidenav-size-condensed">
                                <img src="{{ asset('') }}assets/images/layouts/sidenav-size-condensed.png"
                                    alt="layout-img" class="img-fluid" />
                            </label>
                        </div>
                        <h5 class="mb-0 text-center text-muted mt-2">Condensed</h5>
                    </div>

                    <div class="col-4" id="sidenav-size-on-hover">
                        <div class="form-check sidebar-setting card-radio">
                            <input class="form-check-input" type="radio" name="data-sidenav-size"
                                id="layout-sidenav-size-small-hover" value="on-hover" />
                            <label class="form-check-label p-0 w-100" for="layout-sidenav-size-small-hover">
                                <img src="{{ asset('') }}assets/images/layouts/sidenav-size-on-hover.png"
                                    alt="layout-img" class="img-fluid" />
                            </label>
                        </div>
                        <h5 class="mb-0 text-center text-muted mt-2">On Hover</h5>
                    </div>

                    <div class="col-4" id="sidenav-size-on-hover-active">
                        <div class="form-check sidebar-setting card-radio">
                            <input class="form-check-input" type="radio" name="data-sidenav-size"
                                id="layout-sidenav-size-small-hover-active" value="on-hover-active" />
                            <label class="form-check-label p-0 w-100" for="layout-sidenav-size-small-hover-active">
                                <img src="{{ asset('') }}assets/images/layouts/sidenav-size-on-hover-active.png"
                                    alt="layout-img" class="img-fluid" />
                            </label>
                        </div>
                        <h5 class="mb-0 fs-base text-center text-muted mt-2">On Hover - Show</h5>
                    </div>

                    <div class="col-4" id="sidenav-size-offcanvas">
                        <div class="form-check sidebar-setting card-radio">
                            <input class="form-check-input" type="radio" name="data-sidenav-size"
                                id="layout-sidenav-size-offcanvas" value="offcanvas" />
                            <label class="form-check-label p-0 w-100" for="layout-sidenav-size-offcanvas">
                                <img src="{{ asset('') }}assets/images/layouts/sidenav-size-offcanvas.png"
                                    alt="layout-img" class="img-fluid" />
                            </label>
                        </div>
                        <h5 class="mb-0 text-center text-muted mt-2">Offcanvas</h5>
                    </div>
                </div>
            </div>

            <div id="width" class="p-3 border-bottom border-dashed">
                <h5 class="mb-3 fw-bold">Layout Width</h5>

                <div class="row g-3">
                    <div class="col-4" id="width-fluid">
                        <div class="form-check sidebar-setting card-radio">
                            <input class="form-check-input" type="radio" name="data-layout-width"
                                id="layout-width-fluid" value="fluid" />
                            <label class="form-check-label p-0 w-100" for="layout-width-fluid">
                                <img src="{{ asset('') }}assets/images/layouts/width-fluid.png"
                                    alt="layout-img" class="img-fluid" />
                            </label>
                        </div>
                        <h5 class="mb-0 text-center text-muted mt-2">Fluid</h5>
                    </div>

                    <div class="col-4" id="width-boxed">
                        <div class="form-check sidebar-setting card-radio">
                            <input class="form-check-input" type="radio" name="data-layout-width"
                                id="layout-width-boxed" value="boxed" />
                            <label class="form-check-label p-0 w-100" for="layout-width-boxed">
                                <img src="{{ asset('') }}assets/images/layouts/width-boxed.png"
                                    alt="layout-img" class="img-fluid" />
                            </label>
                        </div>
                        <h5 class="mb-0 text-center text-muted mt-2">Boxed</h5>
                    </div>
                </div>
            </div>

            <div id="dir" class="p-3 border-bottom border-dashed">
                <h5 class="mb-3 fw-bold">Layout Direction</h5>

                <div class="row g-3">
                    <div class="col-4" id="dir-ltr">
                        <div class="form-check sidebar-setting card-radio">
                            <input class="form-check-input" type="radio" name="dir" id="layout-dir-ltr"
                                value="ltr" />
                            <label class="form-check-label p-0 w-100" for="layout-dir-ltr">
                                <img src="{{ asset('') }}assets/images/layouts/dir-ltr.png" alt="layout-img"
                                    class="img-fluid" />
                            </label>
                        </div>
                        <h5 class="mb-0 text-center text-muted mt-2">LTR</h5>
                    </div>

                    <div class="col-4" id="dir-rtl">
                        <div class="form-check sidebar-setting card-radio">
                            <input class="form-check-input" type="radio" name="dir" id="layout-dir-rtl"
                                value="rtl" />
                            <label class="form-check-label p-0 w-100" for="layout-dir-rtl">
                                <img src="{{ asset('') }}assets/images/layouts/dir-rtl.png" alt="layout-img"
                                    class="img-fluid" />
                            </label>
                        </div>
                        <h5 class="mb-0 text-center text-muted mt-2">RTL</h5>
                    </div>
                </div>
            </div>

            <div id="position" class="p-3 border-bottom border-dashed">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0">Layout Position</h5>

                    <div class="d-flex gap-1">
                        <div id="position-fixed">
                            <input type="radio" class="btn-check" name="data-layout-position"
                                id="layout-position-fixed" value="fixed" />
                            <label class="btn btn-sm btn-soft-warning w-sm"
                                for="layout-position-fixed">Fixed</label>
                        </div>
                        <div id="position-scrollable">
                            <input type="radio" class="btn-check" name="data-layout-position"
                                id="layout-position-scrollable" value="scrollable" />
                            <label class="btn btn-sm btn-soft-warning w-sm ms-0"
                                for="layout-position-scrollable">Scrollable</label>
                        </div>
                    </div>
                </div>
            </div>

            <div id="sidenav-user" class="p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <label class="fw-bold m-0" for="sidebaruser-check">Sidebar User Info</label>
                    </h5>
                    <div class="form-check form-switch fs-lg">
                        <input type="checkbox" class="form-check-input" name="sidebar-user"
                            id="sidebaruser-check" />
                    </div>
                </div>
            </div>
        </div>

        <div class="offcanvas-footer border-top p-3 text-center">
            <div class="row justify-content-end">
                <div class="col-6">
                    <a href="https://1.envato.market/uboldadmin" class="btn btn-success fw-semibold py-2 w-100"
                        target="_blank"><i data-lucide="shopping-basket" class="me-2 fs-md"></i> Buy Now</a>
                </div>
                <div class="col-6">
                    <button type="button" class="btn btn-danger fw-semibold py-2 w-100" id="reset-layout"><i
                            data-lucide="refresh-ccw" class="me-2 fs-md"></i> Reset</button>
                </div>
            </div>
        </div>
    </div>
    <!-- end offcanvas-->
    <!-- Vendor js -->
    <script src="{{ asset('') }}assets/js/vendors.min.js"></script>

    <!-- App js -->
    <script src="{{ asset('') }}assets/js/app.js"></script>


    <!-- E Charts js -->
    <script src="{{ asset('') }}assets/plugins/chartjs/chart.umd.js"></script>

    <!-- Custom table -->
    <script src="{{ asset('') }}assets/js/pages/custom-table.js"></script>

    <!-- Dashboard Page js -->
    <script src="{{ asset('') }}assets/js/pages/dashboard-ecommerce.js"></script>
    <!-- Jquery for Datatables-->
    <script src="{{ asset('') }}assets/plugins/jquery/jquery.min.js"></script>

    <!-- Datatables js -->
    <script src="{{ asset('') }}assets/plugins/datatables/dataTables.min.js"></script>
    <script src="{{ asset('') }}assets/plugins/datatables/dataTables.bootstrap5.min.js"></script>
    <script src="{{ asset('') }}assets/plugins/datatables/dataTables.responsive.min.js"></script>
    <script src="{{ asset('') }}assets/plugins/datatables/responsive.bootstrap5.min.js"></script>

    <!-- Page js -->
    <script src="{{ asset('') }}assets/js/pages/datatables-basic.js"></script>
    <script src="{{ asset('') }}assets/plugins/sweetalert2/sweetalert2.min.js"></script>
    <!-- Select2 Plugin Js -->
    <script src="{{ asset('') }}assets/plugins/select2/select2.min.js"></script>
    <script src="{{ asset('') }}assets/js/pages/form-select2.js"></script>
    @stack('scripts')
</body>

</html>
