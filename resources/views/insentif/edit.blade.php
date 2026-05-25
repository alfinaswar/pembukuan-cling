@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <!-- Page Title Header -->
        <div class="page-title-head d-flex align-items-center flex-wrap gap-2 mb-4">
            <div class="flex-grow-1">
                <h4 class="page-main-title m-0 fw-semibold">
                    <i class="ti ti-pencil text-primary me-2"></i>Edit Rule Insentif
                </h4>
            </div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb m-0 py-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('home') }}" class="text-decoration-none text-reset">Home</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('Insentif.index') }}" class="text-decoration-none text-reset">Insentif</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Edit Rule</li>
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
                            <i class="ti ti-file-text me-2"></i>Form Edit Rule Insentif
                        </h5>
                    </div>

                    <!-- Card Body -->
                    <div class="card-body p-4">
                        <form action="{{ route('Insentif.update', encrypt($insentif->id)) }}" method="POST"
                            id="formRuleInsentif">
                            @csrf
                            @method('PUT')

                            <!-- Info Box -->
                            <div class="alert alert-light border mb-4 d-flex align-items-center gap-2">
                                <i class="ti ti-info-circle text-primary"></i>
                                <small class="mb-0 text-dark">Kolom dengan tanda <span class="text-danger">*</span> wajib
                                    diisi.</small>
                            </div>


                            <!-- Role (Readonly) -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold mb-2">
                                    Role <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="ti ti-user-circle text-muted"></i>
                                    </span>
                                    <input type="text" class="form-control"
                                        value="{{ $insentif->getRole->name ?? ($insentif->getRole->name ?? '') }}" readonly
                                        disabled placeholder="Role akan terpilih otomatis">
                                    <input type="hidden" name="Role" id="Role"
                                        value="{{ $insentif->getRole->id ?? ($insentif->getRole->id ?? '') }}">
                                </div>
                                <small class="text-muted d-block mt-1">
                                    <i class="ti ti-help me-1"></i>Role ditentukan dari halaman sebelumnya
                                </small>
                                @error('Role')
                                    <div class="invalid-feedback d-block mt-1">
                                        <i class="ti ti-alert-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Jenis Rule & Operator -->
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label for="JenisRule" class="form-label fw-semibold mb-2">
                                        Jenis Rule <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="ti ti-category text-muted"></i>
                                        </span>
                                        <select id="JenisRule" name="JenisRule"
                                            class="form-select @error('JenisRule') is-invalid @enderror" required>
                                            <option value="">Pilih Jenis Rule</option>
                                            <option value="omzet_shift"
                                                {{ old('JenisRule', $insentif->JenisRule ?? '') == 'omzet_shift' ? 'selected' : '' }}>
                                                omzet_shift
                                            </option>
                                            <option value="pasien_lama"
                                                {{ old('JenisRule', $insentif->JenisRule ?? '') == 'pasien_lama' ? 'selected' : '' }}>
                                                pasien_lama
                                            </option>
                                            <option value="pasien_baru"
                                                {{ old('JenisRule', $insentif->JenisRule ?? '') == 'pasien_baru' ? 'selected' : '' }}>
                                                pasien_baru
                                            </option>
                                            <option value="transaksi"
                                                {{ old('JenisRule', $insentif->JenisRule ?? '') == 'transaksi' ? 'selected' : '' }}>
                                                transaksi
                                            </option>
                                            <option value="tindakan"
                                                {{ old('JenisRule', $insentif->JenisRule ?? '') == 'tindakan' ? 'selected' : '' }}>
                                                tindakan
                                            </option>
                                        </select>

                                    </div>
                                    @error('JenisRule')
                                        <div class="invalid-feedback d-block mt-1">
                                            <i class="ti ti-alert-circle me-1"></i>{{ $message }}
                                        </div>
                                    @enderror
                                </div>


                                <div class="col-md-6">
                                    <label for="Operator" class="form-label fw-semibold mb-2">
                                        Operator <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="ti ti-calculator text-muted"></i>
                                        </span>
                                        <select id="Operator" name="Operator"
                                            class="form-select @error('Operator') is-invalid @enderror" required>
                                            <option value="">Pilih Operator</option>
                                            <option value=">="
                                                {{ old('Operator', $insentif->Operator ?? '') == '>=' ? 'selected' : '' }}>
                                                ≥ Lebih
                                                dari atau sama dengan</option>
                                            <option value="="
                                                {{ old('Operator', $insentif->Operator ?? '') == '=' ? 'selected' : '' }}>=
                                                Sama
                                                dengan</option>
                                            <option value="<="
                                                {{ old('Operator', $insentif->Operator ?? '') == '<=' ? 'selected' : '' }}>
                                                ≤ Kurang
                                                dari atau sama dengan</option>
                                            <option value=">"
                                                {{ old('Operator', $insentif->Operator ?? '') == '>' ? 'selected' : '' }}>＞
                                                Lebih
                                                dari</option>
                                            <option value="<"
                                                {{ old('Operator', $insentif->Operator ?? '') == '<' ? 'selected' : '' }}>＜
                                                Kurang
                                                dari</option>
                                            <option value="kelipatan"
                                                {{ old('Operator', $insentif->Operator ?? '') == 'kelipatan' ? 'selected' : '' }}>
                                                <i class="ti ti-chart-bar"></i> X = Y Kelipatan
                                            </option>


                                        </select>
                                    </div>
                                    @error('Operator')
                                        <div class="invalid-feedback d-block mt-1">
                                            <i class="ti ti-alert-circle me-1"></i>{{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Threshold & Tipe Nominal -->
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label for="Nilai" class="form-label fw-semibold mb-2">
                                        Threshold / Batas Acuan <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="ti ti-target text-muted"></i>
                                        </span>
                                        <input type="text" id="Nilai" name="Nilai"
                                            class="form-control @error('Nilai') is-invalid @enderror"
                                            value="{{ old('Nilai', isset($insentif->Nilai) ? number_format($insentif->Nilai, 0, ',', '.') : '') }}"
                                            required placeholder="Contoh: 6.000.000" min="0" inputmode="numeric"
                                            autocomplete="off">
                                        <span class="input-group-text bg-light text-muted small">IDR</span>
                                    </div>
                                    <small class="text-muted d-block mt-1">
                                        <i class="ti ti-help me-1"></i>Nilai acuan untuk kondisi rule
                                    </small>
                                    @error('Nilai')
                                        <div class="invalid-feedback d-block mt-1">
                                            <i class="ti ti-alert-circle me-1"></i>{{ $message }}
                                        </div>
                                    @enderror
                                </div>



                                <div class="col-md-6">
                                    <label for="TipeNominal" class="form-label fw-semibold mb-2">
                                        Tipe Nominal <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="ti ti-currency text-muted"></i>
                                        </span>
                                        <select id="TipeNominal" name="TipeNominal"
                                            class="form-select @error('TipeNominal') is-invalid @enderror" required>
                                            <option value="">Pilih Tipe Nominal</option>
                                            <option value="fixed"
                                                {{ old('TipeNominal', $insentif->TipeNominal ?? '') == 'fixed' ? 'selected' : '' }}>
                                                💰 Rupiah (Fixed)</option>
                                            <option value="persen"
                                                {{ old('TipeNominal', $insentif->TipeNominal ?? '') == 'persen' ? 'selected' : '' }}>
                                                📊 Persen (% dari omzet)</option>
                                        </select>
                                    </div>
                                    <small class="text-muted d-block mt-1">
                                        <i class="ti ti-help me-1"></i>Pilih apakah nominal insentif fixed atau persen
                                    </small>
                                    @error('TipeNominal')
                                        <div class="invalid-feedback d-block mt-1">
                                            <i class="ti ti-alert-circle me-1"></i>{{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Nominal (Dynamic Label) -->
                            <div class="mb-4">
                                <label for="Nominal" class="form-label fw-semibold mb-2">
                                    <span id="nominalLabel">Nominal Insentif</span> <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="ti ti-cash text-muted" id="nominalIcon"></i>
                                    </span>
                                    <input type="text" id="Nominal" name="Nominal"
                                        class="form-control @error('Nominal') is-invalid @enderror"
                                        value="{{ old('Nominal', isset($insentif->Nominal) ? number_format($insentif->Nominal, 0, ',', '.') : '') }}"
                                        required placeholder="Masukkan nominal" min="0" inputmode="numeric"
                                        autocomplete="off">
                                    <span class="input-group-text bg-light text-muted small" id="nominalSuffix">IDR</span>
                                </div>
                                <small class="text-muted d-block mt-1" id="nominalHint">
                                    <i class="ti ti-help me-1"></i>Masukkan nominal insentif yang akan diberikan
                                </small>
                                @error('Nominal')
                                    <div class="invalid-feedback d-block mt-1">
                                        <i class="ti ti-alert-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>



                            <!-- Berlaku Per & Kondisi Tambahan -->
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label for="BerlakuPer" class="form-label fw-semibold mb-2">
                                        Berlaku Per
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="ti ti-repeat text-muted"></i>
                                        </span>
                                        <select id="BerlakuPer" name="BerlakuPer"
                                            class="form-select @error('BerlakuPer') is-invalid @enderror" required>
                                            <option value="">Pilih frekuensi</option>
                                            <option value="shift"
                                                {{ old('BerlakuPer', $insentif->BerlakuPer ?? '') == 'shift' ? 'selected' : '' }}>
                                                Shift</option>
                                            <option value="transaksi"
                                                {{ old('BerlakuPer', $insentif->BerlakuPer ?? '') == 'transaksi' ? 'selected' : '' }}>
                                                Transaksi</option>
                                        </select>

                                    </div>
                                    <small class="text-muted d-block mt-1">
                                        <i class="ti ti-help me-1"></i>Frekuensi pemberlakuan rule
                                    </small>
                                    @error('BerlakuPer')
                                        <div class="invalid-feedback d-block mt-1">
                                            <i class="ti ti-alert-circle me-1"></i>{{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                {{-- Jika ingin aktifkan kondisi tambahan edit, uncomment dan sesuaikan kolom database --}}
                                {{--
                                <div class="col-md-6">
                                    <label for="KondisiTambahan" class="form-label fw-semibold mb-2">
                                        Kondisi Tambahan
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="ti ti-adjustments text-muted"></i>
                                        </span>
                                        <input type="text" id="KondisiTambahan" name="KondisiTambahan"
                                            class="form-control @error('KondisiTambahan') is-invalid @enderror"
                                            value="{{ old('KondisiTambahan', $insentif->KondisiTambahan ?? '') }}"
                                            placeholder="Opsional: syarat tambahan rule">
                                    </div>
                                    <small class="text-muted d-block mt-1">
                                        <i class="ti ti-help me-1"></i>Opsional: kondisi khusus lainnya
                                    </small>
                                    @error('KondisiTambahan')
                                        <div class="invalid-feedback d-block mt-1">
                                            <i class="ti ti-alert-circle me-1"></i>{{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                --}}
                            </div>

                            <!-- Keterangan -->
                            <div class="mb-4">
                                <label for="Keterangan" class="form-label fw-semibold mb-2">
                                    Keterangan
                                </label>
                                <div class="input-group align-items-start">
                                    <span class="input-group-text bg-light">
                                        <i class="ti ti-notebook text-muted"></i>
                                    </span>
                                    <textarea id="Keterangan" name="Keterangan" class="form-control @error('Keterangan') is-invalid @enderror"
                                        rows="3" placeholder="Jelaskan rule insentif ini secara singkat">{{ old('Keterangan', $insentif->Keterangan ?? '') }}</textarea>
                                </div>
                                @error('Keterangan')
                                    <div class="invalid-feedback d-block mt-1">
                                        <i class="ti ti-alert-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Divider -->
                            <hr class="my-4 text-muted opacity-25">

                            <!-- Actions -->
                            <div class="d-flex justify-content-end gap-2 pt-2">
                                <a href="{{ route('Insentif.index') }}" class="btn btn-light px-4">
                                    <i class="ti ti-arrow-left me-1"></i>Batal
                                </a>
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="ti ti-device-floppy me-1"></i>Update Rule
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Back Link (Mobile Friendly) -->
                <div class="text-center mt-3 d-lg-none">
                    <a href="{{ route('Insentif.index') }}" class="text-muted small text-decoration-none">
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
        .input-group .form-select,
        .input-group textarea.form-control {
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

        /* Readonly input styling */
        .form-control[readonly]:disabled {
            background-color: #f8f9fa;
            opacity: 0.8;
            cursor: not-allowed;
        }

        /* Datalist styling */
        datalist {
            display: none;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ===== DOM Elements =====
            const tipeNominalSelect = document.getElementById('TipeNominal');
            const nominalInput = document.getElementById('Nominal');
            const nominalLabel = document.getElementById('nominalLabel');
            const nominalIcon = document.getElementById('nominalIcon');
            const nominalSuffix = document.getElementById('nominalSuffix');
            const nominalHint = document.getElementById('nominalHint');

            // ===== Dynamic Nominal Field =====
            function updateNominalField() {
                const tipe = tipeNominalSelect.value;

                if (tipe === 'persen') {
                    // Persen mode
                    nominalLabel.textContent = 'Persentase Insentif';
                    nominalIcon.className = 'ti ti-percentage text-muted';
                    nominalSuffix.textContent = '%';
                    nominalHint.innerHTML =
                        '<i class="ti ti-help me-1"></i>Masukkan persentase (%), contoh: 5 untuk 5%';
                    nominalInput.placeholder = 'Contoh: 5';
                    nominalInput.step = '0.01';
                    nominalInput.min = '0';
                    nominalInput.max = '100';
                } else if (tipe === 'rupiah') {
                    // Rupiah mode
                    nominalLabel.textContent = 'Nominal Insentif';
                    nominalIcon.className = 'ti ti-cash text-muted';
                    nominalSuffix.textContent = 'IDR';
                    nominalHint.innerHTML = '<i class="ti ti-help me-1"></i>Masukkan nominal dalam Rupiah';
                    nominalInput.placeholder = 'Contoh: 50000';
                    nominalInput.step = '1000';
                    nominalInput.min = '0';
                    nominalInput.max = '';
                } else {
                    // Default / empty
                    nominalLabel.textContent = 'Nominal Insentif';
                    nominalIcon.className = 'ti ti-cash text-muted';
                    nominalSuffix.textContent = 'IDR';
                    nominalHint.innerHTML = '<i class="ti ti-help me-1"></i>Pilih tipe nominal terlebih dahulu';
                    nominalInput.placeholder = 'Masukkan nominal';
                    nominalInput.step = 'any';
                    nominalInput.min = '0';
                    nominalInput.max = '';
                }
            }

            // Event: Update nominal field when tipe changes
            tipeNominalSelect?.addEventListener('change', updateNominalField);

            // Init on load (if old value exists or if edit)
            if (tipeNominalSelect?.value) {
                updateNominalField();
            }

            // ===== Auto-focus =====
            document.getElementById('JenisRule')?.focus();

            // ===== Optional: Format Nilai field with thousand separator (visual only) =====
            const nilaiInput = document.getElementById('Nilai');
            if (nilaiInput) {
                // Store raw value
                let rawNilai = nilaiInput.value;

                nilaiInput.addEventListener('focus', function() {
                    // Show raw value for editing
                    this.value = rawNilai.replace(/[^0-9]/g, '');
                });

                nilaiInput.addEventListener('blur', function() {
                    // Store raw value
                    rawNilai = this.value.replace(/[^0-9]/g, '');
                    // this.value = rawNilai ? new Intl.NumberFormat('id-ID').format(rawNilai) : '';
                });
            }
        });
    </script>
    <script>
        // Rupiah input formatting
        document.addEventListener('DOMContentLoaded', function() {
            const nilaiInput = document.getElementById('Nilai');
            if (nilaiInput) {
                nilaiInput.addEventListener('input', function(e) {
                    let value = this.value.replace(/[^0-9]/g, '');
                    if (value) {
                        // Format to rupiah
                        this.value = parseInt(value).toLocaleString('id-ID');
                    } else {
                        this.value = '';
                    }
                });

                // On focus, remove formatting
                nilaiInput.addEventListener('focus', function() {
                    this.value = this.value.replace(/[^0-9]/g, '');
                });

                // On blur, reformat
                nilaiInput.addEventListener('blur', function() {
                    let value = this.value.replace(/[^0-9]/g, '');
                    if (value) {
                        this.value = parseInt(value).toLocaleString('id-ID');
                    }
                });
            }
        });
    </script>
    <script>
        // Format input Nominal as Rupiah while typing
        document.addEventListener('DOMContentLoaded', function() {
            const nominalInput = document.getElementById('Nominal');
            if (nominalInput) {
                nominalInput.addEventListener('input', function(e) {
                    let value = this.value.replace(/[^0-9]/g, '');
                    if (value) {
                        this.value = parseInt(value, 10).toLocaleString('id-ID');
                    } else {
                        this.value = '';
                    }
                });
                // Remove formatting on focus for easier editing
                nominalInput.addEventListener('focus', function() {
                    this.value = this.value.replace(/[^0-9]/g, '');
                });
                // Restore formatting on blur
                nominalInput.addEventListener('blur', function() {
                    let value = this.value.replace(/[^0-9]/g, '');
                    if (value) {
                        this.value = parseInt(value, 10).toLocaleString('id-ID');
                    }
                });
            }
        });
    </script>
@endpush
