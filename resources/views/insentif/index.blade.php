@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="page-title-head d-flex align-items-center">
            <div class="flex-grow-1">
                <h4 class="page-main-title m-0">Rule Insentif</h4>
            </div>
            <div class="text-end">
                <ol class="breadcrumb m-0 py-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Master</a></li>
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Insentif</a></li>
                    <li class="breadcrumb-item active">Data Rule Insentif</li>
                </ol>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-header justify-content-between">
                        <h4 class="card-title">Data Rule Insentif</h4>
                        <a href="{{ route('Insentif.create') }}" class="btn btn-primary btn-sm">Tambah Rule Insentif</a>
                    </div>
                    <div class="card-body">
                        <table data-tables="basic" class="table table-striped dt-responsive align-middle mb-0"
                            id="insentifTable">
                            <thead class="thead-sm text-uppercase fs-xxs">
                                <tr>
                                    <th width="5%" style="text-align:center;">#</th>
                                    <th width="85%">Nama</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Data akan dimuat melalui DataTables --}}
                            </tbody>
                        </table>
                    </div>
                    <!-- end card-body-->
                </div>
                <!-- end card-->
            </div>
        </div>
    @endsection
