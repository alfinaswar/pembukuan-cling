@extends('layouts.app')

@section('content')
    <!-- Welcome Banner -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card w-100 bg-primary-subtle overflow-hidden shadow-sm border-0">
                <div class="card-body position-relative">
                    <div class="row align-items-center">
                        <div class="col-sm-7">
                            <div class="d-flex align-items-center mb-3">
                                <div class="rounded-circle overflow-hidden me-3 bg-white p-1">
                                    <img src="{{ asset('assets/images/profile/user-1.jpg') }}" alt="Foto Profil"
                                        class="rounded-circle" width="48" height="48">
                                </div>
                                <div>
                                    <h5 class="fw-semibold mb-0 fs-5">Selamat datang kembali, {{ Auth::user()->name }}! 👋
                                    </h5>
                                    <small class="text-muted">Semoga harimu menyenangkan</small>
                                </div>
                            </div>
                            <p class="mb-0 text-primary-emphasis">
                                <i class="ti ti-calendar-event me-1"></i>
                                {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                            </p>
                        </div>
                        <div class="col-sm-5">
                            <div class="text-end">
                                <img src="{{ asset('assets/images/backgrounds/welcome-bg.svg') }}" alt="Welcome"
                                    class="img-fluid" style="max-height: 120px;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <!-- Total Pendapatan -->
        <div class="col-md-6 col-xl-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <p class="text-muted mb-1 fs-7">Total Pendapatan</p>
                            <h4 class="fw-bold mb-0 text-dark" id="total-pendapatan">Rp 0</h4>
                            <small class="text-success">
                                <i class="ti ti-trending-up me-1"></i>
                                <span id="persen-pendapatan">+12.5%</span> dari bulan lalu
                            </small>
                        </div>
                        <div class="rounded-circle bg-success-subtle p-3">
                            <i class="ti ti-currency-rupiah fs-4 text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Pesanan -->
        <div class="col-md-6 col-xl-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <p class="text-muted mb-1 fs-7">Total Pesanan</p>
                            <h4 class="fw-bold mb-0 text-dark" id="total-pesanan">0</h4>
                            <small class="text-primary">
                                <i class="ti ti-package me-1"></i>
                                <span id="pesanan-baru">5</span> pesanan baru
                            </small>
                        </div>
                        <div class="rounded-circle bg-primary-subtle p-3">
                            <i class="ti ti-shopping-cart fs-4 text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pelanggan Aktif -->
        <div class="col-md-6 col-xl-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <p class="text-muted mb-1 fs-7">Pelanggan Aktif</p>
                            <h4 class="fw-bold mb-0 text-dark" id="total-pelanggan">0</h4>
                            <small class="text-info">
                                <i class="ti ti-users me-1"></i>
                                <span id="pelanggan-online">12</span> sedang online
                            </small>
                        </div>
                        <div class="rounded-circle bg-info-subtle p-3">
                            <i class="ti ti-users fs-4 text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Produk Terjual -->
        <div class="col-md-6 col-xl-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <p class="text-muted mb-1 fs-7">Produk Terjual</p>
                            <h4 class="fw-bold mb-0 text-dark" id="produk-terjual">0</h4>
                            <small class="text-warning">
                                <i class="ti ti-fire me-1"></i>
                                <span id="produk-trending">3</span> produk trending
                            </small>
                        </div>
                        <div class="rounded-circle bg-warning-subtle p-3">
                            <i class="ti ti-package-export fs-4 text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content: Chart + Recent Activity -->
    <div class="row g-4">
        <!-- Chart Area -->
        <div class="col-lg-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-transparent border-0 pb-0">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="card-title fw-semibold mb-0">
                            <i class="ti ti-chart-line me-2 text-primary"></i>
                            Grafik Penjualan
                        </h5>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="ti ti-calendar me-1"></i> Minggu Ini
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="#">Hari Ini</a></li>
                                <li><a class="dropdown-item active" href="#">Minggu Ini</a></li>
                                <li><a class="dropdown-item" href="#">Bulan Ini</a></li>
                                <li><a class="dropdown-item" href="#">Tahun Ini</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="salesChart" height="120"></canvas>
                </div>
            </div>
        </div>
        <!-- Recent Transactions Table -->
        <div class="col-lg-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-transparent border-0 pb-0">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="card-title fw-semibold mb-0">
                            <i class="ti ti-list-check me-2 text-primary"></i>
                            Transaksi Terbaru
                        </h5>
                        <a href="" class="btn btn-sm btn-primary px-4">
                            <i class="ti ti-eye me-1"></i> Lihat Semua
                        </a>
                    </div>
                </div>
                <div class="card-body pt-3">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-3">Kode</th>
                                    <th>Tanggal</th>
                                    <th>Nama Pasien</th>
                                    <th>Jenis</th>
                                    <th>Metode</th>
                                    <th class="text-end">Total Bayar</th>
                                    <th class="text-center">Shift</th>
                                    <th class="text-end pe-3">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentTransaksi as $trx)
                                    <tr class="animate-fadein" style="animation-delay: {{ $loop->index * 0.1 }}s">
                                        <td class="ps-3 fw-semibold text-primary">{{ $trx->Kode }}</td>
                                        <td>
                                            <small
                                                class="d-block">{{ \Carbon\Carbon::parse($trx->Tanggal)->translatedFormat('d M Y') }}</small>
                                            <small
                                                class="text-muted">{{ \Carbon\Carbon::parse($trx->Tanggal)->format('H:i') }}</small>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="rounded-circle bg-primary-subtle text-primary fw-bold d-flex align-items-center justify-content-center me-2"
                                                    style="width: 32px; height: 32px; font-size: 0.75rem">
                                                    {{ substr($trx->NamaPasien, 0, 1) }}
                                                </div>
                                                <span class="fw-medium">{{ $trx->NamaPasien }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border">
                                                <i class="ti ti-user me-1"></i>{{ $trx->JenisPasien }}
                                            </span>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                <i class="ti ti-wallet me-1"></i>
                                                {{ $trx->MetodePembayaran }}
                                            </small>
                                        </td>
                                        <td class="text-end fw-bold text-dark" data-raw="{{ $trx->TotalBayar }}">
                                            {{ number_format($trx->TotalBayar, 0, ',', '.') }}
                                        </td>
                                        <td class="text-center">
                                            {!! $trx->status_badge !!}
                                        </td>
                                        <td class="text-end pe-3">
                                            <div class="btn-group btn-group-sm">

                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4 text-muted">
                                            <i class="ti ti-inbox fs-1 d-block mb-2"></i>
                                            Belum ada transaksi
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- End of row g-4 -->
@endsection

@push('styles')
    <style>
        .fs-7 {
            font-size: 0.875rem;
        }

        .card-header {
            padding: 1rem 1.25rem;
        }

        .table th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
        }

        .table td {
            padding: 1rem 1.25rem;
        }

        .badge {
            font-weight: 500;
            padding: 0.35em 0.65em;
        }

        /* Status badge colors */
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-processing {
            background-color: #cce5ff;
            color: #004085;
        }

        .status-shipped {
            background-color: #d4edda;
            color: #155724;
        }

        .status-completed {
            background-color: #d1ecf1;
            color: #0c5460;
        }

        .status-cancelled {
            background-color: #f8d7da;
            color: #721c24;
        }

        /* Animation for fadeIn feedback */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fadein {
            animation: fadeIn 0.3s ease-out forwards;
        }
    </style>
