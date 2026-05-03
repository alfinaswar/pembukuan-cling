@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="page-title-head d-flex align-items-center">
            <div class="flex-grow-1">
                <h4 class="page-main-title m-0">Edit Shift Kerja</h4>
            </div>
            <div class="text-end">
                <ol class="breadcrumb m-0 py-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Master</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('MasterShift.index') }}">Shift Kerja</a></li>
                    <li class="breadcrumb-item active">Edit Shift Kerja</li>
                </ol>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-xl-12 mx-auto">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Form Edit Shift Kerja</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('MasterShift.update', encrypt($MasterShift->id)) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="mb-1">
                                <label for="nama" class="form-label">Nama Shift <span
                                        class="text-danger">*</span></label>
                                <input type="text" id="nama" name="Nama"
                                    class="form-control @error('Nama') is-invalid @enderror"
                                    value="{{ old('Nama', $MasterShift->Nama) }}" required
                                    placeholder="Masukkan nama shift">
                                @error('Nama')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-1">
                                <label for="jamMulai" class="form-label">Jam Mulai <span
                                        class="text-danger">*</span></label>
                                <input type="time" id="jamMulai" name="JamMulai"
                                    class="form-control @error('JamMulai') is-invalid @enderror"
                                    value="{{ old('JamMulai') !== null ? old('JamMulai') : $MasterShift->JamMulai }}"
                                    required placeholder="Masukkan jam mulai shift" step="60" min="00:00"
                                    max="23:59" data-provider="timepickr" data-time-hrs="true">
                                @error('JamMulai')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                                <small class="text-muted">Format: 24 Jam (contoh: 14:30)</small>
                                <div>
                                    <small class="text-primary">
                                        Waktu Saat Ini:
                                        {{ old('JamMulai') !== null ? old('JamMulai') : $MasterShift->JamMulai }}
                                    </small>
                                </div>
                            </div>
                            <div class="mb-1">
                                <label for="jamSelesai" class="form-label">Jam Selesai <span
                                        class="text-danger">*</span></label>
                                <input type="time" id="jamSelesai" name="JamSelesai"
                                    class="form-control @error('JamSelesai') is-invalid @enderror"
                                    value="{{ old('JamSelesai') !== null ? old('JamSelesai') : $MasterShift->JamSelesai }}"
                                    required placeholder="Masukkan jam selesai shift" step="60" min="00:00"
                                    max="23:59" data-provider="timepickr" data-time-hrs="true">
                                @error('JamSelesai')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                                <small class="text-muted">Format: 24 Jam (contoh: 22:00)</small>
                                <div>
                                    <small class="text-primary">
                                        Waktu Saat Ini:
                                        {{ old('JamSelesai') !== null ? old('JamSelesai') : $MasterShift->JamSelesai }}
                                    </small>
                                </div>
                            </div>

                            <div class="text-end mt-3">
                                <a href="{{ route('MasterShift.index') }}" class="btn btn-light">Batal</a>
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
