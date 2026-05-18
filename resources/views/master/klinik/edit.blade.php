@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <!-- Page Title Header -->
        <div class="page-title-head d-flex align-items-center flex-wrap gap-2 mb-4">
            <div class="flex-grow-1">
                <h4 class="page-main-title m-0 fw-semibold">
                    <i class="ti ti-edit me-2 text-primary"></i>Edit Klinik
                </h4>
            </div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb m-0 py-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('home') }}" class="text-decoration-none text-reset">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('Klinik.index') }}" class="text-decoration-none text-reset">Klinik</a>
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
                            <i class="ti ti-file-text me-2"></i>Form Edit Klinik
                        </h5>
                    </div>

                    <!-- Card Body -->
                    <div class="card-body p-4">
                        <form action="{{ route('Klinik.update', encrypt($MasterKlinik->id)) }}" method="POST"
                            id="formEditKlinik">
                            @csrf
                            @method('PUT')

                            <!-- Info Box -->
                            <div class="alert alert-light border mb-4 d-flex align-items-center gap-2">
                                <i class="ti ti-info-circle text-primary"></i>
                                <small class="mb-0">Kolom dengan tanda <span class="text-danger">*</span> wajib
                                    diisi.</small>
                            </div>

                            <!-- Nama Klinik -->
                            <div class="mb-4">
                                <label for="Nama" class="form-label fw-semibold mb-2">
                                    Nama Klinik <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="ti ti-building-hospital text-muted"></i>
                                    </span>
                                    <input type="text" id="Nama" name="Nama"
                                        class="form-control @error('Nama') is-invalid @enderror"
                                        value="{{ old('Nama', $MasterKlinik->Nama) }}" required
                                        placeholder="Contoh: Klinik Gigi Sehat, Dental Care Plus, dll" autocomplete="off">
                                </div>
                                @error('Nama')
                                    <div class="invalid-feedback d-block mt-1">
                                        <i class="ti ti-alert-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Alamat -->
                            <div class="mb-4">
                                <label for="Alamat" class="form-label fw-semibold mb-2">
                                    Alamat <span class="text-danger">*</span>
                                </label>
                                <div class="input-group align-items-start">
                                    <span class="input-group-text bg-light">
                                        <i class="ti ti-map-pin text-muted"></i>
                                    </span>
                                    <textarea id="Alamat" name="Alamat" class="form-control @error('Alamat') is-invalid @enderror" rows="3"
                                        required placeholder="Masukkan alamat lengkap klinik">{{ old('Alamat', $MasterKlinik->Alamat) }}</textarea>
                                </div>
                                @error('Alamat')
                                    <div class="invalid-feedback d-block mt-1">
                                        <i class="ti ti-alert-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- No. Telp -->
                            <div class="mb-4">
                                <label for="NoTelp" class="form-label fw-semibold mb-2">
                                    No. Telepon
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="ti ti-phone text-muted"></i>
                                    </span>
                                    <input type="tel" id="NoTelp" name="NoTelp"
                                        class="form-control @error('NoTelp') is-invalid @enderror"
                                        value="{{ old('NoTelp', $MasterKlinik->NoTelp) }}"
                                        placeholder="Contoh: 021-12345678 / 0812-3456-7890" inputmode="tel"
                                        autocomplete="tel">
                                </div>
                                <small class="text-muted d-block mt-1">
                                    <i class="ti ti-help me-1"></i>Opsional: nomor kontak klinik
                                </small>
                                @error('NoTelp')
                                    <div class="invalid-feedback d-block mt-1">
                                        <i class="ti ti-alert-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div class="mb-4">
                                <label for="Email" class="form-label fw-semibold mb-2">
                                    Email
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="ti ti-mail text-muted"></i>
                                    </span>
                                    <input type="email" id="Email" name="Email"
                                        class="form-control @error('Email') is-invalid @enderror"
                                        value="{{ old('Email', $MasterKlinik->Email) }}" placeholder="contoh@klinik.com"
                                        autocomplete="email">
                                </div>
                                <small class="text-muted d-block mt-1">
                                    <i class="ti ti-help me-1"></i>Opsional: email resmi klinik
                                </small>
                                @error('Email')
                                    <div class="invalid-feedback d-block mt-1">
                                        <i class="ti ti-alert-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Status (Uncomment jika diperlukan) -->
                            {{--
                        <div class="mb-4">
                            <label for="Status" class="form-label fw-semibold mb-2">
                                Status
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="ti ti-toggle-left text-muted"></i>
                                </span>
                                <select id="Status"
                                        name="Status"
                                        class="form-select @error('Status') is-invalid @enderror">
                                    <option value="1" {{ old('Status', $MasterKlinik->Status) == 1 ? 'selected' : '' }}>Aktif</option>
                                    <option value="0" {{ old('Status', $MasterKlinik->Status) == 0 ? 'selected' : '' }}>Tidak Aktif</option>
                                </select>
                            </div>
                            @error('Status')
                                <div class="invalid-feedback d-block mt-1">
                                    <i class="ti ti-alert-circle me-1"></i>{{ $message }}
                                </div>
                            @enderror
                        </div>
                        --}}

                            <!-- Divider -->
                            <hr class="my-4 text-muted opacity-25">

                            <!-- Actions -->
                            <div class="d-flex justify-content-end gap-2 pt-2">
                                <a href="{{ route('Klinik.index') }}" class="btn btn-light px-4">
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
                    <a href="{{ route('Klinik.index') }}" class="text-muted small text-decoration-none">
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
        .form-select:focus,
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

        .input-group .form-control,
        .input-group textarea.form-control,
        .input-group .form-select {
            border-left: none;
        }

        /* Fix textarea dalam input-group */
        .input-group.align-items-start .input-group-text {
            align-self: flex-start;
            padding-top: 0.75rem;
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
            // Auto-focus ke field pertama
            document.getElementById('Nama')?.focus();

            // Optional: Format NoTelp saat input (hanya angka & separator)
            const noTelpInput = document.getElementById('NoTelp');
            if (noTelpInput) {
                noTelpInput.addEventListener('input', function(e) {
                    // Hanya izinkan angka, spasi, tanda hubung, dan tanda plus
                    this.value = this.value.replace(/[^\d\s\-\+]/g, '');
                });
            }
        });
    </script>
@endpush
