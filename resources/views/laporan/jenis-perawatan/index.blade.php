@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <!-- Page Title Header -->
        <div class="page-title-head d-flex align-items-center flex-wrap gap-2 mb-4">
            <div class="flex-grow-1">
                <h4 class="page-main-title m-0 fw-semibold">
                    <i class="ti ti-report-analytics me-2 text-primary"></i>Laporan Jenis Perawatan
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
                    <li class="breadcrumb-item active" aria-current="page">Laporan Jenis Perawatan</li>
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
                                {{-- Klinik - Only for Superadmin/Management --}}
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

                            {{-- Row 2: Filter Jenis Perawatan (Full Width) --}}
                            <div class="row g-3 mt-2">
                                <div class="col-12">
                                    <label for="jenis_perawatan" class="form-label fw-semibold">
                                        <i class="ti ti-list-check me-1"></i>Jenis Perawatan
                                        <small class="text-muted">(Kosongkan untuk semua jenis)</small>
                                    </label>
                                    <select class="select2 form-control" id="jenis_perawatan" name="jenis_perawatan[]" multiple>
                                        @if(isset($jenisPerawatan) && count($jenisPerawatan))
                                            @foreach($jenisPerawatan as $jp)
                                                <option value="{{ $jp->id }}">{{ $jp->Nama }}</option>
                                            @endforeach
                                        @endif
                                    </select>
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
                        <span class="badge bg-primary" id="totalData">0 Jenis Perawatan</span>
                    </div>
                    <div class="datatables">
                        <!-- Card Body -->
                        <div class="card-body">
                            <!-- DataTable -->
                            <div class="table-responsive">
                                <table class="table table-striped align-middle mb-0" id="previewTable">
                                    <thead class="thead-sm text-uppercase fs-xxs">
                                        <tr>
                                            <th style="width: 60px;" class="text-center">#</th>
                                            <th style="width: 50%;">Nama Perawatan</th>
                                            <th style="width: 20%;" class="text-center">Jumlah Terjual</th>
                                            <th style="width: 30%;" class="text-end">Total Revenue</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tableBody">
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-4">
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

@push('styles')
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    <style>
        .select2-container--bootstrap-5 .select2-selection {
            min-height: 38px;
            padding: 4px 8px;
        }
        .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice {
            background-color: #0d6efd;
            border: none;
            color: #fff;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 13px;
        }
        .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice__remove {
            color: #fff;
            margin-right: 4px;
        }
        .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice__remove:hover {
            color: #ffc107;
        }
    </style>
@endpush

@push('scripts')
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            // Tidak perlu konfigurasi Select2 khusus

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
                const jenisPerawatan = $('#jenis_perawatan').val() ? $('#jenis_perawatan').val() : [];
                return {
                    _token: '{{ csrf_token() }}',
                    klinik_id: $('#klinik_id').val(),
                    tanggal_mulai: $('#tanggal_mulai').val(),
                    tanggal_akhir: $('#tanggal_akhir').val(),
                    'jenis_perawatan[]': jenisPerawatan
                };
            }


            function previewData() {
                const formData = getFormData();

                $.ajax({
                    url: '{{ route("laporan-jenis-perawatan.preview") }}',
                    method: 'POST',
                    data: formData,
                    traditional: true, // ✅ Penting untuk mengirim array via jQuery AJAX
                    beforeSend: function() {
                        $('#tableBody').html('<tr><td colspan="4" class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>');
                        $('#previewSection').show();
                    },
                    success: function(response) {
                        if (response.success) {
                            let html = '';
                            let totalTerjual = 0;
                            let totalRevenue = 0;

                            if (response.data.length === 0) {
                                html = '<tr><td colspan="4" class="text-center text-muted py-4">Tidak ada data untuk filter ini</td></tr>';
                            } else {
                                response.data.forEach((item, index) => {
                                    totalTerjual += parseInt(item.jumlah_terjual);
                                    totalRevenue += parseFloat(item.total_revenue);
                                    html += `
                                        <tr>
                                            <td class="text-center">${index + 1}</td>
                                            <td><span class="fw-semibold text-primary">${item.nama_perawatan}</span></td>
                                            <td class="text-center">
                                                <span class="badge bg-info">${item.jumlah_terjual}x</span>
                                            </td>
                                            <td class="text-end fw-semibold">Rp ${formatRupiah(item.total_revenue)}</td>
                                        </tr>
                                    `;
                                });

                                // Add total row
                                html += `
                                    <tr class="table-primary fw-bold">
                                        <td colspan="2" class="text-end">Total:</td>
                                        <td class="text-center">
                                            <span class="badge bg-primary">${totalTerjual}x</span>
                                        </td>
                                        <td class="text-end">Rp ${formatRupiah(totalRevenue)}</td>
                                    </tr>
                                `;
                            }

                            $('#tableBody').html(html);
                            $('#totalData').text(response.data.length + ' Jenis Perawatan');
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

                let loadingSwal;

                $.ajax({
                    url: '{{ route("laporan-jenis-perawatan.download") }}',
                    method: 'POST',
                    data: formData,
                    traditional: true, // ✅ Penting untuk mengirim array
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
                        let filename = 'laporan-jenis-perawatan.xlsx';
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

            function formatRupiah(angka) {
                return new Intl.NumberFormat('id-ID').format(angka);
            }
        });
    </script>
@endpush
