@extends('layouts.app')

@section('content')
    <div class="row mb-3 align-items-center">
        <div class="col-lg-6 col-md-6 col-sm-12">
            <h5 class="mb-0 fw-semibold">General Report</h5>
            <small class="text-muted">Ciling Dental Clinic</small>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12">
            <div class="input-group mb-3">
                <input type="text" id="FilterTanggal" class="form-control shawCalRanges" name="FilterTanggal"
                    data-url="{{ route('laporan-umum.index') }}" readonly />
                <span class="input-group-text">
                    <i class="ti ti-calendar fs-5"></i>
                </span>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Total Biaya -->
        <div class="col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div>
                            <p class="card-subtitle text-uppercase text-purple">Total Biaya</p>
                            <h3 class="fs-6" id="val-total-biaya">
                                {{ 'Rp ' . number_format($totalBiaya, 0, ',', '.') }}
                            </h3>
                            <small class="text-muted">
                                <span class="me-1">
                                    @php
                                        $sign = $totalBiayaPersen > 0 ? '+' : ($totalBiayaPersen < 0 ? '' : '+');
                                        $color =
                                            $totalBiayaPersen > 0
                                                ? 'text-success'
                                                : ($totalBiayaPersen < 0
                                                    ? 'text-danger'
                                                    : 'text-muted');
                                    @endphp
                                    <span class="{{ $color }}"
                                        id="persen-biaya">{{ $sign . $totalBiayaPersen }}%</span>
                                </span>
                                dibanding bulan kemarin
                            </small>
                        </div>
                        <div class="ms-auto">
                            <span
                                class="bg-primary bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center"
                                style="width:42px; height:42px;">
                                <i class="ti ti-wallet text-primary" style="font-size: 1.7rem;"></i>
                            </span>
                        </div>
                    </div>
                    <div class="progress text-bg-light">
                        <div class="progress-bar text-bg-primary" role="progressbar" style="width: 100%; height: 6px;"
                            aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Jumlah Total Pasien -->
        <div class="col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div>
                            <p class="card-subtitle text-uppercase text-success">Jumlah Total Pasien</p>
                            <h3 class="fs-6" id="val-total-pasien">
                                {{ $totalPasien }}
                            </h3>
                            <small class="text-muted">
                                <span class="me-1">
                                    @php
                                        $sign = $totalPasienPersen > 0 ? '+' : ($totalPasienPersen < 0 ? '' : '+');
                                        $color =
                                            $totalPasienPersen > 0
                                                ? 'text-success'
                                                : ($totalPasienPersen < 0
                                                    ? 'text-danger'
                                                    : 'text-muted');
                                    @endphp
                                    <span class="{{ $color }}"
                                        id="persen-pasien">{{ $sign . $totalPasienPersen }}%</span>
                                </span>
                                dibanding bulan kemarin
                            </small>
                        </div>
                        <div class="ms-auto">
                            <span
                                class="bg-success bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center"
                                style="width:42px; height:42px;">
                                <i class="ti ti-users text-success" style="font-size: 1.7rem;"></i>
                            </span>
                        </div>
                    </div>
                    <div class="progress text-bg-light">
                        <div class="progress-bar text-bg-success" role="progressbar" style="width: 100%; height: 6px;"
                            aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Pasien Baru -->
        <div class="col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div>
                            <p class="card-subtitle text-uppercase text-info">Pasien Baru</p>
                            <h3 class="fs-6" id="val-pasien-baru">
                                {{ $pasienBaru }}
                            </h3>
                            <small class="text-muted">
                                <span class="me-1">
                                    @php
                                        $sign =
                                            $totalPasienBaruPersen > 0 ? '+' : ($totalPasienBaruPersen < 0 ? '' : '+');
                                        $color =
                                            $totalPasienBaruPersen > 0
                                                ? 'text-info'
                                                : ($totalPasienBaruPersen < 0
                                                    ? 'text-danger'
                                                    : 'text-muted');
                                    @endphp
                                    <span class="{{ $color }}"
                                        id="persen-pasien-baru">{{ $sign . $totalPasienBaruPersen }}%</span>
                                </span>
                                dibanding bulan kemarin
                            </small>
                        </div>
                        <div class="ms-auto">
                            <span
                                class="bg-info bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center"
                                style="width:42px; height:42px;">
                                <i class="ti ti-user text-info" style="font-size: 1.7rem;"></i>
                            </span>
                        </div>
                    </div>
                    <div class="progress text-bg-light">
                        <div class="progress-bar text-bg-info" role="progressbar" style="width: 100%; height: 6px;"
                            aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Pasien Lama -->
        <div class="col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div>
                            <p class="card-subtitle text-uppercase text-warning">Pasien Lama</p>
                            <h3 class="fs-6" id="val-pasien-lama">
                                {{ $pasienLama }}
                            </h3>
                            <small class="text-muted">
                                <span class="me-1">
                                    @php
                                        $sign =
                                            $totalPasienLamaPersen > 0 ? '+' : ($totalPasienLamaPersen < 0 ? '' : '+');
                                        $color =
                                            $totalPasienLamaPersen > 0
                                                ? 'text-warning'
                                                : ($totalPasienLamaPersen < 0
                                                    ? 'text-danger'
                                                    : 'text-muted');
                                    @endphp
                                    <span class="{{ $color }}"
                                        id="persen-pasien-lama">{{ $sign . $totalPasienLamaPersen }}%</span>
                                </span>
                                dibanding bulan kemarin
                            </small>
                        </div>
                        <div class="ms-auto">
                            <span
                                class="bg-warning bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center"
                                style="width:42px; height:42px;">
                                <i class="ti ti-user-check text-warning" style="font-size: 1.7rem;"></i>
                            </span>
                        </div>
                    </div>
                    <div class="progress text-bg-light">
                        <div class="progress-bar text-bg-warning" role="progressbar" style="width: 100%; height: 6px;"
                            aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 fw-semibold">
                        <i class="ti ti-wallet me-2"></i>
                        Cara Pembayaran & Total Biaya
                    </h5>
                </div>
                <div class="card-body">
                    <div id="chart-payment-total" style="min-height: 360px;"></div>
                </div>
            </div>
        </div>



        <div class="col-lg-6">
            <div class="card mb-2">
                <div class="p-2 d-flex align-items-stretch h-100">
                    <div class="row w-100">
                        <div class="col-4 col-md-3 d-flex align-items-center pe-0">
                            <img src="../assets/images/products/s1.jpg" class="rounded img-fluid" />
                        </div>
                        <div class="col-8 col-md-9 d-flex align-items-center ps-2">
                            <div>
                                <a href="javascript:void(0)" class="card-title link-primary fw-semibold text-dark">
                                    50% sell on wrist watch
                                </a>
                                <p class="card-subtitle mt-1">
                                    By Daniel Jubile
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card mb-2">
                <div class="p-2 d-flex align-items-stretch h-100">
                    <div class="row w-100">
                        <div class="col-4 col-md-3 d-flex align-items-center pe-0">
                            <img src="../assets/images/products/s1.jpg" class="rounded img-fluid" />
                        </div>
                        <div class="col-8 col-md-9 d-flex align-items-center ps-2">
                            <div>
                                <a href="javascript:void(0)" class="card-title link-primary fw-semibold text-dark">
                                    50% sell on wrist watch
                                </a>
                                <p class="card-subtitle mt-1">
                                    By Daniel Jubile
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="d-flex align-items-center justify-content-between mb-3">

            </div>
        </div>
        {{-- <div class="datatables">
            <div class="card">
                <div class="card-header bg-teal-primary text-white d-flex align-items-center justify-content-between">
                    <h5 class="mb-0 fw-semibold">
                        <i class="ti ti-file-text me-2"></i>
                        Daftar Transaksi Pasien Terbaru
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tabel-transaksi-terbaru"
                            class="table table-striped table-bordered text-nowrap align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Tanggal</th>
                                    <th>Pasien</th>
                                    <th>Jenis Pasien</th>
                                    <th>Pembayaran</th>
                                    <th>Layanan</th>
                                    <th>Total</th>
                                    <th>Shift</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transaksiTerbaru as $i => $tr)
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td>{{ \Carbon\Carbon::parse($tr->created_at)->format('d/m/Y H:i') }}</td>
                                        <td>{{ $tr->NamaPasien ?? '-' }}</td>
                                        <td>{{ $tr->JenisPasien ?? '-' }}</td>
                                        <td>
                                            {{ $tr->rel_metode_pembayaran->Nama ?? ($tr->MetodePembayaran ?? '-') }}
                                        </td>
                                        <td>
                                            {{ $tr->Layanan ?? '-' }}
                                        </td>
                                        <td>
                                            {{ 'Rp ' . number_format($tr->TotalBayar ?? 0, 0, ',', '.') }}
                                        </td>
                                        <td>
                                            {{ $tr->Shift ?? '-' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted">Data tidak tersedia.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div> --}}

    </div>
@endsection
@push('scripts')
    <script>
        $(function() {

            // ── Init daterangepicker ──────────────────────────────────────────
            var defaultStart = moment().startOf('month');
            var defaultEnd = moment().endOf('month');

            $('#FilterTanggal').daterangepicker({
                startDate: defaultStart,
                endDate: defaultEnd,
                locale: {
                    format: 'DD/MM/YYYY',
                    separator: ' - ',
                    applyLabel: 'Terapkan',
                    cancelLabel: 'Batal',
                    fromLabel: 'Dari',
                    toLabel: 'Sampai',
                    customRangeLabel: 'Pilih Sendiri',
                    daysOfWeek: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
                    monthNames: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                    ],
                    firstDay: 1
                },
                ranges: {
                    'Hari Ini': [moment(), moment()],
                    '7 Hari Terakhir': [moment().subtract(6, 'days'), moment()],
                    'Bulan Ini': [moment().startOf('month'), moment().endOf('month')],
                    'Bulan Lalu': [moment().subtract(1, 'month').startOf('month'),
                        moment().subtract(1, 'month').endOf('month')
                    ],
                    'Tahun Ini': [moment().startOf('year'), moment().endOf('year')],
                }
            }, function(start, end) {
                fetchDashboard(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
            });

            // ── Chart instance ────────────────────────────────────────────────
            var chart = null;

            function renderChart(labels, totals) {
                var options = {
                    chart: {
                        type: 'donut',
                        height: 360
                    },
                    series: totals,
                    labels: labels,
                    colors: ['#009EF7', '#F79009', '#14B8A6', '#98A2B3', '#7239EA', '#43E5F5', '#FF8700'],
                    legend: {
                        position: 'right',
                        fontSize: '11px',
                        formatter: function(seriesName, opts) {
                            var value = opts.w.globals.series[opts.seriesIndex];
                            var percent = opts.w.globals.seriesPercent[opts.seriesIndex][0];
                            return seriesName + '&nbsp;&nbsp;Rp ' +
                                value.toLocaleString('id-ID') + '&nbsp;&nbsp;' +
                                percent.toFixed(1) + '%';
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    tooltip: {
                        y: {
                            formatter: v => 'Rp ' + v.toLocaleString('id-ID')
                        }
                    },
                    plotOptions: {
                        pie: {
                            donut: {
                                size: '70%',
                                labels: {
                                    show: true,
                                    name: {
                                        show: true
                                    },
                                    value: {
                                        show: true,
                                        formatter: v => 'Rp ' + Number(v).toLocaleString('id-ID')
                                    },
                                    total: {
                                        show: true,
                                        label: 'Total',
                                        formatter: function(w) {
                                            var t = w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                            return 'Rp ' + t.toLocaleString('id-ID');
                                        }
                                    }
                                }
                            }
                        }
                    },
                    responsive: [{
                        breakpoint: 768,
                        options: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }]
                };

                if (chart) {
                    chart.updateOptions({
                        series: totals,
                        labels: labels
                    });
                } else {
                    chart = new ApexCharts(document.querySelector('#chart-payment-total'), options);
                    chart.render();
                }
            }

            // ── Fetch data via AJAX ───────────────────────────────────────────
            function fetchDashboard(filterTanggal) {
                var url = $('#FilterTanggal').data('url');

                $.ajax({
                    url: url,
                    method: 'GET',
                    data: {
                        FilterTanggal: filterTanggal
                    },
                    beforeSend: function() {
                        // Opsional: loading indicator
                        $('#dashboard-cards').addClass('opacity-50');
                    },
                    success: function(res) {
                        // Update kartu statistik
                        $('#val-total-biaya').text('Rp ' + res.totalBiaya.toLocaleString('id-ID'));
                        $('#val-total-pasien').text(res.totalPasien);
                        $('#val-pasien-baru').text(res.pasienBaru);
                        $('#val-pasien-lama').text(res.pasienLama);

                        updatePersen('#persen-biaya', res.totalBiayaPersen, 'text-success',
                            'text-danger');
                        updatePersen('#persen-pasien', res.totalPasienPersen, 'text-success',
                            'text-danger');
                        updatePersen('#persen-pasien-baru', res.totalPasienBaruPersen, 'text-info',
                            'text-danger');
                        updatePersen('#persen-pasien-lama', res.totalPasienLamaPersen, 'text-warning',
                            'text-danger');

                        // Update chart
                        renderChart(res.paymentChartLabels, res.paymentChartTotals);
                    },
                    complete: function() {
                        $('#dashboard-cards').removeClass('opacity-50');
                    },
                    error: function() {
                        alert('Gagal memuat data. Silakan coba lagi.');
                    }
                });
            }

            function updatePersen(selector, persen, colorPos, colorNeg) {
                var sign = persen > 0 ? '+' : '';
                var color = persen > 0 ? colorPos : (persen < 0 ? colorNeg : 'text-muted');
                $(selector)
                    .text(sign + persen + '%')
                    .removeClass('text-success text-danger text-info text-warning text-muted')
                    .addClass(color);
            }

            // ── Load awal dengan nilai default ───────────────────────────────
            renderChart(@json($paymentChartLabels), @json($paymentChartTotals));
        });
    </script>
@endpush
