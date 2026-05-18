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
        <div class="col-lg-8">
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

        <!-- Quick Actions + Notifications -->
        <div class="col-lg-4">
            <!-- Quick Actions -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-transparent border-0 pb-0">
                    <h5 class="card-title fw-semibold mb-0">
                        <i class="ti ti-bolt me-2 text-warning"></i>
                        Aksi Cepat
                    </h5>
                </div>
                <div class="card-body pt-2">
                    <div class="d-grid gap-2">
                        <a href="#" class="btn btn-primary d-flex align-items-center px-4">
                            <i class="ti ti-plus me-2"></i>
                            Tambah Produk
                        </a>
                        <a href="#" class="btn btn-outline-primary d-flex align-items-center px-4">
                            <i class="ti ti-list-check me-2"></i>
                            Kelola Pesanan
                        </a>
                        <a href="#" class="btn btn-outline-secondary d-flex align-items-center px-4">
                            <i class="ti ti-users me-2"></i>
                            Data Pelanggan
                        </a>
                        <a href="#" class="btn btn-outline-success d-flex align-items-center px-4">
                            <i class="ti ti-file-invoice me-2"></i>
                            Laporan
                        </a>
                    </div>
                </div>
            </div>

            <!-- Recent Notifications -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-transparent border-0 pb-0">
                    <h5 class="card-title fw-semibold mb-0">
                        <i class="ti ti-bell me-2 text-danger"></i>
                        Notifikasi
                    </h5>
                </div>
                <div class="card-body pt-2">
                    <div class="d-flex flex-column gap-3">
                        <!-- Notification Item -->
                        <div class="d-flex align-items-start">
                            <div class="rounded-circle bg-success-subtle p-2 me-3">
                                <i class="ti ti-check text-success"></i>
                            </div>
                            <div class="flex-grow-1">
                                <p class="mb-0 fw-medium fs-7">Pesanan #ORD-001 selesai</p>
                                <small class="text-muted">2 menit yang lalu</small>
                            </div>
                            <span class="badge bg-success-subtle text-success">Baru</span>
                        </div>

                        <div class="d-flex align-items-start">
                            <div class="rounded-circle bg-primary-subtle p-2 me-3">
                                <i class="ti ti-user-plus text-primary"></i>
                            </div>
                            <div class="flex-grow-1">
                                <p class="mb-0 fw-medium fs-7">Pelanggan baru terdaftar</p>
                                <small class="text-muted">15 menit yang lalu</small>
                            </div>
                        </div>

                        <div class="d-flex align-items-start">
                            <div class="rounded-circle bg-warning-subtle p-2 me-3">
                                <i class="ti ti-alert-triangle text-warning"></i>
                            </div>
                            <div class="flex-grow-1">
                                <p class="mb-0 fw-medium fs-7">Stok produk menipis</p>
                                <small class="text-muted">1 jam yang lalu</small>
                            </div>
                        </div>
                    </div>
                    <div class="text-center mt-3">
                        <a href="#" class="btn btn-sm btn-link text-decoration-none">
                            Lihat Semua <i class="ti ti-chevron-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders Table -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-transparent border-0 d-flex align-items-center justify-content-between">
                    <h5 class="card-title fw-semibold mb-0">
                        <i class="ti ti-list-details me-2 text-info"></i>
                        Pesanan Terbaru
                    </h5>
                    <a href="#" class="btn btn-sm btn-outline-primary px-4">
                        <i class="ti ti-eye me-1"></i> Lihat Semua
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 py-3 fw-semibold text-muted fs-7">ID Pesanan</th>
                                    <th class="py-3 fw-semibold text-muted fs-7">Pelanggan</th>
                                    <th class="py-3 fw-semibold text-muted fs-7">Tanggal</th>
                                    <th class="py-3 fw-semibold text-muted fs-7">Total</th>
                                    <th class="py-3 fw-semibold text-muted fs-7">Status</th>
                                    <th class="py-3 fw-semibold text-muted fs-7 text-end pe-4">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="recent-orders-body">
                                <!-- Dummy data will be inserted here by JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ===== FORMAT RUPIAH HELPER =====
            const formatRupiah = (number) => {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(number);
            };

            // ===== DUMMY DATA =====
            const dummyStats = {
                pendapatan: 24750000,
                pesanan: 156,
                pelanggan: 89,
                produkTerjual: 342,
                persenPendapatan: 12.5,
                pesananBaru: 5,
                pelangganOnline: 12,
                produkTrending: 3
            };

            const dummyOrders = [{
                    id: 'ORD-001',
                    customer: 'Budi Santoso',
                    date: '2024-01-15',
                    total: 1250000,
                    status: 'completed'
                },
                {
                    id: 'ORD-002',
                    customer: 'Siti Nurhaliza',
                    date: '2024-01-15',
                    total: 875000,
                    status: 'processing'
                },
                {
                    id: 'ORD-003',
                    customer: 'Ahmad Rizki',
                    date: '2024-01-14',
                    total: 2100000,
                    status: 'shipped'
                },
                {
                    id: 'ORD-004',
                    customer: 'Dewi Lestari',
                    date: '2024-01-14',
                    total: 450000,
                    status: 'pending'
                },
                {
                    id: 'ORD-005',
                    customer: 'Eko Prasetyo',
                    date: '2024-01-13',
                    total: 3200000,
                    status: 'completed'
                },
            ];

            const chartData = {
                labels: ['Sen', 'Sel', 'Rabu', 'Kam', 'Jum', 'Sab', 'Min'],
                data: [12, 19, 15, 22, 18, 25, 30]
            };

            // ===== RENDER STATS =====
            document.getElementById('total-pendapatan').textContent = formatRupiah(dummyStats.pendapatan);
            document.getElementById('persen-pendapatan').textContent = `+${dummyStats.persenPendapatan}%`;
            document.getElementById('total-pesanan').textContent = dummyStats.pesanan;
            document.getElementById('pesanan-baru').textContent = dummyStats.pesananBaru;
            document.getElementById('total-pelanggan').textContent = dummyStats.pelanggan;
            document.getElementById('pelanggan-online').textContent = dummyStats.pelangganOnline;
            document.getElementById('produk-terjual').textContent = dummyStats.produkTerjual;
            document.getElementById('produk-trending').textContent = dummyStats.produkTrending;

            // ===== RENDER ORDERS TABLE =====
            const ordersBody = document.getElementById('recent-orders-body');
            const statusLabels = {
                'pending': {
                    text: 'Menunggu',
                    class: 'status-pending'
                },
                'processing': {
                    text: 'Diproses',
                    class: 'status-processing'
                },
                'shipped': {
                    text: 'Dikirim',
                    class: 'status-shipped'
                },
                'completed': {
                    text: 'Selesai',
                    class: 'status-completed'
                },
                'cancelled': {
                    text: 'Dibatalkan',
                    class: 'status-cancelled'
                }
            };

            dummyOrders.forEach((order, index) => {
                const status = statusLabels[order.status];
                const row = document.createElement('tr');
                row.className = 'animate-fadein';
                row.style.animationDelay = `${index * 0.1}s`;
                row.innerHTML = `
            <td class="ps-4 fw-medium">${order.id}</td>
            <td>
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-secondary-subtle me-2 d-flex align-items-center justify-content-center" style="width:32px;height:32px;">
                        <i class="ti ti-user fs-7"></i>
                    </div>
                    <span class="fw-medium">${order.customer}</span>
                </div>
            </td>
            <td>${new Date(order.date).toLocaleDateString('id-ID', { day: 'numeric', month: 'short' })}</td>
            <td class="fw-semibold">${formatRupiah(order.total)}</td>
            <td><span class="badge ${status.class}">${status.text}</span></td>
            <td class="text-end pe-4">
                <a href="#" class="btn btn-sm btn-outline-secondary px-3">
                    <i class="ti ti-eye"></i>
                </a>
            </td>
        `;
                ordersBody.appendChild(row);
            });

            // ===== CHART.JS CONFIGURATION =====
            const ctx = document.getElementById('salesChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: chartData.labels,
                    datasets: [{
                        label: 'Penjualan (Juta Rupiah)',
                        data: chartData.data,
                        borderColor: '#0d6efd',
                        backgroundColor: 'rgba(13, 110, 253, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#0d6efd',
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
                            backgroundColor: 'rgba(0,0,0,0.8)',
                            titleFont: {
                                size: 13
                            },
                            bodyFont: {
                                size: 12
                            },
                            padding: 12,
                            displayColors: false,
                            callbacks: {
                                label: function(context) {
                                    return `Rp ${context.parsed.y} Jt`;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                borderDash: [5, 5],
                                color: 'rgba(0,0,0,0.05)'
                            },
                            ticks: {
                                callback: function(value) {
                                    return value + ' Jt';
                                },
                                color: '#6c757d',
                                font: {
                                    size: 11
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: '#6c757d',
                                font: {
                                    size: 11
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
@endpush
