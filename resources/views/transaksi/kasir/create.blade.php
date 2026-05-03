@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="page-title-head d-flex align-items-center">
            <div class="flex-grow-1">
                <h4 class="page-main-title m-0">Tambah Shift Kerja</h4>
            </div>
            <div class="text-end">
                <ol class="breadcrumb m-0 py-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Master</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('MasterShift.index') }}">Shift Kerja</a></li>
                    <li class="breadcrumb-item active">Tambah Shift Kerja</li>
                </ol>
            </div>
        </div>

        <!-- MULAI ROW 1 -->
        <div class="row mt-3">
            <div class="col-xl-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Transaksi Kasir</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('Transaksi.store') }}" method="POST" id="formTransaksiKasir">
                            @csrf

                            <!-- Hari & Tanggal -->
                            <div class="mb-3">
                                <label for="Tanggal" class="form-label fw-semibold">Hari & Tanggal</label>
                                <input type="text" id="Tanggal" name="Tanggal" data-provider="flatpickr"
                                    data-date-format="d M, Y" class="form-control @error('Tanggal') is-invalid @enderror"
                                    value="{{ old('Tanggal', date('d M, Y')) }}" required>
                                @error('Tanggal')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Nama Pasien -->
                            <div class="mb-2">
                                <label for="nama_pasien" class="form-label fw-semibold">Nama Pasien</label>
                                <input type="text" id="nama_pasien" name="NamaPasien"
                                    class="form-control @error('nama_pasien') is-invalid @enderror"
                                    placeholder="Cari atau masukkan nama pasien" autocomplete="off" required>
                                @error('nama_pasien')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="JenisPasien" id="pasien_baru"
                                        value="Baru">
                                    <label class="form-check-label" for="pasien_baru">Pasien Baru</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="JenisPasien" id="pasien_lama"
                                        value="Lama">
                                    <label class="form-check-label" for="pasien_lama">Pasien Lama</label>
                                </div>
                            </div>

                            <!-- Jenis Perawatan -->
                            <div class="mb-3">
                                <label class="fw-semibold mb-2">Jenis Perawatan</label>
                                <div class="table-responsive">
                                    <table class="table align-middle table-bordered mb-0" id="table-perawatan">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width: 4%;">No.</th>
                                                <th>Jenis Perawatan</th>
                                                <th style="width: 24%;">Biaya Perawatan</th>
                                                <th style="width: 8%;">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody id="body-perawatan">
                                            <tr>
                                                <td class="text-center align-middle">1</td>
                                                <td>
                                                    <select class="form-control select2 perawatan-select"
                                                        name="JenisPerawatan[0][id]" required data-toggle="select2">
                                                        <option value="">Pilih Jenis Perawatan</option>
                                                        @foreach ($Perawatan as $row)
                                                            <option value="{{ $row->id }}"
                                                                data-harga="{{ $row->Tarif }}">{{ $row->Nama }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control biaya-perawatan bg-light"
                                                        name="JenisPerawatan[0][Biaya]" placeholder="Rp 0">
                                                </td>
                                                <td class="text-center">
                                                    <button type="button"
                                                        class="btn btn-link text-danger px-2 btn-remove-perawatan"
                                                        style="font-size:1.25rem;" title="Hapus">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <button type="button" class="btn btn-outline-primary mt-3" id="btn-tambah-perawatan"><i
                                        class="bi bi-plus-circle"></i> Tambah Perawatan</button>
                            </div>
                            <div class="mb-3">
                                <label for="BiayaAdmin" class="form-label fw-semibold">Biaya Admin</label>
                                <input type="text" class="form-control @error('BiayaAdmin') is-invalid @enderror"
                                    id="biaya_admin" name="BiayaAdmin" placeholder="Masukkan biaya admin"
                                    value="{{ old('BiayaAdmin') }}" required readonly>
                                @error('BiayaAdmin')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-lg-12">
                                <div class="card border-1" style="background-color: #f2fcfd;">
                                    <div class="card-body">
                                        <span class="small text-teal" style="color: #189282;">Total Biaya (Perawatan +
                                            Biaya Admin<span id="info-pasien-baru"></span>)</span>
                                        <h3 class="fw-semibold mt-1 mb-0" style="color: #189282;">
                                            Rp <span id="total-biaya">0</span>
                                        </h3>
                                    </div>
                                </div>
                            </div>

                            <div class="text-end mt-3">
                                <a href="{{ route('MasterShift.index') }}" class="btn btn-light">Batal</a>
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- KANAN ROW 1: Ringkasan Shift & Metode Pembayaran -->
            <div class="col-xl-4">
                <div class="row">
                    <div class="col-12">
                        <div class="card mb-3">
                            <div class="card-header">
                                <h5 class="card-title text-uppercase">Ringkasan Per Shift</h5>
                            </div>
                            <div class="card-body">

                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="card">

                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-uppercase">Metode Pembayaran</label>
                                    <div>
                                        @foreach ($MetodePembayaran as $mp)
                                            <div class="form-check mb-2">
                                                <input
                                                    class="form-check-input @error('MetodePembayaran') is-invalid @enderror"
                                                    type="radio" name="MetodePembayaran"
                                                    id="metode_pembayaran_{{ $mp->id }}"
                                                    value="{{ $mp->id }}"
                                                    {{ old('MetodePembayaran') == $mp->id ? 'checked' : '' }} required>
                                                <label class="form-check-label"
                                                    for="metode_pembayaran_{{ $mp->id }}">
                                                    {{ $mp->Nama }}
                                                </label>
                                            </div>
                                        @endforeach

                                        @error('MetodePembayaran')
                                            <span class="invalid-feedback d-block">{{ $message }}</span>
                                        @enderror
                                        <hr>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center p-2"
                                        style="background: #f2fcfd; border-radius: 6px;">
                                        <span class="form-label fw-bold mb-0" style="font-size: 14px;">Total Bayar</span>
                                        <span class="fw-semibold mb-0" style="color: #189282; font-size: 18px;">
                                            Rp <span id="total-bayar">0</span>
                                        </span>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="col-12">
                        <div class="card mb-3">

                            <div class="card-body">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- MULAI ROW 2, JIKA NANTI DIBUTUHKAN TAMBAHAN KOLOM/PANEL -->
        {{--
        <div class="row mt-3">
            <div class="col-lg-12">
                <!-- Tambah kolom tambahan di row 2 di sini -->
            </div>
        </div>
        --}}
    </div>
    @push('scripts')
        <script>
            let perawatanCount = 1;

            // Helper rupiah format (tanpa Rp jika untuk value)
            function plainRupiahFormat(num) {
                if (!num) return '0';
                return Number(num).toLocaleString('id-ID');
            }

            function rupiahFormat(num) {
                if (!num) return 'Rp 0';
                return 'Rp ' + Number(num).toLocaleString('id-ID');
            }

            function isPasienBaru() {
                return $('input[name="JenisPasien"]:checked').val() === "Baru";
            }

            function recalculateTotal() {
                let total = 0;
                $('.biaya-perawatan').each(function() {
                    let rawVal = $(this).val();
                    let val = (rawVal + '').replace(/[^0-9]/g, '');
                    total += +val;
                });
                let admin = +($('#biaya_admin').val() + '').replace(/[^0-9]/g, '');
                if (isNaN(admin)) admin = 0;
                total += admin;

                if (isPasienBaru()) {
                    $('#info-pasien-baru').html(" (Rp 50.000 untuk pasien baru)");
                } else {
                    $('#info-pasien-baru').html("");
                }
                $('#total-biaya').text(total.toLocaleString('id-ID'));
            }

            // Format input menjadi rupiah saat ketik (saat diinput) - untuk biaya admin
            $('#biaya_admin').on('input', function(e) {
                let value = $(this).val().replace(/[^0-9]/g, '');
                if (!value) value = '0';
                $(this).val(rupiahFormat(value));
                recalculateTotal();
            });

            // Saat submit form, unformat biaya_admin agar backend dapat angka mentah
            $('#formTransaksiKasir').on('submit', function() {
                let adminVal = $('#biaya_admin').val();
                $('#biaya_admin').val((adminVal + '').replace(/[^0-9]/g, ''));
                // Unformat semua biaya-perawatan ke angka
                $('.biaya-perawatan').each(function() {
                    let val = $(this).val();
                    $(this).val((val + '').replace(/[^0-9]/g, ''));
                });
            });

            // Select2
            $(document).ready(function() {
                $('.select2').select2({});

                // Atur biaya admin sesuai tipe pasien
                $('input[name="JenisPasien"]').on('change', function() {
                    if (isPasienBaru()) {
                        $('#biaya_admin').prop('readonly', true).val(rupiahFormat(50000));
                    } else {
                        $('#biaya_admin').val(rupiahFormat(0)).prop('readonly', true);
                    }
                    recalculateTotal();
                });

                // Initial state on page load
                let jenisPasienChecked = $('input[name="JenisPasien"]:checked').val();
                if (jenisPasienChecked === "Lama") {
                    $('#biaya_admin').val(rupiahFormat(0)).prop('readonly', true);
                } else if (jenisPasienChecked === "Baru") {
                    $('#biaya_admin').val(rupiahFormat(50000)).prop('readonly', true);
                } else {
                    $('#biaya_admin').val(rupiahFormat(0)).prop('readonly', true);
                }
                recalculateTotal();
            });

            // Dynamic Add Perawatan
            $('#btn-tambah-perawatan').on('click', function() {
                let idx = perawatanCount;
                let options = @json($Perawatan);
                let selectOpt = `<option value="">Pilih Jenis Perawatan</option>`;
                options.forEach(function(row) {
                    selectOpt += `<option value="${row.id}" data-harga="${row.Tarif}">${row.Nama}</option>`;
                });

                let html = `
        <tr>
            <td class="text-center align-middle"></td>
            <td>
                <select class="form-control select2 perawatan-select" name="JenisPerawatan[${idx}][id]" required data-toggle="select2">
                    ${selectOpt}
                </select>
            </td>
            <td>
                <input type="text" class="form-control biaya-perawatan bg-light" name="JenisPerawatan[${idx}][Biaya]" readonly placeholder="Rp 0">
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-link text-danger px-2 btn-remove-perawatan" style="font-size:1.25rem;" title="Hapus">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        </tr>
        `;
                $('#body-perawatan').append(html);
                perawatanCount++;
                $('.select2').last().select2({});

                // Number NO. urut setiap baris
                $('#body-perawatan tr').each(function(i) {
                    $(this).find('td:first').text(i + 1);
                });
            });

            // Remove Perawatan
            $('#body-perawatan').on('click', '.btn-remove-perawatan', function() {
                $(this).closest('tr').remove();

                // Re-number baris setelah dihapus agar tidak meloncat no urut
                $('#body-perawatan tr').each(function(i) {
                    $(this).find('td:first').text(i + 1);
                });
                recalculateTotal();
            });

            // Set Harga on select changed
            $('#body-perawatan').on('change', '.perawatan-select', function() {
                let harga = $(this).find('option:selected').data('harga') ?? 0;
                let input = $(this).closest('tr').find('.biaya-perawatan');
                input.val(rupiahFormat(harga));
                recalculateTotal();
            });

            // Format biaya perawatan in input for first time if selection done
            $(document).on('change', '.perawatan-select', function() {
                let harga = $(this).find('option:selected').data('harga') ?? 0;
                let input = $(this).closest('tr').find('.biaya-perawatan');
                input.val(rupiahFormat(harga));
                recalculateTotal();
            });

            // Format biaya perawatan ketika diketik (jika diaktifkan menjadi editable)
            $('#body-perawatan').on('input', '.biaya-perawatan', function(e) {
                let val = $(this).val().replace(/[^0-9]/g, '');
                if (!val) val = '0';
                $(this).val(rupiahFormat(val));
                recalculateTotal();
            });

            // On page load: nomor urut always berurutan
            $(function() {
                $('.perawatan-select').each(function() {
                    let selected = $(this).find('option:selected');
                    let harga = selected.data('harga') ?? 0;
                    let input = $(this).closest('tr').find('.biaya-perawatan');
                    input.val(rupiahFormat(harga));
                });
                // Set nomor urut always berurutan on load
                $('#body-perawatan tr').each(function(i) {
                    $(this).find('td:first').text(i + 1);
                });

                // Format biaya admin field on load jika isiannya sudah ada
                let adminCurrent = $('#biaya_admin').val();
                if (adminCurrent && !adminCurrent.match(/Rp/)) {
                    $('#biaya_admin').val(rupiahFormat(adminCurrent.replace(/[^0-9]/g, '')))
                }

                recalculateTotal();

                // Inisialisasi field biaya admin di awal sesuai kondisi radio
                let jenisPasienChecked = $('input[name="JenisPasien"]:checked').val();
                if (jenisPasienChecked === "Lama") {
                    $('#biaya_admin').val(rupiahFormat(0)).prop('readonly', true);
                } else if (jenisPasienChecked === "Baru") {
                    $('#biaya_admin').val(rupiahFormat(50000)).prop('readonly', true);
                } else {
                    $('#biaya_admin').val(rupiahFormat(0)).prop('readonly', true);
                }
            });
        </script>
    @endpush
@endsection
