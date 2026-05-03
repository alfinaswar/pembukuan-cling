@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="page-title-head d-flex align-items-center">
            <div class="flex-grow-1">
                <h4 class="page-main-title m-0">Tambah Metode Pembayaran</h4>
            </div>
            <div class="text-end">
                <ol class="breadcrumb m-0 py-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Master</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('MetodePembayaran.index') }}">Metode Pembayaran</a></li>
                    <li class="breadcrumb-item active">Tambah Metode Pembayaran</li>
                </ol>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-xl-12 mx-auto">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Form Tambah Metode Pembayaran</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('MetodePembayaran.store') }}" method="POST">
                            @csrf
                            <div class="mb-1">
                                <label for="nama" class="form-label">Nama <span class="text-danger">*</span></label>
                                <input type="text" id="nama" name="Nama"
                                    class="form-control @error('Nama') is-invalid @enderror" value="{{ old('Nama') }}"
                                    required placeholder="Masukkan nama metode pembayaran">
                                @error('Nama')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>



                            <div class="text-end mt-3">
                                <a href="{{ route('MetodePembayaran.index') }}" class="btn btn-light">Batal</a>
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </div>
                        </form>
                    </div>
                    <!-- end card-body -->
                </div>
            </div>
        </div>
    </div>
@endsection