@endpush
@push('scripts')
    <script src="{{ asset('') }}assets/libs/apexcharts/dist/apexcharts.min.js"></script>
    <script src="{{ asset('') }}assets/js/apex-chart/apex.pie.init.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // 💰 Format Rupiah dengan animasi counting
            function animateValue(element, start, end, duration, prefix = 'Rp ', suffix = '') {
                let startTimestamp = null;
                const step = (timestamp) => {
                    if (!startTimestamp) startTimestamp = timestamp;
                    const progress = Math.min((timestamp - startTimestamp) / duration, 1);
                    const value = Math.floor(progress * (end - start) + start);
                    element.textContent = prefix + value.toLocaleString('id-ID') + suffix;
                    if (progress < 1) window.requestAnimationFrame(step);
                };
                window.requestAnimationFrame(step);
            }

            // Update stats cards dengan data dari controller
            @if (isset($totalPendapatan))
                animateValue(document.getElementById('total-pendapatan'), 0, {{ $totalPendapatan }}, 1500, 'Rp ',
                    '');
            @endif
            @if (isset($totalPesanan))
                animateValue(document.getElementById('total-pesanan'), 0, {{ $totalPesanan }}, 1200, '', '');
            @endif
            @if (isset($pelangganAktif))
                animateValue(document.getElementById('total-pelanggan'), 0, {{ $pelangganAktif }}, 1000, '', '');
            @endif
            @if (isset($produkTerjual))
                animateValue(document.getElementById('produk-terjual'), 0, {{ $produkTerjual }}, 1000, '', '');
            @endif

            // Update persen perubahan dengan warna dinamis
            const persenEl = document.getElementById('persen-pendapatan');
            const persenValue = @json($persenPerubahan ?? 0);
            if (persenEl) {
                persenEl.textContent = (persenValue >= 0 ? '+' : '') + persenValue + '%';
                persenEl.className = persenValue >= 0 ? 'text-success' : 'text-danger';
                persenEl.parentElement.querySelector('i').className = persenValue >= 0 ?
                    'ti ti-trending-up me-1' :
                    'ti ti-trending-down me-1';
            }

            // 📊 Inisialisasi Chart.js
            const ctx = document.getElementById('salesChart');
            if (ctx) {
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: @json($chartLabels),
                        datasets: [{
                            label: 'Pendapatan',
                            data: @json($chartData),
                            borderColor: '#206bc4',
                            backgroundColor: 'rgba(32, 107, 196, 0.1)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: '#206bc4',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: '#1e293b',
                                titleColor: '#fff',
                                bodyColor: '#cbd5e1',
                                padding: 12,
                                displayColors: false,
                                callbacks: {
                                    label: function(context) {
                                        return 'Rp ' + new Intl.NumberFormat('id-ID').format(context
                                            .parsed.y);
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(0,0,0,0.05)'
                                },
                                ticks: {
                                    callback: function(value) {
                                        return 'Rp ' + (value / 1000) + 'K';
                                    }
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            }

            // 🔄 Auto-format Rupiah pada table (untuk konsistensi)
            document.querySelectorAll('td[data-raw]').forEach(el => {
                const raw = parseFloat(el.dataset.raw);
                if (!isNaN(raw)) {
                    el.textContent = 'Rp ' + raw.toLocaleString('id-ID');
                }
            });
        });
    </script>
@endpush
