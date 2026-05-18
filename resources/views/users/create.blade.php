@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <!-- Page Title Header -->
        <div class="page-title-head d-flex align-items-center flex-wrap gap-2 mb-4">
            <div class="flex-grow-1">
                <h4 class="page-main-title m-0 fw-semibold">
                    <i class="ti ti-plus me-2 text-primary"></i>Tambah User
                </h4>
            </div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb m-0 py-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('home') }}" class="text-decoration-none text-reset">Home</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('users.index') }}" class="text-decoration-none text-reset">Users</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Tambah User</li>
                </ol>
            </nav>
        </div>

        <!-- Form Card -->
        <div class="row justify-content-center">
            <div class="col-xl-12 col-lg-12">
                <div class="card shadow-sm border-0">
                    <!-- Card Header -->
                    <div class="card-header bg-white py-3 border-bottom">
                        <h5 class="card-title mb-0 fw-semibold">
                            <i class="ti ti-file-text me-2"></i>Form Data User
                        </h5>
                    </div>

                    <!-- Card Body -->
                    <div class="card-body p-4">
                        <form action="{{ route('users.store') }}" method="POST" id="formUser">
                            @csrf

                            <!-- Info Box -->
                            <div class="alert alert-light border mb-4 d-flex align-items-center gap-2 text-black">
                                <i class="ti ti-info-circle text-primary"></i>
                                <small class="mb-0 text-black">Kolom dengan tanda <span class="text-danger">*</span> wajib
                                    diisi.</small>
                            </div>


                            <div class="row g-4">
                                <!-- Left Column: Identity & Branch -->
                                <div class="col-md-6">
                                    <!-- Nama -->
                                    <div class="mb-4">
                                        <label for="name" class="form-label fw-semibold mb-2">
                                            Nama Lengkap <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light">
                                                <i class="ti ti-user text-muted"></i>
                                            </span>
                                            <input type="text" id="name" name="name"
                                                class="form-control @error('name') is-invalid @enderror"
                                                value="{{ old('name') }}" required
                                                placeholder="Contoh: Dr. Andi Wijaya, S.KG" autocomplete="name">
                                        </div>
                                        @error('name')
                                            <div class="invalid-feedback d-block mt-1">
                                                <i class="ti ti-alert-circle me-1"></i>{{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <!-- Email -->
                                    <div class="mb-4">
                                        <label for="email" class="form-label fw-semibold mb-2">
                                            Email <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light">
                                                <i class="ti ti-mail text-muted"></i>
                                            </span>
                                            <input type="email" id="email" name="email"
                                                class="form-control @error('email') is-invalid @enderror"
                                                value="{{ old('email') }}" required placeholder="user@klinik.com"
                                                autocomplete="email">
                                        </div>
                                        @error('email')
                                            <div class="invalid-feedback d-block mt-1">
                                                <i class="ti ti-alert-circle me-1"></i>{{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <!-- Cabang (Select2 Default Style) -->
                                    <div class="mb-4">
                                        <label for="cabang_id" class="form-label fw-semibold mb-2">
                                            Cabang <span class="text-danger">*</span>
                                        </label>
                                        <select id="cabang_id" name="cabang_id"
                                            class="form-select @error('cabang_id') is-invalid @enderror"
                                            data-toggle="select2" required>
                                            <option value="">Pilih Cabang</option>
                                            @foreach ($perusahaan as $cabang)
                                                <option value="{{ $cabang->id }}"
                                                    {{ old('cabang_id') == $cabang->id ? 'selected' : '' }}>
                                                    {{ $cabang->Nama }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="text-muted d-block mt-1">
                                            <i class="ti ti-help me-1"></i>Pilih cabang tempat user bekerja
                                        </small>
                                        @error('cabang_id')
                                            <div class="invalid-feedback d-block mt-1">
                                                <i class="ti ti-alert-circle me-1"></i>{{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Right Column: Credentials & Role -->
                                <div class="col-md-6">
                                    <!-- Password -->
                                    <div class="mb-4">
                                        <label for="password" class="form-label fw-semibold mb-2">
                                            Kata Sandi <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light">
                                                <i class="ti ti-lock text-muted"></i>
                                            </span>
                                            <input type="password" id="password" name="password"
                                                class="form-control @error('password') is-invalid @enderror" required
                                                placeholder="Minimal 8 karakter" autocomplete="new-password" minlength="8">
                                            <button type="button"
                                                class="input-group-text bg-light border-start-0 btn-toggle-password"
                                                title="Tampilkan password">
                                                <i class="ti ti-eye text-muted toggle-icon"></i>
                                            </button>
                                        </div>
                                        <small class="text-muted d-block mt-1">
                                            <i class="ti ti-help me-1"></i>Gunakan kombinasi huruf, angka, dan simbol
                                        </small>
                                        @error('password')
                                            <div class="invalid-feedback d-block mt-1">
                                                <i class="ti ti-alert-circle me-1"></i>{{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <!-- Password Confirmation -->
                                    <div class="mb-4">
                                        <label for="password_confirmation" class="form-label fw-semibold mb-2">
                                            Konfirmasi Kata Sandi <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light">
                                                <i class="ti ti-lock-check text-muted"></i>
                                            </span>
                                            <input type="password" id="password_confirmation"
                                                name="password_confirmation"
                                                class="form-control @error('password_confirmation') is-invalid @enderror"
                                                required placeholder="Ulangi kata sandi" autocomplete="new-password">
                                            <button type="button"
                                                class="input-group-text bg-light border-start-0 btn-toggle-password"
                                                title="Tampilkan password">
                                                <i class="ti ti-eye text-muted toggle-icon"></i>
                                            </button>
                                        </div>
                                        @error('password_confirmation')
                                            <div class="invalid-feedback d-block mt-1">
                                                <i class="ti ti-alert-circle me-1"></i>{{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <!-- Role (Select2 Default Style - Multiple) -->
                                    <div class="mb-4">
                                        <label for="roles" class="form-label fw-semibold mb-2">
                                            Role / Hak Akses <span class="text-danger">*</span>
                                        </label>
                                        <select id="roles" name="roles[]"
                                            class="form-select @error('roles') is-invalid @enderror" data-toggle="select2"
                                            required>
                                            @foreach ($roles as $role)
                                                <option value="{{ $role->name }}"
                                                    {{ collect(old('roles'))->contains($role->name) ? 'selected' : '' }}>
                                                    {{ $role->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="text-muted d-block mt-1">
                                            <i class="ti ti-help me-1"></i>Tahan Ctrl/Cmd untuk memilih multiple role
                                        </small>
                                        @error('roles')
                                            <div class="invalid-feedback d-block mt-1">
                                                <i class="ti ti-alert-circle me-1"></i>{{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Divider -->
                            <hr class="my-4 text-muted opacity-25">

                            <!-- Actions -->
                            <div class="d-flex justify-content-end gap-2 pt-2">
                                <a href="{{ route('users.index') }}" class="btn btn-light px-4">
                                    <i class="ti ti-arrow-left me-1"></i>Batal
                                </a>
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="ti ti-device-floppy me-1"></i>Simpan User
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Back Link (Mobile Friendly) -->
                <div class="text-center mt-3 d-lg-none">
                    <a href="{{ route('users.index') }}" class="text-muted small text-decoration-none">
                        <i class="ti ti-arrow-back-up me-1"></i>Kembali ke daftar
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* Fokus input yang lebih halus */
        .form-control:focus,
        .input-group:focus-within {
            box-shadow: 0 0 0 3px rgba(33, 150, 243, 0.15);
            border-color: #2196f3;
        }

        /* Input group icon styling */
        .input-group-text {
            border-right: none;
            min-width: 45px;
            justify-content: center;
            cursor: pointer;
        }

        .input-group .form-control {
            border-left: none;
        }

        /* Smooth transition untuk feedback */
        .invalid-feedback {
            animation: fadeIn 0.2s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-5px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Password toggle button */
        .btn-toggle-password {
            border-left: none !important;
            cursor: pointer;
            transition: color 0.2s;
        }

        .btn-toggle-password:hover {
            color: #2196f3 !important;
        }
    </style>
@endpush

@push('scripts')
    <!-- Select2 JS -->
    <script src="{{ asset('assets/libs/select2/dist/js/select2.full.min.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {



            // ===== Password Toggle Functionality =====
            function setupPasswordToggle(inputId, toggleBtn) {
                const input = document.getElementById(inputId);
                const icon = toggleBtn.querySelector('.toggle-icon');

                if (!input || !icon) return;

                toggleBtn.addEventListener('click', function() {
                    const isPassword = input.type === 'password';
                    input.type = isPassword ? 'text' : 'password';
                    icon.className = isPassword ? 'ti ti-eye-off text-muted toggle-icon' :
                        'ti ti-eye text-muted toggle-icon';
                    toggleBtn.title = isPassword ? 'Sembunyikan password' : 'Tampilkan password';
                });
            }

            // Setup toggle for both password fields
            document.querySelectorAll('.btn-toggle-password').forEach((btn, index) => {
                const inputId = index === 0 ? 'password' : 'password_confirmation';
                setupPasswordToggle(inputId, btn);
            });

            // ===== Auto-focus =====
            document.getElementById('name')?.focus();

            // ===== Password Match Validation (client-side hint) =====
            const password = document.getElementById('password');
            const passwordConfirm = document.getElementById('password_confirmation');

            if (password && passwordConfirm) {
                passwordConfirm.addEventListener('input', function() {
                    if (this.value && this.value !== password.value) {
                        this.setCustomValidity('Kata sandi tidak cocok');
                    } else {
                        this.setCustomValidity('');
                    }
                });

                password.addEventListener('input', function() {
                    if (passwordConfirm.value && passwordConfirm.value !== this.value) {
                        passwordConfirm.setCustomValidity('Kata sandi tidak cocok');
                    } else {
                        passwordConfirm.setCustomValidity('');
                    }
                });
            }
        });
    </script>
@endpush
