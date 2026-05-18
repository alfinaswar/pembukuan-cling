@extends('layouts.app')

@section('content')
    <div class="row mb-3 align-items-center">
        <div class="col-lg-6 col-md-6 col-sm-12">
            <h2 class="mb-0 fw-bold">General Report</h2>
            <small class="text-muted">Ciling Dental Clinic</small>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12">
            <form method="POST" action="{{ route('laporan-umum.store') }}">
                @csrf
                <div class="input-group mb-3">
                    <input type="text" id="FilterTanggal" class="form-control shawCalRanges" name="FilterTanggal"
                        data-url="{{ route('laporan-umum.index') }}" readonly />
                    <span class="input-group-text">
                        <i class="ti ti-calendar fs-5"></i>
                    </span>
                    <button type="submit" class="btn btn-primary" id="btn-terapkan" style="margin-left: 10px;">
                        Terapkan
                    </button>
                </div>
            </form>
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
                                {{ 'Rp ' . number_format($totalBiaya ?? 0, 0, ',', '.') }}
                            </h3>
                            <small class="text-muted">
                                <span class="me-1">
                                    @php
                                        $persen = $totalBiayaPersen ?? 0;
                                        $sign = $persen > 0 ? '+' : ($persen < 0 ? '' : '+');
                                        $color =
                                            $persen > 0 ? 'text-success' : ($persen < 0 ? 'text-danger' : 'text-muted');
                                    @endphp
                                    <span class="{{ $color }}" id="persen-biaya">{{ $sign . $persen }}%</span>
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
                                {{ $totalPasien ?? 0 }}
                            </h3>
                            <small class="text-muted">
                                <span class="me-1">
                                    @php
                                        $persen = $totalPasienPersen ?? 0;
                                        $sign = $persen > 0 ? '+' : ($persen < 0 ? '' : '+');
                                        $color =
                                            $persen > 0 ? 'text-success' : ($persen < 0 ? 'text-danger' : 'text-muted');
                                    @endphp
                                    <span class="{{ $color }}" id="persen-pasien">{{ $sign . $persen }}%</span>
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
                                {{ $pasienBaru ?? 0 }}
                            </h3>
                            <small class="text-muted">
                                <span class="me-1">
                                    @php
                                        $persen = $totalPasienBaruPersen ?? 0;
                                        $sign = $persen > 0 ? '+' : ($persen < 0 ? '' : '+');
                                        $color =
                                            $persen > 0 ? 'text-info' : ($persen < 0 ? 'text-danger' : 'text-muted');
                                    @endphp
                                    <span class="{{ $color }}" id="persen-pasien-baru">{{ $sign . $persen }}%</span>
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
                                {{ $pasienLama ?? 0 }}
                            </h3>
                            <small class="text-muted">
                                <span class="me-1">
                                    @php
                                        $persen = $totalPasienLamaPersen ?? 0;
                                        $sign = $persen > 0 ? '+' : ($persen < 0 ? '' : '+');
                                        $color =
                                            $persen > 0 ? 'text-warning' : ($persen < 0 ? 'text-danger' : 'text-muted');
                                    @endphp
                                    <span class="{{ $color }}"
                                        id="persen-pasien-lama">{{ $sign . $persen }}%</span>
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
                <div class="card-header">
                    <h5 class="mb-0 fw-semibold">
                        <i class="ti ti-stethoscope me-2"></i>
                        Jenis Perawatan Terbanyak
                    </h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @forelse ($jenisPerawatanTerbanyak as $perawatan)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>
                                    {{ $perawatan['JenisPerawatan'] ?? '-' }}
                                </span>
                                <span class="badge bg-primary rounded-pill">
                                    {{ $perawatan['jumlah'] ?? 0 }} Pasien
                                </span>
                            </li>
                        @empty
                            <div class="text-muted">
                                Tidak ada data jenis perawatan terbanyak untuk periode ini.
                            </div>
                        @endforelse
                    </ul>


                </div>
            </div>
        </div>

    </div>
    <div class="row">
        <div class="col-12">
            <div class="d-flex align-items-center justify-content-between mb-3">

            </div>
        </div>
        <div class="datatables">
            <div class="card">
                <div class="card-header bg-teal-primary text-white d-flex align-items-center justify-content-between">
                    <h5 class="mb-0 fw-semibold">
                        <i class="ti ti-file-text me-2"></i>
                        Daftar Transaksi Pasien Terbaru
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="zero_config" class="table table-striped table-bordered text-nowrap align-middle">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Pasien</th>
                                    <th>Jenis Perawatan</th>
                                    <th>Total Biaya</th>
                                    <th>Cara Bayar</th>
                                    <th>Tanggal</th>
                                    <th>Cabang</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (isset($transaksiTerbaru) && count($transaksiTerbaru) > 0)
                                    @foreach ($transaksiTerbaru as $i => $tr)
                                        @php
                                            $detailCount = !empty($tr->TransaksiDetail)
                                                ? count($tr->TransaksiDetail)
                                                : 0;
                                            $rowspan = $detailCount > 0 ? $detailCount : 1;
                                        @endphp
                                        @if ($detailCount > 0)
                                            @foreach ($tr->TransaksiDetail as $j => $td)
                                                <tr>
                                                    @if ($j == 0)
                                                        <td rowspan="{{ $rowspan }}">{{ $i + 1 }}</td>
                                                        <td rowspan="{{ $rowspan }}">{{ $tr->NamaPasien ?? '-' }}
                                                        </td>
                                                    @endif
                                                    <td>
                                                        {{ $td->MasterJenisPerawatan->Nama ?? '-' }}:
                                                        <span class="text-secondary">
                                                            {{ 'Rp ' . number_format($td->Biaya ?? 0, 0, ',', '.') }}
                                                        </span>
                                                    </td>
                                                    @if ($j == 0)
                                                        <td rowspan="{{ $rowspan }}">
                                                            {{ 'Rp ' . number_format($tr->TotalBayar ?? 0, 0, ',', '.') }}
                                                        </td>
                                                        <td rowspan="{{ $rowspan }}">
                                                            {{ $tr->getMetodePembayaran->Nama ?? ($tr->MetodePembayaran ?? '-') }}
                                                        </td>
                                                        <td rowspan="{{ $rowspan }}">
                                                            {{ \Carbon\Carbon::parse($tr->created_at)->format('d/m/Y H:i') }}
                                                        </td>
                                                        <td rowspan="{{ $rowspan }}">
                                                            {{ $tr->getCabang->Nama ?? '-' }}
                                                        </td>
                                                    @endif
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td>{{ $i + 1 }}</td>
                                                <td>{{ $tr->NamaPasien ?? '-' }}</td>
                                                <td>-</td>
                                                <td>{{ 'Rp ' . number_format($tr->TotalBayar ?? 0, 0, ',', '.') }}</td>
                                                <td>{{ $tr->getMetodePembayaran->Nama ?? ($tr->MetodePembayaran ?? '-') }}
                                                </td>
                                                <td>{{ \Carbon\Carbon::parse($tr->created_at)->format('d/m/Y H:i') }}</td>
                                                <td>{{ $tr->getCabang->Nama ?? '-' }}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @endif

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

    </div>
