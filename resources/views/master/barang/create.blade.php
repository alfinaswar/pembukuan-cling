@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="page-title-head d-flex align-items-center flex-wrap gap-2 mb-4">
            <div class="flex-grow-1">
                <h4 class="page-main-title m-0 fw-semibold"><i class="ti ti-plus me-2 text-primary"></i>Tambah Barang</h4>
            </div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb m-0 py-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-reset">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('Barang.index') }}" class="text-decoration-none text-reset">Barang</a></li>
                    <li class="breadcrumb-item active">Tambah Data</li>
                </ol>
            </nav>
        </div>

        <div class="row justify-content-center">
            <div class="col-xl-12 col-lg-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h5 class="card-title mb-0 fw-semibold"><i class="ti ti-file-text me-2"></i>Form Data Barang</h5>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('Barang.store') }}" method="POST" id="formBarang">
                            @csrf
                            <div class="alert alert-light border mb-4 d-flex align-items-center gap-2">
                                <i class="ti ti-info-circle text-primary"></i>
                                <small class="mb-0">Kolom dengan tanda <span class="text-danger">*</span> wajib diisi.</small>
                            </div>

                            <div class="row g-3">
                                {{-- <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold mb-2">Kode Barang <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="ti ti-barcode text-muted"></i></span>
                                        <input type="text" name="KodeBarang" class="form-control @error('KodeBarang') is-invalid @enderror" value="{{ old('KodeBarang') }}" required autocomplete="off" placeholder="BRG-001" style="text-transform: uppercase;">
                                    </div>
                                    @error('KodeBarang')<div class="invalid-feedback d-block mt-1"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div>@enderror
                                </div> --}}

                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold mb-2">Nama Barang <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="ti ti-package text-muted"></i></span>
                                        <input type="text" name="NamaBarang" class="form-control @error('NamaBarang') is-invalid @enderror" value="{{ old('NamaBarang') }}" required autocomplete="off" placeholder="Nama produk">
                                    </div>
                                    @error('NamaBarang')<div class="invalid-feedback d-block mt-1"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div>@enderror
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label class="form-label fw-semibold mb-2">Kategori <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="ti ti-category text-muted"></i></span>
                                        <select name="KategoriBarangId" class="form-select @error('KategoriBarangId') is-invalid @enderror" required>
                                            <option value="">Pilih Kategori</option>
                                            @foreach($kategoris as $kat)
                                                <option value="{{ $kat->id }}" {{ old('KategoriBarangId') == $kat->id ? 'selected' : '' }}>{{ $kat->Nama }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('KategoriBarangId')<div class="invalid-feedback d-block mt-1"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div>@enderror
                                </div>
                            </div>

                            <hr class="my-4 text-muted opacity-25">
                            <div class="d-flex justify-content-end gap-2 pt-2">
                                <a href="{{ route('Barang.index') }}" class="btn btn-light px-4"><i class="ti ti-arrow-left me-1"></i>Batal</a>
                                <button type="submit" class="btn btn-primary px-4"><i class="ti ti-device-floppy me-1"></i>Simpan Data</button>
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
        .form-control:focus, .form-select:focus, .input-group:focus-within { box-shadow: 0 0 0 3px rgba(33, 150, 243, 0.15); border-color: #2196f3; }
        .input-group-text { border-right: none; min-width: 45px; justify-content: center; }
        .input-group .form-control, .input-group .form-select { border-left: none; }
        .invalid-feedback { animation: fadeIn 0.2s ease; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(-5px); } to { opacity: 1; transform: translateY(0); } }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const kodeInput = document.querySelector('input[name="KodeBarang"]');
            kodeInput?.focus();
            kodeInput?.addEventListener('input', function() {
                this.value = this.value.toUpperCase();
            });
        });
    </script>
@endpush
