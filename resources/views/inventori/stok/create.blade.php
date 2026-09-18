@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <!-- Page Title Header -->
        <div class="page-title-head d-flex align-items-center flex-wrap gap-2 mb-4">
            <div class="flex-grow-1">
                <h4 class="page-main-title m-0 fw-semibold">
                    <i class="ti ti-plus me-2 text-primary"></i>Input Stok Barang
                </h4>
            </div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb m-0 py-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}"
                            class="text-decoration-none text-reset">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('Stok.index') }}"
                            class="text-decoration-none text-reset">Stok Barang</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Input Stok</li>
                </ol>
            </nav>
        </div>

        <!-- Form Card -->
        <div class="row justify-content-center">
            <div class="col-xl-12 col-lg-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h5 class="card-title mb-0 fw-semibold">
                            <i class="ti ti-file-text me-2"></i>Form Stok Masuk / Penyesuaian
                        </h5>
                    </div>

                    <div class="card-body p-4">
                        <form action="{{ route('Stok.store') }}" method="POST" id="formStok">
                            @csrf

                            <!-- Info Box -->
                            <div class="alert alert-light border mb-4 d-flex align-items-center gap-2" style="color: #000;">
                                <i class="ti ti-info-circle text-primary"></i>
                                <small class="mb-0" style="color: #000;">
                                    Form ini akan menambahkan stok barang di cabang terpilih dan otomatis mencatat riwayat
                                    mutasi.
                                </small>
                            </div>

                            <div class="row g-3">
                                <!-- Cabang / Klinik -->
                                <!-- Cabang / Klinik -->
                                <div class="col-md-6 mb-3">
                                    <label for="KodeKlinik" class="form-label fw-semibold mb-2">
                                        Cabang / Klinik <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i
                                                class="ti ti-building text-muted"></i></span>
                                        @if (auth()->user()->hasRole('Superadmin'))
                                            <select name="KodeKlinik" id="KodeKlinik"
                                                class="form-select @error('KodeKlinik') is-invalid @enderror" required>
                                                <option value="">Pilih Cabang</option>
                                                @foreach ($kliniks as $k)
                                                    {{-- 🔥 PERBAIKAN DI SINI: Cek old() dulu, lalu $defaultKlinik --}}
                                                    <option value="{{ $k->Kode }}"
                                                        {{ old('KodeKlinik', $defaultKlinik) == $k->Kode ? 'selected' : '' }}>
                                                        {{ $k->Nama }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        @else
                                            {{-- Untuk non-superadmin, langsung isi value-nya --}}
                                            <input type="text" name="KodeKlinik" class="form-control"
                                                value="{{ old('KodeKlinik', $defaultKlinik) }}" readonly>
                                        @endif
                                    </div>
                                    @error('KodeKlinik')
                                        <div class="invalid-feedback d-block mt-1"><i
                                                class="ti ti-alert-circle me-1"></i>{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Jenis Mutasi -->
                                <div class="col-md-6 mb-3">
                                    <label for="JenisMutasi" class="form-label fw-semibold mb-2">
                                        Jenis Mutasi <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i
                                                class="ti ti-arrows-exchange text-muted"></i></span>
                                        <select name="JenisMutasi" id="JenisMutasi"
                                            class="form-select @error('JenisMutasi') is-invalid @enderror" required>
                                            <option value="masuk"
                                                {{ old('JenisMutasi', 'masuk') == 'masuk' ? 'selected' : '' }}>Barang Masuk
                                                (Pembelian/Retur)</option>
                                            <option value="penyesuaian"
                                                {{ old('JenisMutasi') == 'penyesuaian' ? 'selected' : '' }}>Penyesuaian Stok
                                                (Opname)</option>
                                        </select>
                                    </div>
                                    @error('JenisMutasi')
                                        <div class="invalid-feedback d-block mt-1"><i
                                                class="ti ti-alert-circle me-1"></i>{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Barang (Select2) -->
                                <!-- Barang (Select2) -->
                                <div class="col-md-12 mb-3">
                                    <label for="BarangId" class="form-label fw-semibold mb-2">
                                        Pilih Barang <span class="text-danger">*</span>
                                    </label>
                                    <div class="select2-container-wrapper">
                                        <select id="BarangId" name="BarangId"
                                            class="form-select select2-single @error('BarangId') is-invalid @enderror"
                                            required>
                                            <option value="">Cari nama atau kode barang...</option>
                                            @foreach ($barangs as $b)
                                                {{-- 🔥 PERBAIKAN DI SINI: Cek old() dulu, lalu $defaultBarang --}}
                                                <option value="{{ $b->id }}"
                                                    {{ old('BarangId', $defaultBarang) == $b->id ? 'selected' : '' }}>
                                                    {{ $b->KodeBarang }} - {{ $b->NamaBarang }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('BarangId')
                                        <div class="invalid-feedback d-block mt-1"><i
                                                class="ti ti-alert-circle me-1"></i>{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Jumlah -->
                                <div class="col-md-6 mb-3">
                                    <label for="JumlahInput" id="labelJumlah" class="form-label fw-semibold mb-2">
                                        Jumlah Masuk <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="ti ti-hash text-muted"></i></span>
                                        <input type="number" id="JumlahInput" name="JumlahInput"
                                            class="form-control @error('JumlahInput') is-invalid @enderror"
                                            value="{{ old('JumlahInput') }}" required min="1"
                                            placeholder="Masukkan jumlah barang yang masuk" autocomplete="off">
                                    </div>
                                    <small class="text-muted d-block mt-1" id="helperJumlah">
                                        <i class="ti ti-info-circle me-1"></i>Angka ini akan <strong>ditambahkan</strong> ke
                                        stok saat ini.
                                    </small>
                                    @error('JumlahInput')
                                        <div class="invalid-feedback d-block mt-1"><i
                                                class="ti ti-alert-circle me-1"></i>{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Keterangan -->
                                <div class="col-md-12 mb-3">
                                    <label for="Keterangan" class="form-label fw-semibold mb-2">
                                        Keterangan <span class="text-muted fw-normal">(Opsional)</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i
                                                class="ti ti-notes text-muted"></i></span>
                                        <textarea id="Keterangan" name="Keterangan" class="form-control @error('Keterangan') is-invalid @enderror"
                                            rows="3" placeholder="Contoh: Pembelian dari supplier PT. ABC, atau Hasil opname bulanan">{{ old('Keterangan') }}</textarea>
                                    </div>
                                    @error('Keterangan')
                                        <div class="invalid-feedback d-block mt-1"><i
                                                class="ti ti-alert-circle me-1"></i>{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Divider -->
                            <hr class="my-4 text-muted opacity-25">

                            <!-- Actions -->
                            <div class="d-flex justify-content-end gap-2 pt-2">
                                <a href="{{ route('Stok.index') }}" class="btn btn-light px-4">
                                    <i class="ti ti-arrow-left me-1"></i>Batal
                                </a>
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="ti ti-device-floppy me-1"></i>Simpan Stok
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
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <style>
        .form-control:focus,
        .form-select:focus,
        .input-group:focus-within {
            box-shadow: 0 0 0 3px rgba(33, 150, 243, 0.15);
            border-color: #2196f3;
        }

        .input-group-text {
            border-right: none;
            min-width: 45px;
            justify-content: center;
        }

        .input-group .form-control,
        .input-group .form-select {
            border-left: none;
        }

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

        /* Select2 Custom Styling */
        .select2-container {
            width: 100% !important;
        }

        .select2-container .select2-selection--single {
            height: 38px;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            display: flex;
            align-items: center;
        }

        .select2-container--focus .select2-selection--single {
            border-color: #2196f3;
            box-shadow: 0 0 0 3px rgba(33, 150, 243, 0.15);
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 36px;
            padding-left: 12px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px;
            right: 8px;
        }

        .is-invalid~.select2-container .select2-selection--single {
            border-color: #dc3545;
        }
    </style>
@endpush

@push('scripts')
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Select2 Single untuk Barang
            $('#BarangId').select2({
                placeholder: 'Cari nama atau kode barang...',
                allowClear: true,
                width: '100%'
            });

            // Auto-focus ke field pertama (Klinik jika superadmin, atau Barang)
            @if (auth()->user()->hasRole('Superadmin'))
                document.getElementById('KodeKlinik')?.focus();
            @else
                $('#BarangId').focus();
            @endif
        });
        const jenisMutasiSelect = document.getElementById('JenisMutasi');
        const labelJumlah = document.getElementById('labelJumlah');
        const inputJumlah = document.getElementById('JumlahInput');
        const helperJumlah = document.getElementById('helperJumlah');

        jenisMutasiSelect.addEventListener('change', function() {
            if (this.value === 'penyesuaian') {
                labelJumlah.innerHTML = 'Stok Fisik Aktual (Target) <span class="text-danger">*</span>';
                inputJumlah.setAttribute('placeholder', 'Contoh: Hasil hitung fisik ada 11, ketik 11');
                inputJumlah.setAttribute('min', '0'); // Boleh 0 kalau barang habis total
                helperJumlah.innerHTML =
                    '<i class="ti ti-info-circle me-1"></i>Sistem akan otomatis menghitung selisihnya. Stok akan disesuaikan menjadi angka ini.';
            } else {
                labelJumlah.innerHTML = 'Jumlah Masuk <span class="text-danger">*</span>';
                inputJumlah.setAttribute('placeholder', 'Masukkan jumlah barang yang masuk');
                inputJumlah.setAttribute('min', '1');
                helperJumlah.innerHTML =
                    '<i class="ti ti-info-circle me-1"></i>Angka ini akan <strong>ditambahkan</strong> ke stok saat ini.';
            }
        });
    </script>
@endpush
