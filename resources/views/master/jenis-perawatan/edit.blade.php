@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <!-- Page Title Header -->
        <div class="page-title-head d-flex align-items-center flex-wrap gap-2 mb-4">
            <div class="flex-grow-1">
                <h4 class="page-main-title m-0 fw-semibold">
                    <i class="ti ti-edit me-2 text-primary"></i>Edit Jenis Perawatan
                </h4>
            </div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb m-0 py-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('home') }}" class="text-decoration-none text-reset">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('JenisPerawatan.index') }}" class="text-decoration-none text-reset">Jenis
                            Perawatan</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Edit Data</li>
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
                            <i class="ti ti-file-text me-2"></i>Form Edit Jenis Perawatan
                        </h5>
                    </div>

                    <!-- Card Body -->
                    <div class="card-body p-4">
                        <form action="{{ route('JenisPerawatan.update', encrypt($JenisPerawatan->id)) }}" method="POST"
                            id="formEditJenisPerawatan">
                            @csrf
                            @method('PUT')

                            <!-- Info Box -->
                            <div class="alert alert-light border mb-4 d-flex align-items-center gap-2">
                                <i class="ti ti-info-circle text-primary"></i>
                                <small class="mb-0">Kolom dengan tanda <span class="text-danger">*</span> wajib
                                    diisi.</small>
                            </div>

                            <!-- Nama Perawatan -->
                            <div class="mb-4">
                                <label for="Nama" class="form-label fw-semibold mb-2">
                                    Nama Perawatan <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="ti ti-tag text-muted"></i>
                                    </span>
                                    <input type="text" id="Nama" name="Nama"
                                        class="form-control @error('Nama') is-invalid @enderror"
                                        value="{{ old('Nama', $JenisPerawatan->Nama) }}" required
                                        placeholder="Contoh: Scaling & Polishing, Tambal Komposit, dll" autocomplete="off">
                                </div>
                                @error('Nama')
                                    <div class="invalid-feedback d-block mt-1">
                                        <i class="ti ti-alert-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Tarif -->
                            <div class="mb-4">
                                <label for="Tarif" class="form-label fw-semibold mb-2">
                                    Tarif <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="ti ti-cash text-muted"></i>
                                    </span>
                                    <!-- ⚠️ Value raw untuk JS, format via JS -->
                                    <input type="text" id="Tarif" name="Tarif"
                                        class="form-control @error('Tarif') is-invalid @enderror"
                                        data-raw-value="{{ old('Tarif', $JenisPerawatan->Tarif) }}"
                                        value="{{ old('Tarif', $JenisPerawatan->Tarif) }}" required placeholder="0"
                                        inputmode="numeric" autocomplete="off">
                                    <span class="input-group-text bg-light text-muted small">IDR</span>
                                </div>
                                <small class="text-muted d-block mt-1">
                                    <i class="ti ti-help me-1"></i>Masukkan angka saja, format Rupiah otomatis
                                </small>
                                @error('Tarif')
                                    <div class="invalid-feedback d-block mt-1">
                                        <i class="ti ti-alert-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Divider -->
                            <hr class="my-4 text-muted opacity-25">

                            <!-- Actions -->
                            <div class="d-flex justify-content-end gap-2 pt-2">
                                <a href="{{ route('JenisPerawatan.index') }}" class="btn btn-light px-4">
                                    <i class="ti ti-arrow-left me-1"></i>Batal
                                </a>
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="ti ti-device-floppy me-1"></i>Update Data
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Back Link (Mobile Friendly) -->
                <div class="text-center mt-3 d-lg-none">
                    <a href="{{ route('JenisPerawatan.index') }}" class="text-muted small text-decoration-none">
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
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tarifInput = document.getElementById('Tarif');

            function formatRupiah(angka) {
                if (!angka) return '';
                let number = parseInt(angka.toString().replace(/[^0-9]/g, ''));
                if (isNaN(number)) return '';
                return new Intl.NumberFormat('id-ID').format(number);
            }

            function getRawValue(formatted) {
                return formatted.toString().replace(/[^0-9]/g, '');
            }

            function initTarifField() {
                let raw = tarifInput.dataset.rawValue || getRawValue(tarifInput.value);
                if (raw) {
                    tarifInput.value = 'Rp ' + formatRupiah(raw);
                }
            }

            tarifInput.addEventListener('input', function(e) {
                let raw = getRawValue(this.value);
                if (raw) {
                    this.value = 'Rp ' + formatRupiah(raw);
                } else {
                    this.value = '';
                }
            });

            tarifInput.addEventListener('focus', function() {
                this.value = getRawValue(this.value);
            });

            tarifInput.addEventListener('blur', function() {
                let raw = getRawValue(this.value);
                if (raw) {
                    this.value = 'Rp ' + formatRupiah(raw);
                }
            });

            document.getElementById('formEditJenisPerawatan').addEventListener('submit', function(e) {
                tarifInput.value = getRawValue(tarifInput.value);
            });
            initTarifField();
            document.getElementById('Nama')?.focus();
        });
    </script>
@endpush
