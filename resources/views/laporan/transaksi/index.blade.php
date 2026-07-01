@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <!-- Page Title Header -->
        <div class="page-title-head d-flex align-items-center flex-wrap gap-2 mb-4">
            <div class="flex-grow-1">
                <h4 class="page-main-title m-0 fw-semibold">
                    <i class="ti ti-file-analytics me-2 text-primary"></i>Laporan Transaksi
                </h4>
            </div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb m-0 py-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('home') }}" class="text-decoration-none text-reset">Home</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="javascript:void(0)" class="text-decoration-none text-reset">Laporan</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Laporan Transaksi</li>
                </ol>
            </nav>
        </div>

        <!-- Filter Card -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <!-- Card Header -->
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0 fw-semibold">
                            <i class="ti ti-filter me-2"></i>Filter Laporan
                        </h4>
                    </div>
                    <div class="card-body">
                        <form id="filterForm">
                            @csrf
                            <div class="row g-3">
                                @if(auth()->user()->hasRole('Superadmin') || auth()->user()->hasRole('Management'))

                                <div class="col-md-3">
                                    <label for="klinik_id" class="form-label fw-semibold">
                                        <i class="ti ti-building me-1"></i>Klinik
                                    </label>
                                    <select class="form-select" id="klinik_id" name="klinik_id" style="width: 100%;">
                                        <option value="">Semua Klinik</option>
                                        @if(isset($klinik) && count($klinik))
                                            @foreach($klinik as $item)
                                                <option value="{{ $item->Kode }}">{{ $item->Nama }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                @else
                                    @if(isset($klinik) && count($klinik))
                                        @foreach($klinik as $item)
                                            @if(auth()->user()->kodeperusahaan == $item->Kode)
                                                <input type="hidden" id="klinik_id" name="klinik_id" value="{{ $item->Kode }}">
                                            @endif
                                        @endforeach
                                    @endif
                                @endif

                                {{-- Tanggal Mulai --}}
                                <div class="col-md-3">
                                    <label for="tanggal_mulai" class="form-label fw-semibold">
                                        <i class="ti ti-calendar me-1"></i>Tanggal Mulai
                                    </label>
                                    <input type="date" class="form-control" id="tanggal_mulai" name="tanggal_mulai"
                                           value="{{ date('Y-m-01') }}">
                                </div>

                                {{-- Tanggal Akhir --}}
                                <div class="col-md-3">
                                    <label for="tanggal_akhir" class="form-label fw-semibold">
                                        <i class="ti ti-calendar me-1"></i>Tanggal Akhir
                                    </label>
                                    <input type="date" class="form-control" id="tanggal_akhir" name="tanggal_akhir"
                                           value="{{ date('Y-m-d') }}">
                                </div>

                                {{-- Buttons --}}
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold d-block">&nbsp;</label>
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-primary flex-fill" id="btnPreview">
                                            <i class="ti ti-eye me-1"></i>Preview
                                        </button>
                                        <button type="button" class="btn btn-success flex-fill" id="btnDownload">
                                            <i class="ti ti-download me-1"></i>Download
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Preview Card -->
        <div class="row mt-3">
            <div class="col-12">
                <div class="card shadow-sm border-0" id="previewSection" style="display: none;">
                    <!-- Card Header -->
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0 fw-semibold">
                            <i class="ti ti-table me-2"></i>Preview Data
                        </h4>
                        <span class="badge bg-primary" id="totalData">0 Transaksi</span>
                    </div>
                    <div class="datatables">
                        <!-- Card Body -->
                        <div class="card-body">
                            <!-- DataTable -->
                            <div class="table-responsive">
                                <table class="table table-striped align-middle mb-0" id="previewTable">
                                    <thead class="thead-sm text-uppercase fs-xxs">
                                        <tr>
                                            <th style="width:40px;" class="text-center">No</th>
                                            <th style="width:80px;">Kode</th>
                                            <th style="width:110px;">Tanggal</th>
                                            <th style="width:140px;">Nama Pasien</th>
                                            <th style="width:90px;">Jenis Pasien</th>
                                            <th style="width:140px;">Metode Pembayaran</th>
                                            <th style="width:170px;">Layanan</th>
                                            <th style="width:90px;" class="text-end">Total Bayar</th>
                                            <th style="width:110px;">Petugas</th>
                                            <th style="width:70px;">Shift</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tableBody">
                                        <tr>
                                            <td colspan="10" class="text-center text-muted py-4">
                                                Klik tombol Preview untuk menampilkan data
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            // Toast notification config
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
            });

            // Preview Button
            $('#btnPreview').click(function() {
                previewData();
            });

            // Download Button
            $('#btnDownload').click(function() {
                downloadData();
            });

            function getFormData() {
                return {
                    _token: '{{ csrf_token() }}',
                    klinik_id: $('#klinik_id').val(),
                    tanggal_mulai: $('#tanggal_mulai').val(),
                    tanggal_akhir: $('#tanggal_akhir').val()
                };
            }

            function previewData() {
                const formData = getFormData();

                $.ajax({
                    url: '{{ route("laporan-transaksi.preview") }}',
                    method: 'POST',
                    data: formData,
                    beforeSend: function() {
                        $('#tableBody').html('<tr><td colspan="10" class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>');
                        $('#previewSection').show();
                    },
                    success: function(response) {
                        if (response.success) {
                            let html = '';
                            let total = 0;

                            if (response.data.length === 0) {
                                html = '<tr><td colspan="10" class="text-center text-muted py-4">Tidak ada data untuk periode ini</td></tr>';
                            } else {
                                response.data.forEach((item, index) => {
                                    total += parseFloat(item.total_bayar ?? item.total ?? 0);
                                    // Fallback/placeholder for fields unavailable in original response:
                                    let kode = item.kode ?? '-';
                                    let tanggal = item.tanggal ?? '-';
                                    let namaPasien = item.pelanggan ?? item.nama_pasien ?? '-';
                                    let jenisPasien = item.jenis_pasien ?? '-';
                                    let metodePembayaran = item.metode_pembayaran ?? '-';
                                    let layanan = item.layanan ?? '-';
                                    let petugas = item.petugas ?? '-';
                                    let shift = item.shift ?? '-';
                                    // Kolom aksi dihapus

                                    html += `
                                        <tr>
                                            <td class="text-center">${index + 1}</td>
                                            <td>${kode}</td>
                                            <td>${formatDate(tanggal)}</td>
                                            <td>${namaPasien}</td>
                                            <td>${jenisPasien}</td>
                                            <td>${metodePembayaran}</td>
                                            <td>${layanan}</td>
                                            <td class="text-end fw-semibold">Rp ${formatRupiah(item.total_bayar ?? item.total ?? 0)}</td>
                                            <td>${petugas}</td>
                                            <td>${shift}</td>
                                        </tr>
                                    `;
                                });

                                // Add total row
                                html += `
                                    <tr class="table-primary fw-bold">
                                        <td colspan="7" class="text-end">Total:</td>
                                        <td class="text-end">Rp ${formatRupiah(total)}</td>
                                        <td colspan="2"></td>
                                    </tr>
                                `;
                            }

                            $('#tableBody').html(html);
                            $('#totalData').text(response.data.length + ' Transaksi');
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message || 'Terjadi kesalahan'
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON?.message || 'Terjadi kesalahan saat memuat data'
                        });
                    }
                });
            }

            function downloadData() {
                const formData = getFormData();

                // Langsung download dalam format excel tanpa prompt
                downloadFile(formData, 'excel');
            }


            function downloadFile(formData, format) {
                let loadingSwal;

                $.ajax({
                    url: '{{ route("laporan-transaksi.store") }}',
                    method: 'POST',
                    data: { ...formData, format: format },
                    xhrFields: {
                        responseType: 'blob'
                    },
                    beforeSend: function() {
                        loadingSwal = Swal.fire({
                            title: 'Mengunduh...',
                            text: 'Mohon tunggu sebentar',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                    },
                    success: function(blob, status, xhr) {
                        loadingSwal.close();

                        const disposition = xhr.getResponseHeader('Content-Disposition');
                        let filename = 'laporan-transaksi.' + format;
                        if (disposition && disposition.indexOf('attachment') !== -1) {
                            const filenameRegex = /filename\*?=['"]?(?:UTF-\d['"]*)?([^;\r\n"']*)['"]?;?/i;
                            const matches = filenameRegex.exec(disposition);
                            if (matches && matches[1]) {
                                filename = decodeURIComponent(matches[1]);
                            }
                        }

                        const url = window.URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.href = url;
                        a.download = filename;
                        document.body.appendChild(a);
                        a.click();
                        window.URL.revokeObjectURL(url);
                        document.body.removeChild(a);

                        Toast.fire({
                            icon: 'success',
                            title: 'File berhasil diunduh'
                        });
                    },
                    error: function(xhr) {
                        loadingSwal.close();
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON?.message || 'Gagal mengunduh file'
                        });
                    }
                });
            }

            function formatDate(dateString) {
                if(!dateString) return '-';
                const date = new Date(dateString);
                if (isNaN(date.getTime()) || dateString === '-') return '-';
                const options = { day: '2-digit', month: 'short', year: 'numeric' };
                return date.toLocaleDateString('id-ID', options);
            }

            function formatRupiah(angka) {
                return new Intl.NumberFormat('id-ID').format(angka);
            }
        });
    </script>
@endpush
