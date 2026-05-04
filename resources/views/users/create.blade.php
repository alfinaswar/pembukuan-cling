@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="page-title-head d-flex align-items-center">
            <div class="flex-grow-1">
                <h4 class="page-main-title m-0">Tambah User</h4>
            </div>
            <div class="text-end">
                <ol class="breadcrumb m-0 py-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Master</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Users</a></li>
                    <li class="breadcrumb-item active">Tambah User</li>
                </ol>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-xl-12 mx-auto">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Form Tambah User</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('users.store') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-1">
                                        <label for="name" class="form-label">Nama <span
                                                class="text-danger">*</span></label>
                                        <input type="text" id="name" name="name"
                                            class="form-control @error('name') is-invalid @enderror"
                                            value="{{ old('name') }}" required placeholder="Masukkan nama user">
                                        @error('name')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="mb-1">
                                        <label for="email" class="form-label">Email <span
                                                class="text-danger">*</span></label>
                                        <input type="email" id="email" name="email"
                                            class="form-control @error('email') is-invalid @enderror"
                                            value="{{ old('email') }}" required placeholder="Masukkan email user">
                                        @error('email')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    {{-- CABANG --}}
                                    <div class="mb-1">
                                        <label for="cabang_id" class="form-label">Cabang <span
                                                class="text-danger">*</span></label>
                                        <select id="cabang_id" name="cabang_id"
                                            class="form-control select2 @error('cabang_id') is-invalid @enderror"
                                            data-toggle="select2" required>
                                            <option value="">Pilih Cabang</option>
                                            @foreach ($perusahaan as $cabang)
                                                <option value="{{ $cabang->id }}"
                                                    {{ old('cabang_id') == $cabang->id ? 'selected' : '' }}>
                                                    {{ $cabang->Nama }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('cabang_id')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-1">
                                        <label for="password" class="form-label">Kata Sandi <span
                                                class="text-danger">*</span></label>
                                        <input type="password" id="password" name="password"
                                            class="form-control @error('password') is-invalid @enderror" required
                                            placeholder="Masukkan kata sandi">
                                        @error('password')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="mb-1">
                                        <label for="password_confirmation" class="form-label">Konfirmasi Kata Sandi <span
                                                class="text-danger">*</span></label>
                                        <input type="password" id="password_confirmation" name="password_confirmation"
                                            class="form-control @error('password_confirmation') is-invalid @enderror"
                                            required placeholder="Ulangi kata sandi">
                                        @error('password_confirmation')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="mb-1">
                                        <label for="roles" class="form-label">Role <span
                                                class="text-danger">*</span></label>
                                        <select id="roles" name="roles[]"
                                            class="form-control select2 @error('roles') is-invalid @enderror"
                                            data-toggle="select2" required>
                                            @foreach ($roles as $role)
                                                <option value="{{ $role->name }}"
                                                    {{ collect(old('roles'))->contains($role->name) ? 'selected' : '' }}>
                                                    {{ $role->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('roles')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>

                                </div>
                            </div>
                            <div class="text-end mt-3">
                                <a href="{{ route('users.index') }}" class="btn btn-light">Batal</a>
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
