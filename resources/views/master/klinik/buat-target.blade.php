@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <!-- Page Title Header -->
        <div class="page-title-head d-flex align-items-center flex-wrap gap-2 mb-4">
            <div class="flex-grow-1">
                <h4 class="page-main-title m-0 fw-semibold">
                    <i class="ti ti-plus me-2 text-primary"></i>Tambah Target Capaian Klinik
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
                    <li class="breadcrumb-item active" aria-current="page">Tambah Target</li>
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
                            <i class="ti ti-file-text me-2"></i>Form Data Target Capaian - <span
                                class="text-primary">{{ $data->Nama }}</span>

                        </h5>
                    </div>

                    <!-- Card Body -->
                    <div class="card-body p-4">
                        <form action="{{ route('Klinik.simpan-target', encrypt($data->id)) }}" method="POST"
                            id="formTargetKlinik">
                            @csrf

                            <!-- Info Box -->
                            <div class="alert alert-light border mb-4 d-flex align-items-center gap-2" style="color: #000;">
                                <i class="ti ti-info-circle text-primary"></i>
                                <small class="mb-0" style="color: #000;">Kolom dengan tanda <span
                                        class="text-danger">*</span> wajib diisi.
                                </small>
                            </div>

                            <!-- Tahun -->
                            <div class="mb-4">
                                <label for="Tahun" class="form-label fw-semibold mb-2">
                                    Tahun <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="ti ti-calendar-event text-muted"></i>
                                    </span>
                                    <input type="number" id="Tahun" name="Tahun"
                                        class="form-control @error('Tahun') is-invalid @enderror"
                                        value="{{ old('Tahun', date('Y')) }}" required min="2000" max="2100"
                                        placeholder="Contoh: 2024" autocomplete="off">
                                </div>
                                @error('Tahun')
                                    <div class="invalid-feedback d-block mt-1">
                                        <i class="ti ti-alert-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Besar Target -->
                            <div class="mb-4">
                                <label for="BesarTarget" class="form-label fw-semibold mb-2">
                                    Besar Target <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="ti ti-target text-muted"></i>
                                    </span>
                                    <input type="text" id="BesarTarget" name="BesarTarget"
                                        class="form-control rupiah-input @error('BesarTarget') is-invalid @enderror"
                                        value="{{ old('BesarTarget') }}" required placeholder="Contoh: Rp 5.000"
                                        autocomplete="off" inputmode="numeric">
                                </div>
                                @error('BesarTarget')
                                    <div class="invalid-feedback d-block mt-1">
                                        <i class="ti ti-alert-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>


                            <!-- Divider -->
                            <hr class="my-4 text-muted opacity-25">

                            <!-- Actions -->
                            <div class="d-flex justify-content-end gap-2 pt-2">
                                <a href="{{ route('Klinik.target-capaian', encrypt($data->id)) }}"
                                    class="btn btn-light px-4">
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
                    <a href="{{ route('Klinik.target-capaian', encrypt($data->id)) }}"
                        class="text-muted small text-decoration-none">
                        <i class="ti ti-arrow-back-up me-1"></i>Kembali ke daftar target
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

        /* Tambahkan supaya angka pada rupiah-input rata kanan */
        .rupiah-input {
            text-align: right;
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
            document.getElementById('Tahun')?.focus();

            // Format input BesarTarget sebagai Rupiah saat mengetik
            const rupiahInput = document.getElementById('BesarTarget');
            if (rupiahInput) {
                // Fungsi konversi ke format Rupiah
                function formatRupiah(angka, prefix) {
                    var number_string = angka.replace(/[^,\d]/g, '').toString(),
                        split = number_string.split(','),
                        sisa = split[0].length % 3,
                        rupiah = split[0].substr(0, sisa),
                        ribuan = split[0].substr(sisa).match(/\d{3}/gi);

                    if (ribuan) {
                        var separator = sisa ? '.' : '';
                        rupiah += separator + ribuan.join('.');
                    }

                    rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
                    return prefix == undefined ? rupiah : (rupiah ? 'Rp ' + rupiah : '');
                }

                rupiahInput.addEventListener('input', function(e) {
                    let originalValue = this.value;
                    let caretPosition = this.selectionStart;

                    // Hitung offset caret sebelum formatting
                    let valueBefore = originalValue.substring(0, caretPosition).replace(/[^,\d]/g, '')
                        .length;

                    this.value = formatRupiah(this.value, 'Rp ');

                    // Setelah diformat, update posisi caret ke kanan
                    let newCaret = 0,
                        nonformatCount = 0;
                    for (let i = 0; i < this.value.length; i++) {
                        if (this.value[i].match(/\d/)) {
                            nonformatCount++;
                        }
                        if (nonformatCount >= valueBefore) {
                            newCaret = i + 1;
                            break;
                        }
                    }
                    // Jaga caret tetap di akhir jika user kehabisan digit
                    if (newCaret === 0) newCaret = this.value.length;
                    this.setSelectionRange(newCaret, newCaret);
                });

                // Jika submit, konversi ke angka saja (optional, frontend only)
                rupiahInput.form.addEventListener('submit', function(e) {
                    if (rupiahInput.value) {
                        rupiahInput.value = rupiahInput.value.replace(/[^0-9]/g, '');
                    }
                });
            }
        });
    </script>
@endpush
