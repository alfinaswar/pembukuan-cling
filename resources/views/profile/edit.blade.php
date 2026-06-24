@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        {{-- Header & Breadcrumb --}}
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h4 class="mb-1 fw-bold">Ganti Password</h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Ganti Password</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-xl-12 col-lg-8">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-0 pb-0">
                        <h5 class="mb-0 fw-semibold">
                            <i class="ti ti-lock me-2 text-primary"></i>
                            Keamanan Akun
                        </h5>
                        <small class="text-muted">Pastikan password baru Anda kuat dan tidak mudah ditebak</small>
                    </div>
                    <div class="card-body pt-3">
                        <form action="{{ route('profile.update') }}" method="POST" id="formPassword">
                            @csrf
                            @method('PUT')

                            {{-- Info Syarat Password --}}
                            <div class="alert alert-light border mb-4 text-dark" role="alert">
                                <h6 class="alert-heading small fw-semibold mb-2 text-dark">
                                    <i class="ti ti-shield-lock me-1"></i> Syarat Password:
                                </h6>
                                <ul class="small mb-0 ps-3 text-dark">
                                    <li>Minimal <strong>8 karakter</strong></li>
                                    <li>Mengandung <strong>huruf kecil</strong> (a-z)</li>
                                    <li>Mengandung <strong>huruf besar</strong> (A-Z)</li>
                                    <li>Mengandung <strong>angka</strong> (0-9)</li>
                                    <li>Mengandung <strong>simbol</strong> (@$!%*?& dll)</li>
                                </ul>
                            </div>

                            {{-- Password Baru --}}
                            <div class="mb-3">
                                <label for="new_password" class="form-label small fw-medium">
                                    Password Baru <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="ti ti-lock text-muted"></i>
                                    </span>
                                    <input type="password"
                                        class="form-control border-start-0 @error('new_password') is-invalid @enderror"
                                        id="new_password" name="new_password" placeholder="Masukkan password baru">
                                    <button type="button" class="btn btn-light border border-start-0 toggle-pass"
                                        data-target="new_password">
                                        <i class="ti ti-eye"></i>
                                    </button>
                                    @error('new_password')
                                        <div class="invalid-feedback" style="display:block; animation: fadeIn .3s;">
                                            {{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Konfirmasi Password --}}
                            <div class="mb-4">
                                <label for="new_password_confirmation" class="form-label small fw-medium">
                                    Konfirmasi Password Baru <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="ti ti-lock-check text-muted"></i>
                                    </span>
                                    <input type="password" class="form-control border-start-0"
                                        id="new_password_confirmation" name="new_password_confirmation"
                                        placeholder="Ulangi password baru">
                                    <button type="button" class="btn btn-light border border-start-0 toggle-pass"
                                        data-target="new_password_confirmation">
                                        <i class="ti ti-eye"></i>
                                    </button>
                                </div>
                            </div>

                            {{-- Tombol Action --}}
                            <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                                <a href="{{ url()->previous() ?: url('/') }}" class="btn btn-light px-4">
                                    <i class="ti ti-arrow-left me-1"></i> Kembali
                                </a>
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="ti ti-check me-1"></i> Simpan Password
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-4px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .input-group:focus-within .input-group-text {
            background-color: #e7f5ff !important;
            color: #1971c2 !important;
        }
    </style>
@endpush

@push('scripts')
    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                iconColor: '#4BCC1F',
                confirmButtonColor: '#4BCC1F',
            });
        </script>
    @endif

    <script>
        $(function() {
            // Toggle show/hide password
            $('.toggle-pass').on('click', function() {
                const target = $('#' + $(this).data('target'));
                const icon = $(this).find('i');
                if (target.attr('type') === 'password') {
                    target.attr('type', 'text');
                    icon.removeClass('ti-eye').addClass('ti-eye-off');
                } else {
                    target.attr('type', 'password');
                    icon.removeClass('ti-eye-off').addClass('ti-eye');
                }
            });
        });
    </script>
@endpush
