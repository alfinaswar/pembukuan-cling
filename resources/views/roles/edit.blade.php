@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <!-- Page Title -->
        <div class="page-title-head d-flex align-items-center">
            <div class="flex-grow-1">
                <h4 class="page-main-title m-0">Edit Role</h4>
            </div>
            <div class="text-end">
                <ol class="breadcrumb m-0 py-0">
                    <li class="breadcrumb-item"><a href="#">Master</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('roles.index') }}">Role</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </div>
        </div>

        <!-- Error -->
        @if ($errors->any())
            <div class="alert alert-danger mt-2">
                <strong>Error!</strong> Ada data yang belum valid.
                <ul class="mb-0 mt-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Card -->
        <div class="row mt-3">
            <div class="col-12">
                <div class="card">

                    <!-- Header -->
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title">Form Edit Role</h4>

                        <a href="{{ route('roles.index') }}" class="btn btn-secondary btn-sm">
                            ← Kembali
                        </a>
                    </div>

                    <!-- Body -->
                    <div class="card-body">

                        {!! Form::model($role, [
                            'method' => 'PATCH',
                            'route' => ['roles.update', $role->id],
                        ]) !!}

                        <div class="row">

                            <!-- Nama Role -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Role <span class="text-danger">*</span></label>
                                {!! Form::text('name', null, [
                                    'class' => 'form-control',
                                    'placeholder' => 'Contoh: Dokter / Admin / Kasir',
                                ]) !!}
                            </div>

                            <!-- Permission -->
                            <div class="col-12">
                                <label class="form-label">Permission</label>

                                <div class="border rounded p-3" style="max-height: 300px; overflow-y: auto;">
                                    <div class="row">
                                        @foreach ($permission as $value)
                                            <div class="col-md-3 mb-2">
                                                <label class="d-flex align-items-center">
                                                    <input type="checkbox" name="permission[]" value="{{ $value->id }}"
                                                        class="form-check-input me-2"
                                                        {{ in_array($value->id, $rolePermissions) ? 'checked' : '' }}>

                                                    <span>{{ $value->name }}</span>
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <!-- Submit -->
                            <div class="col-12 mt-3 text-end">
                                <button type="submit" class="btn btn-primary">
                                    💾 Update
                                </button>
                            </div>

                        </div>

                        {!! Form::close() !!}

                    </div>
                    <!-- end card-body -->

                </div>
            </div>
        </div>

    </div>
@endsection
