@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <!-- Page Title Header -->
        <div class="page-title-head d-flex align-items-center flex-wrap gap-2 mb-4">
            <div class="flex-grow-1">
                <h4 class="page-main-title m-0 fw-semibold">
                    <i class="ti ti-bullseye me-2 text-primary"></i>Target Capaian Klinik
                </h4>
            </div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb m-0 py-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('home') }}" class="text-decoration-none text-reset">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="javascript:void(0)" class="text-decoration-none text-reset">Master</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Target Capaian</li>
                </ol>
            </nav>
        </div>

        <!-- Tombol Kembali -->
        <div class="mb-3">
            <a href="{{ route('Klinik.index') }}" class="btn btn-solid-primary px-4">
                <i class="ti ti-arrow-left me-1"></i> Kembali
            </a>
        </div>


        <!-- Content Card -->
        <div class="row">
            <div class="col-12">
                <div class="datatables">
                    <div class="card shadow-sm border-0">
                        <!-- Card Header -->
                        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                            <h4 class="card-title mb-0 fw-semibold">
                                <i class="ti ti-list me-2"></i>Data Target Capaian
                            </h4>
                            <a href="{{ route('Klinik.tambah-target', encrypt($data->id)) }}"
                                class="btn btn-primary btn-sm d-flex align-items-center gap-1" id="btnTambahTarget">
                                <i class="ti ti-plus"></i> Tambah Target
                            </a>
                        </div>

                        <!-- Card Body -->
                        <div class="card-body">
                            <!-- DataTable -->
                            <div class="table-responsive">
                                <table class="table table-striped dt-responsive align-middle mb-0" id="targetCapaianTable"
                                    width="100%">
                                    <thead class="thead-sm text-uppercase fs-xxs">
                                        <tr>
                                            <th style="width:40px;" class="text-center">#</th>
                                            <th>Tahun</th>
                                            <th>Besar Target</th>
                                            <th style="width:190px;" class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if ($data && $data->getTarget && $data->getTarget->count())
                                            @foreach ($data->getTarget as $index => $target)
                                                <tr>
                                                    <td class="text-center">{{ $index + 1 }}</td>
                                                    <td>{{ $target->Tahun }}</td>
                                                    <td>Rp.{{ number_format($target->BesarTarget, 0, ',', '.') }}</td>
                                                    <td class="text-center">
                                                        <!-- Placeholder for actions (edit/delete) -->
                                                        <button type="button" class="btn btn-sm btn-warning btn-edit"
                                                            data-id="{{ $target->id }}">
                                                            <i class="ti ti-edit"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-danger btn-delete"
                                                            data-id="{{ $target->id }}">
                                                            <i class="ti ti-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="4" class="text-center text-muted">Tidak ada data Target
                                                    Capaian.</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>

                                <!-- Tambahkan pesan jika tidak ada data -->
                                <div class="no-data-message d-none text-center text-muted mt-3">
                                    Tidak ada data Target Capaian.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
