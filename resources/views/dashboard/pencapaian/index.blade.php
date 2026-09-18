@extends('layouts.app')

@section('content')
<style>
    :root {
        --primary: #6d28d9;
        --primary-dark: #5b21b6;
        --primary-light: #ddd6fe;
        --primary-bg: #f5f3ff;
        --primary-50: #faf5ff;
    }

    body { background: #f8f9fc; }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        flex-wrap: wrap;
        gap: 16px;
    }

    .page-title {
        font-size: 28px;
        font-weight: 800;
        color: #1e1b4b;
        margin: 0;
        letter-spacing: -0.5px;
    }

    .page-subtitle {
        color: #6b7280;
        font-size: 14px;
        margin: 4px 0 0 0;
    }

    .filter-select {
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: 10px 16px;
        font-size: 14px;
        background: white;
        color: #374151;
        font-weight: 500;
        min-width: 180px;
        cursor: pointer;
        transition: all 0.2s;
    }
    .filter-select:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(109, 40, 217, 0.1);
    }

    .card-custom {
        background: white;
        border-radius: 16px;
        border: 1px solid #f0e9ff;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        padding: 24px;
    }

    /* ===== TOP STATS ===== */
    .top-stats {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 24px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }

    .stat-box {
        display: flex;
        align-items: center;
        gap: 20px;
        padding: 24px;
        background: white;
        border-radius: 16px;
        border: 1px solid #f0e9ff;
    }

    .stat-icon {
        width: 64px;
        height: 64px;
        border-radius: 14px;
        background: var(--primary-bg);
        color: var(--primary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
        flex-shrink: 0;
    }

    .stat-label {
        font-size: 11px;
        font-weight: 700;
        color: var(--primary);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 6px;
    }

    .stat-value {
        font-size: 32px;
        font-weight: 800;
        color: #1e1b4b;
        margin-bottom: 4px;
        letter-spacing: -0.5px;
    }

    .stat-desc {
        font-size: 13px;
        color: #6b7280;
    }

    /* Donut */
    .donut-wrapper {
        position: relative;
        width: 220px;
        height: 220px;
    }

    .donut-center-text {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
    }

    .donut-percent {
        font-size: 42px;
        font-weight: 800;
        color: var(--primary);
        line-height: 1;
        letter-spacing: -1px;
    }

    .donut-label {
        font-size: 13px;
        color: #6b7280;
        font-weight: 500;
        margin-top: 4px;
    }

    /* ===== INFO BANNER ===== */
    .info-banner {
        background: linear-gradient(135deg, #f5f3ff 0%, #ede9fe 100%);
        border-radius: 14px;
        padding: 18px 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        flex-wrap: wrap;
        gap: 12px;
    }

    .info-banner-left {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .info-banner-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: var(--primary);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }

    .info-banner-text {
        font-size: 15px;
        color: #374151;
    }

    .info-banner-text strong {
        color: var(--primary);
        font-weight: 700;
    }

    .info-banner-right {
        font-size: 14px;
        color: #6b7280;
    }

    .info-banner-right strong {
        color: var(--primary);
        font-size: 18px;
        font-weight: 700;
    }

    /* ===== MIDDLE: CHART + RINGKASAN ===== */
    .middle-row {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 24px;
        margin-bottom: 24px;
    }

    .chart-card {
        background: white;
        border-radius: 16px;
        border: 1px solid #f0e9ff;
        padding: 24px;
    }

    .chart-title {
        font-size: 18px;
        font-weight: 700;
        color: #1e1b4b;
        margin-bottom: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .chart-legend {
        display: flex;
        gap: 20px;
        font-size: 13px;
        color: #6b7280;
    }

    .legend-dot {
        width: 12px;
        height: 12px;
        border-radius: 3px;
        display: inline-block;
        margin-right: 6px;
        vertical-align: middle;
    }

    .ringkasan-card {
        background: white;
        border-radius: 16px;
        border: 1px solid #f0e9ff;
        padding: 24px;
    }

    .ringkasan-title {
        font-size: 18px;
        font-weight: 700;
        color: #1e1b4b;
        margin-bottom: 16px;
    }

    .ringkasan-item {
        display: flex;
        align-items: center;
        padding: 14px 0;
        border-bottom: 1px solid #f3f4f6;
    }
    .ringkasan-item:last-child { border-bottom: none; }

    .ringkasan-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: var(--primary-bg);
        color: var(--primary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        margin-right: 14px;
        flex-shrink: 0;
    }

    .ringkasan-label {
        flex-grow: 1;
        font-size: 14px;
        color: #4b5563;
    }

    .ringkasan-value {
        font-size: 15px;
        font-weight: 700;
        color: #1e1b4b;
        text-align: right;
    }

    /* ===== BOTTOM: KLINIK CARDS ===== */
    .section-title {
        font-size: 20px;
        font-weight: 700;
        color: #1e1b4b;
        margin-bottom: 16px;
    }

    .klinik-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }

    .klinik-card {
        background: white;
        border-radius: 14px;
        border: 1px solid #f0e9ff;
        padding: 18px;
        transition: all 0.2s;
    }
    .klinik-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(109, 40, 217, 0.1);
    }

    .klinik-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 12px;
    }

    .klinik-icon {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        flex-shrink: 0;
    }

    .klinik-name {
        font-size: 14px;
        font-weight: 600;
        color: #1e1b4b;
        margin-bottom: 2px;
    }

    .klinik-percent {
        font-size: 22px;
        font-weight: 800;
        letter-spacing: -0.5px;
    }

    .klinik-progress {
        height: 8px;
        background: #f0e9ff;
        border-radius: 4px;
        overflow: hidden;
        margin-bottom: 14px;
    }

    .klinik-progress-bar {
        height: 100%;
        border-radius: 4px;
        transition: width 0.6s ease;
    }

    .klinik-detail {
        font-size: 12px;
    }

    .klinik-detail-row {
        display: flex;
        justify-content: space-between;
        padding: 3px 0;
    }

    .klinik-detail-label { color: #6b7280; }
    .klinik-detail-value { font-weight: 600; color: #1e1b4b; }

    /* Warna Klinik */
    .clr-1 { background: #fce7f3; color: #db2777; }
    .clr-2 { background: #ede9fe; color: #7c3aed; }
    .clr-3 { background: #d1fae5; color: #059669; }
    .clr-4 { background: #dbeafe; color: #2563eb; }
    .clr-5 { background: #fef3c7; color: #d97706; }
    .clr-6 { background: #ccfbf1; color: #0d9488; }
    .clr-7 { background: #ffedd5; color: #ea580c; }

    .bar-1 { background: #f472b6; }
    .bar-2 { background: #a78bfa; }
    .bar-3 { background: #34d399; }
    .bar-4 { background: #60a5fa; }
    .bar-5 { background: #fbbf24; }
    .bar-6 { background: #2dd4bf; }
    .bar-7 { background: #fb923c; }

    /* ===== FOOTER ===== */
    .dashboard-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 20px;
        border-top: 1px solid #f0e9ff;
        margin-top: 8px;
        flex-wrap: wrap;
        gap: 12px;
    }

    .footer-item {
        font-size: 13px;
        color: #6b7280;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .footer-item i { color: var(--primary); }

    /* Responsive */
    @media (max-width: 992px) {
        .top-stats {
            grid-template-columns: 1fr;
        }
        .donut-wrapper { margin: 0 auto; }
        .middle-row { grid-template-columns: 1fr; }
        .stat-value { font-size: 26px; }
        .donut-percent { font-size: 34px; }
    }
</style>

<div class="container-fluid py-4">
    {{-- ===== HEADER ===== --}}
    <div class="page-header">
        <div>
            <h1 class="page-title">Dashboard Pencapaian Klinik</h1>
            <p class="page-subtitle">Ringkasan Target dan Pencapaian Pendapatan</p>
        </div>
        <div class="d-flex gap-2 align-items-center">
            <select class="filter-select" id="filterBulan">
                @foreach($daftarBulan as $num => $nama)
                    <option value="{{ $num }}" {{ $bulan == $num ? 'selected' : '' }}>{{ $nama }}</option>
                @endforeach
            </select>
            <select class="filter-select" id="filterTahun">
                @foreach($daftarTahun as $y)
                    <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- ===== TOP STATS (3 kolom: Target | Donut | Pencapaian) ===== --}}
    <div class="row top-stats">
        <div class="col-md-12">
            <div class="stat-box" style="display: flex; align-items: center; justify-content: space-between; width: 100%; gap: 32px;">
                {{-- Target Bulanan --}}
                <div style="flex:1; display: flex; align-items: center; gap: 16px;">
                    <div class="stat-icon">
                        <i class="ti ti-target"></i>
                    </div>
                    <div>
                        <div class="stat-label">Target Bulanan (Semua Klinik)</div>
                        <div class="stat-value">Rp {{ number_format($totalTarget, 0, ',', '.') }}</div>
                        <div class="stat-desc">Target Pendapatan Bulan Ini</div>
                    </div>
                </div>

                {{-- Donut Chart --}}
                <div class="donut-wrapper" style="flex:1; display: flex; flex-direction: column; align-items: center; min-width: 160px;">
                    <canvas id="donutChart" style="max-width: 220px;"></canvas>
                    <div class="donut-center-text">
                        <div class="donut-percent">{{ $persentaseTotal }}%</div>
                        <div class="donut-label">Pencapaian</div>
                    </div>
                </div>

                {{-- Total Pencapaian --}}
                <div style="flex:1; display: flex; align-items: center; gap: 16px; justify-content: flex-end;">
                    <div class="stat-icon">
                        <i class="ti ti-chart-bar"></i>
                    </div>
                    <div>
                        <div class="stat-label">Total Pencapaian</div>
                        <div class="stat-value">Rp {{ number_format($totalPencapaian, 0, ',', '.') }}</div>
                        <div class="stat-desc">Total Pendapatan Saat Ini</div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- ===== INFO BANNER ===== --}}
    <div class="info-banner">
        <div class="info-banner-left">
            <div class="info-banner-icon">
                <i class="ti ti-arrow-up"></i>
            </div>
            <div class="info-banner-text">
                Anda telah mencapai <strong>{{ $persentaseTotal }}%</strong> dari target bulanan semua klinik.
            </div>
        </div>
        <div class="info-banner-right">
            Sisa untuk mencapai target: <strong>Rp {{ number_format($sisaTotal, 0, ',', '.') }}</strong>
        </div>
    </div>

    {{-- ===== MIDDLE: CHART + RINGKASAN ===== --}}
    <div class="middle-row">
        {{-- Bar Chart --}}
        <div class="chart-card">
            <div class="chart-title">
                <span>Perbandingan Pencapaian per Klinik</span>
                <div class="chart-legend">
                    <span><span class="legend-dot" style="background: #ddd6fe;"></span>Target</span>
                    <span><span class="legend-dot" style="background: #6d28d9;"></span>Pencapaian</span>
                </div>
            </div>
            <div style="height: 320px;">
                <canvas id="barChart"></canvas>
            </div>
        </div>

        {{-- Ringkasan --}}
        <div class="ringkasan-card">
            <div class="ringkasan-title">Ringkasan</div>

            <div class="ringkasan-item">
                <div class="ringkasan-icon"><i class="ti ti-target"></i></div>
                <div class="ringkasan-label">Target Bulanan (Semua Klinik)</div>
                <div class="ringkasan-value">Rp {{ number_format($totalTarget, 0, ',', '.') }}</div>
            </div>

            <div class="ringkasan-item">
                <div class="ringkasan-icon"><i class="ti ti-chart-bar"></i></div>
                <div class="ringkasan-label">Total Pencapaian</div>
                <div class="ringkasan-value">Rp {{ number_format($totalPencapaian, 0, ',', '.') }}</div>
            </div>

            <div class="ringkasan-item">
                <div class="ringkasan-icon"><i class="ti ti-percentage"></i></div>
                <div class="ringkasan-label">Persentase Pencapaian</div>
                <div class="ringkasan-value">{{ $persentaseTotal }}%</div>
            </div>

            <div class="ringkasan-item">
                <div class="ringkasan-icon"><i class="ti ti-flag"></i></div>
                <div class="ringkasan-label">Sisa untuk Target</div>
                <div class="ringkasan-value">Rp {{ number_format($sisaTotal, 0, ',', '.') }}</div>
            </div>

            <div class="ringkasan-item">
                <div class="ringkasan-icon"><i class="ti ti-building"></i></div>
                <div class="ringkasan-label">Jumlah Klinik</div>
                <div class="ringkasan-value">{{ $jumlahKlinikAktif }} Cabang</div>
            </div>
        </div>
    </div>

    {{-- ===== BOTTOM: PENCAPAIAN PER KLINIK ===== --}}
    <div class="section-title">Pencapaian per Klinik</div>

    <div class="klinik-grid">
        @foreach($dataPerKlinik as $index => $klinik)
            @php
                $colorIdx = ($index % 7) + 1;
                $barColor = $klinik['persentase'] >= 100 ? '#10b981' :
                           ($klinik['persentase'] >= 75 ? '#6d28d9' :
                           ($klinik['persentase'] >= 50 ? '#f59e0b' : '#ef4444'));
            @endphp
            <div class="klinik-card">
                <div class="klinik-header">
                    <div class="klinik-icon clr-{{ $colorIdx }}">
                        <i class="ti ti-building"></i>
                    </div>
                    <div>
                        <div class="klinik-name">{{ $klinik['nama'] }}</div>
                        <div class="klinik-percent" style="color: {{ $barColor }};">
                            {{ $klinik['persentase'] }}%
                        </div>
                    </div>
                </div>

                <div class="klinik-progress">
                    <div class="klinik-progress-bar" style="width: {{ min(100, $klinik['persentase']) }}%; background: {{ $barColor }};"></div>
                </div>

                <div class="klinik-detail">
                    <div class="klinik-detail-row">
                        <span class="klinik-detail-label">Target</span>
                        <span class="klinik-detail-value">Rp {{ number_format($klinik['target'], 0, ',', '.') }}</span>
                    </div>
                    <div class="klinik-detail-row">
                        <span class="klinik-detail-label">Pencapaian</span>
                        <span class="klinik-detail-value">Rp {{ number_format($klinik['pencapaian'], 0, ',', '.') }}</span>
                    </div>
                    <div class="klinik-detail-row">
                        <span class="klinik-detail-label">Sisa</span>
                        <span class="klinik-detail-value" style="color: #ef4444;">Rp {{ number_format($klinik['sisa'], 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- ===== FOOTER ===== --}}
    <div class="dashboard-footer">
        <div class="footer-item">
            <i class="ti ti-star-filled" style="color: #f59e0b;"></i>
            Data diperbarui per {{ $lastUpdate }} WIB
        </div>
        <div class="footer-item">
            <i class="ti ti-trending-up"></i>
            Terus tingkatkan performa, karena setiap senyum berarti!
        </div>
    </div>
</div>
@endsection
  @php
        $user = auth()->user();
        $today = \Carbon\Carbon::now()->toDateString();
        $forceShiftModal = false;
        if($user) {
            $forceShiftModal = empty($user->shift) || $user->last_login !== $today;
        }
    @endphp

    @if ($forceShiftModal)
        <!-- Modal for Set Shift -->
        <div class="modal fade" id="setShiftModal" tabindex="-1" aria-labelledby="setShiftModalLabel"
            aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-dialog-centered">
                <form method="POST" action="{{ route('home.update-shift') }}" id="setShiftForm">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="setShiftModalLabel">Pilih Shift Kerja Anda</h5>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-warning">
                                @if(empty($user->shift))
                                    Shift Anda belum diatur. Silakan pilih shift aktif anda sebelum melanjutkan.
                                @else
                                    Shift Anda kadaluarsa atau belum diatur hari ini. Silakan pilih shift aktif anda untuk hari ini.
                                @endif
                            </div>
                            <div class="mb-3">
                                <label for="shift" class="form-label">Shift</label>
                                <select name="shift" id="shift" class="form-select" required>
                                    <option value="">-- Pilih Shift --</option>
                                    @foreach ($listShift as $shift)
                                        <option value="{{ $shift->id }}">
                                            {{ $shift->Nama }}
                                            ({{ \Carbon\Carbon::createFromFormat('H:i:s', $shift->JamMulai)->format('H:i') }}
                                            -
                                            {{ \Carbon\Carbon::createFromFormat('H:i:s', $shift->JamSelesai)->format('H:i') }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Simpan Shift</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        @push('scripts')
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    var setShiftModal = new bootstrap.Modal(document.getElementById('setShiftModal'));
                    setShiftModal.show();
                });
            </script>
            @if (session('success'))
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        setTimeout(function() {
                            var alert = document.createElement('div');
                            alert.className = 'alert alert-success alert-dismissible fade show';
                            alert.role = 'alert';
                            alert.innerHTML = '{{ session('success') }}' +
                                '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
                            document.body.appendChild(alert);
                            setTimeout(function() {
                                var bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
                                bsAlert.close();
                            }, 3500);
                        }, 500);
                    });
                </script>
            @endif
        @endpush
    @endif
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    // ===== FILTER CHANGE =====
    $('#filterBulan, #filterTahun').on('change', function() {
        const bulan = $('#filterBulan').val();
        const tahun = $('#filterTahun').val();
        window.location.href = `{{ route('dashboard.pencapaian') }}?bulan=${bulan}&tahun=${tahun}`;
    });

    // ===== DONUT CHART =====
    const donutCtx = document.getElementById('donutChart').getContext('2d');
    const persentase = {{ $persentaseTotal }};
    const sisa = Math.max(0, 100 - persentase);

    new Chart(donutCtx, {
        type: 'doughnut',
        data: {
            datasets: [{
                data: [persentase, sisa],
                backgroundColor: ['#6d28d9', '#f0e9ff'],
                borderWidth: 0,
                cutout: '78%'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: { enabled: false } },
            animation: { animateRotate: true, duration: 1200 }
        }
    });

    // ===== BAR CHART =====
    const barCtx = document.getElementById('barChart').getContext('2d');
    const labels = @json($chartLabels);
    const targets = @json($chartTarget);
    const capaians = @json($chartCapaian);

    new Chart(barCtx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Target',
                    data: targets,
                    backgroundColor: '#ddd6fe',
                    borderRadius: 6,
                    barPercentage: 0.5,
                    categoryPercentage: 0.7
                },
                {
                    label: 'Pencapaian',
                    data: capaians,
                    backgroundColor: '#6d28d9',
                    borderRadius: 6,
                    barPercentage: 0.5,
                    categoryPercentage: 0.7
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1e1b4b',
                    padding: 12,
                    cornerRadius: 8,
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': Rp ' + context.parsed.y.toLocaleString('id-ID');
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#f3f4f6', drawBorder: false },
                    ticks: {
                        callback: function(value) {
                            return (value / 1000000).toFixed(0) + ' JT';
                        },
                        font: { size: 11 },
                        color: '#6b7280'
                    }
                },
                x: {
                    grid: { display: false },
                    ticks: {
                        font: { size: 12, weight: '500' },
                        color: '#4b5563'
                    }
                }
            }
        }
    });
});
</script>
@endpush
