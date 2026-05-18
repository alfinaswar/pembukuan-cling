@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <!-- Page Title Header -->
        <div class="page-title-head d-flex align-items-center flex-wrap gap-2 mb-4">
            <div class="flex-grow-1">
                <h4 class="page-main-title m-0 fw-semibold">
                    <i class="ti ti-plus me-2 text-primary"></i>Tambah Shift Kerja
                </h4>
            </div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb m-0 py-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('home') }}" class="text-decoration-none text-reset">Home</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('MasterShift.index') }}" class="text-decoration-none text-reset">Shift Kerja</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Tambah Data</li>
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
                            <i class="ti ti-file-text me-2"></i>Form Data Shift Kerja
                        </h5>
                    </div>

                    <!-- Card Body -->
                    <div class="card-body p-4">
                        <form action="{{ route('MasterShift.store') }}" method="POST" id="formShift">
                            @csrf

                            <!-- Info Box -->
                            <div class="alert alert-light border mb-4 d-flex align-items-center gap-2">
                                <i class="ti ti-info-circle text-primary"></i>
                                <small class="mb-0 text-black">Kolom dengan tanda <span class="text-danger">*</span> wajib
                                    diisi.</small>
                            </div>


                            <!-- Nama Shift -->
                            <div class="mb-4">
                                <label for="Nama" class="form-label fw-semibold mb-2">
                                    Nama Shift <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="ti ti-tag text-muted"></i>
                                    </span>
                                    <input type="text" id="Nama" name="Nama"
                                        class="form-control @error('Nama') is-invalid @enderror" value="{{ old('Nama') }}"
                                        required placeholder="Contoh: Pagi, Siang, Malam, dll" autocomplete="off">
                                </div>
                                @error('Nama')
                                    <div class="invalid-feedback d-block mt-1">
                                        <i class="ti ti-alert-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Jam Mulai -->
                            <div class="mb-4">
                                <label for="JamMulai" class="form-label fw-semibold mb-2">
                                    Jam Mulai <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="ti ti-clock-start text-muted"></i>
                                    </span>
                                    <input type="time" id="JamMulai" name="JamMulai"
                                        class="form-control @error('JamMulai') is-invalid @enderror"
                                        value="{{ old('JamMulai') }}" required step="60" min="00:00" max="23:59"
                                        data-provider="timepickr" data-time-hrs="true">
                                </div>
                                <small class="text-muted d-block mt-1">
                                    <i class="ti ti-help me-1"></i>Format: 24 Jam (contoh: 08:00, 14:30)
                                </small>
                                @error('JamMulai')
                                    <div class="invalid-feedback d-block mt-1">
                                        <i class="ti ti-alert-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Jam Selesai -->
                            <div class="mb-4">
                                <label for="JamSelesai" class="form-label fw-semibold mb-2">
                                    Jam Selesai <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="ti ti-clock-stop text-muted"></i>
                                    </span>
                                    <input type="time" id="JamSelesai" name="JamSelesai"
                                        class="form-control @error('JamSelesai') is-invalid @enderror"
                                        value="{{ old('JamSelesai') }}" required step="60" min="00:00"
                                        max="23:59" data-provider="timepickr" data-time-hrs="true">
                                </div>
                                <small class="text-muted d-block mt-1">
                                    <i class="ti ti-help me-1"></i>Pastikan jam selesai lebih besar dari jam mulai
                                </small>
                                @error('JamSelesai')
                                    <div class="invalid-feedback d-block mt-1">
                                        <i class="ti ti-alert-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Divider -->
                            <hr class="my-4 text-muted opacity-25">

                            <!-- Actions -->
                            <div class="d-flex justify-content-end gap-2 pt-2">
                                <a href="{{ route('MasterShift.index') }}" class="btn btn-light px-4">
                                    <i class="ti ti-arrow-left me-1"></i>Batal
                                </a>
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="ti ti-device-floppy me-1"></i>Simpan Data
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Back Link (Mobile Friendly) -->
                <div class="text-center mt-3 d-lg-none">
                    <a href="{{ route('MasterShift.index') }}" class="text-muted small text-decoration-none">
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

        /* Time input styling */
        input[type="time"]::-webkit-calendar-picker-indicator {
            filter: invert(0.5);
            cursor: pointer;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-focus ke field pertama
            document.getElementById('Nama')?.focus();

            // Optional: Validasi jam selesai > jam mulai (client-side hint)
            const jamMulai = document.getElementById('JamMulai');
            const jamSelesai = document.getElementById('JamSelesai');

            if (jamMulai && jamSelesai) {
                jamMulai.addEventListener('change', function() {
                    // Set min jamSelesai = jamMulai + 1 jam (opsional)
                    if (jamSelesai.value && jamSelesai.value <= this.value) {
                        jamSelesai.setCustomValidity('Jam selesai harus lebih besar dari jam mulai');
                    } else {
                        jamSelesai.setCustomValidity('');
                    }
                });

                jamSelesai.addEventListener('change', function() {
                    if (jamMulai.value && this.value <= jamMulai.value) {
                        this.setCustomValidity('Jam selesai harus lebih besar dari jam mulai');
                    } else {
                        this.setCustomValidity('');
                    }
                });
            }
        });
    </script>
@endpush
