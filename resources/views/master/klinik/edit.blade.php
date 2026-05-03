@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="page-title-head d-flex align-items-center">
            <div class="flex-grow-1">
                <h4 class="page-main-title m-0">Edit Klinik</h4>
            </div>
            <div class="text-end">
                <ol class="breadcrumb m-0 py-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Master</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('Klinik.index') }}">Klinik</a></li>
                    <li class="breadcrumb-item active">Edit Klinik</li>
                </ol>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-xl-12 mx-auto">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Form Edit Klinik</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('Klinik.update', encrypt($MasterKlinik->id)) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="mb-1">
                                <label for="nama" class="form-label">Nama <span class="text-danger">*</span></label>
                                <input type="text" id="nama" name="Nama"
                                    class="form-control @error('Nama') is-invalid @enderror"
                                    value="{{ old('Nama', $MasterKlinik->Nama) }}" required
                                    placeholder="Masukkan nama klinik">
                                @error('Nama')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mb-1">
                                <label for="alamat" class="form-label">Alamat <span class="text-danger">*</span></label>
                                <textarea id="alamat" name="Alamat" class="form-control @error('Alamat') is-invalid @enderror" rows="3"
                                    required placeholder="Masukkan alamat klinik">{{ old('Alamat', $MasterKlinik->Alamat) }}</textarea>
                                @error('Alamat')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mb-1">
                                <label for="notelp" class="form-label">No. Telp </label>
                                <input type="text" id="notelp" name="NoTelp"
                                    class="form-control @error('NoTelp') is-invalid @enderror"
                                    value="{{ old('NoTelp', $MasterKlinik->NoTelp) }}" placeholder="Masukkan nomor telepon">
                                @error('NoTelp')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mb-1">
                                <label for="email" class="form-label">Email</label>
                                <input type="text" id="email" name="Email"
                                    class="form-control @error('Email') is-invalid @enderror"
                                    value="{{ old('Email', $MasterKlinik->Email) }}"
                                    placeholder="Masukkan email (opsional)">
                                @error('Email')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- <div class="mb-1">
                                <label for="status" class="form-label">Status</label>
                                <select id="status" name="Status"
                                    class="form-select @error('Status') is-invalid @enderror" style="display: block;">
                                    <option value="1"
                                        {{ old('Status', $MasterKlinik->Status) == 1 ? 'selected' : '' }}>Aktif</option>
                                    <option value="0"
                                        {{ old('Status', $MasterKlinik->Status) == 0 ? 'selected' : '' }}>Tidak Aktif
                                    </option>
                                </select>
                                @error('Status')
                                    <span class="invalid-feedback" style="display:block;">{{ $message }}</span>
                                @enderror
                            </div> --}}


                            <div class="text-end mt-3">
                                <a href="{{ route('Klinik.index') }}" class="btn btn-light">Batal</a>
                                <button type="submit" class="btn btn-primary">Update</button>
                            </div>
                        </form>
                    </div>
                    <!-- end card-body -->
                </div>
            </div>
        </div>
    </div>
@endsection
