@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <!-- Page Title Header -->
        <div class="page-title-head d-flex align-items-center flex-wrap gap-2 mb-4">
            <div class="flex-grow-1">
                <h4 class="page-main-title m-0 fw-semibold">
                    <i class="ti ti-users me-2 text-primary"></i>Laporan Pasien & Pendapatan
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
                    <li class="breadcrumb-item active" aria-current="page">Laporan Pasien</li>
                </ol>
            </nav>
        </div>

        <!-- Filter Card -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0 fw-semibold">
                            <i class="ti ti-filter me-2 text-primary"></i>Filter Laporan
                        </h5>
                    </div>
                    <div class="card-body">
                        <form id="filterForm">
                            @csrf
                            <div class="row g-3 align-items-end">
                                @if(auth()->user()->hasRole('Superadmin') || auth()->user()->hasRole('Management'))
                                    <div class="col-md-3">
                                        <label for="klinik_id" class="form-label fw-semibold">
                                            <i class="ti ti-building me-1"></i>Cabang / Klinik
                                        </label>
                                        <select class="form-select" id="klinik_id" name="klinik_id">
                                            <option value="">Semua Cabang / Klinik</option>
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

                                {{-- Filter Tipe Pasien --}}
                                <div class="col-md-3">
                                    <label for="tipe_pasien" class="form-label fw-semibold">
                                        <i class="ti ti-user-check me-1"></i>Tipe Pasien
                                    </label>
                                    <select class="form-select" id="tipe_pasien" name="tipe_pasien">
                                        <option value="">Semua Tipe Pasien (Baru & Lama)</option>
                                        <option value="Baru">Pasien Baru</option>
                                        <option value="Lama">Pasien Lama</option>
                                    </select>
                                </div>

                                {{-- Filter Periode Tanggal (Daterangepicker) --}}
                                <div class="col-md-4">
                                    <label for="FilterTanggal" class="form-label fw-semibold">
                                        <i class="ti ti-calendar me-1"></i>Periode Tanggal
                                    </label>
                                    <div class="input-group">
                                        <input type="text" id="FilterTanggal" name="FilterTanggal"
                                               class="form-control shawCalRanges" readonly
                                               value="{{ date('m/d/Y') . ' - ' . date('m/d/Y') }}" />
                                        <span class="input-group-text cursor-pointer" id="calendarIcon" style="cursor: pointer;">
                                            <i class="ti ti-calendar fs-5"></i>
                                        </span>
                                    </div>
                                </div>

                                {{-- Action Buttons --}}
                                <div class="col-md-2">
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-primary flex-fill" id="btnPreview">
                                            Terapkan
                                        </button>
                                        <button type="button" class="btn btn-success" id="btnDownload" title="Download Excel">
                                            <i class="ti ti-file-spreadsheet me-1"></i>Excel
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary Statistics Section -->
        <div class="row mt-3" id="summarySection" style="display: none;">
            <div class="col-md-3 mb-3">
                <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); border-radius: 10px;">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-white fw-bold text-uppercase mb-1" style="font-size: 0.8rem; letter-spacing: 0.5px;">Total Pendapatan</div>
                                <h4 class="fw-bolder text-white mb-0" id="statTotalPendapatan">Rp 0</h4>
                            </div>
                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 46px; height: 46px; background: rgba(255, 255, 255, 0.25);">
                                <i class="ti ti-wallet fs-2 text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); border-radius: 10px;">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-white fw-bold text-uppercase mb-1" style="font-size: 0.8rem; letter-spacing: 0.5px;">Total Transaksi Pasien</div>
                                <h4 class="fw-bolder text-white mb-0" id="statTotalPasien">0</h4>
                            </div>
                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 46px; height: 46px; background: rgba(255, 255, 255, 0.25);">
                                <i class="ti ti-users fs-2 text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #059669 0%, #047857 100%); border-radius: 10px;">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-white fw-bold text-uppercase mb-1" style="font-size: 0.8rem; letter-spacing: 0.5px;">Pasien Baru</div>
                                <h4 class="fw-bolder text-white mb-1" id="statPasienBaruCount">0 Pasien</h4>
                                <div class="text-white fw-bold" style="font-size: 0.875rem;" id="statPasienBaruPendapatan">Rp 0</div>
                            </div>
                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 46px; height: 46px; background: rgba(255, 255, 255, 0.25);">
                                <i class="ti ti-user-plus fs-2 text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #d97706 0%, #b45309 100%); border-radius: 10px;">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-white fw-bold text-uppercase mb-1" style="font-size: 0.8rem; letter-spacing: 0.5px;">Pasien Lama</div>
                                <h4 class="fw-bolder text-white mb-1" id="statPasienLamaCount">0 Pasien</h4>
                                <div class="text-white fw-bold" style="font-size: 0.875rem;" id="statPasienLamaPendapatan">Rp 0</div>
                            </div>
                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 46px; height: 46px; background: rgba(255, 255, 255, 0.25);">
                                <i class="ti ti-user-check fs-2 text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Preview Card -->
        <div class="row mt-2">
            <div class="col-12">
                <div class="card shadow-sm border-0" id="previewSection" style="display: none;">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0 fw-semibold">
                            <i class="ti ti-table me-2 text-primary"></i>Preview Data Laporan Pasien
                        </h5>
                        <span class="badge bg-primary fs-7" id="totalDataBadge">0 Transaksi</span>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle mb-0" id="tablePasien">
                                <thead class="table-light text-uppercase fs-xxs">
                                    <tr>
                                        <th class="text-center" style="width: 45px;">No</th>
                                        <th style="width: 100px;">Tanggal</th>
                                        <th style="width: 150px;">Nama Pasien</th>
                                        <th style="width: 110px;" class="text-center">Tipe Pasien</th>
                                        <th style="width: 110px;">Dental Unit</th>
                                        <th style="width: 120px;">Dokter</th>
                                        <th style="width: 120px;">Perawat</th>
                                        <th style="width: 120px;">Resepsionis</th>
                                        <th style="width: 120px;">Cabang</th>
                                        <th style="width: 160px;">Jenis Perawatan</th>
                                        <th style="width: 130px;" class="text-end">Total Biaya</th>
                                    </tr>
                                </thead>
                                <tbody id="tableBody">
                                    <tr>
                                        <td colspan="11" class="text-center text-muted py-4">
                                            Klik tombol Terapkan untuk menampilkan data laporan
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Floating Scroll to Top & Bottom Buttons -->
        <div id="scrollNavButtons" class="position-fixed d-flex flex-column gap-2" style="bottom: 30px; right: 30px; z-index: 1060; display: none;">
            <button type="button" class="btn btn-primary rounded-circle shadow d-flex align-items-center justify-content-center" id="btnScrollTop" title="Scroll ke Atas" style="width: 44px; height: 44px;">
                <i class="ti ti-arrow-up fs-4"></i>
            </button>
            <button type="button" class="btn btn-dark text-white rounded-circle shadow d-flex align-items-center justify-content-center" id="btnScrollBottom" title="Scroll ke Bawah (Total)" style="width: 44px; height: 44px;">
                <i class="ti ti-arrow-down fs-4"></i>
            </button>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            // Klik ikon kalender untuk memicu popup daterangepicker
            $('#calendarIcon').click(function() {
                $('#FilterTanggal').focus();
            });

            function getFormData() {
                return {
                    _token: '{{ csrf_token() }}',
                    klinik_id: $('#klinik_id').val(),
                    tipe_pasien: $('#tipe_pasien').val(),
                    FilterTanggal: $('#FilterTanggal').val()
                };
            }

            function formatRupiah(num) {
                return new Intl.NumberFormat('id-ID').format(num);
            }

            // Terapkan (Preview Data)
            $('#btnPreview').click(function() {
                const formData = getFormData();

                $.ajax({
                    url: '{{ route("laporan-pasien.preview") }}',
                    method: 'POST',
                    data: formData,
                    beforeSend: function() {
                        $('#tableBody').html('<tr><td colspan="11" class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>');
                        $('#previewSection').show();
                    },
                    success: function(response) {
                        if (response.success) {
                            // Update Summary Cards
                            $('#statTotalPendapatan').text('Rp ' + formatRupiah(response.summary.total_pendapatan));
                            $('#statTotalPasien').text(response.summary.total_pasien + ' Transaksi');
                            $('#statPasienBaruCount').text(response.summary.pasien_baru_count + ' Pasien');
                            $('#statPasienBaruPendapatan').text('Rp ' + formatRupiah(response.summary.pasien_baru_pendapatan));
                            $('#statPasienLamaCount').text(response.summary.pasien_lama_count + ' Pasien');
                            $('#statPasienLamaPendapatan').text('Rp ' + formatRupiah(response.summary.pasien_lama_pendapatan));
                            $('#summarySection').slideDown();

                            $('#totalDataBadge').text(response.summary.total_pasien + ' Transaksi');

                            // Update Table
                            let html = '';
                            if (!response.data || response.data.length === 0) {
                                html = '<tr><td colspan="11" class="text-center text-muted py-4">Tidak ada data untuk filter dan periode ini</td></tr>';
                            } else {
                                response.data.forEach((item, index) => {
                                    let badgeTipe = item.tipe_pasien === 'Pasien Baru'
                                        ? '<span class="badge bg-success-subtle text-success">Pasien Baru</span>'
                                        : (item.tipe_pasien === 'Pasien Lama'
                                            ? '<span class="badge bg-warning-subtle text-warning">Pasien Lama</span>'
                                            : item.tipe_pasien);

                                    let noTrx = item.kode && item.kode !== '-'
                                        ? `<div class="text-muted fw-normal mt-1" style="font-size: 0.78rem;"><i class="ti ti-receipt me-1"></i>${item.kode}</div>`
                                        : '';

                                    html += `
                                        <tr>
                                            <td class="text-center">${index + 1}</td>
                                            <td>${item.tanggal}</td>
                                            <td class="fw-semibold">
                                                <div>${item.nama_pasien}</div>
                                                ${noTrx}
                                            </td>
                                            <td class="text-center">${badgeTipe}</td>
                                            <td>${item.dental_unit}</td>
                                            <td>${item.dokter}</td>
                                            <td>${item.perawat}</td>
                                            <td>${item.resepsionis}</td>
                                            <td>${item.cabang}</td>
                                            <td><small>${item.jenis_perawatan}</small></td>
                                            <td class="text-end fw-bold text-primary">Rp ${formatRupiah(item.total_biaya)}</td>
                                        </tr>
                                    `;
                                });

                                // Total Row
                                html += `
                                    <tr class="table-primary fw-bold">
                                        <td colspan="10" class="text-end">TOTAL PENDAPATAN:</td>
                                        <td class="text-end text-primary fw-bold">Rp ${formatRupiah(response.summary.total_pendapatan)}</td>
                                    </tr>
                                `;
                            }

                            $('#tableBody').html(html);

                            // Tampilkan floating scroll buttons jika data lebih dari 5
                            if (response.data && response.data.length > 5) {
                                $('#scrollNavButtons').fadeIn();
                            } else {
                                $('#scrollNavButtons').fadeOut();
                            }
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: response.message || 'Gagal memuat data laporan'
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Terjadi kesalahan sistem saat memuat data laporan.'
                        });
                    }
                });
            });

            // Download Data Excel
            $('#btnDownload').click(function() {
                const formData = getFormData();

                const form = $('<form>', {
                    action: '{{ route("laporan-pasien.download") }}',
                    method: 'POST'
                });

                $.each(formData, function(key, value) {
                    form.append($('<input>', {
                        type: 'hidden',
                        name: key,
                        value: value
                    }));
                });

                $('body').append(form);
                form.submit();
                form.remove();
            });

            // Scroll to Top & Bottom
            $('#btnScrollTop').on('click', function() {
                $('html, body').animate({ scrollTop: 0 }, 300);
            });

            $('#btnScrollBottom').on('click', function() {
                $('html, body').animate({ scrollTop: $(document).height() }, 300);
            });

            $(window).on('scroll', function() {
                if ($(this).scrollTop() > 200) {
                    $('#scrollNavButtons').fadeIn();
                } else if ($('#tablePasien tbody tr').length <= 5) {
                    $('#scrollNavButtons').fadeOut();
                }
            });
        });
    </script>
@endpush