@endsection
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // Data dari backend Laravel
            var paymentChartLabels = @json($paymentChartLabels ?? []);
            var paymentChartTotals = @json($paymentChartTotals ?? []).map(item => Number(item));

            var chart = null;

            function getDistinctColors(count) {
                const palette = [
                    '#009EF7', // Biru
                    '#F79009', // Oranye
                    '#14B8A6', // Toska
                    '#EF4444', // Merah
                    '#84CC16', // Lime
                    '#7239EA', // Ungu
                    '#FF8700', // Orange Tua
                    '#10B981', // Emerald
                    '#06B6D4', // Cyan
                    '#F43F5E', // Rose
                    '#EAB308', // Yellow
                    '#3B82F6', // Indigo
                    '#8B5CF6', // Violet
                    '#43E5F5', // Cyan mudah
                    '#A855F7', // Deep Purple
                    '#F97316', // Soft Orange
                    '#0EA5E9' // Light Blue
                ];
                // Kalau jumlah label lebih dari palette, generate warna tambahan
                if (count <= palette.length) {
                    return palette.slice(0, count);
                } else {
                    // Warnanya di-generate dengan hue berbeda
                    let colors = palette.slice();
                    for (let i = palette.length; i < count; i++) {
                        let hue = Math.floor((i * 360) / count);
                        colors.push(`hsl(${hue}, 70%, 55%)`);
                    }
                    return colors;
                }
            }

            function renderChart(labels, totals) {
                const chartColors = getDistinctColors(labels.length);

                var options = {
                    chart: {
                        type: 'donut',
                        height: 300,
                        toolbar: {
                            show: false
                        }
                    },

                    series: totals,

                    labels: labels,

                    colors: chartColors,

                    legend: {
                        show: true,
                        position: 'right',
                        fontSize: '10px',

                        markers: {
                            width: 10,
                            height: 10,
                            radius: 12
                        },

                        itemMargin: {
                            horizontal: 5,
                            vertical: 3
                        },

                        formatter: function(seriesName, opts) {

                            var value = opts.w.globals.series[opts.seriesIndex];

                            var percent = (
                                (value / paymentChartTotals.reduce((a, b) => a + b, 0)) * 100
                            );

                            return `
                            <div style="
                                display:flex;
                                align-items:center;
                                justify-content:space-between;
                                width:290px;
                            ">

                                <span style="
                                    flex:1;
                                    color:#252733;
                                ">
                                    ${seriesName}
                                </span>

                                <span style="
                                    min-width:95px;
                                    text-align:right;
                                    font-weight:600;
                                    color:#111827;
                                ">
                                    Rp ${Number(value).toLocaleString('id-ID')}
                                </span>

                                <span style="
                                    width:45px;
                                    text-align:right;
                                    color:#6B7280;
                                ">
                                    ${percent.toFixed(1)}%
                                </span>

                            </div>
                        `;
                        }
                    },

                    stroke: {
                        width: 0
                    },

                    dataLabels: {
                        enabled: false
                    },

                    tooltip: {
                        enabled: true,
                        y: {
                            formatter: function(v) {
                                return 'Rp ' + Number(v).toLocaleString('id-ID');
                            }
                        }
                    },

                    plotOptions: {
                        pie: {
                            donut: {
                                size: '70%',
                                labels: {
                                    show: true,
                                    name: {
                                        show: true,
                                        offsetY: 20,
                                        fontSize: '14px',
                                        color: '#6B7280',
                                        formatter: function() {
                                            return 'Total';
                                        }
                                    },
                                    value: {
                                        show: true
                                    },
                                    total: {
                                        show: true,
                                        showAlways: true,
                                        label: 'Tot3232wal',
                                        fontSize: '14px',
                                        fontWeight: 500,
                                        color: '#6B7280',

                                        formatter: function() {

                                            var total = paymentChartTotals.reduce((a, b) => a + b, 0);

                                            return 'Rp ' + total.toLocaleString('id-ID');
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
                        labels: labels,
                        colors: chartColors
                    });

                } else {

                    chart = new ApexCharts(
                        document.querySelector('#chart-payment-total'),
                        options
                    );

                    chart.render();
                }
            }
            renderChart(paymentChartLabels, paymentChartTotals);

        });
    </script>
@endpush
