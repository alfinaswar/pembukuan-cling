@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="page-title-head d-flex align-items-center">
            <div class="flex-grow-1">
                <h4 class="page-main-title m-0">Edit Jenis Perawatan</h4>
            </div>
            <div class="text-end">
                <ol class="breadcrumb m-0 py-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Master</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('JenisPerawatan.index') }}">Jenis Perawatan</a></li>
                    <li class="breadcrumb-item active">Edit Jenis Perawatan</li>
                </ol>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-xl-12 mx-auto">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Form Edit Jenis Perawatan</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('JenisPerawatan.update', encrypt($JenisPerawatan->id)) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="mb-1">
                                <label for="nama" class="form-label">Nama <span class="text-danger">*</span></label>
                                <input type="text" id="nama" name="Nama"
                                    class="form-control @error('Nama') is-invalid @enderror"
                                    value="{{ old('Nama', $JenisPerawatan->Nama) }}" required
                                    placeholder="Masukkan nama jenis perawatan">
                                @error('Nama')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mb-1">
                                <label for="tarif" class="form-label">Tarif <span class="text-danger">*</span></label>
                                <input type="text" id="tarif" name="Tarif"
                                    class="form-control @error('Tarif') is-invalid @enderror"
                                    value="{{ old('Tarif', number_format($JenisPerawatan->Tarif, 0, ',', '.')) }}" required
                                    placeholder="Masukkan tarif">
                                @error('Tarif')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="text-end mt-3">
                                <a href="{{ route('JenisPerawatan.index') }}" class="btn btn-light">Batal</a>
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

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var input = document.getElementById('tarif');

            input.addEventListener('input', function(e) {
                var value = this.value.replace(/[^0-9]/g, '');
                if (value) {
                    this.value = formatRupiah(value, 'Rp ');
                } else {
                    this.value = '';
                }
            });

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
                return prefix == undefined ? rupiah : (rupiah ? prefix + rupiah : '');
            }
        });
    </script>
@endpush
