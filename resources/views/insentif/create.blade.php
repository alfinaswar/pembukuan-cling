@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="page-title-head d-flex align-items-center">
            <div class="flex-grow-1">
                <h4 class="page-main-title m-0">Tambah Rule Insentif</h4>
            </div>
            <div class="text-end">
                <ol class="breadcrumb m-0 py-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Master</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('Insentif.index') }}">Insentif</a></li>
                    <li class="breadcrumb-item active">Tambah Rule Insentif</li>
                </ol>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-xl-12 mx-auto">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Form Tambah Rule Insentif</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('Insentif.store') }}" method="POST">
                            @csrf

                            <div class="row">
                                <div class="mb-2 col-md-6">
                                    <label for="Role" class="form-label">Role <span class="text-danger">*</span></label>
                                    <input type="text" id="Role" name="Role"
                                        class="form-control @error('Role') is-invalid @enderror" value="{{ old('Role') }}"
                                        required placeholder="Masukkan Role">
                                    @error('Role')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="mb-2 col-md-6">
                                    <label for="JenisRule" class="form-label">Jenis Rule <span
                                            class="text-danger">*</span></label>
                                    <input type="text" id="JenisRule" name="JenisRule"
                                        class="form-control @error('JenisRule') is-invalid @enderror"
                                        value="{{ old('JenisRule') }}" required placeholder="Masukkan Jenis Rule">
                                    @error('JenisRule')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="mb-2 col-md-4">
                                    <label for="Operator" class="form-label">Operator <span
                                            class="text-danger">*</span></label>
                                    <select id="Operator" name="Operator"
                                        class="form-control @error('Operator') is-invalid @enderror" required>
                                        <option value="">Pilih Operator</option>
                                        <option value=">=" {{ old('Operator') == '>=' ? 'selected' : '' }}>Lebih dari
                                            atau sama dengan</option>
                                        <option value="=" {{ old('Operator') == '=' ? 'selected' : '' }}>Sama dengan
                                        </option>
                                        <option value="<=" {{ old('Operator') == '<=' ? 'selected' : '' }}>Kurang dari
                                            atau sama dengan</option>
                                    </select>

                                    @error('Operator')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>


                                <div class="mb-2 col-md-4">
                                    <label for="Nilai" class="form-label">Treshhold / Batas Acuan <span
                                            class="text-danger">*</span></label>
                                    <input type="number" id="Nilai" name="Nilai"
                                        class="form-control @error('Nilai') is-invalid @enderror"
                                        value="{{ old('Nilai') }}" required placeholder="Masukkan Nilai">
                                    @error('Nilai')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="mb-2 col-md-4">
                                    <label for="TipeNominal" class="form-label">Tipe Nominal <span
                                            class="text-danger">*</span></label>
                                    <select id="TipeNominal" name="TipeNominal"
                                        class="form-control @error('TipeNominal') is-invalid @enderror" required>
                                        <option value="">Pilih Tipe Nominal</option>
                                        <option value="persen" {{ old('TipeNominal') == 'persen' ? 'selected' : '' }}>
                                            Persen</option>
                                        <option value="rupiah" {{ old('TipeNominal') == 'rupiah' ? 'selected' : '' }}>
                                            Rupiah</option>
                                    </select>
                                    @error('TipeNominal')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                            </div>

                            <div class="row">

                                <div class="mb-2 col-md-4">
                                    <label for="Nominal" class="form-label">Nominal <span
                                            class="text-danger">*</span></label>
                                    <input type="number" id="Nominal" name="Nominal"
                                        class="form-control @error('Nominal') is-invalid @enderror"
                                        value="{{ old('Nominal') }}" required placeholder="Masukkan Nominal">
                                    @error('Nominal')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="mb-2 col-md-4">
                                    <label for="BerlakuPer" class="form-label">Berlaku Per</label>
                                    <input type="text" id="BerlakuPer" name="BerlakuPer"
                                        class="form-control @error('BerlakuPer') is-invalid @enderror"
                                        value="{{ old('BerlakuPer') }}" placeholder="Masukkan Berlaku Per">
                                    @error('BerlakuPer')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="mb-2 col-md-4">
                                    <label for="KondisiTambahan" class="form-label">Kondisi Tambahan</label>
                                    <input type="text" id="KondisiTambahan" name="KondisiTambahan"
                                        class="form-control @error('KondisiTambahan') is-invalid @enderror"
                                        value="{{ old('KondisiTambahan') }}" placeholder="Masukkan Kondisi Tambahan">
                                    @error('KondisiTambahan')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-2">
                                <label for="Keterangan" class="form-label">Keterangan</label>
                                <textarea id="Keterangan" name="Keterangan" class="form-control @error('Keterangan') is-invalid @enderror"
                                    placeholder="Masukkan Keterangan">{{ old('Keterangan') }}</textarea>
                                @error('Keterangan')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="text-end mt-3">
                                <a href="{{ route('Insentif.index') }}" class="btn btn-light">Batal</a>
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
